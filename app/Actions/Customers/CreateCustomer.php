<?php

namespace App\Actions\Customers;

use App\Enums\HttpStatusEnum;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class CreateCustomer
{
    protected Hash $hash;

    protected Tenant $tenant;

    public function __construct(
        Hash $hash,
        Tenant $tenant
    ) {
        $this->hash = $hash;
        $this->tenant = $tenant;
    }

    /**
     * create a new customer.
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        try {
            $customer = Customer::query()->create($data);
            $tenantCreate = $this->tenant->create(['id' => $customer->id]);
            $tenantFind = $this->tenant->find($customer->id);
            tenancy()->initialize($tenantFind);

            User::query()->create(
                [
                    'name' => $customer->responsible_name,
                    'customer_id' => $customer->id,
                    'email' => $customer->email,
                    'password' => $this->hash::make('admin'),
                ]
            );

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Customer created successfully',
                    'customer' => $customer,
                    'tenant' => $tenantCreate,
                ],
                HttpStatusEnum::CREATED
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                HttpStatusEnum::OK
            );
        }
    }
}
