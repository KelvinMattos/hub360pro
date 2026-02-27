<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Vendas por Dia (Últimos 30 dias)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Nota: Se Trend não estiver instalado, usaremos dados manuais.
        // Vou assumir que Trend está disponível ou usarei fallback.
        try {
            $data = Trend::model(Order::class)
                ->between(
                    start: now()->subDays(30),
                    end: now(),
                )
                ->perDay()
                ->count();
        } catch (\Exception $e) {
            // Fallback manual se Trend não existir
            $data = collect(range(30, 0))->map(function ($days) {
                $date = now()->subDays($days);
                return (object)[
                    'date' => $date->format('Y-m-d'),
                    'aggregate' => Order::whereDate('date_created', $date)->count(),
                ];
            });
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pedidos',
                    'data' => $data->map(fn ($value) => $value->aggregate),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->map(fn ($value) => \Carbon\Carbon::parse($value->date)->format('d/m')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}