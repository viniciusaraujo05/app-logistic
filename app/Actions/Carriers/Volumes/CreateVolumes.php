<?php

namespace App\Actions\Carriers\Volumes;

use App\Enums\HttpStatusEnum;
use App\Models\Volumes;
use Illuminate\Http\JsonResponse;

class CreateVolumes
{
    /**
     * create a new Tracking in database.
     *
     * @throws \Throwable
     */
    public function execute(array $data): JsonResponse
    {
        try {
            $volumes = Volumes::query()->create($data);

            return response()->json(
                [
                    'status' => true,
                    'tracking' => $volumes,
                ],
                HttpStatusEnum::CREATED
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
