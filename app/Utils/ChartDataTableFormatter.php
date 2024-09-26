<?php

namespace App\Utils;

use App\Enums\Chart\ChartTypesEnum;

class ChartDataTableFormatter
{
    protected IntegrationMapper $integrationMapper;

    public function __construct(IntegrationMapper $integrationMapper)
    {
        $this->integrationMapper = $integrationMapper;
    }

    public function organizeChartsData($charts, string $type): array
    {
        $organizedData = [];
        $columns = [];

        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        foreach ($charts as $chart) {
            $data = json_decode($chart->data, true);

            switch ($type) {
                case ChartTypesEnum::ENCOMENDAS_POR_HORA_DIA:
                    $dateKey = "{$data['year']}-{$data['month']}-{$data['day']}";
                    $hourKey = $data['hour'];
                    $key = "{$dateKey} {$hourKey}";

                    if (!isset($organizedData[$key])) {
                        $organizedData[$key] = [
                            'hora' => $hourKey . ' ' . 'h',
                            'data' => date('d/m/Y', strtotime($dateKey)),
                            'encomendas' => 0,
                        ];
                    }
                    $organizedData[$key]['encomendas'] += 1;
                    $columns = ['hora', 'data', 'encomendas'];
                    break;

                case ChartTypesEnum::ENCOMENDAS_POR_DIA_MES:
                    $dateKey = "{$data['year']}-{$data['month']}-{$data['day']}";

                    if (!isset($organizedData[$dateKey])) {
                        $mes = $months[(int)$data['month']];
                        $organizedData[$dateKey] = [
                            'data' => date('d/m/Y', strtotime($dateKey)),
                            'mes' => $mes,
                            'encomendas' => 0,
                        ];
                    }
                    $organizedData[$dateKey]['encomendas'] += 1;
                    $columns = ['data', 'mes', 'encomendas'];
                    break;

                case ChartTypesEnum::ENVIOS_POR_TRANSPORTADORA:
                    $dateKey = "{$data['year']}-{$data['month']}-{$data['day']}";
                    $key = "{$data['carrier']}_{$dateKey}";

                    if (!isset($organizedData[$key])) {
                        $organizedData[$key] = [
                            'carrier' => $data['carrier'],
                            'data' => date('d/m/Y', strtotime($dateKey)),
                            'envios' => 0,
                        ];
                    }
                    $organizedData[$key]['envios'] += 1;
                    $columns = ['carrier', 'data', 'envios'];
                    break;

                case ChartTypesEnum::TEMPO_MEDIO_ENVIO:
                    $dateKey = "{$data['year']}-{$data['month']}-{$data['day']}";
                    $organizedData[] = [
                        'ordem' => $data['order_id'],
                        'tempo' => round($data['time'], 2) . ' ' . 'minutos',
                        'data' => date('d/m/Y', strtotime($dateKey)),
                    ];
                    $columns = ['ordem', 'tempo', 'data'];
                    break;

                case ChartTypesEnum::ENVIOS_POR_DIA_MES:
                    $dateKey = "{$data['year']}-{$data['month']}-{$data['day']}";
                    if (!isset($organizedData[$dateKey])) {
                        $organizedData[$dateKey] = [
                            'data' => date('d/m/Y', strtotime($dateKey)),
                            'envios' => 0,
                        ];
                    }
                    $organizedData[$dateKey]['envios'] += 1;
                    $columns = ['data', 'envios'];
                    break;

                case ChartTypesEnum::MAPA_ENVIOS_PAIS:
                    $dateFormatted = date('d/m/Y', strtotime($data['date']));
                    $key = "{$data['city']}_{$dateFormatted}";

                    if (!isset($organizedData[$key])) {
                        $organizedData[$key] = [
                            'city' => $data['city'],
                            'data' => $dateFormatted,
                            'envios' => 0,
                        ];
                    }
                    $organizedData[$key]['envios'] += 1;
                    $columns = ['city', 'data', 'envios'];
                    break;

                default:
                    break;
            }
        }

        if ($type === ChartTypesEnum::ENVIOS_POR_TRANSPORTADORA) {
            $formattedData = [];
            foreach ($organizedData as $entry) {
                $carrierIdentifier = $this->integrationMapper->integrationValue($entry['carrier']);
                $formattedData[] = [
                    'carrier' => $carrierIdentifier,
                    'data' => $entry['data'],
                    'envios' => $entry['envios'],
                ];
            }
        } else {
            $formattedData = array_values($organizedData);
        }

        $transformedColumns = array_map(function ($column) {
            return [
                'accessorKey' => $column,
                'header' => ucfirst($column),
            ];
        }, array_values(array_unique($columns)));

        return [
            'data' => $formattedData,
            'columns' => $transformedColumns,
        ];
    }
}
