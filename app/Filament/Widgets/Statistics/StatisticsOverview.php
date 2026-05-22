<?php

namespace App\Filament\Widgets\Statistics;

use App\Filament\Widgets\Statistics\Concerns\InteractsWithStatisticsFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatisticsOverview extends StatsOverviewWidget
{
    use InteractsWithStatisticsFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $filters = $this->statisticsFilters();
        $stats = $this->statistics()->overview($filters);
        $period = $filters->periodLabel();

        return [
            Stat::make('Заказов', (string) $stats['orders_count'])
                ->description($period)
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Выручка', self::formatMoney($stats['revenue']))
                ->description('Без отменённых')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Средний чек', self::formatMoney($stats['average_check']))
                ->description('На оплаченный заказ')
                ->color('info'),
            Stat::make('Выполнено', (string) $stats['done_count'])
                ->description('Новых: ' . $stats['new_count'])
                ->color($stats['new_count'] > 0 ? 'warning' : 'success'),
            Stat::make('Отменено', (string) $stats['cancelled_count'])
                ->color($stats['cancelled_count'] > 0 ? 'danger' : 'gray'),
        ];
    }

    protected function getColumns(): int
    {
        return 5;
    }
}
