<?php

namespace App\Services\Chart;

use App\Enums\Chart\ChartTypesEnum;
use App\Models\Chart;
use App\Models\ChartType;
use App\Models\Order;
use App\Models\OrderStatus;

class ChartDataService
{
    public function saveChartData(Order $order, ?string $carrier = null, array $updatedAt = null, bool $createdNow = false): void
    {
        $status = OrderStatus::find($order->status_id);

        if (!$status) {
            return;
        }

        if ($createdNow) {
            $this->saveDataReceived($order);
            return;
        }

        if ($carrier) {
            $this->saveDataSendByCarrier($order, $carrier);
            return;
        }

        if ($updatedAt) {
            $this->saveDataWhenSendByDate($order, $updatedAt);
            return;
        }

        if ($status->name === 'Em Transito') {
            $this->saveDataWhenSend($order);
        }
    }

    protected function saveDataReceived(Order $order): void
    {
        $currentDate = now();
        $this->saveChartEntry(
            ChartTypesEnum::ENCOMENDAS_POR_HORA_DIA,
            [
                'order_id' => $order->id,
                'hour' => $currentDate->format('H'),
                'day' => $currentDate->format('d'),
                'month' => $currentDate->format('m'),
                'year' => $currentDate->format('Y'),
            ]
        );

        $this->saveChartEntry(
            ChartTypesEnum::ENCOMENDAS_POR_DIA_MES,
            [
                'order_id' => $order->id,
                'day' => $currentDate->format('d'),
                'month' => $currentDate->format('m'),
                'year' => $currentDate->format('Y'),
            ]
        );
    }

    private function saveChartEntry(string $chartType, array $data): void
    {
        $chartTypeId = $this->getChartTypeId($chartType);
        Chart::create([
            'chart_type_id' => $chartTypeId,
            'data' => json_encode($data),
        ]);
    }

    private function getChartTypeId(string $identifier): int
    {
        return ChartType::query()
            ->where('identifier', $identifier)
            ->value('id');
    }

    protected function saveDataSendByCarrier(Order $order, string $carrier): void
    {
        $currentDate = now();
        $this->saveChartEntry(
            ChartTypesEnum::ENVIOS_POR_TRANSPORTADORA,
            [
                'order_id' => $order->id,
                'carrier' => $carrier,
                'day' => $currentDate->format('d'),
                'month' => $currentDate->format('m'),
                'year' => $currentDate->format('Y'),
            ]
        );
    }

    protected function saveDataWhenSendByDate(Order $order, array $updatedAt): void
    {
        $timeDifference = $updatedAt['old']->diffInMinutes($updatedAt['new']);

        $timeDifference = round($timeDifference, 2);

        $currentDate = now();
        $this->saveChartEntry(
            ChartTypesEnum::TEMPO_MEDIO_ENVIO,
            [
                'order_id' => $order->id,
                'time' => $timeDifference,
                'day' => $currentDate->format('d'),
                'month' => $currentDate->format('m'),
                'year' => $currentDate->format('Y'),
            ]
        );
    }

    protected function saveDataWhenSend(Order $order): void
    {
        $currentDate = now();
        $this->saveChartEntry(
            ChartTypesEnum::ENVIOS_POR_DIA_MES,
            [
                'order_id' => $order->id,
                'day' => $currentDate->format('d'),
                'month' => $currentDate->format('m'),
                'year' => $currentDate->format('Y'),
            ]
        );

        $this->saveMapEntry($order);
    }

    protected function saveMapEntry(Order $order): void
    {
        $shippingAddress = $order->shipping_address;

        if (isset($shippingAddress['city'])) {
            $this->saveChartEntry(
                ChartTypesEnum::MAPA_ENVIOS_PAIS,
                [
                    'order_id' => $order->id,
                    'date' => now()->format('Y-m-d'),
                    'city' => $shippingAddress['city'],
                ]
            );
        }
    }
}
