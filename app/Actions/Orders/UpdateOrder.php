<?php

namespace App\Actions\Orders;

use App\Enums\HttpStatusEnum;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Throwable;

class UpdateOrder
{
    /**
     * Atualiza um cliente existente.
     *
     * @param array $data Dados do cliente a serem atualizados.
     * @param int $orderId ID do cliente a ser atualizado.
     *
     * @throws Throwable
     */
    public function execute(array $data, int $orderId): JsonResponse
    {
        try {
            $order = Order::query()->findOrFail($orderId);

            $order->update($data);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Customer updated successfully',
                    'customer' => $order,
                ],
                HttpStatusEnum::OK
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }
}
