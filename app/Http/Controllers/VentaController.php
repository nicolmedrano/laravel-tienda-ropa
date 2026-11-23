<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    /**
     * Listado histórico de ventas
     */
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'detalles.variante.producto', 'pagos']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_factura', 'ilike', "%{$buscar}%")
                  ->orWhereHas('cliente', function ($qc) use ($buscar) {
                      $qc->where('nombre', 'ilike', "%{$buscar}%")
                         ->orWhere('apellido', 'ilike', "%{$buscar}%");
                  });
            });
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')->paginate(15);

        return view('ventas.index', compact('ventas'));
    }

    /**
     * Punto de Venta (POS) interactivo
     */
    public function pos()
    {
        $productos = Producto::with(['categoria', 'variantes' => function ($q) {
            $q->where('estado', true);
        }])
        ->where('estado', true)
        ->get();

        $clientes = Cliente::where('estado', true)->orderBy('nombre')->get();

        return view('ventas.pos', compact('productos', 'clientes'));
    }

    /**
     * Registrar una nueva venta delegando la lógica al servicio.
     */
    public function store(StoreVentaRequest $request, VentaService $ventaService)
    {
        $venta = $ventaService->registrar($request->validated(), Auth::id());

        return redirect()->route('ventas.ticket', $venta->id_venta)
            ->with('success', "Venta {$venta->numero_factura} procesada con éxito por Bs. {$venta->total}.");
    }

    /**
     * Visualización e impresión de ticket de venta
     */
    public function ticket(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.variante.producto.categoria', 'pagos']);
        return view('ventas.ticket', compact('venta'));
    }

    /**
     * Cancelar / anular venta con reversión de inventario delegando al servicio.
     */
    public function anular(Request $request, Venta $venta, VentaService $ventaService)
    {
        if ($venta->estado === 'anulada') {
            return back()->with('error', 'Esta venta ya se encuentra anulada.');
        }

        $ventaService->anular($venta, Auth::id());

        return back()->with('success', "Venta {$venta->numero_factura} anulada. El stock fue revertido al inventario.");
    }
}
