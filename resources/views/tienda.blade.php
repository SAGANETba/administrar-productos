<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tienda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($products->isEmpty())
                <div class="bg-white p-8 rounded-lg shadow text-center text-gray-500">
                    Aún no hay productos disponibles.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col">
                            <div class="h-48 bg-gray-100 flex items-center justify-center">
                                @if ($product->image)
                                    <img src="{{ $product->image->url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                @else
                                    <span class="text-gray-400 text-sm">Sin imagen</span>
                                @endif
                            </div>
                            <div class="p-4 flex flex-col grow">
                                <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1 grow">{{ $product->description }}</p>

                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-lg font-bold text-emerald-700">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-xs text-gray-500">
                                        @if ($product->stock > 0)
                                            {{ $product->stock }} en stock
                                        @else
                                            Agotado
                                        @endif
                                    </span>
                                </div>

                                @if ($product->stock > 0)
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-3 flex gap-2">
                                        @csrf
                                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                               class="w-16 rounded-md border-gray-300 shadow-sm text-sm">
                                        <button type="submit"
                                                class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-md px-3 py-2">
                                            Agregar al carrito
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="mt-3 bg-gray-200 text-gray-500 text-sm font-medium rounded-md px-3 py-2 cursor-not-allowed">
                                        Sin stock
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
