<?php

namespace App\Actions\Carriers\Volumes;

use App\Enums\HttpStatusEnum;
use App\Models\Volumes;
use Illuminate\Http\JsonResponse;

class ShowVolumes
{
    /**
     * show a Volumes.
     *
     * @param  string  $orderCode  orderCode.
     *
     * @throws \Throwable
     */
    public function execute(string $orderCode): JsonResponse
    {
        try {
            $volumes = Volumes::query()->where('order_code', $orderCode)->get();
            if (! $volumes) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Volumes not found',
                    ],
                    HttpStatusEnum::NOT_FOUND
                );
            }

            return response()->json(
                [
                    'status' => true,
                    'volumes' => $volumes,
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
