<?php

namespace App\Actions\Auth;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CheckSession
{
    public function execute(int $userId): JsonResponse
    {
        try {
            $user = User::query()->findOrFail($userId);

            $token = $user->tokens()
                ->where('name', 'API TOKEN')
                ->where('tokenable_id', $userId)
                ->where('tokenable_type', User::class)
                ->orderBy('id', 'desc')
                ->first();

            if ($token->expires_at && Carbon::parse($token->expires_at)->isPast()) {
                $token->delete();

                return response()->json(
                    [
                        'message' => 'Expired session token',
                        'status' => false,
                    ],
                    HttpStatusEnum::UNAUTHORIZED
                );
            }

            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');

            return response()->json(
                [
                    'message' => 'Valid session token',
                    'status' => true,
                    'session' => [
                        'username' => $user->name,
                        'roles' => $roles,
                        'permissions' => $permissions,
                        'profileImage' => $user->profile_image,
                    ],
                ],
                HttpStatusEnum::OK
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => 'Error checking user session',
                ],
                HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }
}
