<?php

namespace App\Actions\Auth;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DestroyUser
{
    /**
     * Delete a User.
     *
     * @param  int  $userId  user ID.
     *
     * @throws \Throwable
     */
    public function execute(int $userId): JsonResponse
    {
        try {
            $user = User::query()->findOrFail($userId);
            if ($user->id === auth()->id()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'You cannot delete yourself.',
                    ],
                    HttpStatusEnum::BAD_REQUEST
                );
            }

            $user->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'User deleted successfully',
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
