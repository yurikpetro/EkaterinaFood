<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function index()
    {
        return view('cart', [
            'items' => $this->cart->items(),
            'total' => $this->cart->total(),
            'cartCount' => $this->cart->count(),
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->cart->add(
            (int) $validated['product_id'],
            (int) ($validated['quantity'] ?? 1),
        );

        return back()->with('success', 'Добавлено в корзину');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->cart->update(
            (int) $validated['product_id'],
            (int) $validated['quantity'],
        );

        return redirect()->route('cart.index');
    }

    public function remove(int $productId)
    {
        $this->cart->remove($productId);

        return redirect()->route('cart.index');
    }
}
