@props([
    'name' => null,
    'value' => 1,
    'min' => 0,
    'max' => null,
])

<div class="qty-stepper" data-qty-stepper>
    <div class="qty-stepper__control">
        <button type="button"
                class="qty-stepper__btn qty-stepper__btn--dec"
                data-qty-decrement
                aria-label="Уменьшить">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" d="M5 12h14"/>
            </svg>
        </button>
        <input type="number"
               data-qty-input
               value="{{ $value }}"
               min="{{ $min }}"
               @if($max !== null) max="{{ $max }}" @endif
               @if($name) name="{{ $name }}" @endif
               {{ $attributes->class('qty-stepper__input') }}
               inputmode="numeric">
        <button type="button"
                class="qty-stepper__btn qty-stepper__btn--inc"
                data-qty-increment
                aria-label="Увеличить">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
            </svg>
        </button>
    </div>
</div>
