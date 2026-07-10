<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Admite filtros opcionales por query string:
     *   ?buscar=texto       -> busca en el nombre del producto
     *   ?precio_min=100     -> precio mayor o igual a 100
     *   ?precio_max=500     -> precio menor o igual a 500
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with('image')
            ->when($request->filled('buscar'), fn ($query) => $query->where('name', 'like', '%'.$request->input('buscar').'%'))
            ->when($request->filled('precio_min'), fn ($query) => $query->where('price', '>=', $request->input('precio_min')))
            ->when($request->filled('precio_max'), fn ($query) => $query->where('price', '<=', $request->input('precio_max')))
            ->latest()
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * GET /api/products/stock-bajo?limite=5
     * Lista los productos cuyo stock esta por debajo del limite indicado (por defecto 5).
     */
    public function stockBajo(Request $request): AnonymousResourceCollection
    {
        $limite = (int) $request->input('limite', 5);

        $products = Product::with('image')
            ->where('stock', '<', $limite)
            ->orderBy('stock')
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * POST /api/products
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        $path = $request->file('image')->store('products', 'public');

        $product->image()->create([
            'path' => $path,
        ]);

        return (new ProductResource($product->load('image')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/products/{product}
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('image'));
    }

    /**
     * PUT/PATCH /api/products/{product}
     */
    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $product->update($request->safe()->except('image'));

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image->path);
                $product->image->delete();
            }

            $path = $request->file('image')->store('products', 'public');

            $product->image()->create([
                'path' => $path,
            ]);
        }

        return new ProductResource($product->load('image'));
    }

    /**
     * DELETE /api/products/{product}
     */
    public function destroy(Product $product): JsonResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image->path);
            $product->image->delete();
        }

        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ], 200);
    }
}
