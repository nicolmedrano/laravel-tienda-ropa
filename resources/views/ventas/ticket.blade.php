@extends('layouts.app')

@section('title', 'Comprobante de Venta - ' . $venta->numero_factura)

@section('content')
<div class="container-fluid p-0 d-flex flex-column align-items-center">
    <!-- Botones de Acción Superiores -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print" style="width: 100%; max-width: 480px;">
        <a href="{{ route('ventas.pos') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al POS
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-list me-1"></i> Ventas
            </a>
            <button onclick="window.print()" class="btn btn-boutique-rose btn-sm fw-bold">
                <i class="bi bi-printer me-1"></i> Imprimir Ticket
            </button>
        </div>
    </div>

    <!-- TICKET DE VENTA (ESTILO TÉRMICO ELEGANTE) -->
    <div class="card-boutique p-4 bg-white shadow-sm ticket-imprimible" style="width: 100%; max-width: 460px; font-family: 'Plus Jakarta Sans', monospace; font-size: 0.88rem; border: 1px dashed var(--boutique-border);">
        <!-- Encabezado de la Tienda -->
        <div class="text-center pb-3 border-bottom mb-3">
            <h4 class="font-serif fw-bold m-0" style="letter-spacing: -0.5px;">BOUTIQUE ELEGANCE</h4>
            <div class="text-muted small">Alta Costura y Confecciones</div>
            <div class="small">Av. Principal Los Rosales #1024, La Paz - Bolivia</div>
            <div class="small">Teléfono: +591 2 2790000 &bull; NIT: 8493021018</div>
        </div>

        <!-- Datos del Comprobante y Relaciones del Sistema -->
        <div class="mb-3 small">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Factura N°:</span>
                <span class="fw-bold">{{ $venta->numero_factura }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Fecha y Hora:</span>
                <span>{{ $venta->fecha_venta ? $venta->fecha_venta->format('d/m/Y H:i:s') : now()->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Atendido por:</span>
                <span>{{ $venta->usuario->nombre ?? 'Valentina' }} {{ $venta->usuario->apellido ?? 'Nicol' }} ({{ ucfirst($venta->usuario->rol ?? 'Vendedora') }})</span>
            </div>
            <div class="d-flex justify-content-between pt-1 mt-1 border-top">
                <span class="text-muted">Cliente:</span>
                <span class="fw-semibold">{{ $venta->cliente ? $venta->cliente->nombre_completo : 'Cliente Ocasional' }}</span>
            </div>
            @if($venta->cliente && $venta->cliente->telefono)
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Teléfono:</span>
                    <span>{{ $venta->cliente->telefono }}</span>
                </div>
            @endif
        </div>

        <!-- Tabla de Prendas Vendidas -->
        <div class="border-top border-bottom py-2 mb-3">
            <table class="w-100 small">
                <thead>
                    <tr class="text-muted border-bottom" style="font-size: 0.75rem;">
                        <th class="text-start pb-1">Prenda / Talla / Color</th>
                        <th class="text-center pb-1">Cant.</th>
                        <th class="text-end pb-1">P. Unit</th>
                        <th class="text-end pb-1">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->detalles as $det)
                        <tr>
                            <td class="py-1">
                                <div class="fw-semibold">{{ $det->variante->producto->nombre ?? 'Prenda' }}</div>
                                <small class="text-muted">{{ $det->variante->talla ?? '' }} / {{ $det->variante->color ?? '' }}</small>
                            </td>
                            <td class="text-center py-1">{{ $det->cantidad }}</td>
                            <td class="text-end py-1">Bs. {{ number_format($det->precio_unitario, 2) }}</td>
                            <td class="text-end py-1 fw-bold">Bs. {{ number_format($det->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="mb-3 small">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Subtotal:</span>
                <span>Bs. {{ number_format($venta->subtotal, 2) }}</span>
            </div>
            @if($venta->descuento > 0)
                <div class="d-flex justify-content-between mb-1 text-success">
                    <span>Descuento aplicado:</span>
                    <span>- Bs. {{ number_format($venta->descuento, 2) }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between fs-5 fw-bold pt-1 border-top" style="color: var(--boutique-dark);">
                <span>TOTAL:</span>
                <span>Bs. {{ number_format($venta->total, 2) }}</span>
            </div>
        </div>

        <!-- Métodos de Pago Registrados -->
        <div class="p-2 bg-light rounded small mb-3 border">
            <div class="fw-semibold mb-1">Detalle del Pago:</div>
            @foreach($venta->pagos as $pago)
                <div class="d-flex justify-content-between">
                    <span>Método: <strong class="text-uppercase">{{ $pago->metodo }}</strong></span>
                    <span>Bs. {{ number_format($pago->monto, 2) }}</span>
                </div>
                @if($pago->referencia)
                    <div class="text-muted" style="font-size: 0.75rem;">Ref: {{ $pago->referencia }}</div>
                @endif
            @endforeach
        </div>

        <!-- Pie de Ticket -->
        <div class="text-center text-muted small pt-2 border-top">
            <div class="fw-semibold mb-1">¡Gracias por su preferencia!</div>
            <div>Prendas exclusivas diseñadas para resaltar su elegancia.</div>
            <div class="mt-2" style="font-size: 0.7rem;">Cambios permitidos dentro de los 7 días con este comprobante y etiquetas intactas.</div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body {
            background-color: #FFFFFF !important;
        }
        .sidebar, .top-navbar, .no-print {
            display: none !important;
        }
        .main-wrapper {
            margin-left: 0 !important;
        }
        .content-area {
            padding: 0 !important;
        }
        .ticket-imprimible {
            border: none !important;
            box-shadow: none !important;
            max-width: 100% !important;
            width: 100% !important;
        }
    }
</style>
@endpush
