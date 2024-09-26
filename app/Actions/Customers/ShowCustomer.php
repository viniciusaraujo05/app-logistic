<?php

namespace App\Actions\Customers;

use App\Enums\HttpStatusEnum;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class ShowCustomer
{
    /**
     * show a customer.
     *
     * @param  int  $customerId  client ID.
     *
     * @throws \Throwable
     */
    public function execute(int $customerId): JsonResponse
    {
        try {
            $customer = Customer::query()->find($customerId);
            if (! $customer) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Customer not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            return response()->json(
                [
                    'status' => true,
                    'customer' => $customer,
                ],
                HttpStatusEnum::OK
            );
        } catch (\Throwable $th) {
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
