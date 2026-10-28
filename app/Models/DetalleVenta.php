<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';
    protected $primaryKey = 'id_detalle_venta';
    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'id_variante',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'created_at',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    /**
     * Venta se relaciona con Producto a través de la variante
     */
    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante', 'id_variante');
    }
}
