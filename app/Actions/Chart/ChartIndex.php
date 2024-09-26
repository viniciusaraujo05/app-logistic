<?php

namespace App\Actions\Chart;

use App\Enums\HttpStatusEnum;
use App\Models\Chart;
use App\Models\ChartType;
use Illuminate\Http\JsonResponse;
use Throwable;

class ChartIndex
{
    public function execute(): JsonResponse
    {
        try {
            $chartTypes = ChartType::query()->pluck('identifier', 'id')->toArray();

            $charts = Chart::query()->get();

            $organizedCharts = [];

            foreach ($charts as $chart) {
                $chartTypeName = $chartTypes[$chart->chart_type_id];
                $organizedCharts[$chartTypeName][] = json_decode($chart->data, true);
            }

            return response()->json(
                [
                    'status' => true,
                    'charts' => $organizedCharts,
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
