<?php

namespace App\Actions\Carriers;

use App\Enums\HttpStatusEnum;
use App\Enums\IntegrationEnum;
use App\Models\Token;
use Illuminate\Http\JsonResponse;

class ListCarriers
{
    public function execute(): JsonResponse
    {
        try {
            $token = Token::query()
                ->join('public.integrations', 'tokens.integration_id', '=', 'public.integrations.id')
                ->whereIn(
                    'integrations.name',
                    [
                        IntegrationEnum::VASP,
                        IntegrationEnum::SELFSHIPPING,
                        IntegrationEnum::PICKUP,
                        IntegrationEnum::CTT,
                        IntegrationEnum::CORREOS,
                    ]
                )
                ->select(
                    'tokens.id as token_id',
                    'tokens.integration_id',
                    'public.integrations.id as integration_id',
                    'public.integrations.name as integration_name'
                )
                ->get();

            if (!$token) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Token not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            return response()->json(
                [
                    'status' => true,
                    'carrier' => $token,
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
