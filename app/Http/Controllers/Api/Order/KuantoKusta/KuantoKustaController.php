<?php

namespace App\Http\Controllers\Api\Order\KuantoKusta;

use App\Http\Controllers\Controller;
use App\Http\Requests\KuantoKusta\GetOrdersRequest;
use App\Services\Marketplace\KuantoKusta\OrderService;
use Illuminate\Http\JsonResponse;

class KuantoKustaController extends Controller
{
    /**
     * Synchronize orders from KuantoKusta.
     *
     * @param  OrderService  $orderService  The order service instance.
     * @param  GetOrdersRequest  $request  The request containing possible parameters:
     *                                     - createdAt: Nullable, format: Y-m-d H:i:s
     *                                     createdAt_lt=SomeDateTime - createdAt_gte:
     *                                     Nullable, format: Y-m-d H:i:s Greater than
     *                                     or equal, >= - createdAt_gt: Required,
     *                                     format: Y-m-d H:i:s Greater than, > -
     *                                     createdAt_lte: Nullable, format: Y-m-d
     *                                     H:i:s Less than or equal, <= -
     *                                     createdAt_lt: Nullable, format: Y-m-d H:i:s
     *                                     Less than, < - orderState: Nullable,
     *                                     string, must be one:
     *                                     KuantoKusta/OrderStateEnum - page:
     *                                     Required, integer, min: 1 -
     *                                     maxResultsPerPage: Required, integer, min:
     *                                     1, max: 100
     */
    public function sync(GetOrdersRequest $request, OrderService $orderService): JsonResponse
    {
        $validatedData = $request->validated();

        $orders = $orderService->getOrders($validatedData);

        return $orderService->syncOrders($orders);
    }
}
