<?php

use App\Extensions\PermissionMigration;

return new class () extends PermissionMigration {
    protected array $permissions = [
        'configuration.view',
        'configuration.edit',
        'configuration.create',
        'configuration.update',
    ];
};
