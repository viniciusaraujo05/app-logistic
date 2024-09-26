<?php

namespace App\Actions\Carriers\Tracking;

use App\Enums\HttpStatusEnum;
use App\Models\Tracking;
use Illuminate\Http\JsonResponse;

class ShowTracking
{
    /**
     * Show a Tracking.
     *
     * @param  string  $orderId  orderId.
     *
     * @throws \Throwable
     */
    public function execute(string $orderId): JsonResponse
    {
        try {
            $tracking = Tracking::query()->where('order_id', $orderId)->get();
            if (! $tracking) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Tracking not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            return response()->json(
                [
                    'status' => true,
                    'tracking' => $tracking,
                ],
                HttpStatusEnum::OK
            );
        } catch (\Throwable $th) {
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
