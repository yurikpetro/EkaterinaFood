@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-extrabold text-warm-brown mb-8">Корзина</h1>

        @if($items->isEmpty())
            <div class="bg-white rounded-2xl p-8 text-center border border-cream-dark">
                <p class="text-xl mb-6">Корзина пуста</p>
                <a href="{{ route('menu') }}" class="inline-block bg-terracotta text-white font-bold text-lg px-8 py-3 rounded-xl hover:bg-terracotta-dark transition">
                    Перейти в меню
                </a>
            </div>
        @else
            <div class="space-y-4 mb-8">
                @foreach($items as $item)
                    @php $product = $item['product']; @endphp
                    <div class="bg-white rounded-2xl border border-cream-dark p-5">
                        <div class="flex justify-between items-start gap-4 mb-4">
                            <div>
                                <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                                <p class="text-terracotta font-semibold">{{ $product->formattedPrice() }} / {{ $product->unit }}</p>
                            </div>
                            <p class="text-xl font-extrabold whitespace-nowrap">
                                {{ number_format($item['subtotal'], 0, ',', ' ') }} ₽
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <label class="font-semibold text-sm">Кол-во</label>
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0"
                                       class="w-20 text-lg border-2 border-cream-dark rounded-lg px-2 py-1">
                                <button type="submit" class="text-olive font-semibold hover:underline">Обновить</button>
                            </form>
                            <form action="{{ route('cart.remove', $product->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-semibold hover:underline">Удалить</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-olive/10 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-olive uppercase">Итого</p>
                    <p class="text-4xl font-extrabold text-warm-brown">{{ number_format($total, 0, ',', ' ') }} ₽</p>
                </div>
                <a href="{{ route('checkout.show') }}"
                   class="bg-terracotta text-white text-xl font-bold px-8 py-4 rounded-2xl hover:bg-terracotta-dark transition text-center">
                    Оформить заказ
                </a>
            </div>
        @endif
    </div>
@endsection
