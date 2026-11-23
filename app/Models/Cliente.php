<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'tipo_cliente',
        'nombre',
        'apellido',
        'ci_nit',
        'fecha_nacimiento',
        'telefono',
        'telefono_alternativo',
        'email',
        'canal_preferido',
        'direccion',
        'origen',
        'etiquetas',
        'notas',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_nacimiento' => 'date',
        'etiquetas' => 'array',
    ];

    /**
     * Relación con Ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente', 'id_cliente');
    }

    /**
     * Múltiples direcciones de entrega asociadas
     */
    public function direcciones()
    {
        return $this->hasMany(ClienteDireccion::class, 'id_cliente', 'id_cliente')
                    ->orderByDesc('es_principal')
                    ->orderBy('id_direccion');
    }

    /**
     * Dirección principal
     */
    public function direccionPrincipal()
    {
        return $this->hasOne(ClienteDireccion::class, 'id_cliente', 'id_cliente')
                    ->where('es_principal', true);
    }

    /* =========================================================================
       ACCESORS Y MUTATORS (Normalización y Calidad de Datos)
       ========================================================================= */

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }

    /**
     * Normalizar nombre: trim y primera letra en mayúscula por palabra
     */
    public function setNombreAttribute($value): void
    {
        $this->attributes['nombre'] = mb_convert_case(trim((string) $value), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Normalizar apellido: trim y primera letra en mayúscula por palabra
     */
    public function setApellidoAttribute($value): void
    {
        $this->attributes['apellido'] = mb_convert_case(trim((string) $value), MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Normalizar email: minúsculas y trim
     */
    public function setEmailAttribute($value): void
    {
        $val = trim((string) $value);
        $this->attributes['email'] = $val !== '' ? strtolower($val) : null;
    }

    /**
     * Normalizar teléfono en formato estándar Bolivia (+591 XXXXXXXX)
     */
    public function setTelefonoAttribute($value): void
    {
        $this->attributes['telefono'] = static::normalizarNumeroBolivia($value);
    }

    public function setTelefonoAlternativoAttribute($value): void
    {
        $this->attributes['telefono_alternativo'] = static::normalizarNumeroBolivia($value);
    }

    /**
     * Limpia y formatea un número boliviano.
     * Ejemplo: "71234567" -> "+591 71234567"
     *           "59171234567" -> "+591 71234567"
     */
    public static function normalizarNumeroBolivia(?string $numero): ?string
    {
        if (!$numero) {
            return null;
        }

        // Remover caracteres no numéricos
        $soloDigitos = preg_replace('/\D/', '', $numero);

        if (!$soloDigitos) {
            return null;
        }

        // Si empieza con prefijo de Bolivia (591) y tiene 11 dígitos
        if (str_starts_with($soloDigitos, '591') && strlen($soloDigitos) === 11) {
            $celular = substr($soloDigitos, 3);
            return "+591 {$celular}";
        }

        // Si son 8 dígitos típicos de Bolivia
        if (strlen($soloDigitos) === 8) {
            return "+591 {$soloDigitos}";
        }

        // Si no cumple el patrón exacto, guardar con trim limpio
        return trim($numero);
    }

    /**
     * Retorna solo los dígitos con código de país para enlaces de WhatsApp: wa.me/591XXXXXXXX
     */
    public function getWhatsappNumeroAttribute(): ?string
    {
        if (!$this->telefono) {
            return null;
        }

        $soloDigitos = preg_replace('/\D/', '', $this->telefono);

        if (strlen($soloDigitos) === 8) {
            return "591{$soloDigitos}";
        }

        if (str_starts_with($soloDigitos, '591') && strlen($soloDigitos) === 11) {
            return $soloDigitos;
        }

        return $soloDigitos ?: null;
    }

    /**
     * Enlace directo a WhatsApp web / app
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $num = $this->whatsapp_numero;
        if (!$num) {
            return null;
        }

        $primerNombre = explode(' ', trim($this->nombre))[0] ?? 'Estimada/o';
        $mensaje = rawurlencode("¡Hola {$primerNombre}! Te escribimos de Bella Girls Boutique 🌸");

        return "https://wa.me/{$num}?text={$mensaje}";
    }

    /**
     * Obtiene el texto de la dirección principal o la columna direccion tradicional
     */
    public function getDireccionCompletaAttribute(): string
    {
        if ($this->relationLoaded('direcciones') && $this->direcciones->isNotEmpty()) {
            $principal = $this->direcciones->firstWhere('es_principal', true) ?? $this->direcciones->first();
            return $principal->direccion_completa;
        }

        return $this->direccion ?: 'Sin dirección registrada';
    }

    /* =========================================================================
       SCOPES DE BÚSQUEDA Y FILTRADO
       ========================================================================= */

    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        // 1. Búsqueda por texto (nombre, apellido, teléfono, email, ci_nit)
        if (!empty($filtros['buscar'])) {
            $buscar = trim($filtros['buscar']);
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('apellido', 'ilike', "%{$buscar}%")
                  ->orWhere('telefono', 'ilike', "%{$buscar}%")
                  ->orWhere('telefono_alternativo', 'ilike', "%{$buscar}%")
                  ->orWhere('email', 'ilike', "%{$buscar}%")
                  ->orWhere('ci_nit', 'ilike', "%{$buscar}%")
                  ->orWhere('direccion', 'ilike', "%{$buscar}%");
            });
        }

        // 2. Filtro por origen
        if (!empty($filtros['origen'])) {
            $query->where('origen', $filtros['origen']);
        }

        // 3. Filtro por etiqueta
        if (!empty($filtros['etiqueta'])) {
            $etiqueta = $filtros['etiqueta'];
            $query->whereJsonContains('etiquetas', $etiqueta);
        }

        // 4. Filtro por estado
        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $query->where('estado', filter_var($filtros['estado'], FILTER_VALIDATE_BOOLEAN));
        }

        // 5. Filtro por tipo de cliente
        if (!empty($filtros['tipo_cliente'])) {
            $query->where('tipo_cliente', $filtros['tipo_cliente']);
        }

        return $query;
    }
}
