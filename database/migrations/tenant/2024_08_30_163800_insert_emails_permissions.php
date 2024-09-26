<?php

use App\Extensions\PermissionMigration;

return new class () extends PermissionMigration {
    protected array $permissions = [
        'email.view',
        'email.create',
        'email.edit',
        'email.delete',
    ];
};
