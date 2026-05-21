@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-extrabold text-warm-brown mb-2">Оформление заказа</h1>
        <p class="text-lg text-warm-brown/70 mb-8">Итого: <strong class="text-terracotta">{{ number_format($total, 0, ',', ' ') }} ₽</strong></p>

        <div class="bg-white rounded-2xl border border-cream-dark p-5 mb-8">
            <h2 class="font-bold text-lg mb-3">Ваш заказ</h2>
            <ul class="space-y-2 text-base">
                @foreach($items as $item)
                    <li class="flex justify-between gap-2">
                        <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                        <span class="font-semibold">{{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST" class="bg-white rounded-2xl border border-cream-dark p-6 sm:p-8 space-y-5">
            @csrf

            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <label>Не заполняйте</label>
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label for="customer_name" class="block font-bold mb-1">Ваше имя *</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                       class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none @error('customer_name') border-red-400 @enderror">
                @error('customer_name')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="customer_phone" class="block font-bold mb-1">Телефон *</label>
                <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                       placeholder="+7 900 000-00-00"
                       class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none @error('customer_phone') border-red-400 @enderror">
                @error('customer_phone')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="block font-bold mb-2">Способ получения *</legend>
                <div class="flex flex-col sm:flex-row gap-3">
                    <label class="flex items-center gap-2 bg-cream px-4 py-3 rounded-xl cursor-pointer flex-1">
                        <input type="radio" name="delivery_type" value="delivery" {{ old('delivery_type', 'delivery') === 'delivery' ? 'checked' : '' }}
                               class="w-5 h-5 text-terracotta" onchange="document.getElementById('address-field').classList.remove('hidden')">
                        <span class="font-semibold">Доставка</span>
                    </label>
                    <label class="flex items-center gap-2 bg-cream px-4 py-3 rounded-xl cursor-pointer flex-1">
                        <input type="radio" name="delivery_type" value="pickup" {{ old('delivery_type') === 'pickup' ? 'checked' : '' }}
                               class="w-5 h-5 text-terracotta" onchange="document.getElementById('address-field').classList.add('hidden')">
                        <span class="font-semibold">Самовывоз</span>
                    </label>
                </div>
                <p class="text-sm text-warm-brown/60 mt-2">Самовывоз: {{ $pickupAddress }}</p>
            </fieldset>

            <div id="address-field" class="{{ old('delivery_type') === 'pickup' ? 'hidden' : '' }}">
                <label for="address" class="block font-bold mb-1">Адрес доставки</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}"
                       class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none">
                @error('address')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="desired_date" class="block font-bold mb-1">Желаемая дата</label>
                    <input type="date" name="desired_date" id="desired_date" value="{{ old('desired_date') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none">
                </div>
                <div>
                    <label for="desired_time" class="block font-bold mb-1">Время</label>
                    <input type="text" name="desired_time" id="desired_time" value="{{ old('desired_time') }}"
                           placeholder="например, к 14:00"
                           class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none">
                </div>
            </div>

            <div>
                <label for="comment" class="block font-bold mb-1">Комментарий</label>
                <textarea name="comment" id="comment" rows="3"
                          class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none">{{ old('comment') }}</textarea>
            </div>

            <p class="text-base text-warm-brown/70">Оплата при получении или по договорённости. Мы перезвоним для подтверждения.</p>

            <button type="submit"
                    class="w-full bg-terracotta text-white text-xl font-bold py-4 rounded-2xl hover:bg-terracotta-dark transition">
                Отправить заказ
            </button>
        </form>
    </div>
@endsection
