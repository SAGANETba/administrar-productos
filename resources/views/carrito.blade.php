<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Carrito de compras') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if (empty($items))
                    <div class="p-8 text-center text-gray-500">
                        Tu carrito está vacío. <a href="{{ route('store.index') }}" class="text-emerald-700 font-medium">Ir a la tienda</a>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="p-4">Producto</th>
                                <th class="p-4">Precio</th>
                                <th class="p-4">Cantidad</th>
                                <th class="p-4">Subtotal</th>
                                <th class="p-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr class="border-t">
                                    <td class="p-4 flex items-center gap-3">
                                        @if ($item['product']->image)
                                            <img src="{{ $item['product']->image->url }}" class="w-12 h-12 object-cover rounded">
                                        @endif
                                        {{ $item['product']->name }}
                                    </td>
                                    <td class="p-4">${{ number_format($item['product']->price, 2) }}</td>
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                                   max="{{ $item['product']->stock }}" class="w-16 rounded-md border-gray-300 text-sm">
                                            <button type="submit" class="text-emerald-700 text-xs underline">Actualizar</button>
                                        </form>
                                    </td>
                                    <td class="p-4 font-medium">${{ number_format($item['subtotal'], 2) }}</td>
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('cart.remove', $item['product']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 text-xs underline">Quitar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 border-t flex items-center justify-between bg-gray-50">
                        <span class="text-lg font-semibold">Total: ${{ number_format($total, 2) }}</span>
                        @auth
                            <a href="{{ route('checkout.show') }}"
                               class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-md px-4 py-2">
                                Ir a pagar
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-md px-4 py-2">
                                Inicia sesión para pagar
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
