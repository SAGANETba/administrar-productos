<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pedido') }} #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg p-4 mb-6 text-sm">
                Estado del pedido: <strong>{{ ucfirst($order->status) }}</strong> ·
                Realizado el {{ $order->created_at->format('d/m/Y H:i') }}
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="p-4">Producto</th>
                            <th class="p-4">Precio unitario</th>
                            <th class="p-4">Cantidad</th>
                            <th class="p-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-t">
                                <td class="p-4 flex items-center gap-3">
                                    @if ($item->product && $item->product->image)
                                        <img src="{{ $item->product->image->url }}" class="w-12 h-12 object-cover rounded">
                                    @endif
                                    {{ $item->product_name }}
                                </td>
                                <td class="p-4">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="p-4">{{ $item->quantity }}</td>
                                <td class="p-4 font-medium">${{ number_format($item->subtotal(), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 border-t flex justify-between font-semibold text-lg bg-gray-50">
                    <span>Total</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6 text-sm">
                <h3 class="font-semibold text-gray-800 mb-2">Datos de envío</h3>
                <p><span class="text-gray-500">Nombre:</span> {{ $order->nombre_cliente }}</p>
                <p><span class="text-gray-500">Dirección:</span> {{ $order->direccion_envio }}</p>
            </div>

            <a href="{{ route('orders.index') }}" class="inline-block mt-6 text-emerald-700 text-sm underline">
                ← Volver a mis pedidos
            </a>
        </div>
    </div>
</x-app-layout>
