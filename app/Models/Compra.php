<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id_compra';

    protected $fillable = [
        'numero_comprobante',
        'id_proveedor',
        'id_usuario',
        'fecha_compra',
        'total',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha_compra' => 'datetime',
    ];

    /**
     * Proveedor se relaciona con Compra para proporcionar los datos del proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    /**
     * Usuario se relaciona con Compra para identificar quién registra la adquisición
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Detalle de prendas adquiridas
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra', 'id_compra');
    }
}
