<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'precio_venta' => 'required|numeric|min:0',
            'costo_compra' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Máx 5MB y formatos permitidos
            'variantes' => 'nullable|array',
            'variantes.*.talla' => 'nullable|string|max:10',
            'variantes.*.color' => 'required_with:variantes|string|max:50',
            'variantes.*.stock' => 'required_with:variantes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.unique' => 'Este código de producto ya se encuentra registrado.',
            'nombre.required' => 'El nombre de la prenda es obligatorio.',
            'id_categoria.required' => 'Debe seleccionar una categoría.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'costo_compra.required' => 'El costo de compra es obligatorio.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes' => 'Formato no permitido. Solo se aceptan imágenes JPG, JPEG, PNG y WEBP.',
            'imagen.max' => 'La imagen no debe superar los 5 Megabytes de tamaño.',
        ];
    }
}
