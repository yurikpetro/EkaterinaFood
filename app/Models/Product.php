<?php

namespace App\Models;

use App\Enums\ProductUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'unit',
        'min_quantity',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'unit' => ProductUnit::class,
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function formattedPrice(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }

    public function formattedPricePerUnit(): string
    {
        return $this->formattedPrice() . ' / ' . $this->unit->priceUnitLabel();
    }

    public function formatAmount(int $quantity): string
    {
        return $this->unit->formatAmount($quantity);
    }

    public function calculateSubtotal(int $quantity): int
    {
        return $this->unit->calculateSubtotal($this->price, $quantity);
    }

    public function normalizeAmount(int $quantity): int
    {
        return max($quantity, $this->min_quantity);
    }
}
