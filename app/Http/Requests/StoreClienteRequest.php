<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preparar y normalizar los datos antes de validar
     */
    protected function prepareForValidation(): void
    {
        // Limpiar espacios en blanco
        $this->merge([
            'nombre' => is_string($this->nombre) ? trim($this->nombre) : $this->nombre,
            'apellido' => is_string($this->apellido) ? trim($this->apellido) : $this->apellido,
            'telefono' => is_string($this->telefono) ? trim($this->telefono) : $this->telefono,
            'telefono_alternativo' => is_string($this->telefono_alternativo) ? trim($this->telefono_alternativo) : $this->telefono_alternativo,
            'email' => is_string($this->email) && trim($this->email) !== '' ? strtolower(trim($this->email)) : null,
            'ci_nit' => is_string($this->ci_nit) && trim($this->ci_nit) !== '' ? trim($this->ci_nit) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'tipo_cliente' => ['nullable', 'string', 'in:persona,empresa'],
            'ci_nit' => ['nullable', 'string', 'max:30'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],

            // Teléfono obligatorio: debe cumplir el formato de celular boliviano (8 dígitos que empiezan con 6 o 7, con o sin +591)
            'telefono' => [
                'required',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {
                    $digitos = preg_replace('/\D/', '', $value);
                    if (str_starts_with($digitos, '591') && strlen($digitos) === 11) {
                        $digitos = substr($digitos, 3);
                    }
                    if (strlen($digitos) !== 8 || !in_array($digitos[0], ['6', '7'])) {
                        $fail('El teléfono debe ser un celular válido de Bolivia (8 dígitos que comiencen con 6 o 7).');
                    }
                },
            ],

            'telefono_alternativo' => [
                'nullable',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $digitos = preg_replace('/\D/', '', $value);
                    if (str_starts_with($digitos, '591') && strlen($digitos) === 11) {
                        $digitos = substr($digitos, 3);
                    }
                    if (strlen($digitos) < 7 || strlen($digitos) > 8) {
                        $fail('El teléfono alternativo debe tener 7 u 8 dígitos.');
                    }
                },
            ],

            'email' => ['nullable', 'email', 'max:150'],
            'canal_preferido' => ['nullable', 'string', 'in:whatsapp,llamada,correo'],
            'origen' => ['nullable', 'string', 'in:facebook,instagram,tiktok,whatsapp,referido,tienda_fisica,otro'],
            'etiquetas' => ['nullable', 'array'],
            'etiquetas.*' => ['string', 'max:50'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'estado' => ['nullable'],

            // Datos de dirección principal o direcciones múltiples
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'zona' => ['nullable', 'string', 'max:150'],
            'calle' => ['nullable', 'string', 'max:200'],
            'referencia' => ['nullable', 'string', 'max:500'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],

            // Múltiples direcciones (opcional)
            'direcciones' => ['nullable', 'array'],
            'direcciones.*.alias' => ['nullable', 'string', 'max:50'],
            'direcciones.*.ciudad' => ['nullable', 'string', 'max:100'],
            'direcciones.*.zona' => ['nullable', 'string', 'max:150'],
            'direcciones.*.calle' => ['nullable', 'string', 'max:200'],
            'direcciones.*.referencia' => ['nullable', 'string', 'max:500'],
            'direcciones.*.latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'direcciones.*.longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'direcciones.*.es_principal' => ['nullable'],

            // Acción extra al guardar (ej. "guardar_y_pedido")
            'accion' => ['nullable', 'string', 'in:guardar,guardar_y_pedido'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'apellido.required' => 'El apellido del cliente es obligatorio.',
            'apellido.max' => 'El apellido no debe exceder los 100 caracteres.',
            'telefono.required' => 'El número de teléfono/WhatsApp es obligatorio para coordinar entregas.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser una fecha futura.',
            'ci_nit.max' => 'El CI/NIT no debe superar los 30 caracteres.',
        ];
    }
}
