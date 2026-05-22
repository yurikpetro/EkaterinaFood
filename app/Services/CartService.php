<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function items(): Collection
    {
        $cart = session(self::SESSION_KEY, []);

        if ($cart === []) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function (int $quantity, int $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                $quantity = $product->normalizeAmount($quantity);

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'quantity_label' => $product->formatAmount($quantity),
                    'subtotal' => $product->calculateSubtotal($quantity),
                ];
            })
            ->filter()
            ->values();
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($productId);

        $cart = session(self::SESSION_KEY, []);
        $current = $cart[$productId] ?? 0;
        $cart[$productId] = $product->normalizeAmount($current + $quantity);

        session([self::SESSION_KEY => $cart]);
    }

    public function update(int $productId, int $quantity): void
    {
        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($productId);

        $cart = session(self::SESSION_KEY, []);

        if ($quantity < $product->min_quantity) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function remove(int $productId): void
    {
        $cart = session(self::SESSION_KEY, []);
        unset($cart[$productId]);
        session([self::SESSION_KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return count(session(self::SESSION_KEY, []));
    }

    public function total(): int
    {
        return $this->items()->sum('subtotal');
    }

    public function isEmpty(): bool
    {
        return $this->items()->isEmpty();
    }

    /**
     * @return array<int, int>
     */
    public function quantities(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function toJson(): array
    {
        return [
            'total' => $this->total(),
            'cartCount' => $this->count(),
            'items' => $this->items()->map(fn (array $item) => [
                'product_id' => $item['product']->id,
                'name' => $item['product']->name,
                'quantity' => $item['quantity'],
                'quantity_label' => $item['quantity_label'],
                'unit' => $item['product']->unit->getLabel(),
                'unit_type' => $item['product']->unit->value,
                'price' => $item['product']->price,
                'price_label' => $item['product']->formattedPricePerUnit(),
                'subtotal' => $item['subtotal'],
            ])->values()->all(),
        ];
    }
}
