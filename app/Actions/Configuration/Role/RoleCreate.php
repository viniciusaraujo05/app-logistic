<?php

namespace App\Actions\Configuration\Role;

use App\Enums\HttpStatusEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class RoleCreate
{
    public function execute($data): JsonResponse
    {
        try {
            App::setLocale('pt');

            $name = $data['name'];
            $permissions = $data['permissions'];

            $existingRole = Role::where('name', $name)->first();

            if ($existingRole) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Role already exists with this name',
                    ],
                    HttpStatusEnum::BAD_REQUEST
                );
            }

            $role = new Role();
            $role->name = $name;
            $role->save();

            $inverseMapping = getInversePermissionMapping();

            $permissions = array_map(function ($permissionName) use ($inverseMapping) {
                return $inverseMapping[$permissionName] ?? $permissionName;
            }, $permissions);

            $allPermissions = Permission::whereIn('name', $permissions)->get();

            $role->permissions()->sync($allPermissions);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Role created successfully',
                    'role' => $role,
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
