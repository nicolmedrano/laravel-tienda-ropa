<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Módulo Cliente
     */
    public function index(Request $request)
    {
        $query = Cliente::withCount('ventas');

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('apellido', 'ilike', "%{$buscar}%")
                  ->orWhere('telefono', 'ilike', "%{$buscar}%")
                  ->orWhere('email', 'ilike', "%{$buscar}%");
            });
        }

        $clientes = $query->orderBy('apellido')->paginate(15);

        return view('clientes.index', compact('clientes'));
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());

        return redirect()->route('clientes.index')
            ->with('success', "Cliente {$cliente->nombre_completo} registrado exitosamente.");
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['ventas' => function ($q) {
            $q->orderBy('fecha_venta', 'desc')->with('detalles.variante.producto');
        }]);

        return view('clientes.show', compact('cliente'));
    }

    public function update(StoreClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()->route('clientes.index')
            ->with('success', "Datos del cliente {$cliente->nombre_completo} actualizados.");
    }

    public function toggleEstado(Cliente $cliente)
    {
        $cliente->update(['estado' => !$cliente->estado]);
        $estadoTexto = $cliente->estado ? 'activado' : 'desactivado';

        return back()->with('success', "Cliente {$cliente->nombre_completo} {$estadoTexto}.");
    }
}
