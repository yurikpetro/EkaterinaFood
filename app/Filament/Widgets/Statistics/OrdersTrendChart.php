<?php

namespace App\Filament\Widgets\Statistics;

use App\Filament\Widgets\Statistics\Concerns\InteractsWithStatisticsFilters;
use Filament\Widgets\ChartWidget;

class OrdersTrendChart extends ChartWidget
{
    use InteractsWithStatisticsFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Динамика заказов';

    protected ?string $description = 'Выручка и количество заказов по дням';

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $trend = $this->statistics()->trendByDay($this->statisticsFilters());

        return [
            'datasets' => [
                [
                    'label' => 'Выручка, ₽',
                    'data' => $trend['revenue'],
                    'borderColor' => '#d97706',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.1)',
                    'yAxisID' => 'y',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Заказов',
                    'data' => $trend['orders'],
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'yAxisID' => 'y1',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $trend['labels'],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getOptions(): ?array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}
