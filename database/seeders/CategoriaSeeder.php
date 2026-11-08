<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Blusas y Camisas',
                'descripcion' => 'Prendas superiores elegantes, formales y casuales en seda, lino y algodón',
                'usa_talla' => true,
            ],
            [
                'nombre' => 'Pantalones',
                'descripcion' => 'Pantalones de corte palazzo, pitillo, sastre y jeans contemporáneos',
                'usa_talla' => true,
            ],
            [
                'nombre' => 'Vestidos',
                'descripcion' => 'Vestidos de fiesta, midi casuales, cóctel y vestidos de verano',
                'usa_talla' => true,
            ],
            [
                'nombre' => 'Chaquetas y Blazers',
                'descripcion' => 'Abrigos ligeros, blazers estructurados y chaquetas de piel y paño',
                'usa_talla' => true,
            ],
            [
                'nombre' => 'Faldas',
                'descripcion' => 'Faldas plisadas, midi, tubo y minis con acabados sofisticados',
                'usa_talla' => true,
            ],
            [
                'nombre' => 'Accesorios',
                'descripcion' => 'Cinturones de cuero, bufandas de seda, pashminas y complementos',
                'usa_talla' => false,
            ],
            [
                'nombre' => 'Carteras',
                'descripcion' => 'Carteras de mano, bandoleras, bolsos ejecutivos y sobres de fiesta',
                'usa_talla' => false,
            ],
            [
                'nombre' => 'Bisutería',
                'descripcion' => 'Collares, pulseras, aretes, anillos y complementos finos',
                'usa_talla' => false,
            ],
        ];

        foreach ($categorias as $cat) {
            $categoria = Categoria::firstOrCreate(
                ['nombre' => $cat['nombre']],
                array_merge($cat, ['estado' => true])
            );

            // Si ya existía, asegurarse de que conserve o actualice su flag usa_talla
            if (!$categoria->wasRecentlyCreated && isset($cat['usa_talla'])) {
                $categoria->update(['usa_talla' => $cat['usa_talla']]);
            }
        }
    }
}
