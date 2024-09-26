<?php

use App\Models\OrderStatus;

if (!function_exists('getOrderStatusIdByName')) {
    /**
     * Get the ID of the order status by its name.
     *
     * @param string $name
     * @return int|null
     */
    function getOrderStatusIdByName(string $name): ?int
    {
        $status = OrderStatus::query()->where('name', $name)->first();

        return $status ? $status->id : null;
    }
}
