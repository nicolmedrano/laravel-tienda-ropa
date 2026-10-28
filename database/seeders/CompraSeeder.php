<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\DetalleCompra;
use Illuminate\Database\Seeder;

class CompraSeeder extends Seeder
{
    public function run(): void
    {
        $compra1 = Compra::firstOrCreate(
            ['numero_comprobante' => 'COM-2026-0001'],
            [
                'id_proveedor' => 1,
                'id_usuario' => 1,
                'fecha_compra' => now()->subDays(15),
                'total' => 880.00,
                'estado' => 'completada',
                'observaciones' => 'Reabastecimiento temporada primavera',
            ]
        );

        DetalleCompra::firstOrCreate(
            ['id_compra' => $compra1->id_compra, 'id_variante' => 1],
            ['cantidad' => 20, 'costo_unitario' => 22.00, 'subtotal' => 440.00]
        );

        DetalleCompra::firstOrCreate(
            ['id_compra' => $compra1->id_compra, 'id_variante' => 2],
            ['cantidad' => 20, 'costo_unitario' => 22.00, 'subtotal' => 440.00]
        );

        $compra2 = Compra::firstOrCreate(
            ['numero_comprobante' => 'COM-2026-0002'],
            [
                'id_proveedor' => 2,
                'id_usuario' => 1,
                'fecha_compra' => now()->subDays(10),
                'total' => 1240.00,
                'estado' => 'completada',
                'observaciones' => 'Lote lino y pantalones palazzo',
            ]
        );

        DetalleCompra::firstOrCreate(
            ['id_compra' => $compra2->id_compra, 'id_variante' => 5],
            ['cantidad' => 20, 'costo_unitario' => 30.00, 'subtotal' => 600.00]
        );

        DetalleCompra::firstOrCreate(
            ['id_compra' => $compra2->id_compra, 'id_variante' => 7],
            ['cantidad' => 20, 'costo_unitario' => 32.00, 'subtotal' => 640.00]
        );
    }
}
