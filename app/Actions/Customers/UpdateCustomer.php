<?php

namespace App\Actions\Customers;

use App\Enums\HttpStatusEnum;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class UpdateCustomer
{
    /**
     * Atualiza um cliente existente.
     *
     * @param  array  $data  Dados do cliente a serem atualizados.
     * @param  int  $customerId  ID do cliente a ser atualizado.
     *
     * @throws \Throwable
     */
    public function execute(array $data, int $customerId): JsonResponse
    {
        try {
            $customer = Customer::query()->findOrFail($customerId);
            $customer->update($data);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Customer updated successfully',
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
