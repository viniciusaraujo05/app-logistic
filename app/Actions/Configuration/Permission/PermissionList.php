<?php

namespace App\Actions\Configuration\Permission;

use App\Enums\HttpStatusEnum;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class PermissionList
{
    public function execute(): JsonResponse
    {
        try {
            App::setLocale('pt');

            $permissions = Permission::all();

            $translatedPermissions = $permissions->map(function ($permission) {
                return Lang::get('permissions.'.$permission->name);
            });

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Permissions retrieved successfully',
                    'permissions' => $translatedPermissions,
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
