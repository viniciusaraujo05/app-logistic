<?php

use App\Extensions\PermissionMigration;

return new class () extends PermissionMigration {
    protected array $permissions = [
        'parameterization.view',
        'order_status.view',
        'integration.view',
        'roles.view',
        'roles.create',
        'roles.edit',
        'roles.delete',
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',
        'profile.view',
        'profile.edit',
        'account.view',
        'account.edit',
    ];
};
