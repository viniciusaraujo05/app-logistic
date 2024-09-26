<?php

namespace App\Actions\Customers;

use App\Enums\HttpStatusEnum;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class IndexCustomer
{
    /**
     * show a customer.
     *
     * @param  int  $customerId  client ID.
     *
     * @throws \Throwable
     */
    public function execute(): JsonResponse
    {
        try {
            $customers = Customer::query()->get();

            if (! $customers) {
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
                    'customer' => $customers,
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
