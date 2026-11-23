@extends('layouts.app')

@section('title', 'Panel Principal - Dashboard')

@push('styles')
<style>
    /* Estilos específicos del Dashboard estilo Inventory Management */
    .kpi-card {
        background: var(--card, #FFFFFF);
        border: 1px solid var(--peach-soft, #F3B5A0);
        border-radius: 18px;
        padding: 20px 22px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 18px rgba(74, 31, 42, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(74, 31, 42, 0.10);
    }

    .kpi-card.alert-card {
        border-color: var(--pink, #FBA2A8);
        background: linear-gradient(135deg, rgba(251, 162, 168, 0.15) 0%, rgba(254, 163, 142, 0.08) 100%);
    }

    .kpi-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .kpi-icon-box.coral {
        background: rgba(254, 163, 142, 0.20);
        color: var(--coral-contrast, #D84C38);
    }

    .kpi-icon-box.cream {
        background: rgba(255, 223, 195, 0.60);
        color: #8C4A14;
    }

    .kpi-icon-box.pink {
        background: rgba(251, 162, 168, 0.30);
        color: #C02A48;
    }

    .kpi-icon-box.wine {
        background: rgba(74, 31, 42, 0.12);
        color: var(--wine-dark, #4A1F2A);
    }

    .kpi-value {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.85rem;
        font-weight: 700;
        color: var(--wine-dark, #4A1F2A);
        margin: 6px 0 2px 0;
        line-height: 1.15;
    }

    [data-theme="dark"] .kpi-value {
        color: #FFDFC3;
    }

    .kpi-label {
        color: var(--muted, #7D5863);
        font-size: 0.82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .chart-card {
        background: var(--card, #FFFFFF);
        border: 1px solid var(--peach-soft, #F3B5A0);
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 4px 18px rgba(74, 31, 42, 0.05);
        height: 100%;
    }

    .chart-card-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--wine-dark, #4A1F2A);
        margin: 0;
    }

    [data-theme="dark"] .chart-card-title {
        color: #FFDFC3;
    }

    .chart-card-subtitle {
        font-size: 0.80rem;
        color: var(--muted, #7D5863);
        margin-top: 2px;
    }

    /* Ranking en barras horizontales */
    .ranking-item {
        margin-bottom: 14px;
    }

    .ranking-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--wine-dark, #4A1F2A);
    }

    [data-theme="dark"] .ranking-label {
        color: #FFDFC3;
    }

    .ranking-count {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--coral-contrast, #D84C38);
    }

    .ranking-progress-bar-bg {
        height: 10px;
        background: rgba(243, 181, 160, 0.25);
        border-radius: 50px;
        overflow: hidden;
        margin-top: 5px;
    }

    .ranking-progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--peach-coral) 0%, var(--coral-contrast) 100%);
        border-radius: 50px;
        transition: width 0.8s ease;
    }

    /* Leyenda interactiva de canales */
    .channel-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.80rem;
        font-weight: 600;
        color: var(--wine-dark, #4A1F2A);
    }

    [data-theme="dark"] .channel-legend-item {
        color: #FFDFC3;
    }

    .channel-legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- ENCABEZADO Y ACCIONES PRINCIPALES DEL DASHBOARD -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="font-serif fw-bold m-0" style="color: var(--wine-dark, #4A1F2A); font-size: 1.85rem;">
                Panel de Control &bull; Bella Girls
            </h2>
            <p class="text-muted m-0" style="font-size: 0.88rem;">
                Resumen ejecutivo de ventas, canales comerciales y estado del inventario en tiempo real.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.pos') }}" class="btn btn-boutique-rose fw-bold shadow-sm">
                <i class="bi bi-cart-plus me-1"></i> Nueva Venta (POS)
            </a>
            @if(Auth::user() && Auth::user()->esAdmin())
                <a href="{{ route('compras.create') }}" class="btn btn-outline-boutique fw-semibold">
                    <i class="bi bi-box-arrow-in-down me-1"></i> Ingresar Compra
                </a>
            @endif
        </div>
    </div>

    <!-- 1. FILA DE TARJETAS KPI CON ICONOS (REFERENCIA IMAGEN 3) -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Total de Clientes -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label">Total Clientes</span>
                    <div class="kpi-icon-box cream">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ number_format($totalClientes) }}</div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: var(--peach-soft, #F3B5A0) !important;">
                    <small class="text-muted" style="font-size: 0.78rem;">Cartera activa registrada</small>
                    <a href="{{ route('clientes.index') }}" class="text-decoration-none fw-semibold" style="font-size: 0.78rem; color: var(--coral-contrast);">Ver todos &rarr;</a>
                </div>
            </div>
        </div>

        <!-- KPI 2: Pedidos del Mes -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label">Pedidos del Mes</span>
                    <div class="kpi-icon-box coral">
                        <i class="bi bi-bag-check-fill"></i>
                    </div>
                </div>
                <div class="kpi-value">{{ number_format($pedidosMes) }}</div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: var(--peach-soft, #F3B5A0) !important;">
                    <small class="text-muted" style="font-size: 0.78rem;">Transacciones completadas</small>
                    <a href="{{ route('ventas.index') }}" class="text-decoration-none fw-semibold" style="font-size: 0.78rem; color: var(--coral-contrast);">Historial &rarr;</a>
                </div>
            </div>
        </div>

        <!-- KPI 3: Ventas del Mes -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label">Ventas del Mes</span>
                    <div class="kpi-icon-box wine">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div class="kpi-value" style="color: var(--coral-contrast, #D84C38);">
                    Bs. {{ number_format($ventasMes, 2) }}
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: var(--peach-soft, #F3B5A0) !important;">
                    <small class="text-muted" style="font-size: 0.78rem;">Ingresos netos facturados</small>
                    <span class="badge" style="background: var(--okbg); color: var(--ok); font-size: 0.70rem;">Activo</span>
                </div>
            </div>
        </div>

        <!-- KPI 4: Productos con Stock Bajo (Resaltada en Rosa/Alerta) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card alert-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="kpi-label" style="color: #9C1F38;">Stock Bajo (Alerta)</span>
                    <div class="kpi-icon-box pink">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
                <div class="kpi-value" style="color: #C02A48;">{{ $productosStockBajo }}</div>
                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top" style="border-color: rgba(251, 162, 168, 0.4) !important;">
                    <small class="text-muted" style="font-size: 0.78rem;">Variantes con &le; 5 prendas</small>
                    <a href="#seccionAlertasStock" class="text-decoration-none fw-semibold" style="font-size: 0.78rem; color: #C02A48;">Atender &darr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SECCIÓN DE GRÁFICOS INTERACTIVOS (CHART.JS) -->
    <div class="row g-3 mb-4">
        <!-- Gráfico 1: Líneas/Áreas - Ventas vs. Gastos (6 meses) -->
        <div class="col-12 col-lg-8">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="chart-card-title">Ventas vs. Gastos Operativos</h5>
                        <div class="chart-card-subtitle">Evolución financiera de los últimos 6 meses en Bolivianos (Bs.)</div>
                    </div>
                    <div class="d-flex align-items-center gap-3" style="font-size: 0.78rem; font-weight: 600;">
                        <span class="d-flex align-items-center gap-1">
                            <span style="width: 12px; height: 12px; background: #D84C38; border-radius: 3px; display: inline-block;"></span>
                            <span>Ventas</span>
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span style="width: 12px; height: 12px; background: #FEA38E; border-radius: 3px; display: inline-block;"></span>
                            <span>Gastos / Compras</span>
                        </span>
                    </div>
                </div>
                <div style="position: relative; height: 270px; width: 100%;">
                    <canvas id="chartVentasGastos"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Circular (Doughnut) - Ventas por Canal -->
        <div class="col-12 col-lg-4">
            <div class="chart-card">
                <div class="mb-2">
                    <h5 class="chart-card-title">Ventas por Canal</h5>
                    <div class="chart-card-subtitle">Distribución según WhatsApp, Redes y Tienda</div>
                </div>
                <div style="position: relative; height: 210px; width: 100%;" class="d-flex align-items-center justify-content-center">
                    <canvas id="chartCanalesVenta"></canvas>
                </div>
                <!-- Leyenda de canales personalizada (5 colores oficiales) -->
                <div class="row g-2 mt-2 pt-2 border-top" style="border-color: var(--peach-soft, #F3B5A0) !important;">
                    <div class="col-6">
                        <div class="channel-legend-item">
                            <span class="channel-legend-dot" style="background: #FEA38E;"></span>
                            <span>WhatsApp</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="channel-legend-item">
                            <span class="channel-legend-dot" style="background: #FFDFC3; border: 1px solid #F3B5A0;"></span>
                            <span>Instagram</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="channel-legend-item">
                            <span class="channel-legend-dot" style="background: #FBA2A8;"></span>
                            <span>Facebook</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="channel-legend-item">
                            <span class="channel-legend-dot" style="background: #D84C38;"></span>
                            <span>TikTok</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="channel-legend-item">
                            <span class="channel-legend-dot" style="background: #F3B5A0;"></span>
                            <span>Tienda Física</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. RANKING TOP 10 PRODUCTOS Y ALERTAS DE INVENTARIO -->
    <div class="row g-3 mb-4">
        <!-- Ranking en Barras Horizontales: Top 10 Productos Más Vendidos -->
        <div class="col-12 col-lg-5">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="chart-card-title">Top 10 Productos Más Vendidos</h5>
                        <div class="chart-card-subtitle">Prendas con mayor demanda comercial</div>
                    </div>
                    <a href="{{ route('productos.index') }}" class="btn btn-xs btn-outline-secondary">Catálogo</a>
                </div>

                <div class="overflow-auto pe-1" style="max-height: 380px;">
                    @php
                        $maxVendido = !empty($chartTopData) ? max($chartTopData) : 1;
                        if ($maxVendido == 0) $maxVendido = 1;
                    @endphp

                    @forelse($chartTopLabels as $index => $label)
                        @php
                            $cant = $chartTopData[$index] ?? 0;
                            $porcentaje = min(100, round(($cant / $maxVendido) * 100));
                        @endphp
                        <div class="ranking-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="ranking-label text-truncate" title="{{ $label }}">
                                    <span class="text-muted me-1 fw-bold">#{{ $index + 1 }}</span> {{ $label }}
                                </span>
                                <span class="ranking-count">{{ $cant }} unids.</span>
                            </div>
                            <div class="ranking-progress-bar-bg">
                                <div class="ranking-progress-bar-fill" style="width: {{ $porcentaje }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            Aún no se registran datos suficientes para el ranking.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tablas: Alertas de Stock Bajo y Ventas Recientes -->
        <div class="col-12 col-lg-7" id="seccionAlertasStock">
            <!-- Alertas de Stock -->
            <div class="chart-card mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: var(--peach-soft, #F3B5A0) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-6" style="color: var(--bad, #C0392B);"></i>
                        <h6 class="m-0 fw-bold chart-card-title" style="font-size: 1.05rem;">Alertas de Stock Bajo</h6>
                    </div>
                    @if(Auth::user() && Auth::user()->esAdmin())
                        <a href="{{ route('compras.create') }}" class="btn btn-sm btn-outline-boutique">
                            <i class="bi bi-plus-circle me-1"></i> Reabastecer
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted" style="font-size: 0.78rem;">
                                <th>Prenda</th>
                                <th>Talla / Color</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alertasStock as $variante)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 170px;">{{ $variante->producto->nombre ?? 'Prenda' }}</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $variante->codigo_sku }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(254, 163, 142, 0.2); color: var(--wine-dark); font-size: 0.72rem;">
                                             {{ $variante->talla }} &bull; {{ $variante->color }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <strong style="color: {{ $variante->stock_actual == 0 ? 'var(--bad, #C0392B)' : 'var(--warn, #B87333)' }};">
                                            {{ $variante->stock_actual }}
                                        </strong>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge" style="font-size: 0.70rem; {{ $variante->stock_actual == 0 ? 'background: var(--badbg, #FDEDEC); color: var(--bad, #C0392B); border: 1px solid var(--bad, #C0392B);' : 'background: var(--warnbg, #FFF1E0); color: var(--warn, #B87333); border: 1px solid var(--warn, #B87333);' }}">
                                            {{ $variante->stock_actual == 0 ? 'Agotado' : 'Crítico' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted small">
                                        <i class="bi bi-check-circle text-success me-1"></i> Todo el inventario tiene stock óptimo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ventas Recientes -->
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: var(--peach-soft, #F3B5A0) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt-cutoff" style="color: var(--coral-contrast);"></i>
                        <h6 class="m-0 fw-bold chart-card-title" style="font-size: 1.05rem;">Últimas Ventas</h6>
                    </div>
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-outline-secondary">Ver todas</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted" style="font-size: 0.78rem;">
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th class="text-end">Monto</th>
                                <th class="text-center">Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventasRecientes as $v)
                                <tr>
                                    <td class="fw-bold" style="color: var(--wine-dark);">{{ $v->numero_factura }}</td>
                                    <td>
                                        <div class="small fw-semibold">{{ $v->cliente->nombre_completo ?? 'Cliente Mostrador' }}</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $v->fecha_venta ? $v->fecha_venta->format('d/m/Y H:i') : '' }}</small>
                                    </td>
                                    <td class="text-end fw-bold" style="color: var(--coral-contrast);">
                                        Bs. {{ number_format($v->total, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('ventas.ticket', $v->id_venta) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="Ver Ticket">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted small">
                                        No hay ventas registradas recientemente.
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

@push('scripts')
<!-- Chart.js 4 (Librería Ligera y Confiable para Gráficos en Canvas) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Paleta oficial de tokens en JavaScript
        const COLOR_CORAL = '#D84C38';
        const COLOR_PEACH = '#FEA38E';
        const COLOR_PINK = '#FBA2A8';
        const COLOR_CREAM = '#FFDFC3';
        const COLOR_PEACH_SOFT = '#F3B5A0';
        const COLOR_WINE = '#4A1F2A';

        /* =====================================================================
           1. GRÁFICO DE LÍNEAS / ÁREAS: VENTAS VS. GASTOS (ÚLTIMOS 6 MESES)
           ===================================================================== */
        const ctxVentasGastos = document.getElementById('chartVentasGastos');
        if (ctxVentasGastos) {
            new Chart(ctxVentasGastos, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartMesesLabels) !!},
                    datasets: [
                        {
                            label: 'Ventas (Bs.)',
                            data: {!! json_encode($chartVentasData) !!},
                            borderColor: COLOR_CORAL,
                            backgroundColor: 'rgba(216, 76, 56, 0.12)',
                            fill: true,
                            tension: 0.38,
                            borderWidth: 2.5,
                            pointBackgroundColor: COLOR_CORAL,
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Gastos / Compras (Bs.)',
                            data: {!! json_encode($chartGastosData) !!},
                            borderColor: COLOR_PEACH,
                            backgroundColor: 'rgba(254, 163, 142, 0.08)',
                            fill: true,
                            tension: 0.38,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointBackgroundColor: COLOR_PEACH,
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 3.5,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: COLOR_WINE,
                            titleColor: '#FFDFC3',
                            bodyColor: '#FFFFFF',
                            padding: 10,
                            borderRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: Bs. ${context.parsed.y.toLocaleString('es-BO', { minimumFractionDigits: 2 })}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(243, 181, 160, 0.25)'
                            },
                            ticks: {
                                font: { size: 11 },
                                callback: function(val) {
                                    return 'Bs. ' + val;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 12, weight: '600' } }
                        }
                    }
                }
            });
        }

        /* =====================================================================
           2. GRÁFICO CIRCULAR (DOUGHNUT): VENTAS POR CANAL
           ===================================================================== */
        const ctxCanales = document.getElementById('chartCanalesVenta');
        if (ctxCanales) {
            new Chart(ctxCanales, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartCanalesLabels) !!},
                    datasets: [{
                        data: {!! json_encode($chartCanalesData) !!},
                        backgroundColor: [
                            '#FEA38E', // WhatsApp: Coral / Durazno
                            '#FFDFC3', // Instagram: Crema
                            '#FBA2A8', // Facebook: Rosa
                            '#D84C38', // TikTok: Coral Contraste
                            '#F3B5A0'  // Tienda Física: Durazno Suave
                        ],
                        borderWidth: 3,
                        borderColor: '#FFFFFF',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: COLOR_WINE,
                            titleColor: '#FFDFC3',
                            bodyColor: '#FFFFFF',
                            padding: 10,
                            borderRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    return ` ${context.label}: Bs. ${val.toLocaleString('es-BO', { minimumFractionDigits: 2 })}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
