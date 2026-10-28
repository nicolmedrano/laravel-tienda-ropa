<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVariante extends Model
{
    protected $table = 'variantes_producto';
    protected $primaryKey = 'id_variante';

    protected $fillable = [
        'id_producto',
        'codigo_sku',
        'talla',
        'color',
        'stock_actual',
        'estado',
    ];

    protected $casts = [
        'stock_actual' => 'integer',
        'estado' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_variante', 'id_variante');
    }

    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'id_variante', 'id_variante');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_variante', 'id_variante');
    }

    public function getDescripcionCompletaAttribute(): string
    {
        $nombre = $this->producto ? $this->producto->nombre : 'Prenda';
        return "{$nombre} (Talla: {$this->talla}, Color: {$this->color})";
    }
}
