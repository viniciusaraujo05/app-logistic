<?php

namespace App\Http\Controllers\Api\OrderStatus;

use App\Actions\Orders\OrderStatus\CreateOrderStatus;
use App\Actions\Orders\OrderStatus\DeleteOrderStatus;
use App\Actions\Orders\OrderStatus\IndexOrderStatus;
use App\Actions\Orders\OrderStatus\ReorderOrderStatus;
use App\Actions\Orders\OrderStatus\ToggleActiveOrderStatus;
use App\Actions\Orders\OrderStatus\UpdateOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderStatus\CreateOrderStatusRequest;
use App\Http\Requests\Orders\OrderStatus\OrderStatusRequest;
use App\Http\Requests\Orders\OrderStatus\ReorderStatusRequest;
use App\Http\Requests\Orders\OrderStatus\UpdateOrderStatusRequest;
use Illuminate\Http\JsonResponse;

class OrderStatusController extends Controller
{
    public function index(IndexOrderStatus $indexOrder): JsonResponse
    {
        return $indexOrder->execute();
    }

    public function create(CreateOrderStatusRequest $request, CreateOrderStatus $createOrderStatus): JsonResponse
    {
        return $createOrderStatus->execute($request->all());
    }

    public function update(UpdateOrderStatusRequest $request, UpdateOrderStatus $updateOrderStatus): JsonResponse
    {
        return $updateOrderStatus->execute($request->all());
    }

    public function toggleActive(
        OrderStatusRequest $request,
        ToggleActiveOrderStatus $activeOrderStatus
    ): JsonResponse {
        return $activeOrderStatus->execute($request->get('order_status_id'));
    }

    public function deleteStatus(OrderStatusRequest $request, DeleteOrderStatus $deleteOrderStatus): JsonResponse
    {
        return $deleteOrderStatus->execute($request->get('order_status_id'));
    }

    public function reorder(ReorderStatusRequest $request, ReorderOrderStatus $reorderOrderStatus): JsonResponse
    {
        return $reorderOrderStatus->execute($request->all());
    }
}
