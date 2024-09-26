<?php

namespace App\Services\Carriers\SelfShipping;

use App\Actions\Carriers\SelfShipping\SaveLabel;
use App\Actions\Carriers\Tracking\CreateTracking;
use App\Actions\Carriers\Volumes\CreateVolumes;
use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Repositories\TokenRepository;

class SelfShippingService
{
    private CreateTracking $createTracking;

    private CreateVolumes $createVolumes;

    private SaveLabel $saveLabel;

    /**
     * Constructs a new instance of the class.
     *
     * @param TokenRepository $tokenRepository The token repository used to retrieve tokens.
     */
    public function __construct()
    {
        $this->createTracking = new CreateTracking();
        $this->createVolumes = new CreateVolumes();
        $this->saveLabel = new SaveLabel();
    }

    /**
     * Saves the service data by executing tracking, creating volumes, and saving the label.
     *
     * @param array $data The data containing tracking, order code, and volumes information.
     * @return mixed The result of saving the service data.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function saveService(array $data)
    {
        try {
            $this->createTracking->execute(
                [
                    'tracking' => $data['tracking'],
                    'order_code' => $data['order_code'],
                    'integration_id' => $data['integration_id'],
                    'volumes_total' => $data['number_of_volumes'],
                    'order_id' => $data['order_id'],
                    'carrier_name' => 'Self Shipping',
                    'additional' => json_encode($data['car']) ?? json_encode($data['pickup']),
                ]
            );

            array_map(
                fn (array $volume) => $this->createVolumes->execute(
                    [
                        'volume_id' => $volume['bar_code'],
                        'weight' => $volume['weight'],
                        'order_code' => $data['order_code'],
                        'integration_id' => getIntegrationIdByName(IntegrationEnum::SELFSHIPPING),
                    ]
                ),
                $data['volumes']
            );

            $this->saveLabel->execute($data);

            return response()->json(
                [
                    'message' => 'sucesss',
                ],
                HttpStatusEnum::OK
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
}
