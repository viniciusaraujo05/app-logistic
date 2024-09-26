<?php

namespace App\Http\Controllers\Api\Chart;

use App\Actions\Chart\ChartIndex;
use App\Actions\Chart\ChartShow;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chart\ChartShowRequest;
use Illuminate\Http\JsonResponse;

class ChartController extends Controller
{
    public function index(ChartIndex $chartIndex): JsonResponse
    {
        return $chartIndex->execute();
    }

    public function show(ChartShowRequest $request, ChartShow $chartShow): JsonResponse
    {
        return $chartShow->execute($request->get('type'));
    }
}
