<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Order::query()->whereDate('created_at', today());
        $newToday = (clone $today)->where('status', OrderStatus::New)->count();

        return [
            Stat::make('Заказов сегодня', (clone $today)->count())
                ->description('Всего за сегодня')
                ->color('primary'),
            Stat::make('Новых', $newToday)
                ->description('Ждут подтверждения')
                ->color($newToday > 0 ? 'warning' : 'success'),
            Stat::make('В работе', Order::query()->whereIn('status', [OrderStatus::Confirmed, OrderStatus::InProgress])->count())
                ->description('Подтверждённые и готовятся')
                ->color('info'),
        ];
    }
}
