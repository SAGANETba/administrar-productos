<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = auth()->user()->orders()->latest()->get();

        return view('pedidos.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);

        return view('pedidos.show', [
            'order' => $order->load('items.product.image'),
        ]);
    }
}
