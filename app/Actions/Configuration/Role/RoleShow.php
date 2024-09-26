<?php

namespace App\Actions\Configuration\Role;

use App\Enums\HttpStatusEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class RoleShow
{
    public function execute(int $roleId): JsonResponse
    {
        try {
            App::setLocale('pt');

            $role = Role::with('permissions')->where('name', '!=', 'admin')->findOrFail($roleId);

            $allPermissions = Permission::all();

            $assignedPermissions = $role->permissions->pluck('name')->all();

            $availablePermissions = $allPermissions->reject(function ($permission) use ($assignedPermissions) {
                return in_array($permission->name, $assignedPermissions);
            });

            $translatedAssignedPermissions = $role->permissions->map(function ($permission) {
                return Lang::get('permissions.'.$permission->name);
            });

            $translatedAvailablePermissions = $availablePermissions->map(function ($permission) {
                return Lang::get('permissions.'.$permission->name);
            });

            $formattedRole = [
                'id' => $role->id,
                'name' => $role->name,
                'assigned_permissions' => $translatedAssignedPermissions,
                'available_permissions' => $translatedAvailablePermissions,
            ];

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Role and permissions retrieved successfully',
                    'role' => $formattedRole,
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
