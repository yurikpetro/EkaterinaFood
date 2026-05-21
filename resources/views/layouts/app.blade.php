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
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-xl font-extrabold text-terracotta leading-tight">
                {{ config('app.name') }}
            </a>
            <nav class="flex items-center gap-3 sm:gap-5 text-base font-semibold">
                <a href="{{ route('menu') }}" class="hover:text-terracotta transition @if(request()->routeIs('menu')) text-terracotta @endif">Меню</a>
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-1 bg-olive text-white px-4 py-2 rounded-full hover:bg-olive-dark transition">
                    Корзина
                    @if(($cartCount ?? 0) > 0)
                        <span class="bg-terracotta text-white text-sm font-bold min-w-[1.5rem] h-6 px-1.5 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="max-w-5xl mx-auto px-4 pt-4">
            <div class="bg-olive/15 text-olive-dark border border-olive/30 rounded-xl px-4 py-3 font-medium">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-5xl mx-auto px-4 pt-4">
            <div class="bg-red-50 text-red-800 border border-red-200 rounded-xl px-4 py-3 font-medium">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    @unless(request()->routeIs('cart.*', 'checkout.*'))
        @include('components.cart-floating')
    @endunless

    <footer class="bg-warm-brown text-cream mt-12">
        <div class="max-w-5xl mx-auto px-4 py-8 text-center text-base">
            <p class="font-bold text-lg mb-2">{{ config('app.name') }}</p>
            <p>Домашняя еда с заботой · Заказы через сайт или мессенджер</p>
        </div>
    </footer>
</body>
</html>
