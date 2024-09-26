<?php

namespace App\Http\Controllers\Api\Carriers;

use App\Actions\Carriers\Volumes\ShowVolumes;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class VolumesController extends Controller
{
    /**
     * show a Volumes.
     *
     * @param  string  $orderCode  orderCode.
     *
     * @throws \Throwable
     */
    public function show(string $orderCode, ShowVolumes $showVolumes): JsonResponse
    {
        return $showVolumes->execute($orderCode);
    }
}
