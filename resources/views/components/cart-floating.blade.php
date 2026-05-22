<div
    id="cart-floating"
    class="fixed bottom-4 right-4 z-40 w-[min(100vw-2rem,22rem)] pb-[env(safe-area-inset-bottom)] @if(($cartCount ?? 0) === 0) hidden @endif"
    aria-live="polite"
>
    <div class="bg-white rounded-2xl shadow-2xl border-2 border-olive/30 overflow-hidden">
        <button
            type="button"
            id="cart-floating-toggle"
            class="w-full flex items-center justify-between gap-3 px-4 py-3 bg-olive text-white font-bold text-left hover:bg-olive-dark transition btn-press"
            aria-expanded="false"
            aria-controls="cart-floating-panel"
        >
            <span class="flex items-center gap-2">
                <span id="cart-floating-badge"
                      class="bg-terracotta text-white text-sm font-extrabold min-w-[1.75rem] h-7 px-2 rounded-full flex items-center justify-center">
                    {{ $cartCount ?? 0 }}
                </span>
                <span>Корзина</span>
            </span>
            <span id="cart-floating-total" class="text-lg whitespace-nowrap">{{ number_format($cartTotal ?? 0, 0, ',', ' ') }} ₽</span>
            <svg id="cart-floating-chevron" class="w-5 h-5 shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="cart-floating-panel" class="hidden max-h-64 overflow-y-auto border-t border-cream-dark">
            <ul id="cart-floating-list" class="divide-y divide-cream-dark">
                @foreach($cartItems ?? [] as $item)
                    @php $product = $item['product']; @endphp
                    <li class="px-4 py-3 flex justify-between gap-2 text-base" data-floating-item="{{ $product->id }}">
                        <div class="min-w-0">
                            <p class="font-semibold text-warm-brown leading-snug truncate">{{ $product->name }}</p>
                            <p class="text-sm text-muted">
                                {{ $item['quantity'] }} {{ $product->unit }} × {{ number_format($product->price, 0, ',', ' ') }} ₽
                            </p>
                        </div>
                        <p class="font-bold text-terracotta shrink-0">
                            {{ number_format($item['subtotal'], 0, ',', ' ') }} ₽
                        </p>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="px-4 py-3 bg-cream border-t border-cream-dark flex flex-col gap-2">
            <div class="flex justify-between items-center font-bold text-lg">
                <span>Итого</span>
                <span id="cart-floating-footer-total" class="text-terracotta">{{ number_format($cartTotal ?? 0, 0, ',', ' ') }} ₽</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cart.index') }}"
                   class="btn-press flex-1 text-center bg-white border-2 border-olive text-olive font-bold py-2.5 rounded-xl hover:bg-olive/5 text-base">
                    В корзину
                </a>
                <a href="{{ route('checkout.show') }}"
                   class="btn-press flex-1 text-center bg-terracotta text-white font-bold py-2.5 rounded-xl hover:bg-terracotta-dark text-base">
                    Заказать
                </a>
            </div>
        </div>
    </div>
</div>
