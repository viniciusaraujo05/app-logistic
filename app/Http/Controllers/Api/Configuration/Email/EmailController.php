<?php

namespace App\Http\Controllers\Api\Configuration\Email;

use App\Actions\Configuration\Email\CreateEmail;
use App\Actions\Configuration\Email\DeleteEmail;
use App\Actions\Configuration\Email\GetAvailableStatuses;
use App\Actions\Configuration\Email\IndexEmail;
use App\Actions\Configuration\Email\ShowEmail;
use App\Actions\Configuration\Email\UpdateEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\Email\CreateEmailRequest;
use App\Http\Requests\Configuration\Email\DeleteEmailRequest;
use App\Http\Requests\Configuration\Email\ShowEmailRequest;
use App\Http\Requests\Configuration\Email\UpdateEmailRequest;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function listAll(IndexEmail $indexEmail): JsonResponse
    {
        return $indexEmail->execute();
    }

    public function show(ShowEmailRequest $request, ShowEmail $showEmail): JsonResponse
    {
        return $showEmail->execute($request->get('email_id'));
    }

    public function update(UpdateEmailRequest $request, UpdateEmail $updateEmail): JsonResponse
    {
        return $updateEmail->execute($request->all());
    }

    public function create(CreateEmailRequest $request, CreateEmail $createEmail): JsonResponse
    {
        return $createEmail->execute($request->all());
    }

    public function delete(DeleteEmailRequest $request, DeleteEmail $deleteEmail): JsonResponse
    {
        return $deleteEmail->execute($request->get('email_id'));
    }

    public function getAvailableStatuses(GetAvailableStatuses $getAvailableStatuses): JsonResponse
    {
        return $getAvailableStatuses->execute();
    }
}
