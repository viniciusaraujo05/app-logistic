<?php

namespace App\Actions\Configuration\Email;

use App\Enums\HttpStatusEnum;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Throwable;

class ShowEmail
{
    public function execute(int $emailId): JsonResponse
    {
        try {
            $email = Email::with('status')->find($emailId);

            if (!$email) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Email not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            $emailData = [
                'id' => $email->id,
                'name' => $email->name,
                'html_content' => $email->html_content,
                'design' => $email->design,
                'status' => [
                    'status_name' => $email->status->name,
                    'status_id' => $email->status->id,
                ],
                'created_at' => $email->created_at,
                'updated_at' => $email->updated_at,
            ];

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Mail found successfully',
                    'email' => $emailData,
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
