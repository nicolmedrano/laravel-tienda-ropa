<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'Camila', 'apellido' => 'Vargas', 'telefono' => '+591 78901234', 'email' => 'camila.vargas@email.com', 'direccion' => 'Calle Los Rosales #342, Calacoto'],
            ['nombre' => 'Mateo', 'apellido' => 'Fernández', 'telefono' => '+591 79812345', 'email' => 'mateo.fernandez@email.com', 'direccion' => 'Av. Las Palmas #501, San Miguel'],
            ['nombre' => 'Luciana', 'apellido' => 'Morales', 'telefono' => '+591 71298765', 'email' => 'luciana.morales@email.com', 'direccion' => 'Av. Arce #1280, Edif. Platino'],
            ['nombre' => 'Andrés', 'apellido' => 'Guzmán', 'telefono' => '+591 73456789', 'email' => 'andres.guzman@email.com', 'direccion' => 'Calle 21 de San Miguel #110'],
            ['nombre' => 'Valeria', 'apellido' => 'Rios', 'telefono' => '+591 74567890', 'email' => 'valeria.rios@email.com', 'direccion' => 'Condominio La Florida Casa 14'],
        ];

        foreach ($clientes as $c) {
            Cliente::firstOrCreate(
                ['email' => $c['email']],
                array_merge($c, ['estado' => true])
            );
        }
    }
}
