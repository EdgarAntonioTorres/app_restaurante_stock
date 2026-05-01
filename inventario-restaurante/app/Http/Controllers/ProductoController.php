<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Lote;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('lotes')->get();
        $stock_bajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();
        $por_caducar = Lote::where('fecha_caducidad', '<=', now()->addDays(3))->get();

        $historial = Movimiento::with(['producto', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $consumoDiario = Movimiento::where('tipo', 'salida')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as fecha, SUM(cantidad) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        $consumoPorCategoria = Movimiento::where('movimientos.tipo', 'salida')
            ->where('movimientos.created_at', '>=', now()->subDays(7))
            ->join('productos', 'movimientos.producto_id', '=', 'productos.id')
            ->selectRaw('DATE(movimientos.created_at) as fecha, productos.categoria, SUM(movimientos.cantidad) as total')
            ->groupBy('fecha', 'productos.categoria')
            ->orderBy('fecha', 'asc')
            ->get();

        $consumoPorProducto = Movimiento::where('movimientos.tipo', 'salida')
            ->where('movimientos.created_at', '>=', now()->subDays(7))
            ->join('productos', 'movimientos.producto_id', '=', 'productos.id')
            ->selectRaw('productos.nombre, productos.unidad, SUM(movimientos.cantidad) as total')
            ->groupBy('productos.nombre', 'productos.unidad')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $consumoIndividual = Movimiento::where('movimientos.tipo', 'salida')
            ->where('movimientos.created_at', '>=', now()->subDays(30))
            ->join('productos', 'movimientos.producto_id', '=', 'productos.id')
            ->selectRaw('movimientos.producto_id, DATE(movimientos.created_at) as fecha, SUM(movimientos.cantidad) as total')
            ->groupBy('movimientos.producto_id', 'fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        // ── Datos para gráfica de motivos de consumo ─────────────────
        $consumoPorMotivo = Movimiento::where('tipo', 'salida')
            ->whereNotNull('motivo')
            ->selectRaw('motivo, SUM(cantidad) as total')
            ->groupBy('motivo')
            ->pluck('total', 'motivo');

        $motivoData = [
            'uso_cocina'       => $consumoPorMotivo['uso_cocina']       ?? 0,
            'producto_danado'  => $consumoPorMotivo['producto_danado']  ?? 0,
            'producto_vencido' => $consumoPorMotivo['producto_vencido'] ?? 0,
        ];

        return view('dashboard', compact(
            'productos',
            'stock_bajo',
            'por_caducar',
            'historial',
            'consumoDiario',
            'consumoPorCategoria',
            'consumoPorProducto',
            'consumoIndividual',
            'motivoData'
        ));
    }

    public function create() {}

    public function store(Request $request)
    {
        Producto::create($request->all());
        return redirect('/dashboard')->with('success', 'Producto creado');
    }

    public function show(Producto $producto) {}

    public function edit(Producto $producto) {}

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $producto->update([
            'nombre'      => $request->nombre,
            'categoria'   => $request->categoria,
            'unidad'      => $request->unidad,
            'stock_minimo' => $request->stock_minimo,
        ]);

        return redirect('/dashboard')->with('success', 'Producto actualizado con éxito');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->lotes()->delete();
        $producto->delete();

        return redirect('/dashboard')->with('success', 'Producto eliminado del sistema');
    }

    public function consumir(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
            'motivo'      => 'required|in:uso_cocina,producto_danado,producto_vencido',
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $cantidad = (int) $request->cantidad;

        if ($cantidad > $producto->stock_actual) {
            return redirect('/dashboard')->with(
                'error',
                "Stock insuficiente. Solo hay {$producto->stock_actual} {$producto->unidad} disponibles de \"{$producto->nombre}\"."
            );
        }

        // ── Consumo FIFO por lotes ──────────────────────────────────
        $restante = $cantidad;
        $lotes = Lote::where('producto_id', $producto->id)
            ->orderBy('fecha_caducidad', 'asc')
            ->get();

        foreach ($lotes as $lote) {
            if ($restante <= 0) break;

            if ($lote->cantidad <= $restante) {
                $restante -= $lote->cantidad;
                $lote->delete();
            } else {
                $lote->cantidad -= $restante;
                $lote->save();
                $restante = 0;
            }
        }

        $producto->stock_actual -= $cantidad;
        $producto->save();

        Movimiento::create([
            'producto_id' => $producto->id,
            'user_id'     => Auth::id(),
            'tipo'        => 'salida',
            'cantidad'    => $cantidad,
            'motivo'      => $request->motivo,
        ]);

        return redirect('/dashboard')->with('success', 'Consumo registrado');
    }

    public function alertas()
    {
        $stock_bajo  = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();
        $por_caducar = Lote::where('fecha_caducidad', '<=', now()->addDays(3))->get();

        return response()->json([
            'stock_bajo'  => $stock_bajo,
            'por_caducar' => $por_caducar,
        ]);
    }
}