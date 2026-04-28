<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Movimiento;

class LoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lote = Lote::create([
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'fecha_ingreso' => now(),
            'fecha_caducidad' => $request->fecha_caducidad
        ]);

        // actualizar stock
        $producto = Producto::find($request->producto_id);
        $producto->stock_actual += $request->cantidad;
        $producto->save();

        // registrar movimiento
        Movimiento::create([
            'producto_id' => $producto->id,
            'tipo' => 'entrada',
            'cantidad' => $request->cantidad
        ]);

        return redirect('/dashboard')->with('success', 'Lote agregado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lote $lote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lote $lote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lote $lote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lote $lote)
    {
        //
    }
}
