<?php

namespace App\Support\Statistics;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderStatisticsFilters
{
    /**
     * @param  list<OrderStatus>  $statuses
     */
    public function __construct(
        public Carbon $from,
        public Carbon $to,
        public array $statuses = [],
        public ?DeliveryType $deliveryType = null,
    ) {}

    /**
     * @param  array<string, mixed>|null  $pageFilters
     */
    public static function from(?array $pageFilters): self
    {
        $filters = $pageFilters ?? [];
        $period = $filters['period'] ?? '30days';

        [$from, $to] = match ($period) {
            'today' => [today()->startOfDay(), today()->endOfDay()],
            '7days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            '30days' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'prev_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'custom' => [
                Carbon::parse($filters['from'] ?? now()->subDays(29))->startOfDay(),
                Carbon::parse($filters['to'] ?? now())->endOfDay(),
            ],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };

        $deliveryType = filled($filters['delivery_type'] ?? null)
            ? DeliveryType::from($filters['delivery_type'])
            : null;

        return new self($from, $to, self::parseStatuses($filters['status'] ?? null), $deliveryType);
    }

    /**
     * @return list<OrderStatus>
     */
    private static function parseStatuses(mixed $value): array
    {
        if (blank($value)) {
            return [];
        }

        $items = is_array($value) ? $value : [$value];

        return array_values(array_map(
            fn (mixed $status): OrderStatus => $status instanceof OrderStatus
                ? $status
                : OrderStatus::from((string) $status),
            $items,
        ));
    }

    public function hasStatusFilter(): bool
    {
        return $this->statuses !== [];
    }

    /**
     * @return list<string>
     */
    public function statusValues(): array
    {
        return array_map(fn (OrderStatus $status): string => $status->value, $this->statuses);
    }

    public function periodLabel(): string
    {
        if ($this->from->isSameDay($this->to)) {
            return $this->from->format('d.m.Y');
        }

        return $this->from->format('d.m.Y') . ' — ' . $this->to->format('d.m.Y');
    }

    /**
     * @param  Builder<\App\Models\Order>  $query
     * @return Builder<\App\Models\Order>
     */
    public function applyToOrders(Builder $query): Builder
    {
        return $query
            ->whereBetween('created_at', [$this->from, $this->to])
            ->when($this->hasStatusFilter(), fn (Builder $q): Builder => $q->whereIn('status', $this->statusValues()))
            ->when($this->deliveryType, fn (Builder $q): Builder => $q->where('delivery_type', $this->deliveryType));
    }

}
