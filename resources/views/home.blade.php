@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <section class="relative overflow-hidden">
        <div class="page-wide py-16 sm:py-24 text-center">
            <p class="text-olive font-semibold uppercase tracking-wide text-sm mb-3">Домашняя кухня на заказ</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-warm-brown leading-tight mb-4">
                {{ $heroTitle }}
            </h1>
            <p class="text-xl sm:text-2xl text-muted max-w-2xl mx-auto mb-8">
                {{ $heroSubtitle }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('menu') }}"
                   class="btn-press inline-block bg-terracotta text-white text-xl font-bold px-8 py-4 rounded-2xl hover:bg-terracotta-dark shadow-lg">
                    Смотреть меню
                </a>
                @if($phone)
                    <a href="tel:{{ preg_replace('/\D+/', '', $phone) }}"
                       class="btn-press inline-block bg-white border-2 border-olive text-olive text-xl font-bold px-8 py-4 rounded-2xl hover:bg-olive/5">
                        {{ $phone }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="page-wide pb-16">
        <div class="bg-white rounded-3xl shadow-sm border border-cream-dark p-8 sm:p-10">
            <h2 class="text-2xl font-bold text-terracotta mb-4">О тёте Кате</h2>
            <p class="text-lg leading-relaxed text-warm-brown/90 whitespace-pre-line">{{ $aboutText }}</p>

            <div class="grid sm:grid-cols-3 gap-6 mt-10">
                <div class="text-center p-4 rounded-2xl bg-cream">
                    <div class="text-3xl mb-2">🍲</div>
                    <h3 class="font-bold text-lg">Домашняя еда</h3>
                    <p class="text-base mt-1 text-muted">Обеды и ужины на каждый день</p>
                </div>
                <div class="text-center p-4 rounded-2xl bg-cream">
                    <div class="text-3xl mb-2">🎉</div>
                    <h3 class="font-bold text-lg">Праздники</h3>
                    <p class="text-base mt-1 text-muted">Пицца в школу, дни рождения</p>
                </div>
                <div class="text-center p-4 rounded-2xl bg-cream">
                    <div class="text-3xl mb-2">🏢</div>
                    <h3 class="font-bold text-lg">Корпоративы</h3>
                    <p class="text-base mt-1 text-muted">Питание для офиса и мероприятий</p>
                </div>
            </div>
        </div>

        @if($whatsapp || $telegram || $telegramChannel || $max)
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 justify-center items-center mt-8">
                @if($whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $whatsapp) }}" target="_blank" rel="noopener"
                       class="btn-press w-full sm:w-auto inline-block bg-whatsapp text-white font-bold text-lg px-8 py-4 rounded-xl hover:brightness-95">
                        Написать в WhatsApp
                    </a>
                @endif
                @if($telegram)
                    <a href="{{ $telegram }}" target="_blank" rel="noopener"
                       class="btn-press w-full sm:w-auto inline-block bg-telegram text-white font-semibold px-6 py-3 rounded-xl hover:brightness-95">
                        Написать в Telegram
                    </a>
                @endif
                @if($telegramChannel)
                    <a href="{{ $telegramChannel }}" target="_blank" rel="noopener"
                       class="w-full sm:w-auto text-center text-telegram font-semibold underline underline-offset-4 hover:text-telegram/80 px-4 py-2">
                        Telegram-канал
                    </a>
                @endif
                @if($max)
                    <a href="{{ $max }}" target="_blank" rel="noopener"
                       class="btn-press w-full sm:w-auto inline-block bg-max text-white font-semibold px-6 py-3 rounded-xl hover:brightness-95">
                        Написать в Max
                    </a>
                @endif
            </div>
        @endif
    </section>
@endsection
