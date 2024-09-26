<?php

namespace App\Actions\Customers;

use App\Enums\HttpStatusEnum;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class DestroyCustomer
{
    /**
     * delete a customer.
     *
     * @param  int  $customerId  client ID.
     *
     * @throws \Throwable
     */
    public function execute(int $customerId): JsonResponse
    {
        try {
            $customer = Customer::query()->findOrFail($customerId);
            $customer->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Customer deleted successfully',
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
