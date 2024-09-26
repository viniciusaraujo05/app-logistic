<?php

namespace App\Actions\Orders;

use App\Enums\HttpStatusEnum;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class DestroyOrder
{
    /**
     * delete a order.
     *
     * @param  string  $orderCode  order ID.
     *
     * @throws \Throwable
     */
    public function execute(string $orderCode): JsonResponse
    {
        try {
            $order = Order::query()->where('order_code', $orderCode)->first();
            if (! $order) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Order not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            $order->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Order deleted successfully',
                ],
                HttpStatusEnum::OK
            );
        } catch (\Throwable $th) {
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
