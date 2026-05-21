@extends('layouts.app')

@section('title', 'Меню')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-warm-brown mb-2">Меню</h1>
        <p class="text-lg text-warm-brown/70 mb-8">Выберите блюда и количество — цена обновится автоматически</p>

        @forelse($categories as $category)
            <section id="category-{{ $category->id }}" class="mb-12">
                <h2 class="text-2xl font-bold text-terracotta border-b-2 border-terracotta/20 pb-2 mb-6">
                    {{ $category->name }}
                </h2>

                <div class="grid gap-6">
                    @foreach($category->activeProducts as $product)
                        <article class="bg-white rounded-2xl border border-cream-dark overflow-hidden flex flex-col sm:flex-row gap-4 p-4 sm:p-5 shadow-sm">
                            @if($product->image_path)
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}"
                                     class="w-full sm:w-36 h-36 object-cover rounded-xl shrink-0">
                            @else
                                <div class="w-full sm:w-36 h-36 bg-cream rounded-xl flex items-center justify-center text-4xl shrink-0">
                                    🍽️
                                </div>
                            @endif

                            <div class="flex-1 flex flex-col">
                                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                    <h3 class="text-xl font-bold">{{ $product->name }}</h3>
                                    <span class="text-xl font-extrabold text-terracotta whitespace-nowrap">
                                        {{ $product->formattedPrice() }}
                                        <span class="text-base font-normal text-warm-brown/60">/ {{ $product->unit }}</span>
                                    </span>
                                </div>
                                @if($product->description)
                                    <p class="text-base text-warm-brown/80 mb-4">{{ $product->description }}</p>
                                @endif
                                @if($product->min_quantity > 1)
                                    <p class="text-sm text-olive mb-2">Минимум: {{ $product->min_quantity }} {{ $product->unit }}</p>
                                @endif

                                <form action="{{ route('cart.add') }}" method="POST" class="mt-auto flex flex-wrap items-end gap-3">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <label class="flex flex-col text-sm font-semibold">
                                        Кол-во
                                        <input type="number" name="quantity" value="{{ $product->min_quantity }}" min="{{ $product->min_quantity }}"
                                               class="mt-1 w-24 text-lg border-2 border-cream-dark rounded-xl px-3 py-2 focus:border-terracotta focus:outline-none">
                                    </label>
                                    <button type="submit"
                                            class="btn-press bg-terracotta text-white font-bold text-lg px-6 py-3 rounded-xl shadow-sm hover:bg-terracotta-dark">
                                        В корзину
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <p class="text-xl text-center py-12">Меню скоро появится. Позвоните нам!</p>
        @endforelse
    </div>
@endsection
