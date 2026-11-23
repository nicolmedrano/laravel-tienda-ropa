<?php

namespace App\Contracts;

interface NumeradorFactura
{
    /**
     * Genera el número de factura para un ID de venta dado.
     *
     * @param int $idVenta
     * @return string
     */
    public function numeroPara(int $idVenta): string;
}
