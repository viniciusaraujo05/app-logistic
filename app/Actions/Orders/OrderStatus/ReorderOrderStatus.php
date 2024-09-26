<?php

namespace App\Actions\Orders\OrderStatus;

use App\Enums\HttpStatusEnum;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReorderOrderStatus
{
    /**
     * Reorder the order statuses based on provided data
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $newOrders = $data['new_orders'];

            foreach ($newOrders as $order) {
                $orderStatusId = $order['order_id'];
                $newOrder = $order['order'];

                $orderStatus = OrderStatus::query()->findOrFail($orderStatusId);
                $orderStatus->order = $newOrder;
                $orderStatus->save();
            }

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
