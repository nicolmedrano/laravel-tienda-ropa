<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    /**
     * Gestión y arqueo de pagos
     */
    public function index(Request $request)
    {
        $query = Pago::with(['venta.cliente', 'venta.usuario']);

        if ($request->filled('metodo')) {
            $query->where('metodo', $request->get('metodo'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->paginate(15);

        $totalesPorMetodo = Pago::where('estado', 'completado')
            ->selectRaw('metodo, SUM(monto) as total')
            ->groupBy('metodo')
            ->pluck('total', 'metodo');

        return view('pagos.index', compact('pagos', 'totalesPorMetodo'));
    }
}
