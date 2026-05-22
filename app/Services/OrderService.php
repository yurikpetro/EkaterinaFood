<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\ProductUnit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function createFromCart(array $data): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new \RuntimeException('Корзина пуста');
        }

        return DB::transaction(function () use ($data, $items) {
            $total = $items->sum('subtotal');

            $order = Order::query()->create([
                'number' => $this->generateNumber(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'delivery_type' => $data['delivery_type'],
                'address' => $data['address'] ?? null,
                'desired_date' => $data['desired_date'] ?? null,
                'desired_time' => $data['desired_time'] ?? null,
                'comment' => $data['comment'] ?? null,
                'status' => OrderStatus::New,
                'total' => $total,
            ]);

            foreach ($items as $item) {
                /** @var Product $product */
                $product = $item['product'];

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'unit' => $product->unit->getLabel(),
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $this->cart->clear();

            return $order->load('items');
        });
    }

    public function generateNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "EK-{$date}-";

        $last = Order::query()
            ->where('number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('number');

        $sequence = 1;

        if ($last) {
            $sequence = (int) substr($last, -3) + 1;
        }

        return $prefix . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    public function whatsAppText(Order $order): string
    {
        $lines = [
            "Здравствуйте! Мой заказ {$order->number}",
            "Имя: {$order->customer_name}",
            "Телефон: {$order->customer_phone}",
            '',
            'Состав заказа:',
        ];

        foreach ($order->items as $item) {
            $unit = ProductUnit::fromLegacy($item->unit);
            $amount = $unit->formatAmount($item->quantity);
            $price = number_format($item->unit_price, 0, ',', ' ');
            $subtotal = number_format($item->subtotal, 0, ',', ' ');
            $lines[] = "• {$item->product_name} — {$amount} × {$price} ₽/{$unit->priceUnitLabel()} = {$subtotal} ₽";
        }

        $lines[] = '';
        $lines[] = 'Итого: ' . $order->formattedTotal();
        $lines[] = 'Способ: ' . $order->delivery_type->getLabel();

        if ($order->address) {
            $lines[] = 'Адрес: ' . $order->address;
        }

        if ($order->desired_date) {
            $date = $order->desired_date->format('d.m.Y');
            $time = $order->desired_time ? " в {$order->desired_time}" : '';
            $lines[] = "Дата: {$date}{$time}";
        }

        if ($order->comment) {
            $lines[] = 'Комментарий: ' . $order->comment;
        }

        return implode("\n", $lines);
    }

    public function adminWhatsAppText(Order $order): string
    {
        return $this->whatsAppText($order);
    }

    public function whatsAppUrl(string $phone, string $text): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '8')) {
            $digits = '7' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($text);
    }
}
