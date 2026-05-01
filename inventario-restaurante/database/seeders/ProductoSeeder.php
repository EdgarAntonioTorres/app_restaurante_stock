<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Pastas
            ['nombre' => 'Pasta spaghetti', 'categoria' => 'Pastas', 'unidad' => 'kg', 'stock_actual' => 15, 'stock_minimo' => 5],
            ['nombre' => 'Pasta penne', 'categoria' => 'Pastas', 'unidad' => 'kg', 'stock_actual' => 12, 'stock_minimo' => 5],
            ['nombre' => 'Pasta fettuccine', 'categoria' => 'Pastas', 'unidad' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 4],
            ['nombre' => 'Harina 00', 'categoria' => 'Pastas', 'unidad' => 'kg', 'stock_actual' => 20, 'stock_minimo' => 8],

            // Lácteos
            ['nombre' => 'Queso parmesano', 'categoria' => 'Lácteos', 'unidad' => 'kg', 'stock_actual' => 5, 'stock_minimo' => 3],
            ['nombre' => 'Queso mozzarella', 'categoria' => 'Lácteos', 'unidad' => 'kg', 'stock_actual' => 6, 'stock_minimo' => 3],
            ['nombre' => 'Crema para cocinar', 'categoria' => 'Lácteos', 'unidad' => 'litros', 'stock_actual' => 8, 'stock_minimo' => 3],
            ['nombre' => 'Mantequilla', 'categoria' => 'Lácteos', 'unidad' => 'kg', 'stock_actual' => 4, 'stock_minimo' => 2],

            // Proteínas
            ['nombre' => 'Carne molida de res', 'categoria' => 'Proteínas', 'unidad' => 'kg', 'stock_actual' => 8, 'stock_minimo' => 4],
            ['nombre' => 'Panceta', 'categoria' => 'Proteínas', 'unidad' => 'kg', 'stock_actual' => 4, 'stock_minimo' => 2],
            ['nombre' => 'Huevo', 'categoria' => 'Proteínas', 'unidad' => 'piezas', 'stock_actual' => 60, 'stock_minimo' => 24],

            // Verduras
            ['nombre' => 'Tomate bola', 'categoria' => 'Verduras', 'unidad' => 'kg', 'stock_actual' => 10, 'stock_minimo' => 5],
            ['nombre' => 'Ajo', 'categoria' => 'Verduras', 'unidad' => 'kg', 'stock_actual' => 2, 'stock_minimo' => 1],
            ['nombre' => 'Cebolla blanca', 'categoria' => 'Verduras', 'unidad' => 'kg', 'stock_actual' => 5, 'stock_minimo' => 3],
            ['nombre' => 'Albahaca fresca', 'categoria' => 'Verduras', 'unidad' => 'kg', 'stock_actual' => 1, 'stock_minimo' => 1],
            ['nombre' => 'Champiñones', 'categoria' => 'Verduras', 'unidad' => 'kg', 'stock_actual' => 3, 'stock_minimo' => 2],

            // Aceites y conservas
            ['nombre' => 'Aceite de oliva EV', 'categoria' => 'Aceites', 'unidad' => 'litros', 'stock_actual' => 10, 'stock_minimo' => 4],
            ['nombre' => 'Tomate triturado', 'categoria' => 'Conservas', 'unidad' => 'kg', 'stock_actual' => 15, 'stock_minimo' => 6],

            // Panadería
            ['nombre' => 'Pan ciabatta', 'categoria' => 'Panadería', 'unidad' => 'piezas', 'stock_actual' => 20, 'stock_minimo' => 10],
            ['nombre' => 'Masa para pizza', 'categoria' => 'Panadería', 'unidad' => 'kg', 'stock_actual' => 6, 'stock_minimo' => 3],
        ];

        foreach ($productos as $data) {
            Producto::create($data);
        }
    }
}