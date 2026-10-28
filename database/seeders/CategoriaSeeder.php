<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Blusas y Camisas', 'descripcion' => 'Prendas superiores elegantes, formales y casuales en seda, lino y algodón'],
            ['nombre' => 'Pantalones', 'descripcion' => 'Pantalones de corte palazzo, pitillo, sastre y jeans contemporáneos'],
            ['nombre' => 'Vestidos', 'descripcion' => 'Vestidos de fiesta, midi casuales, cóctel y vestidos de verano'],
            ['nombre' => 'Chaquetas y Blazers', 'descripcion' => 'Abrigos ligeros, blazers estructurados y chaquetas de piel y paño'],
            ['nombre' => 'Faldas', 'descripcion' => 'Faldas plisadas, midi, tubo y minis con acabados sofisticados'],
            ['nombre' => 'Accesorios', 'descripcion' => 'Cinturones de cuero, bufandas de seda, pashminas y complementos'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(['nombre' => $cat['nombre']], array_merge($cat, ['estado' => true]));
        }
    }
}
