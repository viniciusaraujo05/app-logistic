<?php

namespace App\Http\Controllers\Api\Carriers;

use App\Actions\Carriers\ListCarriers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CarrierController extends Controller
{
    public function getAll(
        ListCarriers $listCarriers,
    ): JsonResponse {
        return $listCarriers->execute();
    }
}
