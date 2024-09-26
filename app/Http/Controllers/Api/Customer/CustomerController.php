<?php

namespace App\Http\Controllers\Api\Customer;

use App\Actions\Customers\CreateCustomer;
use App\Actions\Customers\DestroyCustomer;
use App\Actions\Customers\IndexCustomer;
use App\Actions\Customers\ShowCustomer;
use App\Actions\Customers\UpdateCustomer;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Show all customer data using the provided request and CreateCustomer instance.
     *
     * @param  IndexCustomer  $indexCustomer  The IndexCustomer instance
     */
    public function index(IndexCustomer $indexCustomer): JsonResponse
    {
        return $indexCustomer->execute();
    }

    /**
     * Store the customer data using the provided request and CreateCustomer instance.
     *
     * @param  CustomerRequest  $request  The request containing the customer data
     * @param  CreateCustomer  $createCustomer  The CreateCustomer instance
     */
    public function create(CustomerRequest $request, CreateCustomer $createCustomer): JsonResponse
    {
        return $createCustomer->execute($request->all());
    }

    /**
     * Update the customer data using the provided request and UpdateCustomer instance.
     *
     * @param  CustomerRequest  $request  The CustomerRequest containing the customer data
     * @param  UpdateCustomer  $updateCustomer  The UpdateCustomer instance
     */
    public function update(CustomerRequest $request, UpdateCustomer $updateCustomer, $customerId): JsonResponse
    {
        return $updateCustomer->execute($request->all(), $customerId);
    }

    /**
     * Show the customer data using the provided request and ShowCustomer instance.
     *
     * @param  ShowCustomer  $showCustomer  The ShowCustomer instance
     * @param  int  $customerId  The id of the customer to show
     */
    public function show(ShowCustomer $showCustomer, int $customerId): JsonResponse
    {
        return $showCustomer->execute($customerId);
    }

    /**
     * Delete customer data using the provided request and DestroyCustomer instance.
     *
     * @param  DestroyCustomer  $destroyCustomer  The DestroyCustomer instance
     * @param  int  $customerId  The id of the customer to delete
     */
    public function destroy(DestroyCustomer $destroyCustomer, int $customerId): JsonResponse
    {
        return $destroyCustomer->execute($customerId);
    }
}
