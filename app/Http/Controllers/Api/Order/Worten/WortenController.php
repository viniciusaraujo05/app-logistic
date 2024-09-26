<?php

namespace App\Http\Controllers\Api\Order\Worten;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\Worten\OrderService;
use Illuminate\Http\JsonResponse;

class WortenController extends Controller
{
    /**
     * Synchronize orders from Worten.
     *
     * @param  OrderService  $orderService  The order service instance.
     */
    public function sync(OrderService $orderService): JsonResponse
    {
        $orders = $orderService->getOrders();

        return $orderService->syncOrders($orders);
    }
}
