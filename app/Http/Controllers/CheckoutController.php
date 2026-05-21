<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryType;
use App\Services\CartService;
use App\Services\OrderService;
use App\Support\Settings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orders,
    ) {}

    public function show()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('menu')->with('error', 'Корзина пуста — выберите блюда из меню');
        }

        return view('checkout', [
            'items' => $this->cart->items(),
            'total' => $this->cart->total(),
            'cartCount' => $this->cart->count(),
            'pickupAddress' => Settings::get(Settings::PICKUP_ADDRESS),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            abort(422);
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s\+\-\(\)]{10,}$/'],
            'delivery_type' => ['required', Rule::enum(DeliveryType::class)],
            'address' => ['required_if:delivery_type,delivery', 'nullable', 'string', 'max:500'],
            'desired_date' => ['nullable', 'date', 'after_or_equal:today'],
            'desired_time' => ['nullable', 'string', 'max:50'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'max:0'],
        ], [
            'customer_name.required' => 'Укажите ваше имя',
            'customer_phone.required' => 'Укажите телефон для связи',
            'address.required_if' => 'Укажите адрес доставки',
        ]);

        try {
            $order = $this->orders->createFromCart($validated);
        } catch (\RuntimeException $e) {
            return redirect()->route('menu')->with('error', $e->getMessage());
        }

        $whatsappUrl = $this->orders->whatsAppUrl(
            Settings::get(Settings::WHATSAPP),
            $this->orders->whatsAppText($order),
        );

        return redirect()
            ->route('checkout.success', $order)
            ->with('whatsapp_url', $whatsappUrl);
    }

    public function success(\App\Models\Order $order)
    {
        return view('checkout-success', [
            'order' => $order->load('items'),
            'whatsappUrl' => session('whatsapp_url'),
            'phone' => Settings::get(Settings::PHONE),
        ]);
    }
}
