<?php

namespace App\Services\Carriers\Vasp;

use App\Actions\Carriers\Correos\MergePDF;
use App\Actions\Carriers\SaveLabel;
use App\Actions\Carriers\Tracking\CreateTracking;
use App\Actions\Carriers\Volumes\CreateVolumes;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Integrations\Carriers\Vasp\VaspIntegration;
use App\Models\Order;
use App\Repositories\TokenRepository;
use App\Utils\LabelUtils;
use Illuminate\Http\JsonResponse;

class VaspService
{
    private $tokens;

    private TokenRepository $tokenRepository;

    private CreateTracking $createTracking;

    private CreateVolumes $createVolumes;

    private SaveLabel $saveLabel;

    private MergePDF $mergePDF;

    /**
     * Constructs a new instance of the class.
     *
     * @param TokenRepository $tokenRepository The token repository used to retrieve tokens.
     */
    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->tokens = $this->tokenRepository->get(IntegrationEnum::VASP);
        $this->createTracking = new CreateTracking();
        $this->createVolumes = new CreateVolumes();
        $this->saveLabel = new SaveLabel();
        $this->mergePDF = new MergePDF();
    }

    /**
     * Creates a new service by posting the provided service data to the Vasp API endpoint.
     *
     * @param array $data The service data to be posted.
     * @return mixed The response from the Vasp API.
     */
    public function createService(array $data): mixed
    {
        $endpoint = '/api/V3/Shipment/Service/Label/LabelType/ZPL';

        $integration = new VaspIntegration($this->tokens[0]['url']);

        return $integration->post(
            $this->getToken(),
            $endpoint,
            [],
            $data,
        );
    }

    /**
     * Retrieves the access token for the API.
     *
     * @return array The access token.
     */
    public function getToken()
    {
        $endpoint = '/token';

        foreach ($this->tokens as $token) {
            $tokenData = json_decode($token, true);
            $token = json_decode($tokenData['token'], true);
            $integration = new VaspIntegration($token['url']);

            $token = $integration->genereteToken(
                $token,
                $endpoint,
            );
        }

        return $token;
    }

    /**
     * Retrieves tracking information for a given client barcode or reference.
     *
     * @param string $clientBarCodeOrReference The client barcode or reference to retrieve tracking information for.
     * @return array The tracking information for the given client barcode or reference.
     */
    public function getTrackAndRace(string $clientBarCodeOrReference): array
    {
        $endpoint = '/api/V3/TrackAndTrace/' . $clientBarCodeOrReference;
        $tokensData = json_decode($this->tokens, true);

        return collect($tokensData)
            ->map(
                fn ($token) => (new VaspIntegration($token['url']))->get(
                    $this->getToken(),
                    $endpoint,
                    [],
                )
            )
            ->collapse()
            ->all();
    }

    /**
     * Retrieves service types based on the provided postal code.
     *
     * @param string $postalCode The postal code to retrieve service types for.
     * @return array The service types retrieved based on the postal code.
     */
    public function getServiceTypes(string $postalCode): array
    {
        $endpoint = '/api/V3/ServiceTypes/' . $postalCode;
        $tokensData = json_decode($this->tokens, true);

        return collect($tokensData)
            ->map(
                fn ($token) => (new VaspIntegration($token['url']))->get(
                    $this->getToken(),
                    $endpoint,
                    [],
                )
            )
            ->collapse()
            ->first();
    }

    /**
     * Saves a service with the given data and order code.
     *
     * @param array $data The data of the service to be saved.
     * @param string $orderCode The code of the order associated with the service.
     * @return JsonResponse The JSON response indicating the success or failure of the operation.
     *
     * @throws \Exception If an error occurs while saving the service.
     */
    public function saveService(array $data, array $order)
    {
        try {
            $orderId = Order::query()->where('order_code', $order['order_code'])->first()->id;
            $this->createTracking->execute(
                [
                    'tracking' => $data['service']['clientBarCode'],
                    'order_code' => $order['order_code'],
                    'order_id' => $orderId,
                    'carrier_name' => 'Vasp',
                    'integration_id' => getIntegrationIdByName(IntegrationEnum::VASP),
                ]
            );

            array_map(
                fn (array $volume) => $this->createVolumes->execute(
                    [
                        'volume_id' => $volume['volumeBarCode'],
                        'weight' => $volume['weight'],
                        'order_code' => $order['order_code'],
                        'integration_id' => getIntegrationIdByName(IntegrationEnum::VASP),
                    ]
                ),
                $data['service']['volumes']
            );

            $label = $this->createLabels($data, $order);
            $this->mergePDF->execute($label, $order['order_code']);

            return response()->json(
                [
                    'message' => 'success',
                ],
                HttpStatusEnum::CREATED
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => $e->getMessage(),
                ],
                HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Creates labels based on the given labels and order.
     *
     * @param array<array{labels: string}> $labels The labels to create.
     * @param array{label_type: string, order_code: string} $order The order containing the label type.
     * @return string[] The array of created label names.
     */
    public function createLabels(array $data, array $order): array
    {
        $createdLabels = [];
        foreach ($data['labels'] as $label) {
            $labelType = $order['label_type'];
            $labelContent = $labelType === 'ZPL' ?
                LabelUtils::convertZplToPdf(base64_decode($label)) :
                $this->labelPDF($data['service']['clientBarCode']);

            $labelName = uniqid();
            $this->saveLabel->execute($labelContent, $labelName, 'PDF');

            $createdLabels[] = $labelName;
        }

        return $createdLabels;
    }

    /**
     * Retrieves the PDF label for a given tracking number.
     *
     * @param string $tracking The tracking number to retrieve the label for.
     * @return mixed The PDF label data.
     */
    public function labelPDF(string $tracking): mixed
    {
        $endpoint = '/api/V3/ShippingGuides/' . $tracking;

        $pdf = collect($this->tokens)
            ->map(
                fn ($token) => (new VaspIntegration($token['url']))->get(
                    $this->getToken(),
                    $endpoint,
                    [],
                )
            )
            ->collapse()
            ->first();

        return base64_decode($pdf['document']);
    }
}
