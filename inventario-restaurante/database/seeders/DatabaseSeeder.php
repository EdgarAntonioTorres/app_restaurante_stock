<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,       // 1. Usuarios
            ProductoSeeder::class,   // 2. Productos
            LoteSeeder::class,       // 3. Lotes
            MovimientoSeeder::class, // 4. Movimientos
        ]);
    }
}