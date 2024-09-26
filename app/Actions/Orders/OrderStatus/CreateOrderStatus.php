<?php

namespace App\Actions\Orders\OrderStatus;

use App\Enums\HttpStatusEnum;
use App\Enums\Order\OrderStatusTypeEnum;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateOrderStatus
{
    /**
     * Create a new order status and return all order statuses
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $statusTypeId = OrderStatusTypeEnum::getId($data['order_status_type']);

            if ($data['order_status_type'] === OrderStatusTypeEnum::getName(OrderStatusTypeEnum::PREPARATION)) {
                $statuses = OrderStatus::query()
                    ->where('status_type_id', OrderStatusTypeEnum::TRACKING)
                    ->orderByDesc('order')
                    ->get();

                foreach ($statuses as $status) {
                    $status->order = $status->order + 1;
                    $status->save();
                }

                $minTrackingOrder = OrderStatus::query()
                    ->where('status_type_id', OrderStatusTypeEnum::TRACKING)
                    ->min('order');

                $newOrder = $minTrackingOrder - 1;
            }

            if ($data['order_status_type'] === OrderStatusTypeEnum::getName(OrderStatusTypeEnum::TRACKING)) {
                $maxOrder = OrderStatus::query()
                    ->where('status_type_id', $statusTypeId)
                    ->max('order');

                $newOrder = $maxOrder + 1;
            }

            $orderStatus = new OrderStatus();
            $orderStatus->name = $data['order_status_name'];
            $orderStatus->status_type_id = $statusTypeId;
            $orderStatus->order = $newOrder;
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
