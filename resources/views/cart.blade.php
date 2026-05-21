@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <div id="cart-page"
         class="max-w-3xl mx-auto px-4 py-10"
         data-update-url="{{ route('cart.update') }}"
         data-csrf="{{ csrf_token() }}">

        <h1 class="text-3xl font-extrabold text-warm-brown mb-8">Корзина</h1>

        @if($items->isEmpty())
            <div class="bg-white rounded-2xl p-8 text-center border border-cream-dark">
                <p class="text-xl mb-6">Корзина пуста</p>
                <a href="{{ route('menu') }}" class="inline-block bg-terracotta text-white font-bold text-lg px-8 py-3 rounded-xl hover:bg-terracotta-dark transition">
                    Перейти в меню
                </a>
            </div>
        @else
            <div id="cart-items" class="space-y-4 mb-8">
                @foreach($items as $item)
                    @php $product = $item['product']; @endphp
                    <div class="relative bg-white rounded-2xl border border-cream-dark p-5"
                         data-cart-item
                         data-product-id="{{ $product->id }}"
                         data-unit-price="{{ $product->price }}"
                         data-min-quantity="{{ $product->min_quantity }}">
                        <div class="flex justify-between items-start gap-4 mb-4 pr-2">
                            <div>
                                <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                                <p class="text-terracotta font-semibold">{{ $product->formattedPrice() }} / {{ $product->unit }}</p>
                            </div>
                            <p class="text-xl font-extrabold whitespace-nowrap" data-cart-subtotal>
                                {{ number_format($item['subtotal'], 0, ',', ' ') }} ₽
                            </p>
                        </div>
                        <div class="flex items-end justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-sm shrink-0">Кол-во</span>
                                <x-quantity-stepper
                                    data-cart-quantity
                                    :value="$item['quantity']"
                                    :min="0"
                                />
                            </div>
                            <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="cart-item-remove flex items-center justify-center w-11 h-11 rounded-xl cursor-pointer text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors"
                                        aria-label="Удалить из корзины"
                                        title="Удалить">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-olive/10 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-olive uppercase">Итого</p>
                    <p class="text-4xl font-extrabold text-warm-brown" data-cart-total>{{ number_format($total, 0, ',', ' ') }} ₽</p>
                </div>
                <a href="{{ route('checkout.show') }}"
                   class="bg-terracotta text-white text-xl font-bold px-8 py-4 rounded-2xl hover:bg-terracotta-dark transition text-center">
                    Оформить заказ
                </a>
            </div>
        @endif
    </div>
@endsection
