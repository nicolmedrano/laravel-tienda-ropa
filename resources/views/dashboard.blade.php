@extends('layouts.app')

@section('title', 'Panel Principal')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado de bienvenida -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-serif fw-bold mb-1" style="color: var(--boutique-dark);">Panel de Control</h2>
            <p class="text-muted m-0">Monitoreo general de ventas, compras, inventario y relaciones operativas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.pos') }}" class="btn btn-boutique-rose">
                <i class="bi bi-cart-plus me-1"></i> Nueva Venta (POS)
            </a>
            <a href="{{ route('compras.create') }}" class="btn btn-boutique">
                <i class="bi bi-box-arrow-in-down me-1"></i> Ingresar Compra
            </a>
        </div>
    </div>

    <!-- TARJETAS DE INDICADORES (KPIs) -->
    <div class="row g-3 mb-4">
        <!-- Ingresos por Ventas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-boutique p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Ingresos Ventas</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #EBF7EE; color: #1E6B34; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">Bs. {{ number_format($totalVentas, 2) }}</h3>
                <small class="text-muted">{{ $cantidadVentas }} ventas completadas</small>
            </div>
        </div>

        <!-- Inversión en Compras -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-boutique p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Total Compras</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #FDF2F4; color: #9E5664; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">Bs. {{ number_format($totalCompras, 2) }}</h3>
                <small class="text-muted">{{ $cantidadCompras }} órdenes a proveedores</small>
            </div>
        </div>

        <!-- Catálogo de Prendas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-boutique p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Prendas en Catálogo</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #F4EBE2; color: #7A5333; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-handbag"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalPrendas }}</h3>
                <small class="text-muted">Modelos activos con fotos</small>
            </div>
        </div>

        <!-- Clientes Registrados -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-boutique p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Cartera de Clientes</span>
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: #F0F4F8; color: #2B579A; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalClientes }}</h3>
                <small class="text-muted">Clientes con historial de compras</small>
            </div>
        </div>
    </div>

    <!-- SECCIÓN: ALERTAS DE STOCK BAJO & VENTAS RECIENTES -->
    <div class="row g-4">
        <!-- Alertas de Stock Bajo -->
        <div class="col-12 col-lg-6">
            <div class="card-boutique h-100">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                        <h6 class="m-0 fw-bold">Alertas de Stock Bajo</h6>
                    </div>
                    <a href="{{ route('compras.create') }}" class="btn btn-sm btn-outline-secondary">Reabastecer</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size: 0.8rem;">
                                <th>Prenda</th>
                                <th>SKU / Talla</th>
                                <th>Color</th>
                                <th class="text-center">Stock</th>
                                <th>Acción</th>
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
                                        <span class="badge {{ $variante->stock_actual == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                            {{ $variante->stock_actual }} un.
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('compras.create') }}" class="btn btn-sm btn-light border py-1 px-2" title="Comprar a proveedor">
                                            <i class="bi bi-cart-plus"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
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
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary"></i>
                        <h6 class="m-0 fw-bold">Últimas Ventas Registradas</h6>
                    </div>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-outline-secondary">Ver Todas</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="font-size: 0.8rem;">
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th class="text-end">Total</th>
                                <th>Ticket</th>
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
                                    <td class="text-end fw-bold">
                                        Bs. {{ number_format($venta->total, 2) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('ventas.ticket', $venta->id_venta) }}" class="btn btn-sm btn-light border py-1 px-2" title="Imprimir Comprobante">
                                            <i class="bi bi-printer"></i>
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
