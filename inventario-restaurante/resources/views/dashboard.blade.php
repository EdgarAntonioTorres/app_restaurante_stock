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
        {{-- SECCIÓN 1: ALERTAS --}}
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
        <div class="table-responsive mb-5" style="max-height: 420px; overflow-y: auto;">
            <table class="table table-dark table-striped table-hover align-middle" style="min-width: 600px;">
                <thead style="position: sticky; top: 0; z-index: 1; background-color: #1a1a1a;">
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
                                <div style="position:relative;">
                                    <input type="text" id="lote_buscar"
                                        class="form-control bg-dark text-light border-secondary"
                                        placeholder="🔍 Buscar producto…" autocomplete="off">
                                    <div id="lote_dropdown" class="bg-dark border border-secondary rounded"
                                        style="display:none;position:absolute;top:100%;left:0;right:0;z-index:999;max-height:200px;overflow-y:auto;">
                                        @foreach($productos as $producto)
                                            <div class="px-3 py-2 text-light lote-option"
                                                style="cursor:pointer;font-size:0.85rem;" data-id="{{ $producto->id }}"
                                                data-nombre="{{ $producto->nombre }}"
                                                onmouseover="this.style.background='#2d2d2d'"
                                                onmouseout="this.style.background='transparent'">
                                                {{ $producto->nombre }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="producto_id" id="lote_producto_id" required>
                                </div>
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

        {{-- FORMULARIO CONSUMIR — ahora con campo motivo --}}
        <h2 class="fw-light mb-3">Consumir Producto (Salida)</h2>
        <div class="card bg-black border-secondary mb-5">
            <div class="card-body">
                <form action="/consumir" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label text-secondary small">Seleccionar Producto</label>
                            <div style="position:relative;">
                                <input type="text" id="consumir_buscar"
                                    class="form-control bg-dark text-light border-secondary"
                                    placeholder="🔍 Buscar producto…" autocomplete="off">
                                <div id="consumir_dropdown" class="bg-dark border border-secondary rounded"
                                    style="display:none;position:absolute;top:100%;left:0;right:0;z-index:999;max-height:200px;overflow-y:auto;">
                                    @foreach($productos as $producto)
                                        <div class="px-3 py-2 text-light consumir-option"
                                            style="cursor:pointer;font-size:0.85rem;" data-id="{{ $producto->id }}"
                                            data-nombre="{{ $producto->nombre }}"
                                            onmouseover="this.style.background='#2d2d2d'"
                                            onmouseout="this.style.background='transparent'">
                                            {{ $producto->nombre }}
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="producto_id" id="consumir_producto_id" required>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label text-secondary small">Cantidad a retirar</label>
                            <input type="number" name="cantidad"
                                class="form-control bg-dark text-light border-secondary" min="1" required>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="form-label text-secondary small">Motivo de consumo</label>
                            <select name="motivo" class="form-select bg-dark text-light border-secondary" required>
                                <option value="uso_cocina">🍳 Uso en cocina (preparación normal)</option>
                                <option value="producto_danado">⚠️ Producto dañado al recibir</option>
                                <option value="producto_vencido">🗓️ Producto vencido</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <button type="submit" class="btn btn-outline-light w-100">Consumir</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECCIÓN 4: GRÁFICAS EXISTENTES --}}
        {{-- ============================================================ --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card bg-black border-secondary h-100">
                    <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase">
                        Estado de Suministros (Semáforo de Stock)
                    </div>
                    <div class="card-body p-0">
                        <div style="overflow-x: auto; overflow-y: hidden;">
                            <div id="chartStockWrapper" style="min-width: 100%; height: 300px; padding: 1rem;">
                                <canvas id="chartStock"></canvas>
                            </div>
                        </div>
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

        <div class="row mb-4">
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

        <div class="row g-4 mb-4">
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

        <div class="row mb-5">
            <div class="col-12">
                <div class="card bg-black border-secondary">
                    <div
                        class="card-header bg-transparent border-secondary d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="text-secondary small text-uppercase">Historial de Consumo por Producto</span>
                        {{-- Selector con búsqueda --}}
                        <div style="position: relative; max-width: 280px; width: 100%;">
                            <input type="text" id="selectorProductoBuscar"
                                class="form-control form-control-sm bg-dark text-light border-secondary"
                                placeholder="🔍 Buscar producto…" autocomplete="off" style="padding-right: 2rem;">
                            <div id="selectorProductoDropdown" class="bg-dark border border-secondary rounded"
                                style="display:none; position:absolute; top:100%; left:0; right:0; z-index:999; max-height:220px; overflow-y:auto;">
                                @foreach($productos as $producto)
                                    <div class="px-3 py-2 text-light selector-option"
                                        style="cursor:pointer; font-size:0.85rem;" data-id="{{ $producto->id }}"
                                        data-nombre="{{ $producto->nombre }}" data-unidad="{{ $producto->unidad }}"
                                        onmouseover="this.style.background='#2d2d2d'"
                                        onmouseout="this.style.background='transparent'">
                                        {{ $producto->nombre }}
                                    </div>
                                @endforeach
                            </div>
                            {{-- Campo oculto que guarda el id seleccionado --}}
                            <select id="selectorProducto" class="d-none">
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                        data-unidad="{{ $producto->unidad }}">
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
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
        {{-- SECCIÓN 4D: GRÁFICA DE MOTIVOS DE CONSUMO (NUEVA) --}}
        {{-- ============================================================ --}}
        <div class="row mb-5">
            <div class="col-lg-6">
                <div class="card bg-black border-secondary h-100">
                    <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase">
                        Motivos de Consumo (Total acumulado)
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="chartMotivos" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center">
                <div class="w-100">
                    <div class="mb-3 p-3 rounded border border-secondary bg-black">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span
                                style="width:14px;height:14px;border-radius:3px;background:rgba(32,201,151,0.7);display:inline-block;"></span>
                            <span class="small text-light fw-semibold">🍳 Uso en cocina</span>
                        </div>
                        <p class="text-secondary small mb-0">Retiro normal por parte del cocinero para preparar
                            platillos.</p>
                    </div>
                    <div class="mb-3 p-3 rounded border border-secondary bg-black">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span
                                style="width:14px;height:14px;border-radius:3px;background:rgba(255,193,7,0.7);display:inline-block;"></span>
                            <span class="small text-light fw-semibold">⚠️ Producto dañado al recibir</span>
                        </div>
                        <p class="text-secondary small mb-0">Producto que llegó en mal estado y no fue detectado al
                            momento de la compra.</p>
                    </div>
                    <div class="p-3 rounded border border-secondary bg-black">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span
                                style="width:14px;height:14px;border-radius:3px;background:rgba(220,53,69,0.7);display:inline-block;"></span>
                            <span class="small text-light fw-semibold">🗓️ Producto vencido</span>
                        </div>
                        <p class="text-secondary small mb-0">Producto que llegó a su fecha de caducidad sin ser
                            utilizado.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECCIÓN 5: KARDEX --}}
        {{-- ============================================================ --}}
        <h2 class="fw-light mb-3 text-secondary">Kardex de Movimientos</h2>

        {{-- Controles superiores --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                <label class="text-secondary small mb-0">Mostrar</label>
                <select id="kardex_per_page" class="form-select form-select-sm bg-dark text-light border-secondary"
                    style="width:auto;">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                </select>
                <span class="text-secondary small">por página</span>
            </div>
            <input type="text" id="kardex_buscar"
                class="form-control form-control-sm bg-dark text-light border-secondary"
                placeholder="🔍 Filtrar por producto, usuario, tipo…" style="max-width: 280px;">
        </div>

        <div class="table-responsive mb-2 shadow-sm">
            <table class="table table-dark table-sm table-hover border-secondary" id="kardexTable">
                <thead class="bg-black text-secondary small text-uppercase"
                    style="position:sticky;top:0;z-index:1;background-color:#0a0a0a;">
                    <tr>
                        <th class="fw-normal py-2 px-3">Fecha</th>
                        <th class="fw-normal py-2">Producto</th>
                        <th class="fw-normal py-2 text-center">Tipo</th>
                        <th class="fw-normal py-2">Cantidad</th>
                        <th class="fw-normal py-2">Motivo</th>
                        <th class="fw-normal py-2">Usuario</th>
                    </tr>
                </thead>
                <tbody id="kardexBody">
                    @foreach($historial as $mov)
                        <tr class="border-secondary align-middle kardex-row"
                            data-texto="{{ strtolower($mov->producto->nombre . ' ' . $mov->tipo . ' ' . ($mov->user->name ?? '') . ' ' . ($mov->motivo ?? '')) }}">
                            <td class="small text-secondary px-3">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $mov->producto->nombre }}</td>
                            <td class="text-center">
                                <span
                                    class="badge {{ $mov->tipo == 'entrada' ? 'bg-success' : 'bg-info' }} bg-opacity-25 {{ $mov->tipo == 'entrada' ? 'text-success' : 'text-info' }} border {{ $mov->tipo == 'entrada' ? 'border-success' : 'border-info' }} px-2">
                                    {{ strtoupper($mov->tipo) }}
                                </span>
                            </td>
                            <td class="fw-bold">{{ $mov->cantidad }} {{ $mov->producto->unidad }}</td>
                            <td class="small">
                                @if($mov->motivo === 'uso_cocina')
                                    <span class="text-success">🍳 Uso en cocina</span>
                                @elseif($mov->motivo === 'producto_danado')
                                    <span class="text-warning">⚠️ Dañado al recibir</span>
                                @elseif($mov->motivo === 'producto_vencido')
                                    <span class="text-danger">🗓️ Vencido</span>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
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

        {{-- Paginación y contador --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-5">
            <span id="kardex_info" class="text-secondary small"></span>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="kardex_pagination"></ul>
            </nav>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const productos = @json($productos);

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

            // Gráfica Semáforo (Bar) — todos los productos con scroll horizontal
            const todosStockProductos = productos; // todos, no solo top 10
            const barWidth = 48; // px por barra
            const minChartWidth = Math.max(todosStockProductos.length * barWidth, 600);
            const wrapper = document.getElementById('chartStockWrapper');
            wrapper.style.width = minChartWidth + 'px';

            new Chart(document.getElementById('chartStock'), {
                type: 'bar',
                data: {
                    labels: todosStockProductos.map(p => p.nombre),
                    datasets: [{
                        label: 'Nivel de Stock',
                        data: todosStockProductos.map(p => p.stock_actual),
                        backgroundColor: todosStockProductos.map(p => {
                            if (p.stock_actual <= p.stock_minimo) return 'rgba(220, 53, 69, 0.7)';
                            else if (p.stock_actual <= p.stock_minimo * 2) return 'rgba(255, 193, 7, 0.7)';
                            else return 'rgba(25, 135, 84, 0.7)';
                        }),
                        borderColor: todosStockProductos.map(p => {
                            if (p.stock_actual <= p.stock_minimo) return '#dc3545';
                            if (p.stock_actual <= p.stock_minimo * 2) return '#ffc107';
                            return '#198754';
                        }),
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#2d2d2d' }, ticks: { color: '#6c757d' } },
                        x: { grid: { display: false }, ticks: { color: '#6c757d', font: { size: 10 }, maxRotation: 45, minRotation: 30 } }
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

            // Gráfica Tendencia (Line)
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

            // Consumo por Categoría (Líneas múltiples)
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

            // Top 10 Productos (Barras horizontales)
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

            // Consumo individual por producto
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
                if (chartIndividual) chartIndividual.destroy();

                chartIndividual = new Chart(ctxIndividual, {
                    type: 'bar',
                    data: {
                        labels: datos.map(d => d.fecha),
                        datasets: [{
                            label: `${nombre} (${unidad})`,
                            data: datos.map(d => d.total),
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

            // ── SELECTOR DE PRODUCTO PARA GRÁFICA INDIVIDUAL ─────────────
            // Inicializar con el primer producto del dropdown
            const primerOpcionDropdown = document.querySelector('.selector-option');
            if (primerOpcionDropdown) {
                document.getElementById('selectorProductoBuscar').value = primerOpcionDropdown.dataset.nombre;
                renderChartIndividual(
                    primerOpcionDropdown.dataset.id,
                    primerOpcionDropdown.dataset.nombre,
                    primerOpcionDropdown.dataset.unidad
                );
            }

            // El <select> oculto ya no controla la gráfica; se elimina su listener.

            // ── BUSCADOR SELECTOR HISTORIAL ───────────────────────────────
            function initSearchableDropdown(inputId, dropdownId, optionClass, onSelect) {
                const input = document.getElementById(inputId);
                const dropdown = document.getElementById(dropdownId);
                if (!input || !dropdown) return;


                input.addEventListener('focus', () => {
                    input.value = '';
                    filterOptions('', dropdown, optionClass);
                    dropdown.style.display = 'block';
                });


                input.addEventListener('input', () => {
                    filterOptions(input.value, dropdown, optionClass);
                    dropdown.style.display = 'block';
                });
                document.addEventListener('click', (e) => {
                    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.style.display = 'none';
                    }
                });
                dropdown.querySelectorAll('.' + optionClass).forEach(opt => {
                    opt.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        input.value = opt.dataset.nombre;
                        dropdown.style.display = 'none';
                        onSelect(opt);
                    });
                });
            }

            function filterOptions(query, dropdown, optionClass) {
                const q = query.toLowerCase().trim();
                dropdown.querySelectorAll('.' + optionClass).forEach(opt => {
                    const match = opt.dataset.nombre.toLowerCase().includes(q);
                    opt.style.display = match ? 'block' : 'none';
                });
            }

            // Historial de consumo por producto
            initSearchableDropdown('selectorProductoBuscar', 'selectorProductoDropdown', 'selector-option', (opt) => {
                renderChartIndividual(opt.dataset.id, opt.dataset.nombre, opt.dataset.unidad);
            });

            // Agregar lote
            initSearchableDropdown('lote_buscar', 'lote_dropdown', 'lote-option', (opt) => {
                document.getElementById('lote_producto_id').value = opt.dataset.id;
            });

            // Consumir
            initSearchableDropdown('consumir_buscar', 'consumir_dropdown', 'consumir-option', (opt) => {
                document.getElementById('consumir_producto_id').value = opt.dataset.id;
            });

            // ── GRÁFICA DE MOTIVOS (Doughnut — NUEVA) ────────────────────────
            const motivoData = @json($motivoData);

            new Chart(document.getElementById('chartMotivos'), {
                type: 'doughnut',
                data: {
                    labels: ['🍳 Uso en cocina', '⚠️ Dañado al recibir', '🗓️ Vencido'],
                    datasets: [{
                        data: [
                            motivoData.uso_cocina,
                            motivoData.producto_danado,
                            motivoData.producto_vencido
                        ],
                        backgroundColor: [
                            'rgba(32, 201, 151, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(220, 53, 69, 0.7)'
                        ],
                        borderColor: [
                            '#20c997',
                            '#ffc107',
                            '#dc3545'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#adb5bd', font: { size: 12 }, padding: 16 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = ctx.parsed;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${val} unidades (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });

        // ── KARDEX PAGINACIÓN ─────────────────────────────────────────────
        (function () {
            let currentPage = 1;
            const perPageSel = document.getElementById('kardex_per_page');
            const buscarInput = document.getElementById('kardex_buscar');
            const infoEl = document.getElementById('kardex_info');
            const paginationEl = document.getElementById('kardex_pagination');

            function getAllRows() {
                return Array.from(document.querySelectorAll('.kardex-row'));
            }

            function getFilteredRows() {
                const q = buscarInput.value.toLowerCase().trim();
                return getAllRows().filter(row => !q || row.dataset.texto.includes(q));
            }

            function render() {
                const perPage = parseInt(perPageSel.value);
                const rows = getFilteredRows();
                const total = rows.length;
                const totalPages = Math.max(1, Math.ceil(total / perPage));
                currentPage = Math.min(currentPage, totalPages);

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;

                // Ocultar todas, mostrar solo las de la página actual
                getAllRows().forEach(r => r.style.display = 'none');
                rows.forEach((r, i) => {
                    r.style.display = (i >= start && i < end) ? '' : 'none';
                });

                // Info
                const from = total === 0 ? 0 : start + 1;
                const to = Math.min(end, total);
                infoEl.textContent = `Mostrando ${from}–${to} de ${total} movimientos`;

                // Paginación
                paginationEl.innerHTML = '';

                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
                prevLi.innerHTML = `<a class="page-link bg-dark text-light border-secondary" href="#">‹</a>`;
                prevLi.querySelector('a').addEventListener('click', (e) => {
                    e.preventDefault(); if (currentPage > 1) { currentPage--; render(); }
                });
                paginationEl.appendChild(prevLi);

                // Números — mostrar hasta 5 páginas centradas
                const range = 2;
                for (let p = 1; p <= totalPages; p++) {
                    if (p === 1 || p === totalPages || (p >= currentPage - range && p <= currentPage + range)) {
                        const li = document.createElement('li');
                        li.className = 'page-item' + (p === currentPage ? ' active' : '');
                        li.innerHTML = `<a class="page-link border-secondary ${p === currentPage ? 'bg-secondary text-light' : 'bg-dark text-secondary'}" href="#">${p}</a>`;
                        li.querySelector('a').addEventListener('click', (e) => {
                            e.preventDefault(); currentPage = p; render();
                        });
                        paginationEl.appendChild(li);
                    } else if (
                        (p === currentPage - range - 1 && p > 1) ||
                        (p === currentPage + range + 1 && p < totalPages)
                    ) {
                        const li = document.createElement('li');
                        li.className = 'page-item disabled';
                        li.innerHTML = `<span class="page-link bg-dark text-secondary border-secondary">…</span>`;
                        paginationEl.appendChild(li);
                    }
                }

                // Next
                const nextLi = document.createElement('li');
                nextLi.className = 'page-item' + (currentPage === totalPages ? ' disabled' : '');
                nextLi.innerHTML = `<a class="page-link bg-dark text-light border-secondary" href="#">›</a>`;
                nextLi.querySelector('a').addEventListener('click', (e) => {
                    e.preventDefault(); if (currentPage < totalPages) { currentPage++; render(); }
                });
                paginationEl.appendChild(nextLi);
            }

            perPageSel.addEventListener('change', () => { currentPage = 1; render(); });
            buscarInput.addEventListener('input', () => { currentPage = 1; render(); });
            render();
        })();
    </script>
</body>

</html>