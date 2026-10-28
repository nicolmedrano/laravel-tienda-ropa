<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::firstOrCreate(
            ['usuario' => 'admin'],
            [
                'nombre' => 'Valentina',
                'apellido' => 'Nicol',
                'password' => Hash::make('admin123'),
                'rol' => 'administrador',
                'estado' => true,
            ]
        );

        Usuario::firstOrCreate(
            ['usuario' => 'vendedor'],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Mendoza',
                'password' => Hash::make('vendedor123'),
                'rol' => 'vendedor',
                'estado' => true,
            ]
        );

        Usuario::firstOrCreate(
            ['usuario' => 'sofia_ventas'],
            [
                'nombre' => 'Sofía',
                'apellido' => 'Herrera',
                'password' => Hash::make('vendedor123'),
                'rol' => 'vendedor',
                'estado' => true,
            ]
        );
    }
}
