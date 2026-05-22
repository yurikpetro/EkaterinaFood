@props([
    'unit' => 'portion',
    'value' => 1,
    'min' => 1,
    'name' => null,
])

@php
    $unitEnum = \App\Enums\ProductUnit::tryFrom($unit) ?? \App\Enums\ProductUnit::Portion;
    $isKg = $unitEnum === \App\Enums\ProductUnit::Kg;
    $isGram = $unitEnum === \App\Enums\ProductUnit::Gram;
    $displayValue = $isKg ? rtrim(rtrim(number_format($value / 10, 1, '.', ''), '0'), '.') : $value;
    $displayMin = $isKg ? rtrim(rtrim(number_format($min / 10, 1, '.', ''), '0'), '.') : $min;
    $displayStep = $isKg ? '0.1' : ($isGram ? '100' : '1');
    $suffix = $isKg ? 'кг' : ($isGram ? 'г' : $unitEnum->getLabel());
@endphp

<div {{ $attributes->class(['qty-stepper']) }}
     data-amount-stepper
     data-unit="{{ $unitEnum->value }}"
     data-step="{{ $unitEnum->stepAmount() }}"
     data-min-internal="{{ $min }}">
    <div class="qty-stepper__control">
        <button type="button"
                class="qty-stepper__btn qty-stepper__btn--dec"
                data-amount-decrement
                aria-label="Уменьшить">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" d="M5 12h14"/>
            </svg>
        </button>
        <div class="qty-stepper__input-wrap">
            <input type="number"
                   data-amount-display
                   value="{{ $displayValue }}"
                   min="{{ $displayMin }}"
                   step="{{ $displayStep }}"
                   class="qty-stepper__input"
                   inputmode="decimal">
            <input type="hidden"
                   data-amount-value
                   value="{{ $value }}"
                   @if($name) name="{{ $name }}" @endif>
            <span class="qty-stepper__suffix">{{ $suffix }}</span>
        </div>
        <button type="button"
                class="qty-stepper__btn qty-stepper__btn--inc"
                data-amount-increment
                aria-label="Увеличить">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
            </svg>
        </button>
    </div>
</div>
