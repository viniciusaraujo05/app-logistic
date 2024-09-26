<?php

namespace App\Actions\Configuration\User;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserIndex
{
    public function execute(int $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);

            $formattedUser = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'last_name' => $user->last_name,
                'profile_image' => $user->profile_image,
                'phone_number' => $user->phone_number,
            ];

            return response()->json([
                'status' => true,
                'message' => 'User data retrieved successfully',
                'user' => $formattedUser,
            ], HttpStatusEnum::OK);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }
}
