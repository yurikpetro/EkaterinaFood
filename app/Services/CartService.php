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

                $quantity = max($quantity, $product->min_quantity);

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
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
        $cart[$productId] = max($current + $quantity, $product->min_quantity);

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
        return $this->items()->sum('quantity');
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
                'unit' => $item['product']->unit,
                'price' => $item['product']->price,
                'subtotal' => $item['subtotal'],
            ])->values()->all(),
        ];
    }
}
