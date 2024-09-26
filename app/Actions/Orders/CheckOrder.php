<?php

namespace App\Actions\Orders;

use App\Enums\FixedStatusesEnum;
use App\Models\Order;

class CheckOrder
{
    /**
     * Executes the function with the given order code.
     *
     * @param string $orderCode The order code to execute the function with.
     * @param string|null $email The email to filter by (optional).
     * @param int|null $status The status to check (optional).
     * @param int|null $statusPending The pending status to check (optional).
     * @return bool Returns true if the order exists, false otherwise.
     */
    public function execute(
        string  $orderCode,
        ?string $email = null,
        ?int    $status = null,
        ?int    $statusPending = null
    ): bool {
        $query = Order::query()->where('order_code', $orderCode);

        if ($email !== null) {
            $query->where('email', $email);
        }

        $order = $query->select('id', 'status_id')->first();
        if ($order && $status !== null &&
            $statusPending !== null &&
            $order->status_id !== $status &&
            $order->status_id === $statusPending) {
            $statusId = getOrderStatusIdByName(FixedStatusesEnum::PREPARATION);
            $query->update(['status_id' => $statusId]);
        }

        return (bool)$order;
    }
}
