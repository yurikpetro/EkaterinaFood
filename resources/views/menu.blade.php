@extends('layouts.app')

@section('title', 'Меню')

@section('content')
    <div class="page-wide py-10" id="menu-page" data-cart-add-url="{{ route('cart.add') }}" data-csrf="{{ csrf_token() }}">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-warm-brown mb-2">Меню</h1>
        <p class="text-lg text-muted mb-8">Выберите блюда и укажите количество</p>

        @if($categories->isNotEmpty())
            <nav class="menu-category-nav" aria-label="Категории меню">
                <div class="flex gap-2 overflow-x-auto pb-1" id="menu-category-chips">
                    @foreach($categories as $category)
                        <a href="#category-{{ $category->id }}"
                           class="menu-category-chip"
                           data-category-chip="{{ $category->id }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </nav>
        @endif

        @forelse($categories as $category)
            <section id="category-{{ $category->id }}" class="mb-12 scroll-mt-36" data-category-section="{{ $category->id }}">
                <h2 class="text-2xl font-bold text-terracotta border-b-2 border-terracotta/20 pb-2 mb-6">
                    {{ $category->name }}
                </h2>

                <div class="grid gap-6">
                    @foreach($category->activeProducts as $product)
                        @php $inCartQty = $cartQuantities[$product->id] ?? 0; @endphp
                        <article class="bg-white rounded-2xl border border-cream-dark overflow-hidden flex flex-col sm:flex-row gap-4 p-4 sm:p-5 shadow-sm"
                                 data-product-card="{{ $product->id }}">
                            @if($product->image_path)
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}"
                                     class="w-full sm:w-36 h-36 object-cover rounded-xl shrink-0">
                            @else
                                <div class="w-full sm:w-36 h-36 bg-cream rounded-xl flex items-center justify-center text-4xl shrink-0">
                                    🍽️
                                </div>
                            @endif

                            <div class="flex-1 flex flex-col">
                                <h3 class="text-xl font-bold mb-1">{{ $product->name }}</h3>
                                <p class="text-xl font-extrabold text-terracotta mb-2 sm:mb-0">
                                    {{ $product->formattedPricePerUnit() }}
                                </p>
                                @if($inCartQty > 0)
                                    <p class="text-sm font-semibold text-olive mb-2" data-in-cart-badge="{{ $product->id }}">
                                        В корзине: {{ $product->formatAmount($inCartQty) }}
                                    </p>
                                @else
                                    <p class="text-sm font-semibold text-olive mb-2 hidden" data-in-cart-badge="{{ $product->id }}"></p>
                                @endif
                                @if($product->description)
                                    <p class="text-base text-muted mb-4">{{ $product->description }}</p>
                                @endif
                                @if($product->min_quantity > ($product->unit->isWeighted() ? 0 : 1))
                                    <p class="text-sm text-olive mb-2">Минимум: {{ $product->formatAmount($product->min_quantity) }}</p>
                                @endif

                                <form action="{{ route('cart.add') }}" method="POST"
                                      class="mt-auto flex flex-wrap items-end gap-3"
                                      data-add-to-cart-form>
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <label class="flex flex-col gap-1.5 text-sm font-semibold">
                                        {{ $product->unit->amountInputLabel() }}
                                        <x-amount-stepper
                                            name="quantity"
                                            :unit="$product->unit->value"
                                            :value="$product->min_quantity"
                                            :min="$product->min_quantity"
                                        />
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
