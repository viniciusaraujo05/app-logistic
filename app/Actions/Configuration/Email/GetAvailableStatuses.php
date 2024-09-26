<?php

namespace App\Actions\Configuration\Email;

use App\Enums\HttpStatusEnum;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Throwable;

class GetAvailableStatuses
{
    public function execute(): JsonResponse
    {
        try {
            $statuses = OrderStatus::query()
                ->select('id', 'name')
                ->where('is_active', true)
                ->whereDoesntHave('email')
                ->get();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Statuses returned successfully',
                    'statuses' => $statuses,
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
