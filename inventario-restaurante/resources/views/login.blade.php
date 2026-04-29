<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — StockRest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light d-flex align-items-center justify-content-center" style="min-height:100vh;">

    <div class="card bg-black border-secondary" style="width: 100%; max-width: 400px;">
        <div class="card-header bg-transparent border-secondary text-secondary small text-uppercase fw-normal">
            StockRest — Acceso
        </div>
        <div class="card-body p-4">
            <h4 class="fw-light mb-4">Iniciar Sesión</h4>

            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-secondary small">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control bg-dark text-light border-secondary" placeholder="usuario@ejemplo.com"
                        required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label text-secondary small">Contraseña</label>
                    <input type="password" name="password" class="form-control bg-dark text-light border-secondary"
                        placeholder="••••••••" required>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-secondary small" for="remember">
                        Recordarme
                    </label>
                </div>
                <button type="submit" class="btn btn-outline-light w-100">Entrar</button>
            </form>
        </div>
    </div>

</body>

</html>