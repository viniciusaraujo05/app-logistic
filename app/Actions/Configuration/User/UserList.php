<?php

namespace App\Actions\Configuration\User;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserList
{
    public function execute(): JsonResponse
    {
        try {
            $users = User::all();

            $formattedUsers = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Customer created successfully',
                    'users' => $formattedUsers,
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
