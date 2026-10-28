<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'id_categoria',
        'precio_venta',
        'costo_compra',
        'stock_minimo',
        'imagen',
        'estado',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'costo_compra' => 'decimal:2',
        'stock_minimo' => 'integer',
        'estado' => 'boolean',
    ];

    protected $appends = [
        'stock_total',
        'imagen_url',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Tallas, Colores y Stock
     */
    public function variantes()
    {
        return $this->hasMany(ProductoVariante::class, 'id_producto', 'id_producto');
    }

    public function getStockTotalAttribute(): int
    {
        return (int) $this->variantes()->where('estado', true)->sum('stock_actual');
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) {
            return null;
        }

        // Si es una URL externa o ruta pública
        if (str_starts_with($this->imagen, 'http://') || str_starts_with($this->imagen, 'https://')) {
            return $this->imagen;
        }

        if (str_starts_with($this->imagen, '/uploads/') || str_starts_with($this->imagen, '/storage/')) {
            return asset($this->imagen);
        }

        return Storage::disk('public')->url($this->imagen);
    }
}
