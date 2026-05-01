<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lote;
use App\Models\Producto;

class LoteSeeder extends Seeder
{
    public function run(): void
    {
        $lote = function (string $nombre, int $cantidad, int $diasCaducidad) {
            $producto = Producto::where('nombre', $nombre)->first();
            if (!$producto)
                return;

            Lote::create([
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'fecha_ingreso' => now()->subDays(rand(1, 4))->toDateString(),
                'fecha_caducidad' => now()->addDays($diasCaducidad)->toDateString(),
            ]);
        };

        // Pastas secas — larga caducidad
        $lote('Pasta spaghetti', 15, 365);
        $lote('Pasta penne', 12, 365);
        $lote('Pasta fettuccine', 10, 365);
        $lote('Harina 00', 20, 180);

        // Lácteos — dos lotes: uno próximo a vencer para activar la alerta
        $lote('Queso parmesano', 2, 2);  // vence en 2 días
        $lote('Queso parmesano', 3, 20);
        $lote('Queso mozzarella', 3, 3);  // vence en 3 días
        $lote('Queso mozzarella', 3, 12);
        $lote('Crema para cocinar', 8, 10);
        $lote('Mantequilla', 4, 25);

        // Proteínas — perecederos
        $lote('Carne molida de res', 8, 3);  // vence en 3 días
        $lote('Panceta', 4, 7);
        $lote('Huevo', 60, 21);

        // Verduras frescas
        $lote('Tomate bola', 10, 7);
        $lote('Ajo', 2, 30);
        $lote('Cebolla blanca', 5, 15);
        $lote('Albahaca fresca', 1, 2);  // vence en 2 días
        $lote('Champiñones', 3, 5);

        // Aceites y conservas
        $lote('Aceite de oliva EV', 10, 365);
        $lote('Tomate triturado', 15, 365);

        // Panadería — caducidad muy corta
        $lote('Pan ciabatta', 20, 2);  // vence en 2 días
        $lote('Masa para pizza', 6, 3);  // vence en 3 días
    }
}