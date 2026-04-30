<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios — StockRest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        ::placeholder {
            color: #6c757d !important;
            opacity: 1;
        }
    </style>
</head>

<body class="bg-dark text-light">

    <nav class="navbar navbar-dark bg-black border-bottom border-secondary px-3">
        <a class="navbar-brand fw-light" href="{{ url('/dashboard') }}">StockRest</a>
        <div class="d-flex gap-3 align-items-center">
            <span class="text-secondary small">{{ auth()->user()->name }}
                <span class="badge bg-secondary ms-1">{{ auth()->user()->rol }}</span>
            </span>
            <a href="{{ url('/dashboard') }}" class="nav-link text-secondary">Dashboard</a>
            <a href="{{ url('/contact') }}" class="nav-link text-secondary">Contacto</a>
            <a href="{{ url('/usuarios') }}" class="nav-link text-light">Usuarios</a>
            <form method="POST" action="/logout" class="mb-0">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">Salir</button>
            </form>
        </div>
    </nav>

    <div class="container py-4">

        <h1 class="fw-light mb-4">Gestión de Usuarios</h1>

        @if(session('success'))
            <div class="alert alert-success bg-success bg-opacity-10 border-success text-success small mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger small mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabla de usuarios existentes --}}
        <div class="table-responsive mb-5">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-uppercase small text-secondary fw-normal">Nombre</th>
                        <th class="text-uppercase small text-secondary fw-normal">Correo</th>
                        <th class="text-uppercase small text-secondary fw-normal">Rol</th>
                        <th class="text-uppercase small text-secondary fw-normal">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge 
                                    @if($usuario->rol === 'administrador') bg-danger
                                    @elseif($usuario->rol === 'gerente') bg-warning text-dark
                                    @else bg-secondary
                                    @endif">
                                    {{ $usuario->rol }}
                                </span>
                            </td>
                            <td>
                                @if($usuario->id !== auth()->id())
                                    <form method="POST" action="/usuarios/{{ $usuario->id }}"
                                        onsubmit="return confirm('¿Eliminar a {{ $usuario->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                @else
                                    <span class="text-secondary small">— tú —</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Formulario crear usuario --}}
        <h2 class="fw-light mb-3">Crear Usuario</h2>
        <div class="card bg-black border-secondary">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger small mb-3">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form method="POST" action="/usuarios">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label text-secondary small">Nombre</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control bg-dark text-light border-secondary" placeholder="Nombre completo"
                                required>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="form-label text-secondary small">Correo</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control bg-dark text-light border-secondary"
                                placeholder="correo@ejemplo.com" required>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label text-secondary small">Rol</label>
                            <select name="rol" class="form-select bg-dark text-light border-secondary" required>
                                <option value="">— elegir —</option>
                                <option value="administrador" {{ old('rol') === 'administrador' ? 'selected' : '' }}>
                                    Administrador</option>
                                <option value="gerente" {{ old('rol') === 'gerente' ? 'selected' : '' }}>Gerente</option>
                                <option value="cocinero" {{ old('rol') === 'cocinero' ? 'selected' : '' }}>Cocinero
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label text-secondary small">Contraseña</label>
                            <input type="password" name="password"
                                class="form-control bg-dark text-light border-secondary"
                                placeholder="Mínimo 6 caracteres" required>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <label class="form-label text-secondary small">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation"
                                class="form-control bg-dark text-light border-secondary"
                                placeholder="Repetir contraseña" required>
                        </div>
                        <div class="col-md-12 col-lg-auto">
                            <button type="submit" class="btn btn-outline-light w-100">Crear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>