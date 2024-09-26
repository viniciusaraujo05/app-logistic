<?php

namespace App\Actions\Configuration\Account;

use App\Enums\HttpStatusEnum;
use App\Models\AccountData;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AccountIndex
{
    public function execute(): JsonResponse
    {
        try {
            $accountData = AccountData::firstOrFail();

            $employeeCount = User::all()->count();

            $formattedAccountData = [
                'logo_image_url' => $accountData->logo_image_url,
                'website' => $accountData->website,
                'business_area' => $accountData->business_area,
                'contact' => $accountData->contact,
                'employees_count' => $employeeCount,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Account data retrieved successfully',
                'account_data' => $formattedAccountData,
            ], HttpStatusEnum::OK);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }
}
