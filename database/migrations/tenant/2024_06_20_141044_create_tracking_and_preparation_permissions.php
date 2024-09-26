<?php

use App\Extensions\PermissionMigration;

return new class () extends PermissionMigration {
    protected array $permissions = [
        'tracking.view',
        'tracking.edit',
        'tracking.create',
        'tracking.update',
        'preparation.view',
        'preparation.edit',
        'preparation.create',
        'preparation.update',
    ];
};
