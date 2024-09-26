<?php

namespace App\Actions\Configuration\Role;

use App\Enums\HttpStatusEnum;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class RoleList
{
    public function execute(): JsonResponse
    {
        try {
            App::setLocale('pt');

            $roles = Role::with('permissions')->where('name', '!=', 'admin')->get();

            $formattedRoles = $roles->map(function ($role) {
                $translatedPermissions = $role->permissions->map(function ($permission) {
                    return Lang::get('permissions.'.$permission->name);
                });

                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $translatedPermissions->implode(', '),
                ];
            });

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Roles and permissions retrieved successfully',
                    'roles' => $formattedRoles,
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
