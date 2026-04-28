<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<nav style="background-color: #333; padding: 10px;">
    <a href="{{ url('/dashboard') }}" style="color: white; margin-right: 15px; text-decoration: none;">
        Dashboard
    </a>

    <a href="{{ url('/contact') }}" style="color: white; text-decoration: none;">
        Contacto
    </a>
</nav>

<h1>Inventario</h1>

<table class="table table-striped" border="1">
    <tr>
        <th>Nombre</th>
        <th>Unidad</th>
        <th>Stock</th>
        <th>Mínimo</th>
        <th>Fecha caducidad</th>
    </tr>

    @foreach($productos as $producto)
        <tr>
            <td>{{ $producto->nombre }}</td>
            <td>{{ $producto->unidad }}</td>
            <td>{{ $producto->stock_actual }}</td>
            <td>{{ $producto->stock_minimo }}</td>
            <td>
                @foreach($producto->lotes->unique('fecha_caducidad') as $lote)
                    <p>{{ $lote->fecha_caducidad }}</p>
                @endforeach
            </td>
        </tr>
    @endforeach
</table>

<h2>Crear Producto</h2>

<form method="POST" action="/productos">
    @csrf

    <input type="text" name="nombre" placeholder="Nombre" required>

    <input type="text" name="categoria" placeholder="Categoría">

    <input type="text" name="unidad" placeholder="Unidad (kg, piezas)" required>

    <input type="number" name="stock_minimo" placeholder="Stock mínimo" min="0" required>

    <button type="submit">Crear Producto</button>
</form>

<h2>Agregar Lote</h2>

<form method="POST" action="/lotes">
    @csrf

    <label>Producto:</label>
    <select name="producto_id" required>
        @foreach($productos as $producto)
            <option value="{{ $producto->id }}">
                {{ $producto->nombre }}
            </option>
        @endforeach
    </select>

    <input type="number" name="cantidad" placeholder="Cantidad" min="1" required>

    <input type="date" name="fecha_caducidad" required>

    <button type="submit">Agregar Lote</button>
</form>

<h2>Consumir producto</h2>

<form action="/consumir" method="POST">
    @csrf

    <label>Producto:</label>
    <select name="producto_id" required>
        @foreach($productos as $producto)
            <option value="{{ $producto->id }}">
                {{ $producto->nombre }}
            </option>
        @endforeach
    </select>

    <label>Cantidad:</label>
    <input type="number" name="cantidad" min="1" required>

    <button type="submit">Consumir</button>
</form>

<h2>⚠️ Stock Bajo</h2>
@foreach($stock_bajo as $p)
    <p>{{ $p->nombre }}</p>
@endforeach

<h2>⏳ Por caducar</h2>
@foreach($por_caducar->unique('producto_id') as $lote)
    <p>{{ $lote->producto->nombre }} - Caduca: {{ $lote->fecha_caducidad }}</p>
@endforeach