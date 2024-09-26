<?php

namespace App\Http\Controllers\Api\Configuration\User;

use App\Actions\Configuration\User\RemoveUserImage;
use App\Actions\Configuration\User\UpdateUser;
use App\Actions\Configuration\User\UserIndex;
use App\Actions\Configuration\User\UserList;
use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\User\UpdateUserRequest;
use App\Http\Requests\Configuration\User\UserIndexRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(UserIndexRequest $userIndexRequest, UserIndex $userIndex): JsonResponse
    {
        return $userIndex->execute($userIndexRequest->get('user_id'));
    }

    public function update(UpdateUserRequest $updateUserRequest, UpdateUser $updateUser): JsonResponse
    {
        return $updateUser->execute($updateUserRequest->all());
    }

    public function removeImage(UpdateUserRequest $updateUserRequest, RemoveUserImage $removeUserImage): JsonResponse
    {
        return $removeUserImage->execute($updateUserRequest->get('user_id'));
    }

    public function listAll(UserList $userList): JsonResponse
    {
        return $userList->execute();
    }
}
