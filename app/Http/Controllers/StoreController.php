<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(): View
    {
        $products = Product::with('image')->latest()->get();

        return view('tienda', ['products' => $products]);
    }
}
