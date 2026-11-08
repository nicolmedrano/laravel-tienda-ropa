@extends('layouts.app')

@section('title', 'Módulo Producto - Catálogo de Prendas')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado de Producto -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1" style="font-size: 0.72rem;">Producto</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Catálogo de Productos</h2>
            </div>
            <p class="text-muted m-0">Prendas de vestir con fotografía, control de tallas, colores y existencias sincronizadas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.tabla') }}" class="btn btn-outline-boutique">
                <i class="bi bi-table me-1"></i> Vista Tabla
            </a>
            <a href="{{ route('productos.create') }}" class="btn btn-boutique-rose">
                <i class="bi bi-plus-lg me-1"></i> Registrar Prenda
            </a>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre o código (ej. BLU-001)..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="categoria" class="form-select" onchange="this.form.submit()">
                    <option value="">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id_categoria }}" {{ request('categoria') == $cat->id_categoria ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-boutique w-100">Filtrar</button>
                @if(request()->hasAny(['buscar', 'categoria']))
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- CUADRÍCULA DE TARJETAS DE CATÁLOGO -->
    <div class="row g-4">
        @forelse($productos as $producto)
            @php
                $stockTotal = $producto->stock_total;
                $tallasUnicas = $producto->variantes->pluck('talla')->unique()->values();
                $coloresUnicos = $producto->variantes->pluck('color')->unique()->values();
            @endphp
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card-boutique h-100 d-flex flex-column overflow-hidden position-relative">
                    
                    <!-- FOTOGRAFÍA UNIFORME EN LA PARTE SUPERIOR (object-fit: contain) -->
                    <div style="height: 240px; background: var(--color-bg-subtle); display: flex; align-items: center; justify-content: center; position: relative; border-bottom: 1px solid var(--color-border); padding: 12px;">
                        @if($producto->imagen_url)
                            <img src="{{ $producto->imagen_url }}" 
                                 alt="{{ $producto->nombre }}" 
                                 style="max-height: 100%; max-width: 100%; object-fit: contain; border-radius: 6px;"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-center text-muted\'><i class=\'bi bi-handbag display-4\' style=\'color: var(--color-secondary);\'></i><span class=\'d-block small mt-1\'>Sin foto</span></div>';">
                        @else
                            <!-- IMAGEN DE RESPALDO CON PALETA ROSADA -->
                            <div class="text-center text-muted py-4">
                                <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--color-bg-light); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px; border: 1px solid var(--color-accent);">
                                    <i class="bi bi-handbag" style="font-size: 2rem; color: var(--color-primary);"></i>
                                </div>
                                <span class="d-block text-muted" style="font-size: 0.78rem;">Prenda sin fotografía</span>
                            </div>
                        @endif

                        <!-- Badge de stock disponible arriba a la derecha -->
                        <span class="position-absolute top-0 end-0 m-2 badge {{ $stockTotal > 5 ? 'bg-light text-dark border' : ($stockTotal > 0 ? 'bg-warning' : 'bg-danger') }}" style="box-shadow: var(--shadow-pink-xs);">
                            @if($stockTotal > 0)
                                Stock: {{ $stockTotal }} un.
                            @else
                                Agotado
                            @endif
                        </span>
                    </div>

                    <!-- CUERPO DE LA TARJETA -->
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <!-- Código y Categoría -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">{{ $producto->codigo }}</span>
                            <span class="badge badge-pink-soft" style="font-size: 0.72rem;">
                                {{ $producto->categoria->nombre ?? 'Sin Categoría' }}
                            </span>
                        </div>

                        <!-- Nombre de la Prenda -->
                        <h5 class="fw-bold mb-1" style="font-size: 1.05rem; color: var(--color-text-dark);">
                            <a href="{{ route('productos.show', $producto->id_producto) }}" class="text-decoration-none" style="color: var(--color-text-dark);">
                                {{ $producto->nombre }}
                            </a>
                        </h5>

                        <!-- Descripción breve -->
                        <p class="text-muted mb-2" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.4em;">
                            {{ $producto->descripcion ?? 'Prenda de alta costura confeccionada con tejidos seleccionados.' }}
                        </p>

                        <!-- Tallas y Colores disponibles -->
                        <div class="mb-3 pt-2 border-top" style="border-color: var(--color-border) !important;">
                            <div class="d-flex align-items-center justify-content-between mb-1" style="font-size: 0.75rem;">
                                <span class="text-muted">Tallas:</span>
                                <div class="d-flex gap-1 flex-wrap justify-content-end">
                                    @foreach($tallasUnicas as $t)
                                        <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ $t }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between" style="font-size: 0.75rem;">
                                <span class="text-muted">Colores:</span>
                                <span class="fw-semibold text-end" style="color: var(--color-text-dark); max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $coloresUnicos->implode(', ') }}
                                </span>
                            </div>
                        </div>

                        <!-- PRECIO EN BOLIVIANOS (Bs.) Y BOTÓN AGREGAR AL CARRITO "+" -->
                        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--color-border) !important;">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1;">Precio venta</small>
                                <span class="fw-bold" style="font-size: 1.25rem; color: var(--color-primary);">
                                    Bs. {{ number_format($producto->precio_venta, 2) }}
                                </span>
                            </div>

                            <div class="d-flex gap-1">
                                <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-sm btn-light border py-2 px-2" title="Editar prenda y foto">
                                    <i class="bi bi-pencil text-primary"></i>
                                </a>
                                @if($stockTotal > 0)
                                    <a href="{{ route('ventas.pos') }}?agregar={{ $producto->id_producto }}" class="btn btn-boutique-rose py-2 px-3 fw-bold" title="Agregar al carrito de venta" style="border-radius: 8px;">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary py-2 px-3 disabled" title="Sin existencias">
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-boutique p-5 text-center text-muted">
                    <i class="bi bi-search display-4 d-block mb-3" style="color: var(--color-primary);"></i>
                    <h5 class="fw-bold" style="color: var(--color-text-dark);">No se encontraron prendas con los filtros seleccionados</h5>
                    <p class="mb-3">Puede modificar la búsqueda o registrar una nueva prenda en el catálogo.</p>
                    <a href="{{ route('productos.create') }}" class="btn btn-boutique-rose">
                        <i class="bi bi-plus-lg me-1"></i> Registrar Nueva Prenda
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $productos->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
