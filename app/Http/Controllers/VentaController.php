<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
     * Registrar una nueva venta con transacción ACID en PostgreSQL
     */
    public function store(StoreVentaRequest $request)
    {
        $data = $request->validated();
        $idUsuario = Auth::id() ?? 1;

        $venta = DB::transaction(function () use ($data, $idUsuario) {
            $subtotalGeneral = 0;

            // 1. Validar existencias y calcular subtotales bloqueando filas con lockForUpdate()
            $itemsValidados = [];
            foreach ($data['items'] as $item) {
                $variante = ProductoVariante::with('producto')
                    ->where('id_variante', $item['id_variante'])
                    ->lockForUpdate() // Bloqueo de fila para consistencia concurrente
                    ->firstOrFail();

                $cantidad = (int) $item['cantidad'];

                if ($variante->stock_actual < $cantidad) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$variante->producto->nombre} (Talla: {$variante->talla}, Color: {$variante->color}). Stock actual: {$variante->stock_actual}, solicitado: {$cantidad}.",
                    ]);
                }

                $precioUnitario = (float) $item['precio_unitario'];
                $subtotalLinea = $cantidad * $precioUnitario;
                $subtotalGeneral += $subtotalLinea;

                $itemsValidados[] = [
                    'variante' => $variante,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalLinea,
                ];
            }

            $descuento = (float) ($data['descuento'] ?? 0);
            $totalFinal = max(0, $subtotalGeneral - $descuento);

            // 2. Generar correlativo de factura
            $ultimoId = Venta::max('id_venta') ?? 0;
            $numeroFactura = 'FAC-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

            // 3. Crear registro de Venta
            $nuevaVenta = Venta::create([
                'numero_factura' => $numeroFactura,
                'id_cliente' => $data['id_cliente'] ?? null,
                'id_usuario' => $idUsuario,
                'fecha_venta' => now(),
                'subtotal' => $subtotalGeneral,
                'descuento' => $descuento,
                'total' => $totalFinal,
                'estado' => 'completada',
            ]);

            // 4. Crear detalles, descontar stock y asentar Kardex
            foreach ($itemsValidados as $item) {
                $variante = $item['variante'];
                $stockAnterior = $variante->stock_actual;
                $stockPosterior = $stockAnterior - $item['cantidad'];

                DetalleVenta::create([
                    'id_venta' => $nuevaVenta->id_venta,
                    'id_variante' => $variante->id_variante,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => now(),
                ]);

                // Actualizar inventario
                $variante->update(['stock_actual' => $stockPosterior]);

                // Registrar en Kardex
                MovimientoInventario::create([
                    'id_variante' => $variante->id_variante,
                    'tipo' => 'salida_venta',
                    'cantidad' => -$item['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stockPosterior,
                    'motivo' => "Venta {$nuevaVenta->numero_factura}",
                    'id_usuario' => $idUsuario,
                    'fecha' => now(),
                ]);
            }

            // 5. Registrar Pago
            Pago::create([
                'id_venta' => $nuevaVenta->id_venta,
                'monto' => $totalFinal,
                'metodo' => $data['metodo_pago'],
                'referencia' => $data['referencia_pago'] ?? null,
                'estado' => 'completado',
                'fecha_pago' => now(),
            ]);

            return $nuevaVenta;
        });

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
     * Cancelar / anular venta con reversión de inventario
     */
    public function anular(Request $request, Venta $venta)
    {
        if ($venta->estado === 'anulada') {
            return back()->with('error', 'Esta venta ya se encuentra anulada.');
        }

        $idUsuario = Auth::id() ?? 1;

        DB::transaction(function () use ($venta, $idUsuario) {
            $venta->load('detalles.variante');

            // Revertir existencias de cada prenda
            foreach ($venta->detalles as $detalle) {
                $variante = $detalle->variante;
                $stockAnterior = $variante->stock_actual;
                $stockPosterior = $stockAnterior + $detalle->cantidad;

                $variante->update(['stock_actual' => $stockPosterior]);

                MovimientoInventario::create([
                    'id_variante' => $variante->id_variante,
                    'tipo' => 'anulacion',
                    'cantidad' => $detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_posterior' => $stockPosterior,
                    'motivo' => "Anulación de venta {$venta->numero_factura}",
                    'id_usuario' => $idUsuario,
                    'fecha' => now(),
                ]);
            }

            $venta->update(['estado' => 'anulada']);

            // Actualizar pagos asociados
            Pago::where('id_venta', $venta->id_venta)->update(['estado' => 'reembolsado']);
        });

        return back()->with('success', "Venta {$venta->numero_factura} anulada. El stock fue revertido al inventario.");
    }
}
