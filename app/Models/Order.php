<?php

namespace App\Models;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'number',
        'customer_name',
        'customer_phone',
        'delivery_type',
        'address',
        'desired_date',
        'desired_time',
        'comment',
        'status',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'desired_date' => 'date',
            'status' => OrderStatus::class,
            'delivery_type' => DeliveryType::class,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedTotal(): string
    {
        return number_format($this->total, 0, ',', ' ') . ' ₽';
    }
}
