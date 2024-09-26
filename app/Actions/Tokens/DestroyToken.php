<?php

namespace App\Actions\Tokens;

use App\Enums\HttpStatusEnum;
use App\Models\Token;
use Illuminate\Http\JsonResponse;

class DestroyToken
{
    /**
     * delete a token.
     *
     * @param  int  $tokenId  token ID.
     *
     * @throws \Throwable
     */
    public function execute(int $tokenId): JsonResponse
    {
        try {
            $customer = Token::query()->find($tokenId);
            if (! $customer) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Token not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            $customer->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Token deleted successfully',
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
