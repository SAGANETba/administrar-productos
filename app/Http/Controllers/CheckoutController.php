<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $items = Cart::items();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('status', 'Tu carrito está vacío.');
        }

        return view('checkout', [
            'items' => $items,
            'total' => Cart::total(),
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre_cliente' => ['required', 'string', 'max:255'],
            'direccion_envio' => ['required', 'string', 'max:255'],
        ]);

        $items = Cart::items();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('status', 'Tu carrito está vacío.');
        }

        foreach ($items as $item) {
            if ($item['quantity'] > $item['product']->stock) {
                return redirect()->route('cart.index')
                    ->with('status', "No hay stock suficiente de \"{$item['product']->name}\".");
            }
        }

        $order = DB::transaction(function () use ($items, $data) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => 'pagado',
                'total' => Cart::total(),
                'nombre_cliente' => $data['nombre_cliente'],
                'direccion_envio' => $data['direccion_envio'],
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product']->price,
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            return $order;
        });

        Cart::clear();

        return redirect()->route('orders.show', $order)
            ->with('status', '¡Pago simulado exitoso! Tu pedido fue registrado.');
    }
}
