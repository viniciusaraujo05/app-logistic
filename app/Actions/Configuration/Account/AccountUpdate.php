<?php

namespace App\Actions\Configuration\Account;

use App\Enums\HttpStatusEnum;
use App\Models\AccountData;
use Illuminate\Http\JsonResponse;

class AccountUpdate
{
    public function execute(array $data): JsonResponse
    {
        try {
            $accountData = AccountData::firstOrFail();

            $accountData->website = $data['website'] ?? $accountData->website;
            $accountData->business_area = $data['business_area'] ?? $accountData->business_area;
            $accountData->logo_image_url = $data['logo_image_url'] ?? $accountData->logo_image_url;

            $accountData->save();

            return response()->json([
                'status' => true,
                'message' => 'Account data retrieved successfully',
                'account_data' => $accountData,
            ], HttpStatusEnum::OK);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }
}
