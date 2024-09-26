<?php

namespace App\Actions\Configuration\User;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UpdateUser
{
    public function execute(array $data): JsonResponse
    {
        try {
            $user = User::findOrFail($data['user_id']);

            $user->name = $data['name'] ?? $user->name;
            $user->last_name = $data['last_name'] ?? $user->last_name;
            $user->phone_number = $data['phone_number'] ?? $user->phone_number;
            $user->profile_image = $data['profile_image'] ?? $user->profile_image;

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Dados do usuário atualizados com sucesso',
                'user' => $user,
            ], HttpStatusEnum::OK);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar os dados do usuário',
                'error' => $th->getMessage(),
            ], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }
}
