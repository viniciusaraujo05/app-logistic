<?php

namespace App\Http\Controllers\Api\Configuration\Permission;

use App\Actions\Configuration\Permission\PermissionList;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function listAll(PermissionList $permissionList): JsonResponse
    {
        return $permissionList->execute();
    }
}
