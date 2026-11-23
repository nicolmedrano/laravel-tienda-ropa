<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\ProductoVariante;
use Illuminate\Validation\ValidationException;

class InventarioService
{
    /**
     * Registra un movimiento de inventario (Kardex) y actualiza el stock de la variante.
     *
     * @param ProductoVariante $variante Variante de producto previamente bloqueada con lockForUpdate
     * @param string $tipo Tipo de movimiento (ej. 'salida_venta', 'anulacion')
     * @param int $cantidad Cantidad con signo (negativa = salida, positiva = entrada)
     * @param string $motivo Motivo descriptivo del movimiento
     * @param int $idUsuario ID del usuario que ejecuta la acción
     * @return void
     *
     * @throws ValidationException Si el stock resultante fuera negativo
     */
    public function registrarMovimiento(
        ProductoVariante $variante,
        string $tipo,
        int $cantidad,
        string $motivo,
        int $idUsuario
    ): void {
        $stockAnterior = $variante->stock_actual;
        $stockPosterior = $stockAnterior + $cantidad;

        if ($stockPosterior < 0) {
            $nombrePrenda = $variante->descripcion_completa;
            $solicitado = abs($cantidad);

            throw ValidationException::withMessages([
                'stock' => "Stock insuficiente para {$nombrePrenda}. Stock disponible: {$stockAnterior}, solicitado: {$solicitado}.",
            ]);
        }

        $variante->update(['stock_actual' => $stockPosterior]);

        MovimientoInventario::create([
            'id_variante' => $variante->id_variante,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_posterior' => $stockPosterior,
            'motivo' => $motivo,
            'id_usuario' => $idUsuario,
            'fecha' => now(),
        ]);
    }
}
