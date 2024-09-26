<?php

namespace App\Http\Controllers\Api\Configuration\Role;

use App\Actions\Configuration\Role\RoleCreate;
use App\Actions\Configuration\Role\RoleDelete;
use App\Actions\Configuration\Role\RoleList;
use App\Actions\Configuration\Role\RoleShow;
use App\Actions\Configuration\Role\RoleUpdate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\Role\CreateRoleRequest;
use App\Http\Requests\Configuration\Role\DeleteRoleRequest;
use App\Http\Requests\Configuration\Role\ShowRoleRequest;
use App\Http\Requests\Configuration\Role\UpdateRoleRequest;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function listAll(RoleList $roleList): JsonResponse
    {
        return $roleList->execute();
    }

    public function show(ShowRoleRequest $request, RoleShow $roleShow): JsonResponse
    {
        return $roleShow->execute($request->get('id'));
    }

    public function update(UpdateRoleRequest $request, RoleUpdate $roleUpdate): JsonResponse
    {
        return $roleUpdate->execute($request->all());
    }

    public function create(CreateRoleRequest $request, RoleCreate $roleCreate): JsonResponse
    {
        return $roleCreate->execute($request->all());
    }

    public function delete(DeleteRoleRequest $request, RoleDelete $roleDelete): JsonResponse
    {
        return $roleDelete->execute($request->get('id'));
    }
}
