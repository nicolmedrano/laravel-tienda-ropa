<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\ProductoVariante;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'codigo' => 'BLU-001',
                'nombre' => 'Blusa Chiffon Satinada',
                'descripcion' => 'Blusa de seda con cuello en V sutil y mangas abullonadas, tejido satinado de suave caída',
                'id_categoria' => 1,
                'precio_venta' => 48.00,
                'costo_compra' => 22.00,
                'stock_minimo' => 4,
                'imagen' => '/uploads/productos/prod-blusa-chiffon.svg',
                'variantes' => [
                    ['sku' => 'BLU-001-S-FUC', 'talla' => 'S', 'color' => 'Fucsia Intenso (#f70071)', 'stock' => 8],
                    ['sku' => 'BLU-001-M-MAG', 'talla' => 'M', 'color' => 'Magenta Vibrante (#ff1b82)', 'stock' => 12],
                    ['sku' => 'BLU-001-M-RCH', 'talla' => 'M', 'color' => 'Rosa Chicle (#ff5aa4)', 'stock' => 7],
                    ['sku' => 'BLU-001-L-RPA', 'talla' => 'L', 'color' => 'Rosa Pastel (#ff96c5)', 'stock' => 6],
                    ['sku' => 'BLU-001-XL-RCL', 'talla' => 'XL', 'color' => 'Rosa Claro (#ffc0dc)', 'stock' => 5],
                ]
            ],
            [
                'codigo' => 'PAN-002',
                'nombre' => 'Pantalón Palazzo de Lino',
                'descripcion' => 'Pantalón de tiro alto con pinzas frontales y cinturón desmontable a tono, fresco y elegante',
                'id_categoria' => 2,
                'precio_venta' => 65.00,
                'costo_compra' => 30.00,
                'stock_minimo' => 5,
                'imagen' => '/uploads/productos/prod-pantalon-palazzo.svg',
                'variantes' => [
                    ['sku' => 'PAN-002-S-BLA', 'talla' => 'S', 'color' => 'Blanco', 'stock' => 6],
                    ['sku' => 'PAN-002-M-BLA', 'talla' => 'M', 'color' => 'Blanco', 'stock' => 9],
                    ['sku' => 'PAN-002-M-BEI', 'talla' => 'M', 'color' => 'Beige', 'stock' => 10],
                    ['sku' => 'PAN-002-L-NEG', 'talla' => 'L', 'color' => 'Negro', 'stock' => 14],
                ]
            ],
            [
                'codigo' => 'VES-003',
                'nombre' => 'Vestido Midi Floral Bohemio',
                'descripcion' => 'Vestido midi con estampado floral exclusivo, silueta envolvente y cintura elástica adaptable',
                'id_categoria' => 3,
                'precio_venta' => 89.00,
                'costo_compra' => 42.00,
                'stock_minimo' => 3,
                'imagen' => '/uploads/productos/prod-vestido-midi.svg',
                'variantes' => [
                    ['sku' => 'VES-003-XS-EST', 'talla' => 'XS', 'color' => 'Estampado', 'stock' => 3],
                    ['sku' => 'VES-003-S-EST', 'talla' => 'S', 'color' => 'Estampado', 'stock' => 8],
                    ['sku' => 'VES-003-M-EST', 'talla' => 'M', 'color' => 'Estampado', 'stock' => 5],
                ]
            ],
            [
                'codigo' => 'CHQ-004',
                'nombre' => 'Blazer Clásico Estructurado',
                'descripcion' => 'Blazer sastre con hombreras suaves, solapa de muesca y forro de satén en tono rosa suave',
                'id_categoria' => 4,
                'precio_venta' => 115.00,
                'costo_compra' => 55.00,
                'stock_minimo' => 3,
                'imagen' => '/uploads/productos/prod-blazer-sastre.svg',
                'variantes' => [
                    ['sku' => 'CHQ-004-S-BEI', 'talla' => 'S', 'color' => 'Beige', 'stock' => 4],
                    ['sku' => 'CHQ-004-M-BEI', 'talla' => 'M', 'color' => 'Beige', 'stock' => 6],
                    ['sku' => 'CHQ-004-M-NEG', 'talla' => 'M', 'color' => 'Negro', 'stock' => 5],
                ]
            ],
            [
                'codigo' => 'FAL-005',
                'nombre' => 'Falda Plisada Satinada',
                'descripcion' => 'Falda midi de cintura elástica dorada con efecto plisado permanente y brillo sedoso',
                'id_categoria' => 5,
                'precio_venta' => 52.00,
                'costo_compra' => 24.00,
                'stock_minimo' => 4,
                'imagen' => '/uploads/productos/prod-falda-plisada.svg',
                'variantes' => [
                    ['sku' => 'FAL-005-S-DOR', 'talla' => 'S', 'color' => 'Dorado Champagne', 'stock' => 7],
                    ['sku' => 'FAL-005-M-DOR', 'talla' => 'M', 'color' => 'Dorado Champagne', 'stock' => 6],
                ]
            ],
            [
                'codigo' => 'BLU-006',
                'nombre' => 'Camisa Oxford Suave',
                'descripcion' => 'Camisa de algodón pima 100% con botones de nácar y corte regular fit impecable',
                'id_categoria' => 1,
                'precio_venta' => 55.00,
                'costo_compra' => 26.00,
                'stock_minimo' => 5,
                'imagen' => '/uploads/productos/prod-camisa-oxford.svg',
                'variantes' => [
                    ['sku' => 'BLU-006-M-AZU', 'talla' => 'M', 'color' => 'Azul Cielo', 'stock' => 11],
                    ['sku' => 'BLU-006-L-BLA', 'talla' => 'L', 'color' => 'Blanco', 'stock' => 8],
                ]
            ],
            [
                'codigo' => 'ACC-007',
                'nombre' => 'Cinturón Piel con Hebilla Dorada',
                'descripcion' => 'Cinturón de cuero vacuno legítimo de 2.5cm con hebilla geométrica de latón pulido',
                'id_categoria' => 6,
                'precio_venta' => 28.00,
                'costo_compra' => 11.00,
                'stock_minimo' => 6,
                'imagen' => '/uploads/productos/prod-cinturon-piel.svg',
                'variantes' => [
                    ['sku' => 'ACC-007-U-MAR', 'talla' => 'Única', 'color' => 'Marrón Suave', 'stock' => 15],
                    ['sku' => 'ACC-007-U-NEG', 'talla' => 'Única', 'color' => 'Negro', 'stock' => 18],
                ]
            ],
        ];

        foreach ($productos as $pData) {
            $variantes = $pData['variantes'];
            unset($pData['variantes']);

            $producto = Producto::firstOrCreate(
                ['codigo' => $pData['codigo']],
                array_merge($pData, ['estado' => true])
            );

            foreach ($variantes as $v) {
                ProductoVariante::firstOrCreate(
                    ['codigo_sku' => $v['sku']],
                    [
                        'id_producto' => $producto->id_producto,
                        'talla' => $v['talla'],
                        'color' => $v['color'],
                        'stock_actual' => $v['stock'],
                        'estado' => true,
                    ]
                );
            }
        }
    }
}
