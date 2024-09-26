<?php

namespace App\Actions\Orders\OrderStatus;

use App\Enums\HttpStatusEnum;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteOrderStatus
{
    /**
     * Delete the order status and re-order remaining statuses
     *
     * @throws Throwable
     */
    public function execute(int $orderStatusId): JsonResponse
    {
        DB::beginTransaction();

        try {
            $orderStatus = OrderStatus::query()->find($orderStatusId);

            if (!$orderStatus) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Order Status not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            if ($orderStatus->is_active) {
                $orderStatus->is_active = false;
                $orderStatus->save();
            }

            $deletedOrder = $orderStatus->order;

            $orderStatus->delete();

            OrderStatus::query()
                ->where('order', '>', $deletedOrder)
                ->decrement('order');

            $allOrderStatuses = OrderStatus::query()
                ->with('statusType:id,name')
                ->orderBy('order')
                ->get();

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Status deleted successfully.',
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
