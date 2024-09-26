<?php

namespace App\Actions\Configuration\Email;

use App\Enums\HttpStatusEnum;
use App\Models\Email;
use Illuminate\Http\JsonResponse;
use Throwable;

class CreateEmail
{
    public function execute(array $data): JsonResponse
    {
        try {
            $email = new Email();
            $email->fill($data);
            $email->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Email created successfully',
                ],
                HttpStatusEnum::CREATED
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
