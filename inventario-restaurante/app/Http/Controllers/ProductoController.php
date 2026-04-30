<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::with('lotes')->get();
        $stock_bajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();
        $por_caducar = Lote::where('fecha_caducidad', '<=', now()->addDays(3))->get();

        // --- NUEVO: Datos para el Kardex (Historial) ---
        $historial = Movimiento::with(['producto', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        // --- NUEVO: Datos para la Gráfica de Consumo (Últimos 7 días) ---
        $consumoDiario = Movimiento::where('tipo', 'salida')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as fecha, SUM(cantidad) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        return view('dashboard', compact('productos', 'stock_bajo', 'por_caducar', 'historial', 'consumoDiario'));
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
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $producto->update([
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'unidad' => $request->unidad,
            'stock_minimo' => $request->stock_minimo,
        ]);

        return redirect('/dashboard')->with('success', 'Producto actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        // Opcional: Eliminar lotes y movimientos asociados antes de borrar el producto
        $producto->lotes()->delete();
        $producto->delete();

        return redirect('/dashboard')->with('success', 'Producto eliminado del sistema');
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

        // --- ACTUALIZADO: Se agrega user_id para el Kardex ---
        // En el método consumir()
Movimiento::create([
    'producto_id' => $producto->id,
    // 'user_id' => Auth::id(),  <-- COMENTA O BORRA ESTA LÍNEA
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