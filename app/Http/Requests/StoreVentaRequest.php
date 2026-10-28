<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'descuento' => 'nullable|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,qr',
            'referencia_pago' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.id_variante' => 'required|exists:variantes_producto,id_variante',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Debe agregar al menos una prenda al carrito de venta.',
            'items.min' => 'El carrito no puede estar vacío.',
            'metodo_pago.required' => 'Debe seleccionar un método de pago.',
        ];
    }
}
