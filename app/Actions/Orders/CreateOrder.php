<?php

namespace App\Actions\Orders;

use App\Enums\HttpStatusEnum;
use App\Models\Order;
use App\Services\Chart\ChartDataService;
use Illuminate\Http\JsonResponse;
use Throwable;

class CreateOrder
{
    /**
     * create a new order.
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        try {
            $order = Order::query()->create($data);

            (new ChartDataService())->saveChartData($order, null, null, true);

            return response()->json(
                [
                    'status' => true,
                ],
                HttpStatusEnum::CREATED
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
