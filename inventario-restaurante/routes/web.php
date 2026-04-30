<?php

use Illuminate\Support\Facades\Route;
use App\Models\Producto;
use App\Models\Lote;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// ── Auth ──────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Rutas protegidas ──────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // --- ACTUALIZADO: Ahora apunta al Controlador para que envíe $historial y $consumoDiario ---
    Route::get('/dashboard', [ProductoController::class, 'index'])->name('dashboard');

    Route::get('/contact', fn() => view('contact'))->name('contact');

    // ── Productos ──────────────────────────────────────
    // Crear producto
    Route::post('/productos', [ProductoController::class, 'store'])
        ->middleware('rol:administrador,gerente');

    // Editar producto (Actualizar)
    Route::put('/productos/{id}', [ProductoController::class, 'update'])
        ->middleware('rol:administrador,gerente');

    // Eliminar producto
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])
        ->middleware('rol:administrador,gerente');

    // ── Lotes y Consumo ────────────────────────────────
    // Agregar lote — administrador y gerente
    Route::post('/lotes', [LoteController::class, 'store'])
        ->middleware('rol:administrador,gerente');

    // Consumir — administrador, gerente y cocinero
    Route::post('/consumir', [ProductoController::class, 'consumir'])
        ->middleware('rol:administrador,gerente,cocinero');

    Route::get('/alertas', [ProductoController::class, 'alertas']);
});

// Gestión de usuarios — solo administrador
Route::middleware(['auth', 'rol:administrador'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index']);
    Route::post('/usuarios', [UserController::class, 'store']);
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy']);
});