<?php

namespace App\Actions\Orders\OrderStatus;

use App\Enums\HttpStatusEnum;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Throwable;

class ToggleActiveOrderStatus
{
    /**
     * Toggle active status
     *
     * @throws Throwable
     */
    public function execute(int $orderStatusId): JsonResponse
    {
        try {
            $orderStatus = OrderStatus::query()->find($orderStatusId);

            if (! $orderStatus) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Order Status not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            $orderStatus->is_active = ! $orderStatus->is_active;

            $orderStatus->save();

            return response()->json(
                [
                    'status' => true,
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
