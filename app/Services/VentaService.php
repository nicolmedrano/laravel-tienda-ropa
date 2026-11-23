<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\ProductoVariante;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaService
{
    public function __construct(
        protected InventarioService $inventarioService
    ) {}

    /**
     * Registra una venta completa de forma atómica.
     *
     * @param array $datos Datos validados de la venta
     * @param int $idUsuario ID del usuario autenticado
     * @return Venta
     */
    public function registrar(array $datos, int $idUsuario): Venta
    {
        return DB::transaction(function () use ($datos, $idUsuario) {
            $itemsConsolidados = $this->consolidarItems($datos['items']);
            $itemsValidados = $this->validarYCalcularItems($itemsConsolidados);

            $subtotalGeneral = array_sum(array_column($itemsValidados, 'subtotal'));
            $descuento = (float) ($datos['descuento'] ?? 0);

            $this->validarDescuento($descuento, $subtotalGeneral);

            $totalFinal = $subtotalGeneral - $descuento;

            $numeroFactura = $this->generarNumeroFactura();

            $venta = $this->crearVenta(
                $numeroFactura,
                $datos,
                $idUsuario,
                $subtotalGeneral,
                $descuento,
                $totalFinal
            );

            $this->procesarDetallesEInventario($venta, $itemsValidados, $idUsuario);

            $this->registrarPago($venta, $totalFinal, $datos);

            return $venta;
        });
    }

    /**
     * Anula una venta existente y revierte el stock al inventario.
     *
     * @param Venta $venta
     * @param int $idUsuario ID del usuario que anula la venta
     * @return void
     *
     * @throws ValidationException Si la venta ya está anulada
     */
    public function anular(Venta $venta, int $idUsuario): void
    {
        if ($venta->estado === 'anulada') {
            throw ValidationException::withMessages([
                'venta' => 'Esta venta ya se encuentra anulada.',
            ]);
        }

        DB::transaction(function () use ($venta, $idUsuario) {
            $venta->load('detalles');

            foreach ($venta->detalles as $detalle) {
                $variante = ProductoVariante::with('producto')
                    ->where('id_variante', $detalle->id_variante)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->inventarioService->registrarMovimiento(
                    $variante,
                    'anulacion',
                    $detalle->cantidad,
                    "Anulación de venta {$venta->numero_factura}",
                    $idUsuario
                );
            }

            $venta->update(['estado' => 'anulada']);

            Pago::where('id_venta', $venta->id_venta)->update(['estado' => 'reembolsado']);
        });
    }

    /**
     * Consolida los items del carrito sumando las cantidades de variantes repetidas.
     *
     * @param array $items
     * @return array<int, int> Mapa asociativo [id_variante => cantidad_total]
     */
    private function consolidarItems(array $items): array
    {
        $consolidados = [];

        foreach ($items as $item) {
            $idVariante = (int) $item['id_variante'];
            $cantidad = (int) $item['cantidad'];

            if (!isset($consolidados[$idVariante])) {
                $consolidados[$idVariante] = 0;
            }

            $consolidados[$idVariante] += $cantidad;
        }

        return $consolidados;
    }

    /**
     * Valida existencias y estado de los items consolidados y calcula los subtotales usando el precio de la base de datos.
     *
     * @param array<int, int> $itemsConsolidados Mapa [id_variante => cantidad]
     * @return array
     *
     * @throws ValidationException
     */
    private function validarYCalcularItems(array $itemsConsolidados): array
    {
        $itemsValidados = [];

        foreach ($itemsConsolidados as $idVariante => $cantidad) {
            $variante = ProductoVariante::with('producto')
                ->where('id_variante', $idVariante)
                ->lockForUpdate()
                ->firstOrFail();

            $this->validarEstadoVarianteYProducto($variante);
            $this->validarStock($variante, $cantidad);

            // El precio unitario proviene exclusivamente de la base de datos (producto)
            $precioUnitario = (float) $variante->producto->precio_venta;
            $subtotalLinea = $cantidad * $precioUnitario;

            $itemsValidados[] = [
                'variante' => $variante,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotalLinea,
            ];
        }

        return $itemsValidados;
    }

    /**
     * Valida que la variante y su producto padre se encuentren activos.
     *
     * @param ProductoVariante $variante
     * @return void
     *
     * @throws ValidationException
     */
    private function validarEstadoVarianteYProducto(ProductoVariante $variante): void
    {
        if (!$variante->estado) {
            throw ValidationException::withMessages([
                'items' => "La variante {$variante->descripcion_completa} se encuentra inactiva.",
            ]);
        }

        if (!$variante->producto || !$variante->producto->estado) {
            $nombre = $variante->producto ? $variante->producto->nombre : 'desconocido';
            throw ValidationException::withMessages([
                'items' => "El producto {$nombre} se encuentra inactivo.",
            ]);
        }
    }

    /**
     * Valida que la variante cuente con stock suficiente para la cantidad solicitada.
     *
     * @param ProductoVariante $variante
     * @param int $cantidad
     * @return void
     *
     * @throws ValidationException
     */
    private function validarStock(ProductoVariante $variante, int $cantidad): void
    {
        if ($variante->stock_actual < $cantidad) {
            throw ValidationException::withMessages([
                'items' => "Stock insuficiente para {$variante->producto->nombre} (Talla: {$variante->talla}, Color: {$variante->color}). Stock actual: {$variante->stock_actual}, solicitado: {$cantidad}.",
            ]);
        }
    }

    /**
     * Valida que el descuento no sea mayor que el subtotal.
     *
     * @param float $descuento
     * @param float $subtotal
     * @return void
     *
     * @throws ValidationException
     */
    private function validarDescuento(float $descuento, float $subtotal): void
    {
        if ($descuento > $subtotal) {
            throw ValidationException::withMessages([
                'descuento' => "El descuento (Bs. {$descuento}) no puede ser mayor que el subtotal (Bs. {$subtotal}).",
            ]);
        }
    }

    /**
     * Genera el correlativo secuencial de factura.
     *
     * @return string
     */
    private function generarNumeroFactura(): string
    {
        $ultimoId = Venta::max('id_venta') ?? 0;
        return 'FAC-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Crea el registro principal de la venta.
     */
    private function crearVenta(
        string $numeroFactura,
        array $datos,
        int $idUsuario,
        float $subtotal,
        float $descuento,
        float $total
    ): Venta {
        return Venta::create([
            'numero_factura' => $numeroFactura,
            'id_cliente' => $datos['id_cliente'] ?? null,
            'id_usuario' => $idUsuario,
            'fecha_venta' => now(),
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'total' => $total,
            'estado' => 'completada',
        ]);
    }

    /**
     * Registra los detalles de la venta y descuenta el stock mediante InventarioService.
     */
    private function procesarDetallesEInventario(Venta $venta, array $itemsValidados, int $idUsuario): void
    {
        foreach ($itemsValidados as $item) {
            $variante = $item['variante'];

            DetalleVenta::create([
                'id_venta' => $venta->id_venta,
                'id_variante' => $variante->id_variante,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal' => $item['subtotal'],
                'created_at' => now(),
            ]);

            $this->inventarioService->registrarMovimiento(
                $variante,
                'salida_venta',
                -$item['cantidad'],
                "Venta {$venta->numero_factura}",
                $idUsuario
            );
        }
    }

    /**
     * Registra el pago asociado a la venta.
     */
    private function registrarPago(Venta $venta, float $monto, array $datos): Pago
    {
        return Pago::create([
            'id_venta' => $venta->id_venta,
            'monto' => $monto,
            'metodo' => $datos['metodo_pago'],
            'referencia' => $datos['referencia_pago'] ?? null,
            'estado' => 'completado',
            'fecha_pago' => now(),
        ]);
    }
}
