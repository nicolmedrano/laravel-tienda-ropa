<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Módulo Proveedor
     */
    public function index(Request $request)
    {
        $query = Proveedor::withCount('compras');

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_empresa', 'ilike', "%{$buscar}%")
                  ->orWhere('contacto', 'ilike', "%{$buscar}%")
                  ->orWhere('telefono', 'ilike', "%{$buscar}%")
                  ->orWhere('email', 'ilike', "%{$buscar}%");
            });
        }

        $proveedores = $query->orderBy('nombre_empresa')->paginate(15);

        return view('proveedores.index', compact('proveedores'));
    }

    public function store(StoreProveedorRequest $request)
    {
        $proveedor = Proveedor::create($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', "Proveedor '{$proveedor->nombre_empresa}' registrado.");
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['compras' => function ($q) {
            $q->orderBy('fecha_compra', 'desc')->with('detalles.variante.producto');
        }]);

        return view('proveedores.show', compact('proveedor'));
    }

    public function update(StoreProveedorRequest $request, Proveedor $proveedor)
    {
        $proveedor->update($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', "Proveedor '{$proveedor->nombre_empresa}' actualizado.");
    }

    public function toggleEstado(Proveedor $proveedor)
    {
        $proveedor->update(['estado' => !$proveedor->estado]);
        $estadoTexto = $proveedor->estado ? 'activado' : 'desactivado';

        return back()->with('success', "Proveedor '{$proveedor->nombre_empresa}' {$estadoTexto}.");
    }
}
