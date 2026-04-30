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
            @if(auth()->user()->rol === 'administrador')
                <a href="{{ url('/usuarios') }}" class="nav-link text-secondary">Usuarios</a>
            @endif
            <form method="POST" action="/logout" class="mb-0">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Salir</button>
            </form>
        </div>
    </nav>

    <div class="container py-4">

        <h1 class="fw-light mb-4">Inventario</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- SECCIÓN 1: ALERTAS (Stock crítico + Vencimientos) --}}
        {{-- ============================================================ --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card bg-black border-danger">
                    <div class="card-header bg-transparent border-danger text-danger fw-light">⚠️ Stock Crítico</div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        @foreach($stock_bajo as $p)
                            <span
                                class="badge bg-danger bg-opacity-25 text-danger border border-danger">{{ $p->nombre }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-black border-warning">
                    <div class="card-header bg-transparent border-warning text-warning fw-light">⏳ Vencimientos Próximos
                    </div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        @foreach($por_caducar->unique('producto_id') as $lote)
                            <span class="badge bg-warning bg-opacity-25 text-warning border border-warning">
                                {{ $lote->producto->nombre }} ({{ $lote->fecha_caducidad }})
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECCIÓN 2: TABLA DE INVENTARIO --}}
        {{-- ============================================================ --}}

        <div class="table-responsive mb-5">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-uppercase small text-secondary fw-normal">Nombre</th>
                        <th class="text-uppercase small text-secondary fw-normal">Unidad</th>
                        <th class="text-uppercase small text-secondary fw-normal">Stock Actual</th>
                        <th class="text-uppercase small text-secondary fw-normal">Mínimo Requerido</th>
                        <th class="text-uppercase small text-secondary fw-normal text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                        <tr>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->unidad }}</td>
                            <td>
                                <span
                                    class="fw-bold {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-danger' : ($producto->stock_actual <= $producto->stock_minimo * 2 ? 'text-warning' : 'text-success') }}">
                                    {{ $producto->stock_actual }}
                                </span>
                            </td>
                            <td>{{ $producto->stock_minimo }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    @if(in_array(auth()->user()->rol, ['administrador', 'gerente']))
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $producto->id }}">Editar</button>
                                        <form action="/productos/{{ $producto->id }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este producto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                        </form>
                                    @else
                                        <span class="text-secondary small italic">Solo lectura</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal{{ $producto->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content bg-dark border-secondary text-light">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title fw-light">Editar {{ $producto->nombre }}</h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="/productos/{{ $producto->id }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small text-secondary">Nombre</label>
                                                <input type="text" name="nombre"
                                                    class="form-control bg-black text-light border-secondary"
                                                    value="{{ $producto->nombre }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small text-secondary">Categoría</label>
                                                <input type="text" name="categoria"
                                                    class="form-control bg-black text-light border-secondary"
                                                    value="{{ $producto->categoria }}">
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label small text-secondary">Unidad</label>
                                                    <input type="text" name="unidad"
                                                        class="form-control bg-black text-light border-secondary"
                                                        value="{{ $producto->unidad }}" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-secondary">Stock Mínimo</label>
                                                    <input type="number" name="stock_minimo"
                                                        class="form-control bg-black text-light border-secondary"
                                                        value="{{ $producto->stock_minimo }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ============================================================ --}}
        {{-- SECCIÓN 3: FORMULARIOS DE ACCIÓN --}}
        {{-- ============================================================ --}}

        @if(in_array(auth()->user()->rol, ['administrador', 'gerente']))
            <h2 class="fw-light mb-3">Crear Producto</h2>
            <div class="card bg-black border-secondary mb-4">
                <div class="card-body">
                    <form method="POST" action="/productos">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label text-secondary small">Nombre</label>
                                <input type="text" name="nombre" class="form-control bg-dark text-light border-secondary"
                                    placeholder="Ej. Tomate" required>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label text-secondary small">Categoría</label>
                                <input type="text" name="categoria" class="form-control bg-dark text-light border-secondary"
                                    placeholder="Ej. Verduras">
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

        @if(in_array(auth()->user()->rol, ['administrador', 'gerente']))
            <h2 class="fw-light mb-3">Agregar Lote (Entrada)</h2>
            <div class="card bg-black border-secondary mb-4">
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
                                <label class="form-label text-secondary small">Cantidad a ingresar</label>
                                <input type="number" name="cantidad"
                                    class="form-control bg-dark text-light border-secondary" min="1" required>
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

        <h2 class="fw-light mb-3">Consumir Producto (Salida)</h2>
        <div class="card bg-black border-secondary mb-5">
            <div class="card-body">
                <form action="/consumir" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-sm-6 col-md-5">
                            <label class="form-label text-secondary small">Seleccionar Producto</label>
                            <select name="producto_id" class="form-select bg-dark text-light border-secondary" required>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label text-secondary small">Cantidad a retirar</label>
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

        {{-- ============================================================ --}}
        {{-- SECCIÓN 4: GRÁFICAS --}}
        {{-- ============================================================ --}}

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card bg-black border-secondary h-100">
                    <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase">
                        Estado de Suministros (Semáforo de Stock)
                    </div>
                    <div class="card-body">
                        <canvas id="chartStock" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-black border-secondary h-100">
                    <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase">
                        Variedad por Categoría
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="chartCategorias" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div class="card bg-black border-secondary">
                    <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase">
                        Tendencia de Consumo (Últimos 7 días)
                    </div>
                    <div class="card-body">
                        <canvas id="chartConsumo" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECCIÓN 4B: CONSUMO POR CATEGORÍA Y POR PRODUCTO --}}
        {{-- ============================================================ --}}
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="card bg-black border-secondary h-100">
                    <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase">
                        Consumo por Categoría (Últimos 7 días)
                    </div>
                    <div class="card-body">
                        <canvas id="chartConsumoCategoria" style="max-height: 280px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card bg-black border-secondary h-100">
                    <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase">
                        Top 10 Productos más Consumidos (Últimos 7 días)
                    </div>
                    <div class="card-body">
                        <canvas id="chartConsumoProducto" style="max-height: 280px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECCIÓN 4C: CONSUMO INDIVIDUAL POR PRODUCTO --}}
        {{-- ============================================================ --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="card bg-black border-secondary">
                    <div
                        class="card-header bg-transparent border-secondary d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="text-secondary small text-uppercase">Historial de Consumo por Producto</span>
                        <select id="selectorProducto"
                            class="form-select form-select-sm bg-dark text-light border-secondary"
                            style="max-width: 260px;">
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                    data-unidad="{{ $producto->unidad }}">
                                    {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="card-body">
                        <canvas id="chartProductoIndividual" style="max-height: 260px;"></canvas>
                        <p id="sinDatosMsg" class="text-secondary text-center small mt-3 d-none">
                            Sin movimientos de salida en los últimos 30 días para este producto.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECCIÓN 5: KARDEX DE MOVIMIENTOS --}}
        {{-- ============================================================ --}}

        <h2 class="fw-light mb-3 text-secondary">Kardex de Movimientos</h2>
        <div class="table-responsive mb-5 shadow-sm">
            <table class="table table-dark table-sm table-hover border-secondary">
                <thead class="bg-black text-secondary small text-uppercase">
                    <tr>
                        <th class="fw-normal py-2 px-3">Fecha</th>
                        <th class="fw-normal py-2">Producto</th>
                        <th class="fw-normal py-2 text-center">Tipo</th>
                        <th class="fw-normal py-2">Cantidad</th>
                        <th class="fw-normal py-2">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $mov)
                        <tr class="border-secondary align-middle">
                            <td class="small text-secondary px-3">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $mov->producto->nombre }}</td>
                            <td class="text-center">
                                <span
                                    class="badge {{ $mov->tipo == 'entrada' ? 'bg-success' : 'bg-info' }} bg-opacity-25 {{ $mov->tipo == 'entrada' ? 'text-success' : 'text-info' }} border {{ $mov->tipo == 'entrada' ? 'border-success' : 'border-info' }} px-2">
                                    {{ strtoupper($mov->tipo) }}
                                </span>
                            </td>
                            <td class="fw-bold">{{ $mov->cantidad }} {{ $mov->producto->unidad }}</td>
                            <td class="small text-secondary">
                                {{ $mov->user->name ?? 'Sistema' }}
                                @if($mov->user)
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary ms-1"
                                        style="font-size: 0.65rem;">
                                        {{ $mov->user->rol }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const productos = @json($productos);
            const topProductos = productos.slice(0, 10);

            const categoriasMap = {};
            productos.forEach(p => {
                const cat = p.categoria || 'Sin Categoría';
                categoriasMap[cat] = (categoriasMap[cat] || 0) + 1;
            });

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#6c757d', font: { size: 11 } } }
                }
            };

            // Gráfica Semáforo (Bar)
            new Chart(document.getElementById('chartStock'), {
                type: 'bar',
                data: {
                    labels: topProductos.map(p => p.nombre),
                    datasets: [{
                        label: 'Nivel de Stock',
                        data: topProductos.map(p => p.stock_actual),
                        backgroundColor: topProductos.map(p => {
                            if (p.stock_actual <= p.stock_minimo) return 'rgba(220, 53, 69, 0.7)';
                            else if (p.stock_actual <= p.stock_minimo * 2) return 'rgba(255, 193, 7, 0.7)';
                            else return 'rgba(25, 135, 84, 0.7)';
                        }),
                        borderColor: topProductos.map(p => {
                            if (p.stock_actual <= p.stock_minimo) return '#dc3545';
                            if (p.stock_actual <= p.stock_minimo * 2) return '#ffc107';
                            return '#198754';
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#2d2d2d' }, ticks: { color: '#6c757d' } },
                        x: { grid: { display: false }, ticks: { color: '#6c757d', font: { size: 10 } } }
                    }
                }
            });

            // Gráfica Categorías (Doughnut)
            new Chart(document.getElementById('chartCategorias'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(categoriasMap),
                    datasets: [{
                        data: Object.values(categoriasMap),
                        backgroundColor: [
                            'rgba(13, 110, 253, 0.5)',
                            'rgba(102, 16, 242, 0.5)',
                            'rgba(214, 33, 150, 0.5)',
                            'rgba(32, 201, 151, 0.5)',
                            'rgba(108, 117, 125, 0.5)'
                        ],
                        borderColor: '#000',
                        borderWidth: 2
                    }]
                },
                options: commonOptions
            });

            // Gráfica de Tendencia (Line)
            const datosConsumo = @json($consumoDiario);
            new Chart(document.getElementById('chartConsumo'), {
                type: 'line',
                data: {
                    labels: datosConsumo.map(d => d.fecha),
                    datasets: [{
                        label: 'Insumos Consumidos',
                        data: datosConsumo.map(d => d.total),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { grid: { color: '#2d2d2d' }, ticks: { color: '#6c757d' } },
                        x: { grid: { display: false }, ticks: { color: '#6c757d' } }
                    }
                }
            });

            // ── Consumo por Categoría (Líneas múltiples) ──────────────────────
            const rawCategoria = @json($consumoPorCategoria);

            const fechasSet = [...new Set(rawCategoria.map(d => d.fecha))].sort();
            const categoriasSet = [...new Set(rawCategoria.map(d => d.categoria))];

            const paletaCat = [
                { border: '#0d6efd', bg: 'rgba(13,110,253,0.12)' },
                { border: '#20c997', bg: 'rgba(32,201,151,0.12)' },
                { border: '#d621a0', bg: 'rgba(214,33,160,0.12)' },
                { border: '#ffc107', bg: 'rgba(255,193,7,0.12)' },
                { border: '#6610f2', bg: 'rgba(102,16,242,0.12)' },
                { border: '#fd7e14', bg: 'rgba(253,126,20,0.12)' },
            ];

            const datasetsCat = categoriasSet.map((cat, i) => {
                const c = paletaCat[i % paletaCat.length];
                return {
                    label: cat,
                    data: fechasSet.map(fecha => {
                        const row = rawCategoria.find(d => d.fecha === fecha && d.categoria === cat);
                        return row ? row.total : 0;
                    }),
                    borderColor: c.border,
                    backgroundColor: c.bg,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: c.border,
                };
            });

            new Chart(document.getElementById('chartConsumoCategoria'), {
                type: 'line',
                data: { labels: fechasSet, datasets: datasetsCat },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#2d2d2d' }, ticks: { color: '#6c757d' } },
                        x: { grid: { display: false }, ticks: { color: '#6c757d' } }
                    }
                }
            });

            // ── Top 10 Productos más Consumidos (Barras horizontales) ─────────
            const rawProducto = @json($consumoPorProducto);

            new Chart(document.getElementById('chartConsumoProducto'), {
                type: 'bar',
                data: {
                    labels: rawProducto.map(p => `${p.nombre} (${p.unidad})`),
                    datasets: [{
                        label: 'Cantidad consumida',
                        data: rawProducto.map(p => p.total),
                        backgroundColor: 'rgba(13,110,253,0.4)',
                        borderColor: '#0d6efd',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#2d2d2d' }, ticks: { color: '#6c757d' } },
                        y: { grid: { display: false }, ticks: { color: '#adb5bd', font: { size: 11 } } }
                    }
                }
            });

            // ── Consumo individual por producto (últimos 30 días) ─────────────
            const todosMovimientos = @json($consumoIndividual);

            const ctxIndividual = document.getElementById('chartProductoIndividual');
            let chartIndividual = null;

            function renderChartIndividual(productoId, nombre, unidad) {
                const datos = todosMovimientos.filter(m => m.producto_id == productoId);
                const sinDatos = document.getElementById('sinDatosMsg');

                if (datos.length === 0) {
                    sinDatos.classList.remove('d-none');
                    if (chartIndividual) { chartIndividual.destroy(); chartIndividual = null; }
                    return;
                }

                sinDatos.classList.add('d-none');

                const labels = datos.map(d => d.fecha);
                const valores = datos.map(d => d.total);

                if (chartIndividual) chartIndividual.destroy();

                chartIndividual = new Chart(ctxIndividual, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: `${nombre} (${unidad})`,
                            data: valores,
                            backgroundColor: 'rgba(13,110,253,0.45)',
                            borderColor: '#0d6efd',
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#2d2d2d' }, ticks: { color: '#6c757d' } },
                            x: { grid: { display: false }, ticks: { color: '#6c757d' } }
                        }
                    }
                });
            }

            const selector = document.getElementById('selectorProducto');

            // Render inicial con el primer producto
            const primerOpt = selector.options[0];
            if (primerOpt) {
                renderChartIndividual(primerOpt.value, primerOpt.dataset.nombre, primerOpt.dataset.unidad);
            }

            // Actualizar al cambiar selección
            selector.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                renderChartIndividual(opt.value, opt.dataset.nombre, opt.dataset.unidad);
            });
        });
    </script>
</body>

</html>