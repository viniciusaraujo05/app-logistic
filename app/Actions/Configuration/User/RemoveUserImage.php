<?php

namespace App\Actions\Configuration\User;

use App\Enums\HttpStatusEnum;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RemoveUserImage
{
    public function execute(string $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);

            $user->profile_image = '';

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Imagem de perfil do usuário removida com sucesso',
            ], HttpStatusEnum::OK);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao remover imagem de perfil do usuário',
                'error' => $th->getMessage(),
            ], HttpStatusEnum::INTERNAL_SERVER_ERROR);
        }
    }
}
