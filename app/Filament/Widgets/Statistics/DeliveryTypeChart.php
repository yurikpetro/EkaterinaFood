<?php

namespace App\Filament\Widgets\Statistics;

use App\Filament\Widgets\Statistics\Concerns\InteractsWithStatisticsFilters;
use Filament\Widgets\ChartWidget;

class DeliveryTypeChart extends ChartWidget
{
    use InteractsWithStatisticsFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Доставка и самовывоз';

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = $this->statistics()->byDeliveryType($this->statisticsFilters());

        if ($data['values'] === []) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e7e5e4'],
                    ],
                ],
                'labels' => ['Нет данных'],
            ];
        }

        return [
            'datasets' => [
                [
                    'data' => $data['values'],
                    'backgroundColor' => ['#d97706', '#0ea5e9'],
                ],
            ],
            'labels' => $data['labels'],
        ];
    }
}
