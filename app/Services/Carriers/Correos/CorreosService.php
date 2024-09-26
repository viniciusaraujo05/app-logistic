<?php

namespace App\Services\Carriers\Correos;

use App\Actions\Carriers\Correos\MergePDF;
use App\Actions\Carriers\SaveLabel;
use App\Actions\Carriers\Tracking\CreateTracking;
use App\Actions\Carriers\Volumes\CreateVolumes;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Integrations\Carriers\Correos\CorreosIntegration;
use App\Models\Order;
use App\Repositories\TokenRepository;
use App\Utils\LabelUtils;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class CorreosService
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
        $this->tokens = $this->tokenRepository->get(IntegrationEnum::CORREOS);
        $this->createTracking = new CreateTracking();
        $this->createVolumes = new CreateVolumes();
        $this->saveLabel = new SaveLabel();
        $this->mergePDF = new MergePDF();
    }

    /**
     * Get the available service types.
     */
    public function getServicesTypes(): JsonResponse
    {
        return response()->json(
            [
                ['id' => '61', 'name' => 'Paq 10'],
                ['id' => '62', 'name' => 'Paq 14'],
                ['id' => '63', 'name' => 'Paq 24'],
                ['id' => '92', 'name' => 'Paq Empresa 14'],
                ['id' => '93', 'name' => 'ePaq 24'],
                ['id' => '46', 'name' => 'Ilhas documentação'],
                ['id' => '26', 'name' => 'Ilhas Express'],
                ['id' => '79', 'name' => 'Ilhas Marítimo'],
                ['id' => '54', 'name' => 'Entrega Plus'],
            ],
            HttpStatusEnum::OK
        );
    }

    /**
     * Creates a new service by posting the provided service data to the Correos API endpoint.
     *
     * @param array $data The service data to be posted.
     */
    public function createService(array $data): mixed
    {
        $endpoint = '/wspsc/apiRestGrabacionEnviok8s/json/grabacionEnvio';

        $service = [];
        foreach ($this->tokens as $token) {
            $apiToken = json_decode($token, true);
            $tokenArray = json_decode($apiToken['token'], true);
            $integration = new CorreosIntegration($apiToken['url']);

            $service = $integration->post(
                $tokenArray,
                $endpoint,
                [],
                $data,
            );
        }

        return $service;
    }

    /**
     * Saves a service with the given data and order code.
     *
     * @param array $data The data of the service to be saved.
     * @param array $order
     * @return JsonResponse
     * @throws Throwable If an error occurs while saving the service.
     */
    public function saveService(array $data, array $order): JsonResponse
    {
        try {
            if ($data['mensajeRetorno']) {
                return response()->json(
                    [
                        'message' => $data['mensajeRetorno'],
                    ],
                    HttpStatusEnum::BAD_REQUEST
                );
            }
            $orderId = Order::query()->where('order_code', $order['order_code'])->first()->id;
            $this->createTracking->execute(
                [
                    'tracking' => $data['datosResultado'],
                    'order_code' => $order['order_code'],
                    'order_id' => $orderId,
                    'carrier_name' => 'Correos',
                    'integration_id' => getIntegrationIdByName(IntegrationEnum::CORREOS),
                ]
            );

            foreach ($data['listaBultos'] as $volume) {
                $this->createVolumes->execute(
                    [
                        'volume_id' => $volume['codUnico'],
                        'weight' => '',
                        'order_code' => $order['order_code'],
                        'integration_id' => getIntegrationIdByName(IntegrationEnum::VASP),
                    ]
                );
            }

            $labels = $this->createLabels($data['etiqueta'], $order);
            $this->mergePDF->execute($labels, $order['order_code']);

            return response()->json(
                [
                    'message' => 'success',
                ],
                HttpStatusEnum::OK
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
     * Creates labels based on the given labels and order.
     *
     * @param array<array{etiqueta1: string, etiqueta2: string}> $labels The labels to create.
     * @param array{label_type: string, order_code: string} $order The order containing the label type.
     * @return string[] The array of created label names.
     */
    public function createLabels(array $labels, array $order): array
    {
        $createdLabels = [];

        foreach ($labels as $label) {
            $labelType = $order['label_type'];
            $labelData = $labelType === 'PDF' ? $label['etiqueta1'] : $label['etiqueta2'];

            $labelContent = base64_decode(base64_decode($labelData));
            if ($labelType === 'ZPL') {
                $labelContent = LabelUtils::convertZplToPdf($labelData);
            }

            $labelName = uniqid();
            $this->saveLabel->execute($labelContent, $labelName, 'PDF');

            $createdLabels[] = $labelName;
        }

        return $createdLabels;
    }
}
