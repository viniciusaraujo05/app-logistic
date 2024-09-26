<?php

namespace App\Actions\Auth;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LogoutUser
{
    /**
     * Logout User.
     *
     * @throws Throwable description of exception
     */
    public function execute(int $userId): JsonResponse
    {
        try {
            $user = User::query()->findOrFail($userId);

            $token = $user->tokens()
                ->where('name', 'API TOKEN')
                ->first();

            if ($token) {
                $token->delete();
            }

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Logout successfully',
                ],
                HttpStatusEnum::OK
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                HttpStatusEnum::BAD_REQUEST
            );
        }
    }
}
