<?php

namespace App\Http\Controllers\Api\Order\Magento;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\Magento\OrderService;

class MagentoController extends Controller
{
    /**
     * A description of the entire PHP function.
     *
     * @param  OrderService  $orderService  description
     */
    public function sync(OrderService $orderService)
    {
        $orders = $orderService->getOrders();

        return $orderService->syncOrders($orders);
    }
}
