<?php

namespace App\Actions\Auth;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser
{
    protected Validator $validator;

    protected Hash $hash;

    public function __construct(Validator $validator, Hash $hash)
    {
        $this->validator = $validator;
        $this->hash = $hash;
    }

    /**
     * A description of the entire PHP function.
     *
     * @param  array  $data  description
     *
     * @throws \Throwable description of exception
     */
    public function execute(array $data): JsonResponse
    {
        try {
            $user = User::query()->create(
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $this->hash::make($data['password']),
                ]
            );

            return response()->json(
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
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
