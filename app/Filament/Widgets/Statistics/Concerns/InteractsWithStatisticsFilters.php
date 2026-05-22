<?php

namespace App\Filament\Widgets\Statistics\Concerns;

use App\Services\OrderStatisticsService;
use App\Support\Statistics\OrderStatisticsFilters;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

trait InteractsWithStatisticsFilters
{
    use InteractsWithPageFilters;

    protected function statisticsFilters(): OrderStatisticsFilters
    {
        return OrderStatisticsFilters::from($this->pageFilters);
    }

    protected function statistics(): OrderStatisticsService
    {
        return app(OrderStatisticsService::class);
    }

    protected static function formatMoney(int $amount): string
    {
        return number_format($amount, 0, ',', ' ') . ' ₽';
    }
}
