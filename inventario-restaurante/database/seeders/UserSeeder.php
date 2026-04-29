<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@stockrest.com',
            'rol'      => 'administrador',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Gerente',
            'email'    => 'gerente@stockrest.com',
            'rol'      => 'gerente',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name'     => 'Cocinero',
            'email'    => 'cocinero@stockrest.com',
            'rol'      => 'cocinero',
            'password' => Hash::make('password'),
        ]);
    }
}