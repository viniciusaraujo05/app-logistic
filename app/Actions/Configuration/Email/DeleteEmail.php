<?php

namespace App\Actions\Configuration\Email;

use App\Enums\HttpStatusEnum;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Throwable;

class DeleteEmail
{
    public function execute(int $emailId): JsonResponse
    {
        try {
            $emails = Email::destroy($emailId);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Role created successfully',
                    'emails' => $emails
                ],
                HttpStatusEnum::OK
            );
        } catch (Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                HttpStatusEnum::INTERNAL_SERVER_ERROR
            );
        }
    }
}
