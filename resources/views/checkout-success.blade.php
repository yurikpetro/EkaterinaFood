@extends('layouts.app')

@section('title', 'Заказ принят')

@section('content')
    <div class="page-narrow py-16 text-center">
        <x-order-steps current="success" />

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-olive/15 text-olive mb-4" aria-hidden="true">
            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-warm-brown mb-4">Спасибо за заказ!</h1>
        <p class="text-xl mb-2">Номер заказа: <strong class="text-terracotta">{{ $order->number }}</strong></p>
        <p class="text-lg text-muted mb-8">
            Сумма: {{ $order->formattedTotal() }}. Мы свяжемся с вами для подтверждения.
        </p>

        <div class="bg-white rounded-2xl border border-cream-dark p-6 text-left mb-8 text-base">
            <h2 class="font-bold text-lg mb-3">Состав заказа</h2>
            <ul class="space-y-2">
                @foreach($order->items as $item)
                    <li>{{ $item->product_name }} — {{ $item->quantity }} {{ $item->unit }} — {{ number_format($item->subtotal, 0, ',', ' ') }} ₽</li>
                @endforeach
            </ul>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                   class="btn-press bg-[#25D366] text-white text-lg font-bold px-8 py-4 rounded-2xl hover:opacity-90">
                    Написать в WhatsApp
                </a>
            @endif
            @if($phone)
                <a href="tel:{{ preg_replace('/\D+/', '', $phone) }}"
                   class="btn-press bg-olive text-white text-lg font-bold px-8 py-4 rounded-2xl hover:bg-olive-dark">
                    Позвонить
                </a>
            @endif
            <a href="{{ route('menu') }}"
               class="btn-press bg-white border-2 border-terracotta text-terracotta text-lg font-bold px-8 py-4 rounded-2xl hover:bg-terracotta/5">
                В меню
            </a>
        </div>
    </div>
@endsection
