<?php

namespace App\Actions\Configuration\Email;

use App\Enums\HttpStatusEnum;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Throwable;

class IndexEmail
{
    public function execute(): JsonResponse
    {
        try {
            $emails = Email::with('status')->get();

            $emails = $emails->map(function ($email) {
                return [
                    'id' => $email->id,
                    'name' => $email->name,
                    'status_name' => $email->status->name,
                    'status_id' => $email->status->id,
                ];
            });

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Mail returned successfully',
                    'emails' => $emails,
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
