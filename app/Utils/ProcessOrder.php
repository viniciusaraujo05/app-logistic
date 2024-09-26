<?php

namespace App\Utils;

use App\Actions\Carriers\Tracking\DestroyTrackingTransit;
use App\Actions\Orders\UpdateOrder;
use App\Actions\Orders\UpdateOrderStatus;
use App\Enums\FixedStatusesEnum;
use App\Enums\IntegrationEnum;
use App\Repositories\TokenRepository;
use App\Services\Carriers\Correos\CorreosTrackingService;
use App\Services\Carriers\Ctt\CttService;
use App\Services\Carriers\Vasp\VaspService;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessOrder
{
    protected TokenRepository $tokenRepository;

    public function __construct()
    {
        $this->tokenRepository = new TokenRepository();
    }

    /**
     * @throws Throwable
     */
    public function execute($order): void
    {
        $integrationId = $order['integration_id'];
        $orderId = $order['order_id'];
        $tracking = $order['tracking'];

        if (str_contains($order['carrier_name'], 'Correos')) {
            if ($this->checkCorreosDeliveryStatus($tracking)) {
                //                $this->finalizeOrder($orderId);
            }
            return;
        }

        $orderService = $this->getOrderService($integrationId);
        if (!$orderService) {
            return;
        }

        $trackingStatus = $orderService->getTrackAndRace($tracking);
        if ($this->isOrderCompleted($trackingStatus)) {
            $this->finalizeOrder($orderId);
        }
    }

    /**
     * Verifica o status de entrega da Correos.
     *
     * @param string $tracking
     * @return bool
     */
    public function checkCorreosDeliveryStatus(string $tracking): bool
    {
        $correosService = new CorreosTrackingService();

        $trackingInfo = $correosService->getTrackingInfo($tracking);

        if (isset($trackingInfo['error'])) {
            Log::error('Erro ao obter informações de rastreamento: ' . $trackingInfo['message']);
            return false;
        }

        foreach ($trackingInfo['eventos'] as $evento) {
            if ($evento['codEvento'] === 'ENTREGADO') {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns an instance of the order service class for the given integration id.
     *
     * @param int $integrationId The id of the integration.
     * @return object|null The order service instance or null if the integration id is not recognized.
     */
    private function getOrderService(int $integrationId): object|null
    {
        return match ($integrationId) {
            getIntegrationIdByName(IntegrationEnum::VASP) => new VaspService($this->tokenRepository),
            getIntegrationIdByName(IntegrationEnum::CTT) => new CttService($this->tokenRepository),
            default => null,
        };
    }

    /**
     * Checks if the order is completed based on the given tracking status.
     *
     *
     * @param array $trackingStatus The tracking status of the order.
     * @return bool True if the order is completed, false otherwise.
     */
    private function isOrderCompleted(array $trackingStatus): bool
    {
        if (
            array_key_exists('serviceState', $trackingStatus)
            && $trackingStatus['serviceState'] === 'DONE'
        ) {
            return true;
        }

        if (in_array('EMM', $trackingStatus, true)) {
            return true;
        }

        return false;
    }

    /**
     * Finalizes an order by setting its status to 'completed' and removing any associated tracking transit data.
     *
     * @param int $orderId The ID of the order to finalize.
     * @throws Throwable
     */
    private function finalizeOrder(int $orderId): void
    {
        $this->updateOrderStatus($orderId);
        $this->destroyTrackingTransit($orderId);
    }

    /**
     * Updates the order status.
     *
     * @param int $orderId The ID of the order to update.
     * @throws Throwable
     */
    private function updateOrderStatus(int $orderId): void
    {
        $orderUpdate = new UpdateOrder();
        $statusId = $this->getOrderStatus(FixedStatusesEnum::DELIVERED);
        $orderUpdate->execute(['status_id' => $statusId], $orderId);
    }

    /**
     * Retrieves the order status based on the provided status name.
     *
     * @param string $status The name of the status.
     * @return int The order status.
     */
    public function getOrderStatus(string $status): int
    {
        $orderStatus = (new UpdateOrderStatus())->getOrderedStatuses()->firstWhere('name_fixed', $status);

        return $orderStatus['order'];
    }

    /**
     * Destroys all associated tracking transit data for the given order ID.
     *
     * @param int $orderId The ID of the order to destroy tracking transit data for.
     */
    private function destroyTrackingTransit(int $orderId): void
    {
        $destroyTrackingTransit = new DestroyTrackingTransit();
        $destroyTrackingTransit->execute($orderId);
    }
}
