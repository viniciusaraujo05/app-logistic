<?php

namespace App\Actions\Configuration\Account;

use App\Enums\HttpStatusEnum;
use App\Models\AccountData;
use Illuminate\Http\JsonResponse;

class RemoveLogoImage
{
    public function execute(): JsonResponse
    {
        try {
            $accountData = AccountData::firstOrFail();

            $accountData->logo_image_url = '';

            $accountData->save();

            return response()->json([
                'status' => true,
                'message' => 'Imagem da conta removida com sucesso!',
            ], HttpStatusEnum::OK);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }
}
