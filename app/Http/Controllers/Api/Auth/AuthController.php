<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\CheckId;
use App\Actions\Auth\CheckSession;
use App\Actions\Auth\CreateUser;
use App\Actions\Auth\DestroyUser;
use App\Actions\Auth\LoginUser;
use App\Actions\Auth\LogoutUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    /**
     * Create User
     *
     * @throws Throwable
     */
    public function store(UserRequest $request, CreateUser $createUser): JsonResponse
    {
        return $createUser->execute($request->all());
    }

    /**
     * Login The User
     *
     * @throws Throwable
     */
    public function loginUser(LoginRequest $request, LoginUser $loginUser): JsonResponse
    {
        return $loginUser->execute($request);
    }

    /**
     * Logout The User
     */
    public function logoutUser(Request $request, LogoutUser $logoutUser): JsonResponse
    {
        return $logoutUser->execute($request->get('userId'));
    }

    /**
     * Check ID
     */
    public function checkId(Request $request, CheckId $checkId): JsonResponse
    {
        return $checkId->execute($request->all());
    }

    /**
     * Delete User
     *
     * @param  int  $userId  user ID
     *
     * @throws Throwable
     */
    public function destroy(int $userId, DestroyUser $destroyUser): JsonResponse
    {
        return $destroyUser->execute($userId);
    }

    /**
     * Check session
     *
     * @param  Request  $request  user
     */
    public function checkSession(Request $request, CheckSession $checkSession): JsonResponse
    {
        return $checkSession->execute($request->get('userId'));
    }
}
