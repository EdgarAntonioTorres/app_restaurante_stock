<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto: Sistema de Stock para Restaurante</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f7f6;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }

        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2980b9;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .team-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .team-list li {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 4px;
            font-weight: bold;
        }

        .tech-stack {
            font-style: italic;
            color: #555;
        }

        .citation {
            font-size: 0.85em;
            color: #7f8c8d;
            vertical-align: super;
        }
    </style>
</head>

<body>
    <nav style="background-color: #333; padding: 10px;">
        <a href="{{ url('/dashboard') }}" style="color: white; margin-right: 15px; text-decoration: none;">
            Dashboard
        </a>

        <a href="{{ url('/contact') }}" style="color: white; text-decoration: none;">
            Contacto
        </a>
    </nav>

    <header>
        <h1>Sistema de Control de Stock y Caducidad</h1>
        <p>Proyecto de Gestión de Inventarios para Restaurantes</p>
    </header>

    <div class="section">
        <h2>Creadores</h2>
        <ul class="team-list">
            <li>Edgar Antonio Torres Saavedra - 02955138</li>
            <li>José Carlos Arangua de Luna - 03096769</li>
            <li>Adrian Alejandro Gaspar Corona - 03093676</li>
            <li>Gerardo Martínez Puente - 05058584</li>
        </ul>
    </div>

    <div class="section">
        <h2>Alcance del Proyecto</h2>
        <ul>
            <li>Control integral de entradas y salidas de mercancía.</li>
            <li>Gestión de alertas para productos próximos a caducar.</li>
            <li>Monitoreo de consumo de insumos en tiempo real.</li>
            <li>Sistema de notificaciones para productos sin stock o vencidos.</li>
            <li>Administración de usuarios con roles específicos: Administrador y Empleado.</li>
        </ul>
    </div>

    <div class="section">
        <h2>Arquitectura y Tecnologías</h2>
        <p class="tech-stack">El sistema se basa en un stack moderno para garantizar escalabilidad y rendimiento:</p>
        <ul>
            <li><strong>Backend:</strong> PHP utilizando el framework Laravel.</li>
            <li><strong>Base de Datos:</strong> MySQL para la persistencia de datos.</li>
            <li><strong>Frontend:</strong> Motor de plantillas Blade de Laravel.</li>
            <li><strong>Comunicación en Tiempo Real:</strong> WebSockets mediante Laravel Echo y Pusher/Socket.io.</li>
            <li><strong>Infraestructura:</strong> Despliegue en AWS (Amazon Web Services).</li>
        </ul>
    </div>

    <div class="section">
        <h2>Lógica de Operación</h2>
        <ul>
            <li><strong>Gestión de Lotes:</strong> Al recibir mercancía se registra el lote junto a su fecha de
                caducidad.</li>
            <li><strong>FIFO Inteligente:</strong> Las salidas de almacén descuentan automáticamente del lote más
                próximo a caducar.</li>
            <li><strong>Automatización:</strong> Tareas programadas (Cron Jobs) que verifican diariamente el stock
                mínimo y vencimientos.</li>
        </ul>
    </div>

    <div class="section">
        <h2>Estructura de Datos Principal</h2>
        <ul>
            <li><strong>Productos:</strong> Control de nombre, categoría y unidades de medida (kg, piezas, litros).</li>
            <li><strong>Movimientos:</strong> Registro histórico detallado de cada entrada y salida.</li>
            <li><strong>Lotes:</strong> Seguimiento específico por fecha de ingreso y caducidad por cada producto.</li>
        </ul>
    </div>

</body>

</html>