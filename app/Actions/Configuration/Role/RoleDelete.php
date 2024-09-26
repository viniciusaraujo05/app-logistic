<?php

namespace App\Actions\Configuration\Role;

use App\Enums\HttpStatusEnum;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class RoleDelete
{
    public function execute($id): JsonResponse
    {
        try {
            App::setLocale('pt');

            $role = Role::findOrFail($id);

            if ($role->name === 'admin') {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Cannot delete admin role',
                    ],
                    HttpStatusEnum::FORBIDDEN
                );
            }

            $role->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Role deleted successfully',
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
