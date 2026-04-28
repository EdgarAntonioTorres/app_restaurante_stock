<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto: Sistema de Stock para Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

    <nav class="navbar navbar-dark bg-black border-bottom border-secondary px-3">
        <a class="navbar-brand fw-light" href="{{ url('/dashboard') }}">StockRest</a>
        <div class="d-flex gap-3">
            <a href="{{ url('/dashboard') }}" class="nav-link text-secondary">Dashboard</a>
            <a href="{{ url('/contact') }}" class="nav-link text-light">Contacto</a>
        </div>
    </nav>

    <div class="container py-4">

        <div class="text-center py-5 mb-4 border border-secondary rounded-3 bg-black">
            <h1 class="fw-light">Sistema de Control de Stock y Caducidad</h1>
            <p class="text-secondary mb-0">Proyecto de Gestión de Inventarios para Restaurantes</p>
        </div>

        <div class="card bg-black border-secondary mb-4">
            <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase fw-normal">
                Creadores
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 border border-secondary rounded-2 text-light small">
                            Edgar Antonio Torres Saavedra <span class="text-secondary">— 02955138</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border border-secondary rounded-2 text-light small">
                            José Carlos Arangua de Luna <span class="text-secondary">— 03096769</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border border-secondary rounded-2 text-light small">
                            Adrian Alejandro Gaspar Corona <span class="text-secondary">— 03093676</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border border-secondary rounded-2 text-light small">
                            Gerardo Martínez Puente <span class="text-secondary">— 05058584</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-black border-secondary mb-4">
            <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase fw-normal">
                Alcance del Proyecto
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> Control integral de entradas y salidas de mercancía.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> Gestión de alertas para productos próximos a caducar.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> Monitoreo de consumo de insumos en tiempo real.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> Notificaciones para productos sin stock o vencidos.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> Roles de usuario: <strong>Administrador</strong> y
                    <strong>Empleado</strong>.
                </li>
            </ul>
        </div>

        <div class="card bg-black border-secondary mb-4">
            <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase fw-normal">
                Arquitectura y Tecnologías
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Backend:</strong> PHP con framework Laravel.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Base de Datos:</strong> MySQL.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Frontend:</strong> Motor de plantillas Blade de
                    Laravel.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Tiempo Real:</strong> WebSockets con Laravel Echo
                    y Pusher/Socket.io.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Infraestructura:</strong> Despliegue en AWS.
                </li>
            </ul>
        </div>

        <div class="card bg-black border-secondary mb-4">
            <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase fw-normal">
                Lógica de Operación
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Gestión de Lotes:</strong> Al recibir mercancía
                    se registra el lote con su fecha de caducidad.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>FIFO Inteligente:</strong> Las salidas descuentan
                    del lote más próximo a caducar automáticamente.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Automatización:</strong> Cron Jobs que verifican
                    diariamente stock mínimo y vencimientos.
                </li>
            </ul>
        </div>

        <div class="card bg-black border-secondary mb-4">
            <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase fw-normal">
                Estructura de Datos Principal
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Productos:</strong> Nombre, categoría y unidades
                    de medida (kg, piezas, litros).
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Movimientos:</strong> Registro histórico de cada
                    entrada y salida.
                </li>
                <li class="list-group-item bg-transparent border-secondary text-light">
                    <span class="text-secondary me-2">—</span> <strong>Lotes:</strong> Seguimiento por fecha de ingreso
                    y caducidad.
                </li>
            </ul>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>