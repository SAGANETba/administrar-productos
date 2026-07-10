<?php

namespace App\Support;

use App\Models\Product;

class Cart
{
    /**
     * @return array<int, array{product: Product, quantity: int, subtotal: float}>
     */
    public static function items(): array
    {
        $cart = session('cart', []);
        $products = Product::with('image')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        $items = [];

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product) {
                continue;
            }

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => (float) $product->price * $quantity,
            ];
        }

        return $items;
    }

    public static function total(): float
    {
        return array_sum(array_column(self::items(), 'subtotal'));
    }

    public static function count(): int
    {
        return array_sum(session('cart', []));
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }
}
