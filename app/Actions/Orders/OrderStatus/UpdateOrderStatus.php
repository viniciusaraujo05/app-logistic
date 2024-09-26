<?php

namespace App\Actions\Orders\OrderStatus;

use App\Enums\HttpStatusEnum;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateOrderStatus
{
    /**
     * Update order status and return all order statuses
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $orderId = $data['order_status_id'];
            $orderName = $data['order_status_name'];

            $orderStatus = OrderStatus::query()->findOrFail($orderId);

            $orderStatus->name = $orderName;
            $orderStatus->save();

            $allOrderStatuses = OrderStatus::query()
                ->with('statusType:id,name')
                ->orderBy('order')
                ->get();

            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'data' => $allOrderStatuses,
                ],
                HttpStatusEnum::OK
            );
        } catch (Throwable $th) {
            DB::rollBack();

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
