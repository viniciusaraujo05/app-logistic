<?php

namespace App\Http\Controllers\Api\Order;

use App\Actions\Orders\IndexOrder;
use App\Actions\Orders\PrintOrder;
use App\Actions\Orders\ShowOrder;
use App\Actions\Orders\UpdateOrder;
use App\Actions\Orders\UpdateOrderData;
use App\Actions\Orders\UpdateOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\IndexOrderRequest;
use App\Http\Requests\Orders\ShowOrderRequest;
use App\Http\Requests\Orders\UpdateOrderDataRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Http\Requests\Orders\UpdateStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class OrderController extends Controller
{
    /**
     * List all the orders.
     *
     * @param IndexOrder $indexOrder description
     *
     * @throws Throwable
     */
    public function index(IndexOrderRequest $request, IndexOrder $indexOrder): JsonResponse
    {
        return $indexOrder->execute($request->all());
    }

    /**
     * Show the order details.
     *
     * @param ShowOrderRequest $request description
     * @param ShowOrder $showOrder An instance of the ShowOrder class
     *
     * @throws Throwable
     */
    public function show(ShowOrderRequest $request, ShowOrder $showOrder): JsonResponse
    {
        return $showOrder->execute($request->get('order_id'));
    }

    /**
     * Updates an order using the provided request and order ID.
     *
     * @param UpdateOrderRequest $request The request containing the updated order data.
     * @param UpdateOrder $updateOrder The service responsible for updating the order.
     * @return JsonResponse The JSON response containing the result of the update operation.
     *
     * @throws Throwable
     */
    public function update(UpdateOrderRequest $request, int $orderId, UpdateOrder $updateOrder): JsonResponse
    {
        return $updateOrder->execute($request->all(), $orderId);
    }

    /**
     * Updates an order using the provided request and order ID.
     *
     * @param UpdateStatusRequest $request The request containing the updated order.
     * @param UpdateOrderStatus $updateOrderStatus The service responsible for updating the order.
     * @return JsonResponse The JSON response containing the result of the update operation.
     *
     * @throws Throwable
     */
    public function updateOrderStatus(
        UpdateStatusRequest $request,
        UpdateOrderStatus   $updateOrderStatus
    ): JsonResponse {
        return $updateOrderStatus->execute($request->get('order_id'), $request->get('carrier'));
    }

    public function updateOrderData(UpdateOrderDataRequest $request, UpdateOrderData $updateOrderData): JsonResponse
    {
        return $updateOrderData->execute($request->all());
    }

    public function print(ShowOrderRequest $request, PrintOrder $printOrder): Response
    {
        return $printOrder->execute($request->get('order_id'));
    }
}
