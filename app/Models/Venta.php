<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';

    protected $fillable = [
        'numero_factura',
        'id_cliente',
        'id_usuario',
        'fecha_venta',
        'subtotal',
        'descuento',
        'total',
        'estado',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_venta' => 'datetime',
    ];

    /**
     * Cliente se relaciona con Venta para proporcionar los datos del cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    /**
     * Usuario se relaciona con Venta para identificar quién registra la operación
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Venta se relaciona con Producto a través de los detalles de ítems vendidos
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    /**
     * Pagos registrados para esta venta
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_venta', 'id_venta');
    }
}
