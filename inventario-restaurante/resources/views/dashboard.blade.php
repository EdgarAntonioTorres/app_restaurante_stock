<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

    <nav class="navbar navbar-dark bg-black border-bottom border-secondary px-3">
        <a class="navbar-brand fw-light" href="{{ url('/dashboard') }}">StockRest</a>
        <div class="d-flex gap-3 align-items-center">
            <span class="text-secondary small">{{ auth()->user()->name }}
                <span class="badge bg-secondary ms-1">{{ auth()->user()->rol }}</span>
            </span>
            <a href="{{ url('/dashboard') }}" class="nav-link text-light">Dashboard</a>
            <a href="{{ url('/contact') }}" class="nav-link text-secondary">Contacto</a>
            <form method="POST" action="/logout" class="mb-0">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Salir</button>
            </form>
        </div>
    </nav>

    <div class="container py-4">

        <h1 class="fw-light mb-4">Inventario</h1>

        <div class="table-responsive mb-5">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-uppercase small text-secondary fw-normal">Nombre</th>
                        <th class="text-uppercase small text-secondary fw-normal">Unidad</th>
                        <th class="text-uppercase small text-secondary fw-normal">Stock</th>
                        <th class="text-uppercase small text-secondary fw-normal">Mínimo</th>
                        <th class="text-uppercase small text-secondary fw-normal">Fecha caducidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                        <tr>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->unidad }}</td>
                            <td>{{ $producto->stock_actual }}</td>
                            <td>{{ $producto->stock_minimo }}</td>
                            <td>
                                @foreach($producto->lotes->unique('fecha_caducidad') as $lote)
                                    <span class="badge bg-secondary">{{ $lote->fecha_caducidad }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Crear Producto: solo administrador --}}
        @if(auth()->user()->rol === 'administrador')
            <h2 class="fw-light mb-3">Crear Producto</h2>
            <div class="card bg-black border-secondary mb-5">
                <div class="card-body">
                    <form method="POST" action="/productos">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label text-secondary small">Nombre</label>
                                <input type="text" name="nombre" class="form-control bg-dark text-light border-secondary"
                                    placeholder="Nombre" required>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label text-secondary small">Categoría</label>
                                <input type="text" name="categoria" class="form-control bg-dark text-light border-secondary"
                                    placeholder="Categoría">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label text-secondary small">Unidad</label>
                                <input type="text" name="unidad" class="form-control bg-dark text-light border-secondary"
                                    placeholder="kg, piezas…" required>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <label class="form-label text-secondary small">Stock mínimo</label>
                                <input type="number" name="stock_minimo"
                                    class="form-control bg-dark text-light border-secondary" placeholder="0" min="0"
                                    required>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-light w-100">Crear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Agregar Lote: administrador y gerente --}}
        @if(in_array(auth()->user()->rol, ['administrador', 'gerente']))
            <h2 class="fw-light mb-3">Agregar Lote</h2>
            <div class="card bg-black border-secondary mb-5">
                <div class="card-body">
                    <form method="POST" action="/lotes">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-6 col-md-4">
                                <label class="form-label text-secondary small">Producto</label>
                                <select name="producto_id" class="form-select bg-dark text-light border-secondary" required>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label text-secondary small">Cantidad</label>
                                <input type="number" name="cantidad"
                                    class="form-control bg-dark text-light border-secondary" placeholder="Cantidad" min="1"
                                    required>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label text-secondary small">Fecha de caducidad</label>
                                <input type="date" name="fecha_caducidad"
                                    class="form-control bg-dark text-light border-secondary" required>
                            </div>
                            <div class="col-sm-6 col-md-2">
                                <button type="submit" class="btn btn-outline-light w-100">Agregar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Consumir: todos --}}
        <h2 class="fw-light mb-3">Consumir Producto</h2>
        <div class="card bg-black border-secondary mb-5">
            <div class="card-body">
                <form action="/consumir" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-sm-6 col-md-5">
                            <label class="form-label text-secondary small">Producto</label>
                            <select name="producto_id" class="form-select bg-dark text-light border-secondary" required>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label text-secondary small">Cantidad</label>
                            <input type="number" name="cantidad"
                                class="form-control bg-dark text-light border-secondary" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-light w-100">Consumir</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card bg-black border-danger">
                    <div class="card-header bg-transparent border-danger text-danger fw-light">
                        ⚠️ Stock Bajo
                    </div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        @foreach($stock_bajo as $p)
                            <span class="badge bg-danger bg-opacity-25 text-danger border border-danger">
                                {{ $p->nombre }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-black border-warning">
                    <div class="card-header bg-transparent border-warning text-warning fw-light">
                        ⏳ Por caducar
                    </div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        @foreach($por_caducar->unique('producto_id') as $lote)
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning">
                                {{ $lote->producto->nombre }} — {{ $lote->fecha_caducidad }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>