<?php

namespace App\Actions\Tokens;

use App\Enums\HttpStatusEnum;
use App\Models\Token;
use Illuminate\Http\JsonResponse;

class ToggleToken
{
    /**
     * Atualiza o status do token para o oposto do valor atual.
     *
     * @param  int  $tokenId  ID do token a ser atualizado.
     */
    public function execute(int $tokenId): JsonResponse
    {
        try {
            $token = Token::query()->findOrFail($tokenId);

            if (! $token) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Token not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            $token->status = ! $token->status;

            $token->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Token status toggled successfully',
                    'token' => $token,
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
