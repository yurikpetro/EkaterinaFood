@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <div class="page-narrow py-10 pb-28 sm:pb-10" id="checkout-page">
        <x-order-steps current="checkout" />

        <h1 class="text-3xl font-extrabold text-warm-brown mb-2">Оформление заказа</h1>
        <p class="text-lg text-muted mb-8 hidden sm:block">
            Итого: <strong class="text-terracotta">{{ number_format($total, 0, ',', ' ') }} ₽</strong>
        </p>

        <div class="bg-white rounded-2xl border border-cream-dark p-5 mb-8">
            <h2 class="font-bold text-lg mb-3">Ваш заказ</h2>
            <ul class="space-y-2 text-base">
                @foreach($items as $item)
                    <li class="flex justify-between gap-2">
                        <span>{{ $item['product']->name }} — {{ $item['quantity_label'] ?? $item['product']->formatAmount($item['quantity']) }}</span>
                        <span class="font-semibold">{{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST"
              id="checkout-form"
              class="bg-white rounded-2xl border border-cream-dark p-6 sm:p-8 space-y-5">
            @csrf

            <div class="hidden" aria-hidden="true">
                <label>Не заполняйте</label>
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label for="customer_name" class="block font-bold mb-1">Ваше имя *</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                       class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta/30 @error('customer_name') border-red-400 @enderror">
                @error('customer_name')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="customer_phone" class="block font-bold mb-1">Телефон *</label>
                <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                       placeholder="+7 900 000-00-00"
                       class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta/30 @error('customer_phone') border-red-400 @enderror">
                @error('customer_phone')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="block font-bold mb-2">Способ получения *</legend>
                <div class="flex flex-col sm:flex-row gap-3" id="delivery-type-options">
                    <label class="delivery-option">
                        <input type="radio" name="delivery_type" value="delivery"
                               {{ old('delivery_type', 'delivery') === 'delivery' ? 'checked' : '' }}
                               class="w-5 h-5 text-terracotta" data-delivery-radio>
                        <span class="font-semibold">Доставка</span>
                    </label>
                    <label class="delivery-option">
                        <input type="radio" name="delivery_type" value="pickup"
                               {{ old('delivery_type') === 'pickup' ? 'checked' : '' }}
                               class="w-5 h-5 text-terracotta" data-delivery-radio>
                        <span class="font-semibold">Самовывоз</span>
                    </label>
                </div>
                <p class="text-sm text-muted mt-2">Самовывоз: {{ $pickupAddress }}</p>
            </fieldset>

            <div id="address-field">
                <label for="address" class="block font-bold mb-1">Адрес доставки</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}"
                       class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta/30">
                @error('address')<p class="text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="desired_date" class="block font-bold mb-1">Желаемая дата</label>
                    <input type="date" name="desired_date" id="desired_date" value="{{ old('desired_date') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta/30">
                </div>
                <div>
                    <label for="desired_time" class="block font-bold mb-1">Время</label>
                    <div class="flex flex-wrap gap-2 mb-2" id="time-presets">
                        <button type="button" class="time-preset" data-time-value="к 12:00">к 12:00</button>
                        <button type="button" class="time-preset" data-time-value="к 14:00">к 14:00</button>
                        <button type="button" class="time-preset" data-time-value="к 18:00">к 18:00</button>
                    </div>
                    <input type="text" name="desired_time" id="desired_time" value="{{ old('desired_time') }}"
                           placeholder="или укажите своё"
                           class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta/30">
                </div>
            </div>

            <div>
                <label for="comment" class="block font-bold mb-1">Комментарий</label>
                <textarea name="comment" id="comment" rows="3"
                          class="w-full text-lg border-2 border-cream-dark rounded-xl px-4 py-3 focus:border-terracotta focus:outline-none focus-visible:ring-2 focus-visible:ring-terracotta/30">{{ old('comment') }}</textarea>
            </div>

            <div class="bg-cream rounded-xl p-4 text-base space-y-2">
                <p>Оплата при получении или по договорённости.</p>
                <p class="font-semibold text-olive">Мы перезвоним для подтверждения заказа.</p>
                @if(!empty($checkoutPhone))
                    <p>
                        Вопросы:
                        <a href="tel:{{ preg_replace('/\D+/', '', $checkoutPhone) }}" class="text-terracotta font-bold hover:underline">
                            {{ $checkoutPhone }}
                        </a>
                    </p>
                @endif
            </div>

            <button type="submit" id="checkout-submit"
                    class="btn-press w-full bg-terracotta text-white text-xl font-bold py-4 rounded-2xl hover:bg-terracotta-dark transition hidden sm:block">
                Отправить заказ
            </button>
        </form>
    </div>

    <div class="checkout-sticky-bar" aria-hidden="false">
        <div class="flex items-center justify-between gap-4 max-w-3xl mx-auto">
            <div>
                <p class="text-xs font-semibold text-olive uppercase">Итого</p>
                <p class="text-2xl font-extrabold text-warm-brown">{{ number_format($total, 0, ',', ' ') }} ₽</p>
            </div>
            <button type="submit" form="checkout-form" id="checkout-submit-mobile"
                    class="btn-press shrink-0 bg-terracotta text-white text-lg font-bold px-6 py-3 rounded-2xl hover:bg-terracotta-dark">
                Отправить
            </button>
        </div>
    </div>
@endsection
