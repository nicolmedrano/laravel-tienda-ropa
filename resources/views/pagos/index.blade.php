@extends('layouts.app')

@section('title', 'Arqueo de Pagos & Cobros')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Arqueo de Pagos & Cobros</h2>
            <p class="text-muted m-0">Consolidación de cobros registrados en el punto de venta por cada medio de pago.</p>
        </div>
    </div>

    <!-- TARJETAS DE TOTALES POR MÉTODO -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Efectivo en Caja</span>
                <h4 class="fw-bold mb-0" style="color: var(--color-success);">Bs. {{ number_format($totalesPorMetodo['efectivo'] ?? 0, 2) }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 text-center">
                <span class="text-muted small fw-semibold d-block mb-1">QR Simple</span>
                <h4 class="fw-bold mb-0" style="color: var(--color-primary);">Bs. {{ number_format($totalesPorMetodo['qr'] ?? 0, 2) }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Tarjetas Débito/Crédito</span>
                <h4 class="fw-bold mb-0" style="color: var(--color-secondary);">Bs. {{ number_format($totalesPorMetodo['tarjeta'] ?? 0, 2) }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 text-center">
                <span class="text-muted small fw-semibold d-block mb-1">Transferencias</span>
                <h4 class="fw-bold mb-0" style="color: var(--color-info);">Bs. {{ number_format($totalesPorMetodo['transferencia'] ?? 0, 2) }}</h4>
            </div>
        </div>
    </div>

    <!-- TABLA DE PAGOS -->
    <div class="card-boutique overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="font-size: 0.82rem;">
                        <th>ID Pago</th>
                        <th>Factura</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Método</th>
                        <th>Referencia</th>
                        <th class="text-end">Monto (Bs.)</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $p)
                        <tr>
                            <td><span class="text-muted">#{{ $p->id_pago }}</span></td>
                            <td><strong class="font-monospace" style="color: var(--color-primary);">{{ $p->venta->numero_factura ?? '—' }}</strong></td>
                            <td>{{ $p->fecha_pago ? $p->fecha_pago->format('d/m/Y H:i') : '' }}</td>
                            <td>
                                <span class="fw-semibold" style="color: var(--color-text-dark);">{{ $p->venta->cliente ? $p->venta->cliente->nombre_completo : 'Cliente Ocasional' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">{{ $p->metodo }}</span>
                            </td>
                            <td><small class="text-muted">{{ $p->referencia ?? '—' }}</small></td>
                            <td class="text-end fw-bold" style="color: var(--color-primary);">Bs. {{ number_format($p->monto, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $p->estado == 'completado' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($p->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No se encontraron pagos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $pagos->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
