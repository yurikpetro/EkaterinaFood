@props(['current' => 'cart'])

@php
    $steps = [
        'cart' => ['label' => 'Корзина', 'route' => route('cart.index')],
        'checkout' => ['label' => 'Данные', 'route' => null],
        'success' => ['label' => 'Готово', 'route' => null],
    ];
    $order = ['cart', 'checkout', 'success'];
    $currentIndex = array_search($current, $order, true);
@endphp

<nav class="order-steps" aria-label="Шаги оформления заказа">
    @foreach($order as $index => $key)
        @php
            $step = $steps[$key];
            $state = $index < $currentIndex ? 'done' : ($index === $currentIndex ? 'active' : '');
        @endphp
        @if($index > 0)
            <span class="text-warm-brown/30 hidden sm:inline" aria-hidden="true">→</span>
        @endif
        @if($state === 'done' && $step['route'])
            <a href="{{ $step['route'] }}" class="order-step order-step--done">
                <span class="order-step__dot">{{ $index + 1 }}</span>
                <span>{{ $step['label'] }}</span>
            </a>
        @else
            <span class="order-step @if($state) order-step--{{ $state }} @endif" @if($state === 'active') aria-current="step" @endif>
                <span class="order-step__dot">{{ $index + 1 }}</span>
                <span>{{ $step['label'] }}</span>
            </span>
        @endif
    @endforeach
</nav>
