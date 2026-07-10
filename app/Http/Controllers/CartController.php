<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('carrito', [
            'items' => Cart::items(),
            'total' => Cart::total(),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $data['quantity'] ?? 1;

        $cart = session('cart', []);
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + $quantity, $product->stock);
        session(['cart' => $cart]);

        return back()->with('status', "\"{$product->name}\" se agregó al carrito.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session('cart', []);

        if (array_key_exists($product->id, $cart)) {
            $cart[$product->id] = min($data['quantity'], $product->stock);
            session(['cart' => $cart]);
        }

        return back()->with('status', 'Carrito actualizado.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('status', "\"{$product->name}\" se quitó del carrito.");
    }
}
