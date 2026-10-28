<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idProducto = $this->route('producto') ? ($this->route('producto')->id_producto ?? $this->route('producto')) : $this->id_producto;

        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('productos', 'codigo')->ignore($idProducto, 'id_producto'),
            ],
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'precio_venta' => 'required|numeric|min:0',
            'costo_compra' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'conservar_imagen' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del producto es obligatorio.',
            'nombre.required' => 'El nombre de la prenda es obligatorio.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes' => 'Formato no permitido. Solo se aceptan imágenes JPG, JPEG, PNG y WEBP.',
            'imagen.max' => 'La imagen no debe superar los 5 Megabytes de tamaño.',
        ];
    }
}
