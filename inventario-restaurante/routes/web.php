<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Models\Producto;
use App\Models\Lote;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\LoteController;

Route::post('/productos', [ProductoController::class, 'store']);
Route::post('/lotes', [LoteController::class, 'store']);

Route::apiResource('productos', ProductoController::class);
Route::apiResource('lotes', LoteController::class);

Route::post('/consumir', [ProductoController::class, 'consumir']);
Route::get('/alertas', [ProductoController::class, 'alertas']);

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $productos = Producto::with('lotes')->get();

    $stock_bajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();

    $por_caducar = Lote::where('fecha_caducidad', '<=', now()->addDays(3))->get();

    return view('dashboard', compact('productos', 'stock_bajo', 'por_caducar'));
})->name('dashboard');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');