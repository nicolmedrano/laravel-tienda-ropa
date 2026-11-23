<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteDireccion extends Model
{
    protected $table = 'cliente_direcciones';
    protected $primaryKey = 'id_direccion';

    protected $fillable = [
        'id_cliente',
        'alias',
        'ciudad',
        'zona',
        'calle',
        'referencia',
        'latitud',
        'longitud',
        'es_principal',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    /**
     * Relación con Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    /**
     * Dirección formateada completa
     */
    public function getDireccionCompletaAttribute(): string
    {
        $partes = [];

        if ($this->calle) {
            $partes[] = $this->calle;
        }

        if ($this->zona) {
            $partes[] = "Zona {$this->zona}";
        }

        if ($this->ciudad) {
            $partes[] = $this->ciudad;
        }

        $texto = implode(', ', $partes);

        if ($this->referencia) {
            $texto .= " (Ref: {$this->referencia})";
        }

        return $texto ?: 'Sin dirección detallada';
    }

    /**
     * Enlace directo a Google Maps o OpenStreetMap
     */
    public function getMapaUrlAttribute(): ?string
    {
        if ($this->latitud && $this->longitud) {
            return "https://www.google.com/maps?q={$this->latitud},{$this->longitud}";
        }
        return null;
    }
}
