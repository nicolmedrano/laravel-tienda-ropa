<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'numero_comprobante' => 'required|string|max:50|unique:compras,numero_comprobante',
            'observaciones' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_variante' => 'required|exists:variantes_producto,id_variante',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.costo_unitario' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_proveedor.required' => 'Debe seleccionar un proveedor.',
            'numero_comprobante.required' => 'El número de comprobante o factura de compra es obligatorio.',
            'items.required' => 'Debe agregar al menos una prenda para registrar la compra.',
        ];
    }
}
