<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compras';
    protected $primaryKey = 'id_detalle_compra';
    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'id_variante',
        'cantidad',
        'costo_unitario',
        'subtotal',
        'created_at',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id_compra');
    }

    /**
     * Se relaciona con Producto a través de la variante de prenda
     */
    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante', 'id_variante');
    }
}
