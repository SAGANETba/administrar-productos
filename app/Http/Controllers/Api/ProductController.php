<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * GET /api/products
     */
    public function index(): AnonymousResourceCollection
    {
        $products = Product::with('image')->latest()->get();

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
