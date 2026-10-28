<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_empresa' => 'required|string|max:150',
            'contacto' => 'required|string|max:100',
            'telefono' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_empresa.required' => 'La razón social o nombre de la empresa proveedora es obligatoria.',
            'contacto.required' => 'El nombre del contacto del proveedor es obligatorio.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
        ];
    }
}
