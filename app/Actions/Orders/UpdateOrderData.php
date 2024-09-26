<?php

namespace App\Actions\Orders;

use App\Enums\HttpStatusEnum;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Throwable;

class UpdateOrderData
{
    public function execute(array $data): JsonResponse
    {
        try {
            $order = Order::query()->findOrFail($data['order_id']);

            if (isset($data['shipping_address'])) {
                $currentShippingAddress = $order->shipping_address ?? [];

                if (isset($data['shipping_address']['street'])) {
                    $street = explode(',', $data['shipping_address']['street'])[0];
                    $data['shipping_address']['street'] = trim($street);
                }

                $updatedShippingAddress = array_merge($currentShippingAddress, $data['shipping_address']);

                $data['shipping_address'] = $updatedShippingAddress;
            }

            $order->fill($data);
            $order->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Data updated successfully',
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
