<?php

namespace App\Actions\Carriers\Tracking;

use App\Enums\HttpStatusEnum;
use App\Models\Tracking;
use App\Models\TrackingTransit;
use Illuminate\Http\JsonResponse;
use Throwable;

class CreateTracking
{
    /**
     * create a new Tracking in database.
     *
     * @throws Throwable
     */
    public function execute(array $data): JsonResponse
    {
        try {
            $tracking = Tracking::query()->create($data);
            TrackingTransit::query()->create(
                [
                    'order_id' => $tracking->order_id,
                    'tracking_id' => $tracking->id
                ]
            );

            return response()->json(
                [
                    'status' => true,
                    'tracking' => $tracking,
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
