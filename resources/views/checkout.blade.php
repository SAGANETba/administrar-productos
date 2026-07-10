<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Resumen del pedido</h3>
                <ul class="divide-y">
                    @foreach ($items as $item)
                        <li class="py-2 flex justify-between text-sm">
                            <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                            <span class="font-medium">${{ number_format($item['subtotal'], 2) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4 pt-4 border-t flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Datos de envío</h3>

                <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-md px-3 py-2 mb-4">
                    Este es un pago simulado con fines de práctica: no se procesa ningún cobro real ni se conecta con
                    una pasarela de pagos.
                </div>

                <form method="POST" action="{{ route('checkout.process') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="nombre_cliente" value="Nombre completo" />
                        <x-text-input id="nombre_cliente" name="nombre_cliente" type="text" class="mt-1 block w-full"
                                      :value="old('nombre_cliente', auth()->user()->name)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre_cliente')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="direccion_envio" value="Dirección de envío" />
                        <x-text-input id="direccion_envio" name="direccion_envio" type="text" class="mt-1 block w-full"
                                      :value="old('direccion_envio')" required placeholder="Calle, número, ciudad" />
                        <x-input-error :messages="$errors->get('direccion_envio')" class="mt-2" />
                    </div>

                    <button type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-md px-4 py-3">
                        Confirmar pago (simulado)
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
