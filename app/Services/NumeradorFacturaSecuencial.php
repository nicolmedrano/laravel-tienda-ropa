<?php

namespace App\Services;

use App\Contracts\NumeradorFactura;

class NumeradorFacturaSecuencial implements NumeradorFactura
{
    /**
     * Genera el número correlativo con formato 'FAC-' y ceros a la izquierda hasta 5 dígitos.
     * Ejemplo: FAC-00004
     *
     * @param int $idVenta
     * @return string
     */
    public function numeroPara(int $idVenta): string
    {
        return 'FAC-' . str_pad((string) $idVenta, 5, '0', STR_PAD_LEFT);
    }
}
