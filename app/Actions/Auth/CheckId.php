<?php

namespace App\Actions\Auth;

use App\Enums\HttpStatusEnum;
use App\Models\AccountData;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Throwable;

class CheckId
{
    /**
     * A description of the entire PHP function.
     *
     * @param array $data description
     *
     * @throws Throwable description of exception
     */
    public function execute(array $data): JsonResponse
    {
        try {
            $tenant = Tenant::query()->where('id', $data['id'])->first();

            if ($tenant) {
                tenancy()->initialize($tenant->id);

                $accountData = AccountData::query()->first();

                $response = response()->json(
                    [
                        'status' => true,
                        'customer' => $tenant->id,
                        'image' => $accountData->logo_image_url,
                    ],
                    HttpStatusEnum::OK
                );

                session(['user_id' => $data['id']]);

                tenancy()->end();

                return $response;
            }

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Tenant not found',
                ],
                HttpStatusEnum::OK
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
