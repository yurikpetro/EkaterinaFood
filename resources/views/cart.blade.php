@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <div id="cart-page"
         class="page-narrow py-10"
         data-update-url="{{ route('cart.update') }}"
         data-csrf="{{ csrf_token() }}">

        <x-order-steps current="cart" />

        <h1 class="text-3xl font-extrabold text-warm-brown mb-8">Корзина</h1>

        <div id="cart-empty-state" class="@if(!$items->isEmpty()) hidden @endif">
            <div class="bg-white rounded-2xl p-8 text-center border border-cream-dark">
                <p class="text-xl mb-6">Корзина пуста</p>
                <a href="{{ route('menu') }}"
                   class="btn-press inline-block bg-terracotta text-white font-bold text-lg px-8 py-3 rounded-xl hover:bg-terracotta-dark">
                    Перейти в меню
                </a>
            </div>
        </div>

        <div id="cart-filled-state" class="@if($items->isEmpty()) hidden @endif">
            <div id="cart-items" class="space-y-4 mb-4">
                @foreach($items as $item)
                    @php $product = $item['product']; @endphp
                    <div class="relative bg-white rounded-2xl border border-cream-dark p-5"
                         data-cart-item
                         data-product-id="{{ $product->id }}"
                         data-unit-price="{{ $product->price }}"
                         data-min-quantity="{{ $product->min_quantity }}"
                         data-unit-type="{{ $product->unit->value }}">
                        <div class="flex justify-between items-start gap-4 mb-4 pr-2">
                            <div>
                                <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                                <p class="text-terracotta font-semibold">{{ $product->formattedPricePerUnit() }}</p>
                            </div>
                            <p class="text-xl font-extrabold whitespace-nowrap" data-cart-subtotal>
                                {{ number_format($item['subtotal'], 0, ',', ' ') }} ₽
                            </p>
                        </div>
                        <p class="text-sm text-amber-800 bg-amber-50 rounded-lg px-3 py-2 mb-3 hidden"
                           data-cart-min-hint
                           role="status">
                            Ниже минимума ({{ $product->formatAmount($product->min_quantity) }}) — позиция не войдёт в заказ. Уменьшите до 0, чтобы удалить.
                        </p>
                        <div class="flex items-end justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-sm shrink-0">{{ $product->unit->amountInputLabel() }}</span>
                                <x-amount-stepper
                                    data-cart-quantity
                                    data-cart-amount
                                    :unit="$product->unit->value"
                                    :value="$item['quantity']"
                                    :min="0"
                                />
                            </div>
                            <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="cart-item-remove btn-press flex items-center gap-1.5 px-3 h-11 rounded-xl cursor-pointer text-red-600 hover:bg-red-50 hover:text-red-700"
                                        aria-label="Удалить {{ $product->name }} из корзины">
                                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14M10 11v6M14 11v6"/>
                                    </svg>
                                    <span class="text-sm font-semibold hidden sm:inline">Удалить</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="mb-6">
                <a href="{{ route('menu') }}" class="text-olive font-semibold hover:text-terracotta transition underline underline-offset-4">
                    ← Продолжить покупки
                </a>
            </p>

            <div class="bg-olive/10 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-olive uppercase">Итого</p>
                    <p class="text-4xl font-extrabold text-warm-brown" data-cart-total>{{ number_format($total, 0, ',', ' ') }} ₽</p>
                </div>
                <a href="{{ route('checkout.show') }}"
                   class="btn-press bg-terracotta text-white text-xl font-bold px-8 py-4 rounded-2xl hover:bg-terracotta-dark text-center">
                    Оформить заказ
                </a>
            </div>
        </div>

        <p id="cart-sync-error" class="hidden mt-4 text-red-700 font-semibold text-center" role="alert"></p>
    </div>
@endsection
