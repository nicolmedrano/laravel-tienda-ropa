<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    /**
     * Listado histórico de compras a proveedores
     */
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'usuario', 'detalles.variante.producto']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('buscar')) {
            $buscar = $request->get('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_comprobante', 'ilike', "%{$buscar}%")
                  ->orWhereHas('proveedor', function ($qp) use ($buscar) {
                      $qp->where('nombre_empresa', 'ilike', "%{$buscar}%");
                  });
            });
        }

        $compras = $query->orderBy('fecha_compra', 'desc')->paginate(15);

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('estado', true)->orderBy('nombre_empresa')->get();
        $productos = Producto::with(['variantes' => function ($q) {
            $q->where('estado', true);
        }])->where('estado', true)->get();

        return view('compras.create', compact('proveedores', 'productos'));
    }

    /**
     * Registrar compra a proveedor con transacción en PostgreSQL
     */
    public function store(StoreCompraRequest $request)
    {
        $data = $request->validated();
        $idUsuario = Auth::id() ?? 1;

        $compra = DB::transaction(function () use ($data, $idUsuario) {
            $totalGeneral = 0;

            // Calcular total de la compra
            foreach ($data['items'] as $item) {
                $totalGeneral += ($item['cantidad'] * $item['costo_unitario']);
            }

            // Crear encabezado de compra
            $nuevaCompra = Compra::create([
                'numero_comprobante' => $data['numero_comprobante'],
                'id_proveedor' => $data['id_proveedor'],
                'id_usuario' => $idUsuario,
                'fecha_compra' => now(),
                'total' => $totalGeneral,
                'estado' => 'completada',
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            // Crear detalles, incrementar stock y asentar Kardex
            foreach ($data['items'] as $item) {
                $variante = ProductoVariante::with('producto')
                    ->where('id_variante', $item['id_variante'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $cantidad = (int) $item['cantidad'];
                $costoUnitario = (float) $item['costo_unitario'];
                $subtotal = $cantidad * $costoUnitario;

                $stockAnterior = $variante->stock_actual;
                $stockPosterior = $stockAnterior + $cantidad;

                DetalleCompra::create([
                    'id_compra' => $nuevaCompra->id_compra,
                    'id_variante' => $variante->id_variante,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costoUnitario,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                ]);

                // Incrementar existencias
                $variante->update(['stock_actual' => $stockPosterior]);

                // Registrar en Kardex
                MovimientoInventario::create([
                    'id_variante' => $variante->id_variante,
                    'tipo' => 'entrada_compra',
                    'cantidad' => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stockPosterior,
                    'motivo' => "Compra prov. {$nuevaCompra->numero_comprobante}",
                    'id_usuario' => $idUsuario,
                    'fecha' => now(),
                ]);
            }

            return $nuevaCompra;
        });

        return redirect()->route('compras.index')
            ->with('success', "Compra {$compra->numero_comprobante} registrada y stock actualizado correctamente.");
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'usuario', 'detalles.variante.producto']);
        return view('compras.show', compact('compra'));
    }

    /**
     * Anular compra validando no dejar existencias en negativo
     */
    public function anular(Request $request, Compra $compra)
    {
        if ($compra->estado === 'anulada') {
            return back()->with('error', 'Esta compra ya se encuentra anulada.');
        }

        $idUsuario = Auth::id() ?? 1;

        DB::transaction(function () use ($compra, $idUsuario) {
            $compra->load('detalles.variante.producto');

            // 1. Validar que ninguna prenda quede en negativo al descontar
            foreach ($compra->detalles as $detalle) {
                $variante = $detalle->variante;
                if ($variante->stock_actual < $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'error' => "No se puede anular la compra: parte del stock de {$variante->producto->nombre} ({$variante->talla}/{$variante->color}) ya fue vendido. Stock actual: {$variante->stock_actual}, cantidad a restar: {$detalle->cantidad}.",
                    ]);
                }
            }

            // 2. Descontar stock
            foreach ($compra->detalles as $detalle) {
                $variante = $detalle->variante;
                $stockAnterior = $variante->stock_actual;
                $stockPosterior = $stockAnterior - $detalle->cantidad;

                $variante->update(['stock_actual' => $stockPosterior]);

                MovimientoInventario::create([
                    'id_variante' => $variante->id_variante,
                    'tipo' => 'anulacion',
                    'cantidad' => -$detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stockPosterior,
                    'motivo' => "Anulación de compra {$compra->numero_comprobante}",
                    'id_usuario' => $idUsuario,
                    'fecha' => now(),
                ]);
            }

            $compra->update(['estado' => 'anulada']);
        });

        return back()->with('success', "Compra {$compra->numero_comprobante} anulada exitosamente y stock ajustado.");
    }
}
