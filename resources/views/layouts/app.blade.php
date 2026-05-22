<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Еда от тёти Кати') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <header class="bg-white/90 backdrop-blur border-b border-cream-dark sticky top-0 z-50">
        <div class="page-wide py-4 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}"
               class="nav-link text-xl font-extrabold text-terracotta leading-tight px-0 @if(request()->routeIs('home')) nav-link--active @endif">
                {{ config('app.name') }}
            </a>
            <nav class="flex items-center gap-2 sm:gap-4 text-base font-semibold">
                <a href="{{ route('menu') }}"
                   class="nav-link @if(request()->routeIs('menu')) nav-link--active @endif">
                    Меню
                </a>
                @if(!empty($headerPhone))
                    <a href="tel:{{ preg_replace('/\D+/', '', $headerPhone) }}"
                       class="nav-link hidden sm:inline-flex items-center text-olive text-sm font-bold">
                        {{ $headerPhone }}
                    </a>
                    <a href="tel:{{ preg_replace('/\D+/', '', $headerPhone) }}"
                       class="nav-link sm:hidden inline-flex items-center justify-center w-10 h-10 rounded-full bg-olive/10 text-olive"
                       aria-label="Позвонить {{ $headerPhone }}">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </a>
                @endif
                <a href="{{ route('cart.index') }}"
                   id="header-cart-link"
                   class="nav-link relative inline-flex items-center justify-center w-11 h-11 rounded-full text-olive hover:bg-olive/10 @if(request()->routeIs('cart.*')) nav-link--active @endif"
                   aria-label="Корзина@if(($cartCount ?? 0) > 0), {{ $cartCount }} позиций@endif">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h15l-1.5 9H7.5L6 6zM6 6 5 3H2M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm8 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    </svg>
                    @if(($cartCount ?? 0) > 0)
                        <span id="header-cart-badge"
                              class="absolute -top-0.5 -right-0.5 bg-terracotta text-white text-xs font-bold min-w-[1.25rem] h-5 px-1 rounded-full flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="page-wide pt-4">
            <div class="flash-alert bg-olive/15 text-olive-dark border border-olive/30" role="alert" data-flash-dismiss>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="page-wide pt-4">
            <div class="flash-alert bg-red-50 text-red-800 border border-red-200" role="alert" data-flash-dismiss>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div id="site-toast" class="is-hidden" role="status" aria-live="polite"></div>

    <main class="flex-1">
        @yield('content')
    </main>

    @unless(request()->routeIs('cart.*', 'checkout.*'))
        @include('components.cart-floating')
    @endunless

    <footer class="bg-warm-brown text-cream mt-12" id="site-footer">
        <div class="page-wide py-8 text-center text-base">
            <p class="font-bold text-lg mb-2">{{ config('app.name') }}</p>
            <p>Домашняя еда с заботой · Заказы через сайт или мессенджер</p>
        </div>
    </footer>
</body>
</html>
