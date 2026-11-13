@extends('layouts.app')

@section('title', 'Panel Principal')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado de bienvenida -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <div>
            <h2 class="font-serif fw-bold mb-1 page-title">Panel de Control</h2>
            <p class="page-subtitle m-0">Monitoreo general de ventas, catálogo de inventario y relaciones operativas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.pos') }}" class="btn btn-boutique-rose fw-semibold">
                <i class="bi bi-cart-plus me-1"></i> Nueva Venta (POS)
            </a>
            @if(Auth::user() && Auth::user()->esAdmin())
            <a href="{{ route('compras.create') }}" class="btn btn-outline-boutique fw-semibold">
                <i class="bi bi-box-arrow-in-down me-1"></i> Ingresar Compra
            </a>
            @endif
        </div>
    </div>

    <!-- TARJETAS DE INDICADORES (KPIs) -->
    <div class="row g-3 mb-3">
        <!-- Ingresos por Ventas -->
        <div class="col-12 col-sm-6 {{ Auth::user() && Auth::user()->esAdmin() ? 'col-xl-3' : 'col-xl-4' }}">
            <div class="card-boutique p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold kpi-label">Ingresos Ventas</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--color-success-bg); color: var(--color-success-text); border: 1px solid var(--color-success-border); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 kpi-value" style="color: var(--color-success-text);">Bs. {{ number_format($totalVentas, 2) }}</h3>
                <small class="text-muted kpi-subtext">{{ $cantidadVentas }} ventas completadas</small>
            </div>
        </div>

        @if(Auth::user() && Auth::user()->esAdmin())
        <!-- Inversión en Compras (Solo Administrador) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-boutique p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold kpi-label">Total Compras</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--color-bg-subtle); color: var(--color-primary); border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 kpi-value" style="color: var(--color-primary);">Bs. {{ number_format($totalCompras, 2) }}</h3>
                <small class="text-muted kpi-subtext">{{ $cantidadCompras }} órdenes a proveedores</small>
            </div>
        </div>
        @endif

        <!-- Catálogo de Prendas -->
        <div class="col-12 col-sm-6 {{ Auth::user() && Auth::user()->esAdmin() ? 'col-xl-3' : 'col-xl-4' }}">
            <div class="card-boutique p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold kpi-label">Prendas en Catálogo</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--color-bg-light); color: var(--color-text-dark); border: 1px solid var(--color-secondary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="bi bi-handbag"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 kpi-value" style="color: var(--color-primary-hover);">{{ $totalPrendas }}</h3>
                <small class="text-muted kpi-subtext">Modelos activos con fotos</small>
            </div>
        </div>

        <!-- Clientes Registrados -->
        <div class="col-12 col-sm-6 {{ Auth::user() && Auth::user()->esAdmin() ? 'col-xl-3' : 'col-xl-4' }}">
            <div class="card-boutique p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold kpi-label">Cartera de Clientes</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: var(--boutique-gradient); color: var(--color-text-on-primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; box-shadow: var(--shadow-pink-xs);">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 kpi-value" style="color: var(--color-text-dark);">{{ $totalClientes }}</h3>
                <small class="text-muted kpi-subtext">Clientes con historial registrado</small>
            </div>
        </div>
    </div>

    <!-- SECCIÓN: ALERTAS DE STOCK BAJO & VENTAS RECIENTES -->
    <div class="row g-3">
        <!-- Alertas de Stock Bajo -->
        <div class="col-12 col-lg-6">
            <div class="card-boutique h-100">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--color-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill" style="color: var(--color-warning); font-size: 1rem;"></i>
                        <h6 class="m-0 fw-bold card-title-boutique">Alertas de Stock Bajo</h6>
                    </div>
                    @if(Auth::user() && Auth::user()->esAdmin())
                        <a href="{{ route('compras.create') }}" class="btn btn-sm btn-outline-secondary">Reabastecer</a>
                    @else
                        <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-secondary">Ver Catálogo</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Prenda</th>
                                <th>SKU / Talla</th>
                                <th>Color</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alertasStock as $variante)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $variante->producto->nombre ?? 'Prenda' }}</div>
                                        <small class="text-muted">{{ $variante->producto->categoria->nombre ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $variante->talla }}</span>
                                        <small class="text-muted d-block">{{ $variante->codigo_sku }}</small>
                                    </td>
                                    <td>{{ $variante->color }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $variante->stock_actual == 0 ? 'bg-danger' : 'bg-warning' }}">
                                            {{ $variante->stock_actual }} un.
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if(Auth::user() && Auth::user()->esAdmin())
                                            <a href="{{ route('compras.create') }}" class="btn btn-action btn-light border" title="Comprar a proveedor">
                                                <i class="bi bi-cart-plus text-primary"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('productos.show', $variante->id_producto) }}" class="btn btn-action btn-light border" title="Ver Prenda en Catálogo">
                                                <i class="bi bi-eye text-primary"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-4 d-block mb-1" style="color: var(--color-success);"></i>
                                        Todas las prendas tienen stock óptimo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Últimas Ventas Procesadas -->
        <div class="col-12 col-lg-6">
            <div class="card-boutique h-100">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="border-color: var(--color-border) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history" style="color: var(--color-primary); font-size: 1rem;"></i>
                        <h6 class="m-0 fw-bold card-title-boutique">Últimas Ventas Registradas</h6>
                    </div>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-outline-secondary">Ver Todas</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventasRecientes as $venta)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $venta->numero_factura }}</span>
                                        <small class="text-muted d-block">{{ $venta->fecha_venta ? $venta->fecha_venta->format('d/m/Y H:i') : '' }}</small>
                                    </td>
                                    <td>{{ $venta->cliente ? $venta->cliente->nombre_completo : 'Cliente Ocasional' }}</td>
                                    <td>
                                        <small class="text-muted">{{ $venta->usuario->nombre ?? 'Usuario' }}</small>
                                    </td>
                                    <td class="text-end fw-bold" style="color: var(--color-primary);">
                                        Bs. {{ number_format($venta->total, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('ventas.ticket', $venta->id_venta) }}" class="btn btn-action btn-light border" title="Imprimir Comprobante">
                                            <i class="bi bi-printer text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No se han registrado ventas recientemente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
