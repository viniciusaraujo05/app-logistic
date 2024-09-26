<?php

namespace App\Actions\Configuration\Email;

use App\Enums\HttpStatusEnum;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Throwable;

class UpdateEmail
{
    public function execute(array $data): JsonResponse
    {
        try {
            $email = Email::findOrFail($data['email_id']);

            $email->name = $data['name'];
            $email->html_content = $data['html_content'];
            $email->design = $data['design'] ?? $email->design;
            $email->status_id = $data['status_id'] ?? $email->status_id;

            $email->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Email updated successfully',
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
