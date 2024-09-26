<?php

namespace App\Extensions;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

abstract class PermissionMigration extends Migration
{
    protected array $permissions = [];

    protected array $deleteOnUp = [];

    protected array $roles = ['admin'];

    /**
     * Suppress ShortMethodName warning for this file.
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function up(): void
    {
        $rolesId = DB::table('roles')
            ->select('id')
            ->where('guard_name', 'api')
            ->whereIn('name', $this->roles)
            ->pluck('id')
            ->toArray();

        $permissionsIds = [];
        foreach ($this->permissions as $permission) {
            $permissionsIds[] = DB::table('permissions')
                ->insertGetId(
                    [
                        'name' => $permission,
                        'guard_name' => 'api',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
        }

        foreach ($rolesId as $roleId) {
            foreach ($permissionsIds as $permissionId) {
                DB::table('role_has_permissions')->insert(
                    [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]
                );
            }
        }

        foreach ($this->deleteOnUp as $permission) {
            DB::table('permissions')
                ->where('name', $permission)
                ->where('guard_name', 'api')
                ->delete();
        }
    }

    public function down(): void
    {
        foreach ($this->permissions as $permission) {
            DB::table('permissions')
                ->where('name', $permission)
                ->where('guard_name', 'api')
                ->delete();
        }
    }
}
