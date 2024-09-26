<?php

namespace App\Actions\Orders\OrderStatus;

use App\Enums\HttpStatusEnum;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Throwable;

class IndexOrderStatus
{
    /**
     * Show all status
     *
     * @throws Throwable
     */
    public function execute(): JsonResponse
    {
        try {
            $orderStatus = OrderStatus::query()
                ->with('statusType:id,name')
                ->orderBy('order')
                ->get();

            if (! $orderStatus) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Order Status not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            return response()->json(
                [
                    'status' => true,
                    'data' => $orderStatus,
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
