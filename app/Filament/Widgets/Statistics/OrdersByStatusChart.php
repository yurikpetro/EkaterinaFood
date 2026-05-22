<?php

namespace App\Filament\Widgets\Statistics;

use App\Filament\Widgets\Statistics\Concerns\InteractsWithStatisticsFilters;
use Filament\Widgets\ChartWidget;

class OrdersByStatusChart extends ChartWidget
{
    use InteractsWithStatisticsFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected ?string $heading = 'По статусам';

    protected int | string | array $columnSpan = 1;

    protected ?string $maxHeight = '260px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $data = $this->statistics()->byStatus($this->statisticsFilters());

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
                    'backgroundColor' => $data['colors'],
                ],
            ],
            'labels' => $data['labels'],
        ];
    }
}
