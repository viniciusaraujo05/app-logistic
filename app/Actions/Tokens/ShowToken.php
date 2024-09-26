<?php

namespace App\Actions\Tokens;

use App\Enums\HttpStatusEnum;
use App\Models\Token;
use Illuminate\Http\JsonResponse;
use Throwable;

class ShowToken
{
    /**
     * show a Token.
     *
     * @param  int  $tokenId  Token ID.
     *
     * @throws Throwable
     */
    public function execute(int $tokenId): JsonResponse
    {
        try {
            $token = Token::query()->find($tokenId);

            if (! $token) {
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
                    'token' => $token,
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
