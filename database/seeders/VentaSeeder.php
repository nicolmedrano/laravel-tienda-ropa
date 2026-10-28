<?php

namespace Database\Seeders;

use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $venta1 = Venta::firstOrCreate(
            ['numero_factura' => 'FAC-00001'],
            [
                'id_cliente' => 1,
                'id_usuario' => 2,
                'fecha_venta' => now()->subDays(5),
                'subtotal' => 113.00,
                'descuento' => 0.00,
                'total' => 113.00,
                'estado' => 'completada',
            ]
        );

        DetalleVenta::firstOrCreate(
            ['id_venta' => $venta1->id_venta, 'id_variante' => 1],
            ['cantidad' => 1, 'precio_unitario' => 48.00, 'subtotal' => 48.00]
        );

        DetalleVenta::firstOrCreate(
            ['id_venta' => $venta1->id_venta, 'id_variante' => 7],
            ['cantidad' => 1, 'precio_unitario' => 65.00, 'subtotal' => 65.00]
        );

        Pago::firstOrCreate(
            ['id_venta' => $venta1->id_venta],
            [
                'monto' => 113.00,
                'metodo' => 'tarjeta',
                'referencia' => 'VOUCHER-98214',
                'estado' => 'completado',
                'fecha_pago' => now()->subDays(5),
            ]
        );

        $venta2 = Venta::firstOrCreate(
            ['numero_factura' => 'FAC-00002'],
            [
                'id_cliente' => 2,
                'id_usuario' => 2,
                'fecha_venta' => now()->subDays(2),
                'subtotal' => 89.00,
                'descuento' => 5.00,
                'total' => 84.00,
                'estado' => 'completada',
            ]
        );

        DetalleVenta::firstOrCreate(
            ['id_venta' => $venta2->id_venta, 'id_variante' => 10],
            ['cantidad' => 1, 'precio_unitario' => 89.00, 'subtotal' => 89.00]
        );

        Pago::firstOrCreate(
            ['id_venta' => $venta2->id_venta],
            [
                'monto' => 84.00,
                'metodo' => 'qr',
                'referencia' => 'QR-BNB-883921',
                'estado' => 'completado',
                'fecha_pago' => now()->subDays(2),
            ]
        );
    }
}
