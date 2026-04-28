<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Movimiento;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Producto::all();
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
        $producto = Producto::create($request->all());
        return redirect('/dashboard')->with('success', 'Producto creado');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        //
    }

    public function consumir(Request $request)
    {
        $cantidad = $request->cantidad;

        $lotes = Lote::where('producto_id', $request->producto_id)
            ->orderBy('fecha_caducidad', 'asc')
            ->get();

        foreach ($lotes as $lote) {
            if ($cantidad <= 0)
                break;

            if ($lote->cantidad <= $cantidad) {
                $cantidad -= $lote->cantidad;
                $lote->delete();
            } else {
                $lote->cantidad -= $cantidad;
                $lote->save();
                $cantidad = 0;
            }
        }

        $producto = Producto::find($request->producto_id);
        $producto->stock_actual -= $request->cantidad;
        $producto->save();

        Movimiento::create([
            'producto_id' => $producto->id,
            'tipo' => 'salida',
            'cantidad' => $request->cantidad
        ]);

        return redirect('/dashboard')->with('success', 'Consumo registrado');
    }

    public function alertas()
    {
        $stock_bajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();

        $por_caducar = Lote::where('fecha_caducidad', '<=', now()->addDays(3))->get();

        return response()->json([
            'stock_bajo' => $stock_bajo,
            'por_caducar' => $por_caducar
        ]);
    }
}
