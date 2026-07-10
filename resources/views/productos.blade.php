<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Productos - Vista de revisión</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f5; color: #1a1a1a; margin: 0; padding: 24px; }
        h1 { color: #2c6e49; }
        p.hint { color: #555; margin-top: -8px; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        th, td { padding: 10px 12px; border-bottom: 1px solid #e2e2e2; text-align: left; vertical-align: middle; }
        th { background: #2c6e49; color: #fff; }
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .sin-imagen { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;
            background: #eee; color: #999; font-size: 10px; border-radius: 6px; }
        .vacio { padding: 24px; text-align: center; color: #777; }
        code { background: #eee; padding: 2px 5px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Productos registrados</h1>
    <p class="hint">Vista solo de lectura para revisar los datos. La API real está en <code>/api/products</code>.</p>

    @if ($products->isEmpty())
        <div class="vacio">Aún no hay productos. Crea uno con <code>POST /api/products</code>.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            @if ($product->image)
                                <img src="{{ $product->image->url }}" alt="{{ $product->name }}">
                            @else
                                <div class="sin-imagen">sin imagen</div>
                            @endif
                        </td>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
