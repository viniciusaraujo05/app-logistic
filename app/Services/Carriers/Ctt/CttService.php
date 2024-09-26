<?php

namespace App\Services\Carriers\Ctt;

use App\Actions\Carriers\SaveLabel;
use App\Actions\Carriers\Tracking\CreateTracking;
use App\Actions\Carriers\Volumes\CreateVolumes;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Integrations\Carriers\Ctt\CttIntegration;
use App\Models\Order;
use App\Repositories\TokenRepository;
use App\Utils\LabelUtils;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use SimpleXMLElement;
use Throwable;

class CttService
{
    private CreateTracking $createTracking;

    private CreateVolumes $createVolumes;

    private SaveLabel $saveLabel;

    private TokenRepository $tokenRepository;

    private Collection $tokens;

    /**
     * Constructs a new instance of the class.
     *
     * @param TokenRepository $tokenRepository The token repository used to retrieve tokens.
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->tokens = $this->tokenRepository->get(IntegrationEnum::CTT);
        $this->createTracking = new CreateTracking();
        $this->createVolumes = new CreateVolumes();
        $this->saveLabel = new SaveLabel();
    }

    /**
     * Get the available service types.
     */
    public function getServicesTypes(): JsonResponse
    {
        return response()->json(
            [
                [
                    'name' => 'Para Amanhã',
                    'service_code' => 'EMSF056.01',
                ],
                [
                    'name' => 'Em 2 Dias',
                    'service_code' => 'EMSF057.01',
                ],
            ],
            HttpStatusEnum::OK
        );
    }

    /**
     * Saves a service with the given data and order code.
     * @param array $data The data of the service to be saved.
     * @return JsonResponse The JSON response indicating the success or failure of the operation.
     *
     * @throws GuzzleException
     * @throws Throwable
     */
    public function saveService(array $data, array $order): JsonResponse
    {
        try {
            $cttResponse = $this->createService($data);
            $orderId = Order::query()->where('order_code', $order['order_code'])->first()->id;
            $this->createTracking->execute(
                [
                    'tracking' => $cttResponse['tracking'],
                    'order_code' => $order['order_code'],
                    'order_id' => $orderId,
                    'carrier_name' => 'Ctt',
                    'integration_id' => getIntegrationIdByName(IntegrationEnum::CTT),
                ]
            );

            $this->createVolumes->execute(
                [
                    'volumes' => $data['number_of_volumes'],
                    'order_code' => $order['order_code'],
                    'integration_id' => getIntegrationIdByName(IntegrationEnum::CTT),
                ]
            );

            $pdfContent = LabelUtils::convertZplToPdf($cttResponse['zpl']);

            if (!$pdfContent) {
                $this->saveLabel->execute(base64_decode($cttResponse['pdf']), $order['order_code'], 'pdf');

                return response()->json(
                    [
                        'message' => 'success',
                    ],
                    HttpStatusEnum::CREATED
                );
            }

            $this->saveLabel->execute($pdfContent, $order['order_code'], 'pdf');

            return response()->json(
                [
                    'message' => 'success',
                ],
                HttpStatusEnum::CREATED
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ],
                HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Creates a service on CTT given the provided data.
     *
     * This function will iterate over the provided tokens and create a service on CTT.
     * If any of the services fail, the function will return the error message from the failing service.
     *
     * @param array $data The data of the service to be created.
     * @return array The response from the service creation, containing the PDF content, ZPL content and tracking number.
     *
     * @throws GuzzleException
     * @throws Throwable
     */
    public function createService(array $data): array
    {
        $endpoint = '/CTTEWSPool/CTTShipmentProviderWS.svc';

        foreach ($this->tokens as $token) {
            $tokenArray = json_decode($token['token'], true);
            $integration = new CttIntegration($token['url']);
            $params = view(
                'requests.ctt_label',
                [
                    'token' => $tokenArray,
                    'data' => $data,
                ]
            )->render();

            $header = [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://tempuri.org/ICTTShipmentProviderWS/CreateShipment',
            ];

            $service = $integration->post(
                $header,
                $endpoint,
                [],
                $params
            );
        }

        $xml = new SimpleXMLElement($service);
        $xml->registerXPathNamespace('a', 'http://schemas.datacontract.org/2004/07/CTTExpressoWS');

        return [
            'pdf' => (string)$xml->xpath('//a:DocumentData/a:File')[0],
            'zpl' => (string)$xml->xpath('//a:LabelData/a:Label')[0],
            'tracking' => (string)$xml->xpath('//a:FirstObject')[0],
        ];
    }

    /**
     * Retrieves the track and race events for the given tracking number.
     *
     * @param string $tracking The tracking number to retrieve events for.
     * @return array  An array of event codes.
     * @throws Exception
     */
    public function getTrackAndRace(string $tracking): array
    {
        $endpoint = '/CTTEWSPool/EventosWS.svc?wsdl';

        foreach ($this->tokens as $token) {
            $tokenArray = json_decode($token['token'], true);
            $integration = new CttIntegration($token['url']);

            $params = view(
                'requests.ctt_track_trace',
                [
                    'token' => $tokenArray,
                    'tracking' => $tracking,
                ]
            )->render();

            $header = [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://tempuri.org/IEventosWS/GetEventosObjectos_V3',
            ];

            $service = $integration->post(
                $header,
                $endpoint,
                [],
                $params
            );
        }

        $xml = new SimpleXMLElement($service);
        $xml->registerXPathNamespace('a', 'http://schemas.datacontract.org/2004/07/BusinessEntities');

        $eventCodes = [];

        foreach ($xml->xpath('//a:_CodigoEvento') as $eventCodeElement) {
            $eventCodes[] = (string)$eventCodeElement;
        }

        return $eventCodes;
    }
}
