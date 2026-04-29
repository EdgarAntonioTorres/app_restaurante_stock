<?php

use Illuminate\Support\Facades\Route;
use App\Models\Producto;
use App\Models\Lote;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\AuthController;

// ── Auth ──────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ── Rutas protegidas ──────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard — todos los roles autenticados
    Route::get('/dashboard', function () {
        $productos   = Producto::with('lotes')->get();
        $stock_bajo  = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();
        $por_caducar = Lote::where('fecha_caducidad', '<=', now()->addDays(3))->get();
        return view('dashboard', compact('productos', 'stock_bajo', 'por_caducar'));
    })->name('dashboard');

    Route::get('/contact', fn() => view('contact'))->name('contact');

    // Crear producto — solo administrador
    Route::post('/productos', [ProductoController::class, 'store'])
         ->middleware('rol:administrador');

    // Agregar lote — administrador y gerente
    Route::post('/lotes', [LoteController::class, 'store'])
         ->middleware('rol:administrador,gerente');

    // Consumir — administrador, gerente y cocinero
    Route::post('/consumir', [ProductoController::class, 'consumir'])
         ->middleware('rol:administrador,gerente,cocinero');

    Route::get('/alertas', [ProductoController::class, 'alertas']);
});