<?php

namespace App\Actions\Tokens;

use App\Enums\HttpStatusEnum;
use App\Models\Token;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class IndexTokens
{
    /**
     * showll a tokens.
     *
     * @throws \Throwable
     */
    public function execute($integrationType): JsonResponse
    {
        try {
            $tokens = Token::query()
                ->whereExists(
                    function ($query) use ($integrationType) {
                        $query->select(DB::raw(1))
                            ->from('public.integrations')
                            ->whereColumn('tokens.integration_id', 'integrations.id')
                            ->where('integrations.integration_type_id', $integrationType);
                    }
                )
                ->select(
                    [
                        'id',
                        'name',
                        'token',
                        'url',
                        'status',
                        'platform',
                        'tokens.integration_id',
                    ]
                )
                ->get();

            if (! $tokens) {
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
                    'customer' => $tokens,
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
