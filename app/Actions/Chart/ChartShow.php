<?php

namespace App\Actions\Chart;

use App\Enums\HttpStatusEnum;
use App\Models\Chart;
use App\Models\ChartType;
use App\Utils\ChartDataTableFormatter;
use App\Utils\IntegrationMapper;
use Illuminate\Http\JsonResponse;
use Throwable;

class ChartShow
{
    protected ChartDataTableFormatter $dataOrganizer;

    public function __construct(IntegrationMapper $integrationMapper)
    {
        $this->dataOrganizer = new ChartDataTableFormatter($integrationMapper);
    }

    public function execute(string $type): JsonResponse
    {
        try {
            $chartTypeId = $this->getChartTypeId($type);
            $charts = Chart::query()->where('chart_type_id', $chartTypeId)->get();

            $organizedCharts = $this->dataOrganizer->organizeChartsData($charts, $type);

            return response()->json(
                [
                    'status' => true,
                    'data' => $organizedCharts['data'],
                    'columns' => $organizedCharts['columns'],
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

    private function getChartTypeId(string $chartType): int
    {
        return ChartType::query()->where('identifier', $chartType)->first()->id;
    }
}
