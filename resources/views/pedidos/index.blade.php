<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis pedidos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                @if ($orders->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        Aún no tienes pedidos. <a href="{{ route('store.index') }}" class="text-emerald-700 font-medium">Ir a la tienda</a>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="p-4">Pedido</th>
                                <th class="p-4">Fecha</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4">Total</th>
                                <th class="p-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr class="border-t">
                                    <td class="p-4">#{{ $order->id }}</td>
                                    <td class="p-4">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-4">
                                        <span class="inline-block bg-emerald-50 text-emerald-700 text-xs font-medium px-2 py-1 rounded">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-medium">${{ number_format($order->total, 2) }}</td>
                                    <td class="p-4">
                                        <a href="{{ route('orders.show', $order) }}" class="text-emerald-700 text-xs underline">Ver detalle</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
