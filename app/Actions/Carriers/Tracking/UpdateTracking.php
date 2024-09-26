<?php

namespace App\Actions\Carriers\Tracking;

use App\Enums\HttpStatusEnum;
use App\Models\Tracking;
use Illuminate\Http\JsonResponse;

class UpdateTracking
{
    /**
     * Atualiza um cliente existente.
     *
     * @param  array  $data  Dados do cliente a serem atualizados.
     * @param  int  $trackingId  ID do cliente a ser atualizado.
     *
     * @throws \Throwable
     */
    public function execute(array $data, int $trackingId): JsonResponse
    {
        try {
            $tracking = Tracking::query()->findOrFail($trackingId);

            $tracking->update($data);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Tracking updated successfully',
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
