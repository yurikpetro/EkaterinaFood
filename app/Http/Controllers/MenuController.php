<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CartService;

class MenuController extends Controller
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function __invoke()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['activeProducts'])
            ->get()
            ->filter(fn (Category $category) => $category->activeProducts->isNotEmpty());

        return view('menu', [
            'categories' => $categories,
            'cartCount' => $this->cart->count(),
        ]);
    }
}
