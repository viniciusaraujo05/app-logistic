<?php

namespace App\Actions\Orders;

use App\Enums\HttpStatusEnum;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\Chart\ChartDataService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Throwable;

class UpdateOrderStatus
{
    /**
     * Atualiza uma ordem existente.
     *
     * @param int $orderId ID da ordem a ser atualizada.
     *
     * @throws Throwable
     */
    public function execute(int $orderId, ?string $carrier = null): JsonResponse
    {
        try {
            $order = Order::findOrFail($orderId);
            $orderedStatuses = $this->getOrderedStatuses();

            $currentStatusIndex = $orderedStatuses->search(fn ($status) => $status->id === $order->status_id);

            if ($currentStatusIndex === false || $currentStatusIndex >= $orderedStatuses->count() - 1) {
                return $this->jsonResponse(false, 'A ordem já está no último status disponível', HttpStatusEnum::NOT_MODIFIED);
            }

            $nextStatus = $orderedStatuses->get($currentStatusIndex + 1);
            $oldStatus = OrderStatus::find($order->status_id);
            $oldUpdatedAt = $order->updated_at;

            $order->status_id = $nextStatus->id;
            $order->save();

            $newUpdatedAt = $order->updated_at;

            if ($oldStatus->name === 'Preparação') {
                (new ChartDataService())->saveChartData($order, null, [
                    'old' => $oldUpdatedAt,
                    'new' => $newUpdatedAt
                ]);
            } else {
                $this->saveChartDataBasedOnCarrier($order, $carrier);
            }

            return $this->jsonResponse(true, 'Status da ordem atualizado com sucesso', HttpStatusEnum::OK);

        } catch (Throwable $th) {
            return $this->jsonResponse(false, $th->getMessage(), HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtém todos os status de ordem em ordem numérica crescente.
     *
     * @return Collection Uma coleção contendo todos os status de ordem em ordem numérica crescente.
     */
    public function getOrderedStatuses(): Collection
    {
        return OrderStatus::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();
    }

    private function jsonResponse(bool $status, string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
        ], $statusCode);
    }

    private function saveChartDataBasedOnCarrier(Order $order, ?string $carrier): void
    {
        (new ChartDataService())->saveChartData($order, $carrier);
    }
}
