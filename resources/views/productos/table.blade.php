@extends('layouts.app')

@section('title', 'Módulo Producto - Gestión Administrativa')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1" style="font-size: 0.72rem;">Producto</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Inventario & Tabla de Prendas</h2>
            </div>
            <p class="text-muted m-0">Lista detallada de códigos, costos, precios, stock total por prenda y acciones.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.index') }}" class="btn btn-outline-boutique">
                <i class="bi bi-grid me-1"></i> Ver Catálogo de Fotos
            </a>
            <a href="{{ route('productos.create') }}" class="btn btn-boutique-rose">
                <i class="bi bi-plus-lg me-1"></i> Registrar Prenda
            </a>
        </div>
    </div>

    <!-- Filtro de Búsqueda -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('productos.tabla') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por código o prenda..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-boutique w-100">Buscar</button>
                @if(request('buscar'))
                    <a href="{{ route('productos.tabla') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE PRENDAS -->
    <div class="card-boutique overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="font-size: 0.82rem;">
                        <th style="width: 70px;">Foto</th>
                        <th>Código</th>
                        <th>Prenda</th>
                        <th>Categoría</th>
                        <th class="text-end">Costo</th>
                        <th class="text-end">Precio (Bs.)</th>
                        <th class="text-center">Stock Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $prod)
                        @php $stockTotal = $prod->stock_total; @endphp
                        <tr>
                            <td>
                                <div style="width: 50px; height: 50px; background: var(--color-bg-subtle); border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--color-border);">
                                    @if($prod->imagen_url)
                                        <img src="{{ $prod->imagen_url }}" alt="{{ $prod->nombre }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                        <i class="bi bi-handbag text-muted"></i>
                                    @endif
                                </div>
                            </td>
                            <td><span class="fw-bold font-monospace" style="color: var(--color-primary);">{{ $prod->codigo }}</span></td>
                            <td>
                                <div class="fw-semibold" style="color: var(--color-text-dark);">{{ $prod->nombre }}</div>
                                <small class="text-muted">{{ $prod->variantes->count() }} variantes registradas</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $prod->categoria->nombre ?? 'N/A' }}</span>
                            </td>
                            <td class="text-end text-muted">Bs. {{ number_format($prod->costo_compra, 2) }}</td>
                            <td class="text-end fw-bold" style="color: var(--color-primary);">Bs. {{ number_format($prod->precio_venta, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $stockTotal > $prod->stock_minimo ? 'bg-success' : ($stockTotal > 0 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $stockTotal }} un.
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $prod->estado ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $prod->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('productos.edit', $prod->id_producto) }}" class="btn btn-light border" title="Editar prenda y foto">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </a>
                                    <form action="{{ route('productos.destroy', $prod->id_producto) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar esta prenda del catálogo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Desactivar">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                No se encontraron registros de prendas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
