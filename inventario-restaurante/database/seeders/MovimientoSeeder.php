<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\User;

class MovimientoSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('rol', 'administrador')->first()?->id ?? 1;
        $gerenteId = User::where('rol', 'gerente')->first()?->id ?? 1;
        $cocineroId = User::where('rol', 'cocinero')->first()?->id ?? 1;

        // ── Entradas iniciales (sin motivo, son entradas no salidas) ──────
        $entradas = [
            ['Pasta spaghetti', 20, $adminId, 30],
            ['Pasta penne', 15, $adminId, 30],
            ['Pasta fettuccine', 12, $adminId, 28],
            ['Harina 00', 25, $adminId, 28],
            ['Queso parmesano', 6, $gerenteId, 25],
            ['Queso mozzarella', 8, $gerenteId, 25],
            ['Crema para cocinar', 10, $gerenteId, 22],
            ['Tomate triturado', 20, $adminId, 20],
            ['Aceite de oliva EV', 12, $adminId, 20],
            ['Carne molida de res', 10, $gerenteId, 18],
            ['Huevo', 90, $adminId, 15],
            ['Pan ciabatta', 30, $gerenteId, 10],
            ['Masa para pizza', 10, $gerenteId, 10],
        ];

        foreach ($entradas as [$nombre, $cantidad, $userId, $diasAtras]) {
            $producto = Producto::where('nombre', $nombre)->first();
            if (!$producto)
                continue;

            $fecha = now()->subDays($diasAtras);
            Movimiento::create([
                'producto_id' => $producto->id,
                'user_id' => $userId,
                'tipo' => 'entrada',
                'cantidad' => $cantidad,
                'motivo' => null,   // las entradas no llevan motivo
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
        }

        // ── Salidas diarias — últimos 30 días ─────────────────────────────
        // La mayoría son uso_cocina; algunos días se registran dañados/vencidos
        // [nombre, cantidad_base, usuario, motivo]
        $consumosDiarios = [
            ['Pasta spaghetti', 2, $cocineroId, 'uso_cocina'],
            ['Pasta penne', 2, $cocineroId, 'uso_cocina'],
            ['Pasta fettuccine', 1, $cocineroId, 'uso_cocina'],
            ['Queso parmesano', 1, $cocineroId, 'uso_cocina'],
            ['Queso mozzarella', 1, $cocineroId, 'uso_cocina'],
            ['Tomate triturado', 2, $cocineroId, 'uso_cocina'],
            ['Aceite de oliva EV', 1, $cocineroId, 'uso_cocina'],
            ['Carne molida de res', 2, $cocineroId, 'uso_cocina'],
            ['Huevo', 8, $cocineroId, 'uso_cocina'],
            ['Pan ciabatta', 5, $gerenteId, 'uso_cocina'],
            ['Masa para pizza', 2, $gerenteId, 'uso_cocina'],
            ['Albahaca fresca', 1, $cocineroId, 'uso_cocina'],
            ['Champiñones', 1, $cocineroId, 'uso_cocina'],
        ];

        // Días donde se registran mermas (dañado o vencido) — más realista
        $mermas = [
            // [diasAtras, nombre, cantidad, motivo]
            [28, 'Queso mozzarella', 1, 'producto_danado'],
            [21, 'Carne molida de res', 1, 'producto_vencido'],
            [18, 'Pan ciabatta', 3, 'producto_vencido'],
            [14, 'Albahaca fresca', 1, 'producto_vencido'],
            [10, 'Crema para cocinar', 1, 'producto_danado'],
            [7, 'Queso parmesano', 1, 'producto_vencido'],
            [5, 'Masa para pizza', 1, 'producto_vencido'],
            [3, 'Champiñones', 1, 'producto_danado'],
            [2, 'Tomate bola', 2, 'producto_vencido'],
        ];

        // Registrar mermas en fechas específicas
        foreach ($mermas as [$diasAtras, $nombre, $cantidad, $motivo]) {
            $producto = Producto::where('nombre', $nombre)->first();
            if (!$producto)
                continue;

            $fecha = now()->subDays($diasAtras);
            Movimiento::create([
                'producto_id' => $producto->id,
                'user_id' => $gerenteId,
                'tipo' => 'salida',
                'cantidad' => $cantidad,
                'motivo' => $motivo,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
        }

        // Registrar consumo diario normal
        for ($diasAtras = 30; $diasAtras >= 1; $diasAtras--) {
            if ($diasAtras % 7 === 0)
                continue; // día de cierre semanal

            $fecha = now()->subDays($diasAtras);

            foreach ($consumosDiarios as [$nombre, $cantidadBase, $userId, $motivo]) {
                $producto = Producto::where('nombre', $nombre)->first();
                if (!$producto)
                    continue;

                $cantidad = max(1, $cantidadBase + rand(-1, 1));

                Movimiento::create([
                    'producto_id' => $producto->id,
                    'user_id' => $userId,
                    'tipo' => 'salida',
                    'cantidad' => $cantidad,
                    'motivo' => $motivo,
                    'created_at' => $fecha,
                    'updated_at' => $fecha,
                ]);
            }
        }
    }
}