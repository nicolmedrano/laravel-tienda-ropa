@extends('layouts.app')

@section('title', 'Módulo Venta - Historial de Ventas')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark text-white px-2 py-1" style="font-size: 0.72rem;">Módulo 2 de 6</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--boutique-dark);">Historial de Ventas</h2>
            </div>
            <p class="text-muted m-0">Registro histórico de transacciones, tickets emitidos y reversión de inventario.</p>
        </div>
        <div>
            <a href="{{ route('ventas.pos') }}" class="btn btn-boutique-rose">
                <i class="bi bi-cart-plus me-1"></i> Nueva Venta (POS)
            </a>
        </div>
    </div>

    <!-- Filtro de Búsqueda -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('ventas.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por N° Factura o nombre de cliente..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <select name="estado" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos los Estados</option>
                    <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-boutique w-100">Filtrar</button>
                @if(request()->hasAny(['buscar', 'estado']))
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE VENTAS -->
    <div class="card-boutique overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr style="font-size: 0.82rem;">
                        <th>Factura</th>
                        <th>Fecha y Hora</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Ítems</th>
                        <th class="text-end">Total (Bs.)</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $v)
                        <tr>
                            <td><span class="fw-bold font-monospace">{{ $v->numero_factura }}</span></td>
                            <td>
                                <div>{{ $v->fecha_venta ? $v->fecha_venta->format('d/m/Y') : '' }}</div>
                                <small class="text-muted">{{ $v->fecha_venta ? $v->fecha_venta->format('H:i') : '' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $v->cliente ? $v->cliente->nombre_completo : 'Cliente Ocasional' }}</div>
                                @if($v->cliente && $v->cliente->telefono)
                                    <small class="text-muted">{{ $v->cliente->telefono }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $v->usuario->nombre ?? 'Usuario' }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $v->detalles->sum('cantidad') }} prenda(s)</small>
                            </td>
                            <td class="text-end fw-bold">
                                Bs. {{ number_format($v->total, 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $v->estado == 'completada' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($v->estado) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('ventas.ticket', $v->id_venta) }}" class="btn btn-light border" title="Ver / Imprimir Ticket">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    @if($v->estado == 'completada')
                                        <form action="{{ route('ventas.anular', $v->id_venta) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de anular esta venta? Las prendas serán devueltas automáticamente al inventario.');">
                                            @csrf
                                            <button type="submit" class="btn btn-light border text-danger" title="Anular venta y reincorporar stock">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No se encontraron registros de ventas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $ventas->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
