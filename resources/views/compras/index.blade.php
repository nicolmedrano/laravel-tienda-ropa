@extends('layouts.app')

@section('title', 'Módulo Compra - Órdenes a Proveedores')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1" style="font-size: 0.72rem;">Compra</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Compras & Abastecimiento</h2>
            </div>
            <p class="text-muted m-0">Recepción de mercadería de proveedores y actualización automática de existencias.</p>
        </div>
        <div>
            <a href="{{ route('compras.create') }}" class="btn btn-boutique-rose">
                <i class="bi bi-box-arrow-in-down me-1"></i> Registrar Nueva Compra
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('compras.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por comprobante o proveedor..." value="{{ request('buscar') }}">
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
                    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE COMPRAS -->
    <div class="card-boutique overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="font-size: 0.82rem;">
                        <th>Comprobante</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Registrado Por</th>
                        <th>Prendas</th>
                        <th class="text-end">Total (Bs.)</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $c)
                        <tr>
                            <td><span class="fw-bold font-monospace" style="color: var(--color-primary);">{{ $c->numero_comprobante }}</span></td>
                            <td>{{ $c->fecha_compra ? $c->fecha_compra->format('d/m/Y H:i') : '' }}</td>
                            <td>
                                <div class="fw-semibold" style="color: var(--color-text-dark);">{{ $c->proveedor->nombre_empresa ?? 'Proveedor General' }}</div>
                                <small class="text-muted">{{ $c->proveedor->contacto ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $c->usuario->nombre ?? 'Usuario' }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $c->detalles->sum('cantidad') }} un. ingresadas</small>
                            </td>
                            <td class="text-end fw-bold" style="color: var(--color-primary);">
                                Bs. {{ number_format($c->total, 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $c->estado == 'completada' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($c->estado) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($c->estado == 'completada')
                                        <form action="{{ route('compras.anular', $c->id_compra) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Anular esta compra? Se descontará el stock ingresado previa validación.');">
                                            @csrf
                                            <button type="submit" class="btn btn-light border text-danger" title="Anular compra">
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
                                No se encontraron registros de compras.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $compras->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
