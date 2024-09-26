<?php

namespace App\Actions\Configuration\Role;

use App\Enums\HttpStatusEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class RoleUpdate
{
    public function execute($data): JsonResponse
    {
        try {
            App::setLocale('pt');

            $id = $data['id'];
            $name = $data['name'];
            $permissions = $data['permissions'];

            $role = Role::findOrFail($id);

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
                    'message' => 'Role updated successfully',
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
