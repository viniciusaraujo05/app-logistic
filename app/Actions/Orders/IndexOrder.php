<?php

namespace App\Actions\Orders;

use App\Enums\HttpStatusEnum;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class IndexOrder
{
    /**
     * Show all orders.
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        $statusTypeName = $data['order_status_type'];
        $filter = $data['filters'] ?? 'orders.id.desc';

        [$column, $direction] = explode('.', $filter);

        $allowedColumns = ['order_code', 'updated_at', 'orders.id'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($column, $allowedColumns) || !in_array($direction, $allowedDirections)) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Invalid filter value',
                ],
                HttpStatusEnum::BAD_REQUEST
            );
        }

        try {
            $orders = Order::query()
                ->select(
                    'orders.id',
                    'orders.products',
                    'orders.status_id',
                    'orders.source_integration_id',
                    'orders.order_code',
                    'orders.responsible',
                    'orders.customer_name',
                    'orders.updated_at',
                    'orders.shipping_method',
                    'order_statuses.name as status_name',
                    'order_statuses.order as ordering',
                    'order_statuses.is_mandatory as mandatory',
                    'status_types.name as status_type_name',
                    'tracking_data.tracking_number',
                    'tracking_data.carrier_name'
                )
                ->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
                ->join('status_types', 'order_statuses.status_type_id', '=', 'status_types.id')
                ->leftJoin(DB::raw('(SELECT DISTINCT ON (order_id) order_id, tracking AS tracking_number, carrier_name FROM tracking ORDER BY order_id, created_at ASC) as tracking_data'), function (JoinClause $join) {
                    $join->on('orders.id', '=', 'tracking_data.order_id');
                })
                ->where('status_types.name', '=', $statusTypeName)
                ->where('order_statuses.is_active', '=', true)
                ->orderBy($column, $direction)
                ->get();

            $orderStatuses = OrderStatus::query()
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->whereHas('statusType', function ($query) use ($statusTypeName) {
                    $query->where('name', $statusTypeName);
                })
                ->with('statusType')
                ->select([
                    'order_statuses.id',
                    'order_statuses.name',
                    'order_statuses.order',
                    'order_statuses.status_type_id'
                ])
                ->get();

            if (!$orders) {
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
                    'data' => $orders,
                    'order_statuses' => $orderStatuses,
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
