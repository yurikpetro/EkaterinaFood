<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Statistics\OrderStatisticsFilters;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderStatisticsService
{
    /**
     * @return Builder<\App\Models\Order>
     */
    public function ordersQuery(OrderStatisticsFilters $filters): Builder
    {
        return $filters->applyToOrders(Order::query());
    }

    /**
     * @return array{
     *     orders_count: int,
     *     revenue: int,
     *     average_check: int,
     *     cancelled_count: int,
     *     new_count: int,
     *     done_count: int,
     * }
     */
    public function overview(OrderStatisticsFilters $filters): array
    {
        $base = $this->ordersQuery($filters);

        $ordersCount = (clone $base)->count();

        $revenueQuery = (clone $base)->where('status', '!=', OrderStatus::Cancelled);
        $revenue = (int) (clone $revenueQuery)->sum('total');
        $revenueOrders = (clone $revenueQuery)->count();

        return [
            'orders_count' => $ordersCount,
            'revenue' => $revenue,
            'average_check' => $revenueOrders > 0 ? (int) round($revenue / $revenueOrders) : 0,
            'cancelled_count' => (clone $base)->where('status', OrderStatus::Cancelled)->count(),
            'new_count' => (clone $base)->where('status', OrderStatus::New)->count(),
            'done_count' => (clone $base)->where('status', OrderStatus::Done)->count(),
        ];
    }

    /**
     * @return array{labels: list<string>, revenue: list<int>, orders: list<int>}
     */
    public function trendByDay(OrderStatisticsFilters $filters): array
    {
        $days = CarbonPeriod::create($filters->from->copy()->startOfDay(), $filters->to->copy()->startOfDay());
        $labels = [];
        $revenueByDate = [];
        $ordersByDate = [];

        foreach ($days as $day) {
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d.m');
            $revenueByDate[$key] = 0;
            $ordersByDate[$key] = 0;
        }

        $rows = $this->ordersQuery($filters)
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('COALESCE(SUM(CASE WHEN status != ? THEN total ELSE 0 END), 0) as revenue', [OrderStatus::Cancelled->value])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        foreach ($rows as $row) {
            $key = Carbon::parse($row->day)->format('Y-m-d');
            if (! array_key_exists($key, $revenueByDate)) {
                continue;
            }
            $revenueByDate[$key] = (int) $row->revenue;
            $ordersByDate[$key] = (int) $row->orders_count;
        }

        return [
            'labels' => $labels,
            'revenue' => array_values($revenueByDate),
            'orders' => array_values($ordersByDate),
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>, colors: list<string>}
     */
    public function byStatus(OrderStatisticsFilters $filters): array
    {
        $labels = [];
        $values = [];
        $colors = [];

        $counts = $this->ordersQuery($filters)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        foreach (OrderStatus::cases() as $status) {
            $count = (int) ($counts[$status->value] ?? 0);
            if ($count === 0) {
                continue;
            }
            $labels[] = $status->getLabel();
            $values[] = $count;
            $colors[] = $this->chartColor($status->getColor());
        }

        return compact('labels', 'values', 'colors');
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    public function byDeliveryType(OrderStatisticsFilters $filters): array
    {
        $labels = [];
        $values = [];

        $counts = $this->ordersQuery($filters)
            ->selectRaw('delivery_type, COUNT(*) as count')
            ->groupBy('delivery_type')
            ->pluck('count', 'delivery_type');

        foreach (DeliveryType::cases() as $type) {
            $count = (int) ($counts[$type->value] ?? 0);
            if ($count === 0) {
                continue;
            }
            $labels[] = $type->getLabel();
            $values[] = $count;
        }

        return compact('labels', 'values');
    }

    /**
     * @return Builder<\App\Models\OrderItem>
     */
    public function topProductsQuery(OrderStatisticsFilters $filters): Builder
    {
        return OrderItem::query()
            ->select([
                DB::raw('MIN(order_items.id) as id'),
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$filters->from, $filters->to])
            ->where('orders.status', '!=', OrderStatus::Cancelled->value)
            ->when($filters->hasStatusFilter(), fn (Builder $q): Builder => $q->whereIn('orders.status', $filters->statusValues()))
            ->when($filters->deliveryType, fn (Builder $q): Builder => $q->where('orders.delivery_type', $filters->deliveryType))
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity');
    }

    /**
     * @return Collection<int, object{id: int, product_name: string, total_quantity: int, total_revenue: int}>
     */
    public function topProducts(OrderStatisticsFilters $filters, int $limit = 15): Collection
    {
        return $this->topProductsQuery($filters)->limit($limit)->get();
    }

    private function chartColor(string $filamentColor): string
    {
        return match ($filamentColor) {
            'warning' => '#f59e0b',
            'info' => '#0ea5e9',
            'primary' => '#d97706',
            'success' => '#22c55e',
            'danger' => '#ef4444',
            default => '#a8a29e',
        };
    }
}
