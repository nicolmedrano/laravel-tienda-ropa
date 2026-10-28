<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            ['nombre_empresa' => 'Textiles & Confecciones Alta Moda S.A.', 'contacto' => 'Mariana Restrepo', 'telefono' => '+591 71234567', 'email' => 'contacto@altamodatextil.com', 'direccion' => 'Av. Industrial 1450, Zona Fabril'],
            ['nombre_empresa' => 'Distribuidora Lino & Algodón Premium', 'contacto' => 'Roberto Paz', 'telefono' => '+591 76543210', 'email' => 'ventas@linoalgodon.com', 'direccion' => 'Calle Comercio 890, Centro'],
            ['nombre_empresa' => 'Moda & Estilo Internacional Ltda.', 'contacto' => 'Claudia Montero', 'telefono' => '+591 72345678', 'email' => 'c.montero@modaint.com', 'direccion' => 'Parque Empresarial Torre 2, Of. 401'],
        ];

        foreach ($proveedores as $p) {
            Proveedor::firstOrCreate(
                ['nombre_empresa' => $p['nombre_empresa']],
                array_merge($p, ['estado' => true])
            );
        }
    }
}
