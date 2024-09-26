<?php

namespace App\Http\Controllers\Api\Configuration\Account;

use App\Actions\Configuration\Account\AccountIndex;
use App\Actions\Configuration\Account\AccountUpdate;
use App\Actions\Configuration\Account\RemoveLogoImage;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(AccountIndex $accountIndex): JsonResponse
    {
        return $accountIndex->execute();
    }

    public function update(Request $accountUpdateRequest, AccountUpdate $accountUpdate): JsonResponse
    {
        return $accountUpdate->execute($accountUpdateRequest->all());
    }

    public function removeImage(RemoveLogoImage $removeLogoImage): JsonResponse
    {
        return $removeLogoImage->execute();
    }
}
