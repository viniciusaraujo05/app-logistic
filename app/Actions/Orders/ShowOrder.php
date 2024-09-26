<?php

namespace App\Actions\Orders;

use App\Enums\HttpStatusEnum;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class ShowOrder
{
    /**
     * show a Order.
     *
     * @param  int  $orderId  Order ID.
     *
     * @throws \Throwable
     */
    public function execute(int $orderId): JsonResponse
    {
        try {
            $order = Order::query()->with('status:id,name')->find($orderId);
            if (! $order) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Order not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            return response()->json(
                [
                    'status' => true,
                    'data' => $order,
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
