<?php

namespace App\Actions\Carriers\Tracking;

use App\Enums\HttpStatusEnum;
use App\Models\TrackingTransit;
use Illuminate\Http\JsonResponse;

class DestroyTrackingTransit
{
    /**
     * delete a trackingTransit.
     *
     * @param string $orderId
     *
     * @return JsonResponse
     */
    public function execute(string $orderId): JsonResponse
    {
        try {
            $trackingTransit = TrackingTransit::query()->where('order_id', $orderId)->first();
            if (!$trackingTransit) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'TrackingTransit not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            $trackingTransit->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'trackingTransit deleted successfully',
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
