<?php

namespace App\Actions\Tokens;

use App\Enums\HttpStatusEnum;
use App\Models\Token;
use Illuminate\Http\JsonResponse;
use Throwable;

class UpdateToken
{
    /**
     * Atualiza um cliente existente.
     *
     * @param  array  $data  Dados do Token a serem atualizados.
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        $tokenId = $data['id'];
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

            $token->update($data);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Customer updated successfully',
                    'customer' => $token,
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
