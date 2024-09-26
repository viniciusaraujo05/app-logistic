<?php

namespace App\Actions\Auth;

use App\Enums\HttpStatusEnum;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class LoginUser
{
    protected Auth $auth;

    protected Validator $validator;

    public function __construct(Auth $auth, Validator $validator)
    {
        $this->auth = $auth;
        $this->validator = $validator;
    }

    /**
     * A description of the entire PHP function.
     *
     * @param  LoginRequest  $request  description
     *
     * @throws Throwable description of exception
     */
    public function execute(LoginRequest $request): JsonResponse
    {
        try {
            if (! $this->auth::attempt($request->only(['email', 'password']))) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Email & Password does not match with our record.',
                    ],
                    HttpStatusEnum::UNAUTHORIZED
                );
            }

            $user = User::query()->where('email', $request->email)->first();

            $token = $user->createToken('API TOKEN');
            $token->accessToken->expires_at = now()->addHours(24);
            $token->accessToken->save();

            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');

            return response()->json(
                [
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => $token->plainTextToken,
                    'user_id' => $user->id,
                    'username' => $user->name,
                    'roles' => $roles,
                    'permissions' => $permissions,
                    'profileImage' => $user->profile_image,
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
