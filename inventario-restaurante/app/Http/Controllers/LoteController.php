<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;

class LoteController extends Controller
{
    public function index()
    {
    }
    public function create()
    {
    }

    public function store(Request $request)
    {
        $lote = Lote::create([
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'fecha_ingreso' => now(),
            'fecha_caducidad' => $request->fecha_caducidad,
        ]);

        // Actualizar stock
        $producto = Producto::find($request->producto_id);
        $producto->stock_actual += $request->cantidad;
        $producto->save();

        // Registrar movimiento con el usuario autenticado
        Movimiento::create([
            'producto_id' => $producto->id,
            'user_id' => Auth::id(),   // ← guarda quién ingresó el lote
            'tipo' => 'entrada',
            'cantidad' => $request->cantidad,
        ]);

        return redirect('/dashboard')->with('success', 'Lote agregado');
    }

    public function show(Lote $lote)
    {
    }
    public function edit(Lote $lote)
    {
    }
    public function update(Request $request, Lote $lote)
    {
    }
    public function destroy(Lote $lote)
    {
    }
}