<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'id_movimiento';

    protected $fillable = [
        'id_variante',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_posterior',
        'motivo',
        'id_usuario',
        'fecha',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_posterior' => 'integer',
        'fecha' => 'datetime',
    ];

    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante', 'id_variante');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
