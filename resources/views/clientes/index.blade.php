@extends('layouts.app')

@section('title', 'Módulo Cliente - Clientes de la Tienda')

@push('styles')
<!-- Leaflet CSS para Mapas Interactivos de Entrega (Ligero, OpenSource, sin API Key) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Estilos específicos para el Módulo de Clientes Bella Girls */
    .client-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        background: linear-gradient(135deg, var(--peach-coral, #FEA38E), var(--coral-contrast, #D84C38));
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(216, 76, 56, 0.25);
        flex-shrink: 0;
    }

    .client-avatar.empresa {
        background: linear-gradient(135deg, #4A1F2A, #36121D);
    }

    /* Pestañas del Modal */
    .nav-tabs-boutique {
        border-bottom: 2px solid var(--peach-soft, #F3B5A0);
        gap: 6px;
    }

    .nav-tabs-boutique .nav-link {
        border: none;
        color: var(--muted, #7D5863);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 10px 16px;
        border-radius: 8px 8px 0 0;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        background: transparent;
    }

    .nav-tabs-boutique .nav-link:hover {
        color: var(--coral-contrast, #D84C38);
        background: rgba(254, 163, 142, 0.12);
    }

    .nav-tabs-boutique .nav-link.active {
        color: var(--coral-contrast, #D84C38);
        background: var(--card, #FFFFFF);
        border-bottom: 3px solid var(--coral-contrast, #D84C38);
    }

    /* Badges de Etiquetas Comerciales */
    .badge-etiqueta-vip {
        background-color: #FFF3CD !important;
        color: #856404 !important;
        border: 1px solid #FFEBAA !important;
    }

    .badge-etiqueta-frecuente {
        background-color: var(--cream, #FFDFC3) !important;
        color: #8C4A14 !important;
        border: 1px solid var(--peach-soft, #F3B5A0) !important;
    }

    .badge-etiqueta-mayorista {
        background-color: #E8E0F5 !important;
        color: #4A154B !important;
        border: 1px solid #D5C2EB !important;
    }

    .badge-etiqueta-nuevo {
        background-color: #D1E7DD !important;
        color: #0F5132 !important;
        border: 1px solid #BADBCC !important;
    }

    /* Selector interactivo de etiquetas en el modal */
    .tag-choice-badge {
        cursor: pointer;
        user-select: none;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1.5px solid var(--peach-soft, #F3B5A0);
        background: var(--card, #FFFFFF);
        color: var(--muted, #7D5863);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .tag-choice-badge.selected {
        border-color: var(--coral-contrast, #D84C38);
        background: var(--coral-contrast, #D84C38);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(216, 76, 56, 0.35);
    }

    /* Orígenes Chips */
    .origin-badge {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(254, 163, 142, 0.12);
        border: 1px solid var(--peach-soft, #F3B5A0);
    }

    /* Contenedor del Mapa */
    #mapaEntrega {
        height: 240px;
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid var(--peach-soft, #F3B5A0);
        z-index: 1;
    }

    /* Tarjetas de múltiples direcciones */
    .card-direccion-item {
        border: 1.5px solid var(--peach-soft, #F3B5A0);
        border-radius: 12px;
        padding: 12px;
        background: var(--card, #FFFFFF);
        transition: all 0.2s ease;
        position: relative;
    }

    .card-direccion-item.es-principal {
        border-color: var(--coral-contrast, #D84C38);
        background: rgba(254, 163, 142, 0.06);
    }

    /* Input con prefijo de teléfono de Bolivia */
    .input-group-phone .input-group-text {
        background-color: var(--cream, #FFDFC3);
        border-color: var(--peach-soft, #F3B5A0);
        color: var(--wine-dark, #4A1F2A);
        font-weight: 600;
    }
        font-weight: 600;
    }

    /* Botón directo de WhatsApp */
    .btn-whatsapp-direct {
        background-color: #25D366;
        color: #ffffff;
        border: none;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-whatsapp-direct:hover {
        background-color: #1EBE5D;
        color: #ffffff;
    }

    /* Alerta de duplicados */
    .alert-duplicado-cliente {
        background-color: #FFF3CD;
        border: 1.5px solid #FFEBAA;
        border-radius: 10px;
        color: #664D03;
        font-size: 0.88rem;
    }

    /* Feedback de error debajo de inputs */
    .invalid-feedback-custom {
        display: block;
        font-size: 0.8rem;
        color: #dc3545;
        margin-top: 3px;
        font-weight: 500;
    }

    /* Modal Fullscreen en móvil */
    @media (max-width: 767.98px) {
        .modal-fullscreen-md-down {
            width: 100vw;
            max-width: none;
            height: 100%;
            margin: 0;
        }
        .modal-fullscreen-md-down .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
        .modal-fullscreen-md-down .modal-body {
            overflow-y: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- ENCABEZADO Y ACCIONES PRINCIPALES -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1"><i class="bi bi-person-heart me-1"></i> Clientes</span>
                <h2 class="font-serif fw-bold m-0 page-title" style="color: var(--color-text-dark);">Gestión Integral de Clientes</h2>
            </div>
            <p class="page-subtitle text-muted m-0">
                Directorio comercial optimizado para entregas a domicilio y atención personalizada por WhatsApp y Redes en Bolivia.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-boutique-rose px-3 shadow-sm" onclick="abrirModalNuevoCliente()">
                <i class="bi bi-person-plus-fill me-1"></i> Registrar Cliente
            </button>
        </div>
    </div>

    <!-- TARJETAS DE RESUMEN COMERCIAL (MÉTRICAS) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(216, 27, 96, 0.1); color: var(--rosa-principal);">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Total Clientes</div>
                    <div class="fs-4 fw-bold font-serif" style="color: var(--color-text-dark);">{{ $totalClientes }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(37, 211, 102, 0.15); color: #25D366;">
                    <i class="bi bi-whatsapp fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Canal WhatsApp</div>
                    <div class="fs-4 fw-bold font-serif" style="color: var(--color-text-dark);">{{ $totalWhatsapp }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(255, 193, 7, 0.18); color: #B8860B;">
                    <i class="bi bi-star-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Clientes VIP</div>
                    <div class="fs-4 fw-bold font-serif" style="color: var(--color-text-dark);">{{ $totalVip }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card-boutique p-3 d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: rgba(15, 122, 75, 0.12); color: var(--ok, #0F7A4B);">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">Clientes Activos</div>
                    <div class="fs-4 fw-bold font-serif" style="color: var(--color-text-dark);">{{ $totalActivos }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- BARRA DE FILTROS Y BÚSQUEDA AVANZADA -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('clientes.index') }}" method="GET" id="formFiltrosClientes" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text border-end-0 bg-transparent text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre, CI/NIT, teléfono o dirección..." value="{{ request('buscar') }}">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <select name="origen" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos los Orígenes</option>
                    @foreach($origenesDisponibles as $key => $item)
                        <option value="{{ $key }}" {{ request('origen') === $key ? 'selected' : '' }}>{{ $item['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="etiqueta" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todas las Etiquetas</option>
                    @foreach($etiquetasDisponibles as $key => $item)
                        <option value="{{ $key }}" {{ request('etiqueta') === $key ? 'selected' : '' }}>{{ $item['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Todos los Estados</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-boutique btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                @if(request()->anyFilled(['buscar', 'origen', 'etiqueta', 'estado', 'tipo_cliente']))
                    <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE CLIENTES ENRIQUECIDA -->
    <div class="card-boutique overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Cliente</th>
                        <th>Contacto (Bolivia)</th>
                        <th>Origen & Etiquetas</th>
                        <th>Dirección Principal</th>
                        <th class="text-center">Compras</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end" style="min-width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cli)
                        <tr>
                            <td><span class="text-muted small">#{{ $cli->id_cliente }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="client-avatar {{ $cli->tipo_cliente === 'empresa' ? 'empresa' : '' }}" title="{{ $cli->tipo_cliente === 'empresa' ? 'Empresa / Razón Social' : 'Persona Natural' }}">
                                        {{ mb_substr($cli->nombre, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: var(--color-text-dark);">
                                            {{ $cli->nombre_completo }}
                                            @if($cli->tipo_cliente === 'empresa')
                                                <span class="badge bg-dark ms-1" style="font-size: 0.65rem;">Empresa</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.78rem;">
                                            @if($cli->ci_nit)
                                                <span><i class="bi bi-card-heading me-1"></i>{{ $cli->ci_nit }}</span>
                                            @endif
                                            @if($cli->fecha_nacimiento)
                                                <span><i class="bi bi-cake2 me-1"></i>{{ $cli->fecha_nacimiento->format('d/m') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-semibold">{{ $cli->telefono ?? 'Sin teléfono' }}</span>
                                        @if($cli->whatsapp_url)
                                            <a href="{{ $cli->whatsapp_url }}" target="_blank" class="btn btn-xs btn-whatsapp-direct px-1 py-0 rounded" title="Abrir WhatsApp Chat">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        @endif
                                    </div>
                                    @if($cli->email)
                                        <div class="text-muted small text-truncate" style="max-width: 180px;" title="{{ $cli->email }}">
                                            <i class="bi bi-envelope me-1"></i>{{ $cli->email }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <!-- Origen -->
                                @php
                                    $origenInfo = $origenesDisponibles[$cli->origen] ?? ['label' => ucfirst($cli->origen ?? 'Tienda'), 'icon' => 'bi-shop', 'color' => '#4A1F2A'];
                                @endphp
                                <div class="mb-1">
                                    <span class="origin-badge" style="color: {{ $origenInfo['color'] }};">
                                        <i class="bi {{ $origenInfo['icon'] }}"></i> {{ $origenInfo['label'] }}
                                    </span>
                                </div>
                                <!-- Etiquetas -->
                                <div>
                                    @if(!empty($cli->etiquetas) && is_array($cli->etiquetas))
                                        @foreach($cli->etiquetas as $tag)
                                            @php
                                                $claseEtiqueta = $etiquetasDisponibles[$tag]['class'] ?? 'badge-etiqueta-frecuente';
                                            @endphp
                                            <span class="badge {{ $claseEtiqueta }} me-1" style="font-size: 0.72rem;">{{ $tag }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">Sin etiquetas</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 260px;">
                                    @php
                                        $dirPrinc = $cli->direccionPrincipal;
                                    @endphp
                                    @if($dirPrinc)
                                        <div class="fw-semibold text-truncate" title="{{ $dirPrinc->direccion_completa }}">
                                            <i class="bi bi-geo-alt text-danger me-1"></i>{{ $dirPrinc->calle }}
                                        </div>
                                        <div class="text-muted small text-truncate">
                                            {{ $dirPrinc->zona ? "Zona {$dirPrinc->zona}, " : '' }}{{ $dirPrinc->ciudad }}
                                            @if($dirPrinc->referencia)
                                                <span class="fst-italic" title="{{ $dirPrinc->referencia }}">({{ Str::limit($dirPrinc->referencia, 20) }})</span>
                                            @endif
                                        </div>
                                    @elseif($cli->direccion)
                                        <div class="text-muted small text-truncate" title="{{ $cli->direccion }}">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $cli->direccion }}
                                        </div>
                                    @else
                                        <span class="text-muted small">Sin dirección registrada</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-bag-check me-1"></i>{{ $cli->ventas_count }} pedido(s)
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $cli->estado ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $cli->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <!-- Botón Crear Pedido directo en POS -->
                                    <a href="{{ route('ventas.pos', ['cliente_id' => $cli->id_cliente]) }}" class="btn btn-light border text-success" title="Crear Venta / Pedido para este cliente">
                                        <i class="bi bi-cart-plus-fill"></i>
                                    </a>
                                    <!-- Botón Editar -->
                                    <button type="button" class="btn btn-light border text-primary" onclick="editarCliente({{ $cli->id_cliente }})" title="Editar Perfil y Direcciones">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <!-- Botón Toggle Estado -->
                                    <form action="{{ route('clientes.toggle', $cli->id_cliente) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-light border {{ $cli->estado ? 'text-warning' : 'text-success' }}" title="{{ $cli->estado ? 'Desactivar' : 'Activar' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-6 d-block mb-2 text-muted"></i>
                                No se encontraron clientes con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINACIÓN -->
    <div class="d-flex justify-content-center">
        {{ $clientes->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- =========================================================================
     MODAL UNIFICADO: REGISTRO Y EDICIÓN COMPLETA DE CLIENTES (4 SECCIONES)
     ========================================================================= -->
<div class="modal fade" id="modalCliente" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="modalClienteTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-md-down">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; overflow: hidden; border: 1px solid var(--peach-soft) !important;">
            <div class="modal-header border-bottom py-3" style="background: linear-gradient(135deg, rgba(255, 223, 195, 0.45), rgba(254, 163, 142, 0.25)); border-bottom: 1.5px solid var(--peach-soft) !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="client-avatar" id="modalAvatarPreview">🌸</span>
                    <div>
                        <h5 class="modal-title font-serif fw-bold m-0" id="modalClienteTitulo" style="color: var(--wine-dark, #4A1F2A);">Registrar Nuevo Cliente</h5>
                        <small class="text-muted" id="modalClienteSubtitulo">Perfil boliviano para ventas por WhatsApp y entregas a domicilio</small>
                    </div>
                </div>
                <button type="button" class="btn-close" onclick="confirmarCerrarModal()" aria-label="Cerrar"></button>
            </div>

            <!-- FORMULARIO PRINCIPAL -->
            <form id="formCliente" method="POST" novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMetodo" value="POST">
                <input type="hidden" name="id_cliente" id="cliente_id_hidden" value="">

                <!-- PESTAÑAS (TABS) -->
                <div class="px-3 pt-3 border-bottom bg-light">
                    <ul class="nav nav-tabs nav-tabs-boutique" id="pestañasCliente" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-personales-btn" data-bs-toggle="tab" data-bs-target="#tab-personales" type="button" role="tab">
                                <i class="bi bi-person-badge"></i> Datos Personales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-contacto-btn" data-bs-toggle="tab" data-bs-target="#tab-contacto" type="button" role="tab">
                                <i class="bi bi-whatsapp"></i> Contacto <span class="badge bg-danger ms-1" id="badgeErrorContacto" style="display:none;">!</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-entrega-btn" data-bs-toggle="tab" data-bs-target="#tab-entrega" type="button" role="tab">
                                <i class="bi bi-geo-alt"></i> Entrega & Mapa
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-comercial-btn" data-bs-toggle="tab" data-bs-target="#tab-comercial" type="button" role="tab">
                                <i class="bi bi-tag"></i> Comercial & Notas
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- ALERTA DE DUPLICADOS EN VIVO -->
                <div id="contenedorAlertaDuplicado" class="px-4 pt-3" style="display: none;">
                    <div class="alert alert-duplicado-cliente d-flex align-items-center justify-content-between p-2 mb-0">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                            <span id="textoAlertaDuplicado">Ya existe un cliente con este dato de contacto.</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-dark fw-bold" id="btnCargarDuplicado">
                            <i class="bi bi-pencil-square me-1"></i> Ver / Editar cliente
                        </button>
                    </div>
                </div>

                <!-- CUERPO DE LAS PESTAÑAS -->
                <div class="modal-body p-4">
                    <div class="tab-content" id="contenidoPestañasCliente">

                        <!-- =======================================================
                             PESTAÑA 1: DATOS PERSONALES
                             ======================================================= -->
                        <div class="tab-pane fade show active" id="tab-personales" role="tabpanel">
                            <!-- Tipo de cliente -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted d-block">Tipo de Cliente</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="tipo_cliente" id="tipo_persona" value="persona" checked>
                                    <label class="btn btn-outline-secondary btn-sm" for="tipo_persona">
                                        <i class="bi bi-person me-1"></i> Persona Natural
                                    </label>

                                    <input type="radio" class="btn-check" name="tipo_cliente" id="tipo_empresa" value="empresa">
                                    <label class="btn btn-outline-secondary btn-sm" for="tipo_empresa">
                                        <i class="bi bi-building me-1"></i> Empresa / Corporativo
                                    </label>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold" for="input_nombre">
                                        Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nombre" id="input_nombre" class="form-control" placeholder="ej. María Valentina" required>
                                    <div class="invalid-feedback-custom" id="error_nombre" style="display: none;"></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold" for="input_apellido">
                                        Apellido <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="apellido" id="input_apellido" class="form-control" placeholder="ej. Suárez Montero" required>
                                    <div class="invalid-feedback-custom" id="error_apellido" style="display: none;"></div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold" for="input_ci_nit">
                                        <span id="label_ci_nit">Cédula de Identidad (CI) o NIT</span> <small class="text-muted">(Opcional)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent text-muted"><i class="bi bi-card-heading"></i></span>
                                        <input type="text" name="ci_nit" id="input_ci_nit" class="form-control" placeholder="ej. 8392019 SC o NIT 1029384029">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-semibold" for="input_fecha_nacimiento">
                                        Fecha de Nacimiento <small class="text-muted">(Para saludos y promociones)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent text-muted"><i class="bi bi-cake2"></i></span>
                                        <input type="date" name="fecha_nacimiento" id="input_fecha_nacimiento" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- =======================================================
                             PESTAÑA 2: CONTACTO Y CANALES
                             ======================================================= -->
                        <div class="tab-pane fade" id="tab-contacto" role="tabpanel">
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-7">
                                    <label class="form-label small fw-semibold" for="input_telefono">
                                        Teléfono / WhatsApp <span class="text-danger">*</span>
                                        <small class="text-muted">(Celular Bolivia: 8 dígitos empezando con 6 o 7)</small>
                                    </label>
                                    <div class="input-group input-group-phone">
                                        <span class="input-group-text">🇧🇴 +591</span>
                                        <input type="tel" name="telefono" id="input_telefono" class="form-control" placeholder="71234567" required maxlength="12">
                                        <a href="#" target="_blank" id="btnWhatsappModal" class="btn btn-whatsapp-direct disabled" title="Enviar WhatsApp a este número">
                                            <i class="bi bi-whatsapp"></i> Chat
                                        </a>
                                    </div>
                                    <div class="invalid-feedback-custom" id="error_telefono" style="display: none;"></div>
                                </div>

                                <div class="col-12 col-md-5">
                                    <label class="form-label small fw-semibold" for="input_telefono_alternativo">
                                        Teléfono Alternativo <small class="text-muted">(Opcional)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent text-muted"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" name="telefono_alternativo" id="input_telefono_alternativo" class="form-control" placeholder="Fijo o de familiar">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-7">
                                    <label class="form-label small fw-semibold" for="input_email">
                                        Correo Electrónico <small class="text-muted">(Para facturación y comprobantes)</small>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent text-muted"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="input_email" class="form-control" placeholder="cliente@ejemplo.com">
                                    </div>
                                    <div class="invalid-feedback-custom" id="error_email" style="display: none;"></div>
                                </div>

                                <div class="col-12 col-md-5">
                                    <label class="form-label small fw-semibold">Canal de Contacto Preferido</label>
                                    <select name="canal_preferido" id="input_canal_preferido" class="form-select">
                                        <option value="whatsapp" selected>💬 WhatsApp (Recomendado)</option>
                                        <option value="llamada">📞 Llamada Telefónica</option>
                                        <option value="correo">✉️ Correo Electrónico</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- =======================================================
                             PESTAÑA 3: ENTREGA Y MÚLTIPLES DIRECCIONES
                             ======================================================= -->
                        <div class="tab-pane fade" id="tab-entrega" role="tabpanel">
                            <!-- Cabecera de direcciones del cliente -->
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold small text-muted">
                                    <i class="bi bi-houses me-1"></i> Direcciones de Entrega Registradas
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarNuevaDireccion" onclick="limpiarFormDireccionNueva()">
                                    <i class="bi bi-plus-circle me-1"></i> Agregar otra dirección
                                </button>
                            </div>

                            <!-- Lista de direcciones agregadas -->
                            <div id="listaDireccionesCliente" class="d-flex flex-column gap-2 mb-3">
                                <!-- Se llena dinámicamente con JS -->
                            </div>

                            <!-- Formulario de la Dirección Activa -->
                            <div class="p-3 border rounded bg-light" id="contenedorFormDireccion">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-secondary" id="badgeFormDireccionTitulo">Dirección Principal</span>
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="checkbox" id="check_es_principal" checked>
                                        <label class="form-check-label small fw-semibold" for="check_es_principal">Marcar como Principal</label>
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small fw-semibold mb-1">Alias (ej. Casa / Oficina)</label>
                                        <input type="text" id="dir_alias" class="form-control form-control-sm" placeholder="Casa, Trabajo, etc." value="Casa">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small fw-semibold mb-1">Ciudad / Municipio <span class="text-danger">*</span></label>
                                        <select id="dir_ciudad" class="form-select form-select-sm">
                                            <option value="Santa Cruz de la Sierra" selected>Santa Cruz de la Sierra</option>
                                            <option value="La Paz">La Paz</option>
                                            <option value="Cochabamba">Cochabamba</option>
                                            <option value="El Alto">El Alto</option>
                                            <option value="Sucre">Sucre</option>
                                            <option value="Tarija">Tarija</option>
                                            <option value="Oruro">Oruro</option>
                                            <option value="Potosí">Potosí</option>
                                            <option value="Trinidad">Trinidad</option>
                                            <option value="Cobija">Cobija</option>
                                            <option value="Montero / Warnes">Montero / Warnes</option>
                                            <option value="Otra">Otra localidad</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small fw-semibold mb-1">Zona / Barrio</label>
                                        <input type="text" id="dir_zona" class="form-control form-control-sm" placeholder="ej. Equipetrol, Calacoto...">
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold mb-1">Calle y N° / Edificio</label>
                                        <input type="text" id="dir_calle" class="form-control form-control-sm" placeholder="ej. Av. San Martín #450, Edif. Torre 1">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label small fw-semibold mb-1">Punto de Referencia</label>
                                        <input type="text" id="dir_referencia" class="form-control form-control-sm" placeholder="ej. Portón café, frente a la farmacia">
                                    </div>
                                </div>

                                <!-- MAPA INTERACTIVO Y COORDENADAS GPS -->
                                <div class="mt-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="small fw-semibold text-muted">
                                            <i class="bi bi-pin-map-fill text-danger me-1"></i> Ubicación GPS (Pin arrastrable en el mapa)
                                        </span>
                                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="geolocalizarUsuario()">
                                            <i class="bi bi-crosshair me-1"></i> Usar mi ubicación actual
                                        </button>
                                    </div>

                                    <div id="mapaEntrega"></div>

                                    <div class="row g-2 mt-1">
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-transparent text-muted" style="font-size: 0.75rem;">Lat</span>
                                                <input type="text" id="dir_latitud" class="form-control form-control-sm text-muted" placeholder="-17.7833" readonly>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-transparent text-muted" style="font-size: 0.75rem;">Lng</span>
                                                <input type="text" id="dir_longitud" class="form-control form-control-sm text-muted" placeholder="-63.1821" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- =======================================================
                             PESTAÑA 4: COMERCIAL Y NOTAS
                             ======================================================= -->
                        <div class="tab-pane fade" id="tab-comercial" role="tabpanel">
                            <!-- Origen del cliente -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Canal de Origen del Cliente</label>
                                <div class="row g-2">
                                    @foreach($origenesDisponibles as $key => $orig)
                                        <div class="col-6 col-md-3">
                                            <input type="radio" class="btn-check" name="origen" id="orig_{{ $key }}" value="{{ $key }}" {{ $key === 'tienda_fisica' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-secondary btn-sm w-100 text-truncate text-start" for="orig_{{ $key }}">
                                                <i class="bi {{ $orig['icon'] }} me-1" style="color: {{ $orig['color'] }};"></i> {{ $orig['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Etiquetas comerciales -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold d-block">Etiquetas de Clasificación</label>
                                <div class="d-flex flex-wrap gap-2" id="contenedorEtiquetasSelector">
                                    <div class="tag-choice-badge" data-tag="VIP" onclick="toggleEtiquetaBadge(this)">
                                        <i class="bi bi-star-fill text-warning"></i> VIP
                                    </div>
                                    <div class="tag-choice-badge" data-tag="frecuente" onclick="toggleEtiquetaBadge(this)">
                                        <i class="bi bi-arrow-repeat text-danger"></i> Frecuente
                                    </div>
                                    <div class="tag-choice-badge" data-tag="mayorista" onclick="toggleEtiquetaBadge(this)">
                                        <i class="bi bi-box-seam text-primary"></i> Mayorista
                                    </div>
                                    <div class="tag-choice-badge" data-tag="nuevo" onclick="toggleEtiquetaBadge(this)">
                                        <i class="bi bi-sparkles text-success"></i> Nuevo
                                    </div>
                                </div>
                                <!-- Inputs ocultos de etiquetas se sincronizan vía JS -->
                                <div id="inputsOcultosEtiquetas"></div>
                            </div>

                            <!-- Notas internas -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="input_notas">
                                    Notas y Preferencias del Cliente
                                </label>
                                <textarea name="notas" id="input_notas" class="form-control" rows="3" placeholder="ej. Talla habitual M, prefiere entregas en las mañanas, alérgica a ciertas telas sintéticas..."></textarea>
                            </div>

                            <!-- Estado del cliente -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="estado" id="switch_estado" value="1" checked>
                                <label class="form-check-label small fw-semibold" for="switch_estado">Cliente Activo (habilitado para ventas y pedidos)</label>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- PIE DEL MODAL CON ACCIONES PRINCIPALES -->
                <div class="modal-footer border-top bg-light py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-light border" onclick="confirmarCerrarModal()">
                        Cancelar
                    </button>

                    <div class="d-flex gap-2">
                        <!-- Guardar Cliente -->
                        <button type="submit" name="accion" value="guardar" id="btnGuardarCliente" class="btn btn-sm btn-outline-boutique fw-semibold">
                            <span class="spinner-border spinner-border-sm me-1" id="spinnerGuardar" style="display: none;" role="status"></span>
                            <span id="textoBtnGuardar">Guardar Cliente</span>
                        </button>

                        <!-- Guardar y Crear Pedido -->
                        <button type="submit" name="accion" value="guardar_y_pedido" id="btnGuardarYPedido" class="btn btn-sm btn-boutique-rose fw-bold shadow-sm">
                            <span class="spinner-border spinner-border-sm me-1" id="spinnerGuardarPedido" style="display: none;" role="status"></span>
                            <i class="bi bi-cart-plus-fill me-1"></i> <span id="textoBtnGuardarPedido">Guardar y crear pedido</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS para Mapas Interactivos -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    /* ==========================================================================
       ESTADO GLOBAL DEL FORMULARIO Y MAPA
       ========================================================================== */
    let mapa = null;
    let marcador = null;
    let modalClienteInstance = null;
    let formularioModificado = false;
    let direccionesCliente = []; // Array de direcciones en edición
    let indiceDireccionEditando = 0;

    // Coordenadas iniciales por defecto (Santa Cruz de la Sierra, Bolivia)
    const COORDENADAS_DEFAULT = { lat: -17.7833, lng: -63.1821 };

    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('modalCliente');
        if (modalElement) {
            modalClienteInstance = new bootstrap.Modal(modalElement);

            // Al abrir el modal, ajustar tamaño de mapa y enfocar primer campo
            modalElement.addEventListener('shown.bs.modal', function () {
                initMapaEntrega();
                document.getElementById('input_nombre').focus();
            });
        }

        // Detectar cambios para advertencia de datos sin guardar
        const form = document.getElementById('formCliente');
        form.addEventListener('input', () => { formularioModificado = true; });
        form.addEventListener('change', () => { formularioModificado = true; });

        // Inicializar listeners de validación en tiempo real
        initValidacionesEnVivo();

        // Inicializar mapa si se abre pestaña de entrega
        document.getElementById('tab-entrega-btn').addEventListener('shown.bs.tab', function () {
            if (mapa) {
                setTimeout(() => mapa.invalidateSize(), 150);
            }
        });

        // Cambio de ciudad en el selector: reubica el mapa
        document.getElementById('dir_ciudad').addEventListener('change', function () {
            reubicarMapaPorCiudad(this.value);
        });

        // Enviar formulario (AJAX y fallback)
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitter = e.submitter || document.getElementById('btnGuardarCliente');
            const accion = submitter.value || 'guardar';
            guardarClienteForm(accion);
        });
    });

    /* ==========================================================================
       MAPA INTERACTIVO (LEAFLET + OPENSTREETMAP)
       ========================================================================== */
    function initMapaEntrega() {
        if (!mapa) {
            mapa = L.map('mapaEntrega').setView([COORDENADAS_DEFAULT.lat, COORDENADAS_DEFAULT.lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapa);

            marcador = L.marker([COORDENADAS_DEFAULT.lat, COORDENADAS_DEFAULT.lng], {
                draggable: true
            }).addTo(mapa);

            // Actualizar inputs al arrastrar el pin
            marcador.on('dragend', function (event) {
                const pos = event.target.getLatLng();
                actualizarInputsCoordenadas(pos.lat, pos.lng);
            });

            // Mover pin al hacer clic en cualquier lugar del mapa
            mapa.on('click', function (e) {
                marcador.setLatLng(e.latlng);
                actualizarInputsCoordenadas(e.latlng.lat, e.latlng.lng);
            });
        } else {
            mapa.invalidateSize();
        }
    }

    function actualizarInputsCoordenadas(lat, lng) {
        document.getElementById('dir_latitud').value = lat.toFixed(6);
        document.getElementById('dir_longitud').value = lng.toFixed(6);
        formularioModificado = true;
    }

    function reubicarMapaPorCiudad(ciudad) {
        const coordenadasCiudades = {
            'Santa Cruz de la Sierra': [-17.7833, -63.1821],
            'La Paz': [-16.5000, -68.1500],
            'Cochabamba': [-17.3895, -66.1568],
            'El Alto': [-16.5042, -68.1633],
            'Sucre': [-19.0333, -65.2627],
            'Tarija': [-21.5355, -64.7296],
            'Oruro': [-17.9833, -67.1500],
            'Potosí': [-19.5836, -65.7531],
            'Trinidad': [-14.8333, -64.9000],
            'Cobija': [-11.0267, -68.7692],
            'Montero / Warnes': [-17.3386, -63.2553]
        };

        const coord = coordenadasCiudades[ciudad] || coordenadasCiudades['Santa Cruz de la Sierra'];
        if (mapa && marcador) {
            mapa.setView(coord, 13);
            marcador.setLatLng(coord);
            actualizarInputsCoordenadas(coord[0], coord[1]);
        }
    }

    function geolocalizarUsuario() {
        if (!navigator.geolocation) {
            alert('La geolocalización no es compatible con este navegador.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                if (mapa && marcador) {
                    mapa.setView([lat, lng], 16);
                    marcador.setLatLng([lat, lng]);
                    actualizarInputsCoordenadas(lat, lng);
                }
            },
            function (err) {
                alert('No se pudo obtener la ubicación GPS: ' + err.message);
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    /* ==========================================================================
       VALIDACIONES EN TIEMPO REAL Y DUPLICADOS
       ========================================================================== */
    function initValidacionesEnVivo() {
        const inputTel = document.getElementById('input_telefono');
        const btnWhatsapp = document.getElementById('btnWhatsappModal');
        const inputEmail = document.getElementById('input_email');
        const inputNombre = document.getElementById('input_nombre');
        const inputApellido = document.getElementById('input_apellido');

        // Formato y validación de teléfono Bolivia
        inputTel.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, ''); // solo dígitos

            // Si pega con 591 adelante
            if (val.startsWith('591') && val.length > 3) {
                val = val.substring(3);
            }

            if (val.length > 8) {
                val = val.substring(0, 8);
            }

            this.value = val;

            const errorDiv = document.getElementById('error_telefono');
            if (val.length === 8 && (val.startsWith('6') || val.startsWith('7'))) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                errorDiv.style.display = 'none';

                // Habilitar enlace de WhatsApp
                btnWhatsapp.classList.remove('disabled');
                btnWhatsapp.href = `https://wa.me/591${val}?text=` + encodeURIComponent("¡Hola! Te escribimos de Bella Girls Boutique 🌸");

                // Verificar duplicados en servidor
                chequearDuplicado('telefono', val);
            } else if (val.length > 0) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                errorDiv.textContent = 'El celular debe tener 8 dígitos y comenzar con 6 o 7.';
                errorDiv.style.display = 'block';
                btnWhatsapp.classList.add('disabled');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
                errorDiv.style.display = 'none';
                btnWhatsapp.classList.add('disabled');
                ocultarAlertaDuplicado();
            }
        });

        // Validación de email
        inputEmail.addEventListener('blur', function () {
            const val = this.value.trim();
            const errorDiv = document.getElementById('error_email');
            if (val.length > 0) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(val)) {
                    this.classList.add('is-invalid');
                    errorDiv.textContent = 'Ingrese un correo electrónico válido.';
                    errorDiv.style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    errorDiv.style.display = 'none';
                    chequearDuplicado('email', val);
                }
            } else {
                this.classList.remove('is-invalid');
                errorDiv.style.display = 'none';
            }
        });
    }

    let timerDuplicado = null;
    function chequearDuplicado(campo, valor) {
        clearTimeout(timerDuplicado);
        timerDuplicado = setTimeout(() => {
            const clienteId = document.getElementById('cliente_id_hidden').value;
            const params = new URLSearchParams();
            params.append(campo, valor);
            if (clienteId) {
                params.append('ignore_id', clienteId);
            }

            fetch(`/clientes/verificar-duplicado?${params.toString()}`)
                .then(res => res.json())
                .then(data => {
                    if (data.duplicado) {
                        mostrarAlertaDuplicado(data.mensaje, data.cliente.id_cliente);
                    } else {
                        ocultarAlertaDuplicado();
                    }
                })
                .catch(() => {});
        }, 350);
    }

    function mostrarAlertaDuplicado(mensaje, idCliente) {
        const contenedor = document.getElementById('contenedorAlertaDuplicado');
        const texto = document.getElementById('textoAlertaDuplicado');
        const btn = document.getElementById('btnCargarDuplicado');

        texto.textContent = mensaje;
        btn.onclick = function () {
            editarCliente(idCliente);
        };
        contenedor.style.display = 'block';
    }

    function ocultarAlertaDuplicado() {
        document.getElementById('contenedorAlertaDuplicado').style.display = 'none';
    }

    /* ==========================================================================
       GESTIÓN DE ETIQUETAS COMERCIALES
       ========================================================================= */
    function toggleEtiquetaBadge(badgeElement) {
        badgeElement.classList.toggle('selected');
        sincronizarEtiquetasOcultas();
        formularioModificado = true;
    }

    function sincronizarEtiquetasOcultas() {
        const contenedorInputs = document.getElementById('inputsOcultosEtiquetas');
        contenedorInputs.innerHTML = '';

        const seleccionadas = document.querySelectorAll('#contenedorEtiquetasSelector .tag-choice-badge.selected');
        seleccionadas.forEach(badge => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'etiquetas[]';
            input.value = badge.getAttribute('data-tag');
            contenedorInputs.appendChild(input);
        });
    }

    function setEtiquetasSeleccionadas(tagsArray) {
        const badges = document.querySelectorAll('#contenedorEtiquetasSelector .tag-choice-badge');
        badges.forEach(badge => {
            const tag = badge.getAttribute('data-tag');
            if (tagsArray && tagsArray.includes(tag)) {
                badge.classList.add('selected');
            } else {
                badge.classList.remove('selected');
            }
        });
        sincronizarEtiquetasOcultas();
    }

    /* ==========================================================================
       GESTIÓN DE MÚLTIPLES DIRECCIONES
       ========================================================================= */
    function renderDirecciones() {
        const lista = document.getElementById('listaDireccionesCliente');
        lista.innerHTML = '';

        if (direccionesCliente.length === 0) {
            lista.innerHTML = `
                <div class="text-muted small text-center py-2 border rounded bg-white">
                    <i class="bi bi-geo-alt me-1"></i> No hay direcciones registradas. Configure la dirección principal abajo.
                </div>
            `;
            return;
        }

        direccionesCliente.forEach((dir, index) => {
            const esPrinc = dir.es_principal;
            const item = document.createElement('div');
            item.className = `card-direccion-item ${esPrinc ? 'es-principal' : ''}`;
            item.innerHTML = `
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="badge ${esPrinc ? 'bg-danger' : 'bg-secondary'} me-1">${dir.alias || 'Dirección'}</span>
                        <strong style="color: var(--color-text-dark);">${dir.calle || 'Sin calle'}</strong>
                        <div class="text-muted small">
                            ${dir.zona ? 'Zona ' + dir.zona + ', ' : ''}${dir.ciudad || 'Santa Cruz'}
                            ${dir.referencia ? '<span class="fst-italic">(' + dir.referencia + ')</span>' : ''}
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        ${!esPrinc ? `<button type="button" class="btn btn-light border text-muted" onclick="marcarDireccionPrincipal(${index})" title="Establecer como principal"><i class="bi bi-star"></i></button>` : ''}
                        <button type="button" class="btn btn-light border text-primary" onclick="cargarDireccionParaEditar(${index})" title="Editar en mapa"><i class="bi bi-pencil"></i></button>
                        ${direccionesCliente.length > 1 ? `<button type="button" class="btn btn-light border text-danger" onclick="eliminarDireccion(${index})" title="Eliminar"><i class="bi bi-trash"></i></button>` : ''}
                    </div>
                </div>
            `;
            lista.appendChild(item);
        });
    }

    function guardarDireccionActualEnMemoria() {
        const alias = document.getElementById('dir_alias').value.trim() || 'Casa';
        const ciudad = document.getElementById('dir_ciudad').value;
        const zona = document.getElementById('dir_zona').value.trim();
        const calle = document.getElementById('dir_calle').value.trim();
        const referencia = document.getElementById('dir_referencia').value.trim();
        const lat = document.getElementById('dir_latitud').value;
        const lng = document.getElementById('dir_longitud').value;
        const esPrincipal = document.getElementById('check_es_principal').checked;

        if (!calle && !zona) {
            return; // No guardar si está vacío
        }

        if (esPrincipal) {
            direccionesCliente.forEach(d => d.es_principal = false);
        }

        const obj = {
            id_direccion: direccionesCliente[indiceDireccionEditando]?.id_direccion || null,
            alias: alias,
            ciudad: ciudad,
            zona: zona,
            calle: calle,
            referencia: referencia,
            latitud: lat ? parseFloat(lat) : null,
            longitud: lng ? parseFloat(lng) : null,
            es_principal: esPrincipal || (direccionesCliente.length === 0)
        };

        if (indiceDireccionEditando < direccionesCliente.length) {
            direccionesCliente[indiceDireccionEditando] = obj;
        } else {
            direccionesCliente.push(obj);
        }

        renderDirecciones();
    }

    function cargarDireccionParaEditar(index) {
        indiceDireccionEditando = index;
        const dir = direccionesCliente[index];
        if (!dir) return;

        document.getElementById('dir_alias').value = dir.alias || 'Casa';
        document.getElementById('dir_ciudad').value = dir.ciudad || 'Santa Cruz de la Sierra';
        document.getElementById('dir_zona').value = dir.zona || '';
        document.getElementById('dir_calle').value = dir.calle || '';
        document.getElementById('dir_referencia').value = dir.referencia || '';
        document.getElementById('check_es_principal').checked = !!dir.es_principal;
        document.getElementById('badgeFormDireccionTitulo').textContent = `Editando: ${dir.alias || 'Dirección'}`;

        if (dir.latitud && dir.longitud) {
            document.getElementById('dir_latitud').value = dir.latitud;
            document.getElementById('dir_longitud').value = dir.longitud;
            if (mapa && marcador) {
                mapa.setView([dir.latitud, dir.longitud], 15);
                marcador.setLatLng([dir.latitud, dir.longitud]);
            }
        }
    }

    function limpiarFormDireccionNueva() {
        guardarDireccionActualEnMemoria();
        indiceDireccionEditando = direccionesCliente.length;

        document.getElementById('dir_alias').value = 'Trabajo';
        document.getElementById('dir_zona').value = '';
        document.getElementById('dir_calle').value = '';
        document.getElementById('dir_referencia').value = '';
        document.getElementById('check_es_principal').checked = false;
        document.getElementById('badgeFormDireccionTitulo').textContent = 'Nueva Dirección';

        reubicarMapaPorCiudad(document.getElementById('dir_ciudad').value);
    }

    function marcarDireccionPrincipal(index) {
        direccionesCliente.forEach((d, i) => {
            d.es_principal = (i === index);
        });
        renderDirecciones();
        if (indiceDireccionEditando === index) {
            document.getElementById('check_es_principal').checked = true;
        }
        formularioModificado = true;
    }

    function eliminarDireccion(index) {
        if (confirm('¿Eliminar esta dirección?')) {
            const eraPrincipal = direccionesCliente[index].es_principal;
            direccionesCliente.splice(index, 1);
            if (eraPrincipal && direccionesCliente.length > 0) {
                direccionesCliente[0].es_principal = true;
            }
            indiceDireccionEditando = 0;
            if (direccionesCliente.length > 0) {
                cargarDireccionParaEditar(0);
            }
            renderDirecciones();
            formularioModificado = true;
        }
    }

    /* ==========================================================================
       APERTURA Y PREPARACIÓN DEL MODAL (NUEVO / EDITAR)
       ========================================================================== */
    function abrirModalNuevoCliente() {
        document.getElementById('formCliente').reset();
        document.getElementById('formCliente').action = "{{ route('clientes.store') }}";
        document.getElementById('formMetodo').value = "POST";
        document.getElementById('cliente_id_hidden').value = "";
        document.getElementById('modalClienteTitulo').textContent = "Registrar Nuevo Cliente";
        document.getElementById('modalClienteSubtitulo').textContent = "Perfil boliviano para ventas por WhatsApp y entregas a domicilio";
        document.getElementById('modalAvatarPreview').textContent = "🌸";
        document.getElementById('textoBtnGuardar').textContent = "Guardar Cliente";
        document.getElementById('textoBtnGuardarPedido').textContent = "Guardar y crear pedido";

        // Reset de campos y estados
        document.getElementById('input_telefono').classList.remove('is-valid', 'is-invalid');
        document.getElementById('btnWhatsappModal').classList.add('disabled');
        ocultarAlertaDuplicado();
        setEtiquetasSeleccionadas(['nuevo']);

        // Reset de direcciones
        direccionesCliente = [{
            alias: 'Casa',
            ciudad: 'Santa Cruz de la Sierra',
            zona: '',
            calle: '',
            referencia: '',
            latitud: COORDENADAS_DEFAULT.lat,
            longitud: COORDENADAS_DEFAULT.lng,
            es_principal: true
        }];
        indiceDireccionEditando = 0;
        cargarDireccionParaEditar(0);
        renderDirecciones();

        // Abrir pestaña inicial
        bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-personales-btn')).show();

        formularioModificado = false;
        modalClienteInstance.show();
    }

    function editarCliente(id) {
        fetch(`/clientes/${id}`, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(cli => {
                document.getElementById('formCliente').action = `/clientes/${cli.id_cliente}`;
                document.getElementById('formMetodo').value = "PUT";
                document.getElementById('cliente_id_hidden').value = cli.id_cliente;
                document.getElementById('modalClienteTitulo').textContent = `Editar: ${cli.nombre_completo}`;
                document.getElementById('modalClienteSubtitulo').textContent = `ID #${cli.id_cliente} • Registrado en el sistema`;
                document.getElementById('modalAvatarPreview').textContent = cli.nombre.charAt(0);
                document.getElementById('textoBtnGuardar').textContent = "Actualizar Cliente";
                document.getElementById('textoBtnGuardarPedido').textContent = "Actualizar y crear pedido";

                // Pestaña 1
                if (cli.tipo_cliente === 'empresa') {
                    document.getElementById('tipo_empresa').checked = true;
                } else {
                    document.getElementById('tipo_persona').checked = true;
                }
                document.getElementById('input_nombre').value = cli.nombre || '';
                document.getElementById('input_apellido').value = cli.apellido || '';
                document.getElementById('input_ci_nit').value = cli.ci_nit || '';
                document.getElementById('input_fecha_nacimiento').value = cli.fecha_nacimiento ? cli.fecha_nacimiento.substring(0, 10) : '';

                // Pestaña 2
                // Extraer solo los 8 dígitos bolivianos si viene con prefijo
                let telClean = (cli.telefono || '').replace(/\D/g, '');
                if (telClean.startsWith('591') && telClean.length === 11) {
                    telClean = telClean.substring(3);
                }
                document.getElementById('input_telefono').value = telClean;
                if (telClean.length === 8) {
                    document.getElementById('input_telefono').classList.add('is-valid');
                    document.getElementById('btnWhatsappModal').classList.remove('disabled');
                    document.getElementById('btnWhatsappModal').href = `https://wa.me/591${telClean}`;
                } else {
                    document.getElementById('btnWhatsappModal').classList.add('disabled');
                }

                document.getElementById('input_telefono_alternativo').value = cli.telefono_alternativo || '';
                document.getElementById('input_email').value = cli.email || '';
                document.getElementById('input_canal_preferido').value = cli.canal_preferido || 'whatsapp';

                // Pestaña 3 (Direcciones)
                direccionesCliente = [];
                if (cli.direcciones && cli.direcciones.length > 0) {
                    direccionesCliente = cli.direcciones.map(d => ({
                        id_direccion: d.id_direccion,
                        alias: d.alias,
                        ciudad: d.ciudad,
                        zona: d.zona,
                        calle: d.calle,
                        referencia: d.referencia,
                        latitud: d.latitud,
                        longitud: d.longitud,
                        es_principal: !!d.es_principal
                    }));
                } else if (cli.direccion) {
                    direccionesCliente.push({
                        alias: 'Principal',
                        ciudad: 'Santa Cruz de la Sierra',
                        zona: '',
                        calle: cli.direccion,
                        referencia: '',
                        latitud: COORDENADAS_DEFAULT.lat,
                        longitud: COORDENADAS_DEFAULT.lng,
                        es_principal: true
                    });
                } else {
                    direccionesCliente.push({
                        alias: 'Casa',
                        ciudad: 'Santa Cruz de la Sierra',
                        zona: '',
                        calle: '',
                        referencia: '',
                        latitud: COORDENADAS_DEFAULT.lat,
                        longitud: COORDENADAS_DEFAULT.lng,
                        es_principal: true
                    });
                }

                indiceDireccionEditando = 0;
                cargarDireccionParaEditar(0);
                renderDirecciones();

                // Pestaña 4
                const radioOrigen = document.getElementById(`orig_${cli.origen}`);
                if (radioOrigen) radioOrigen.checked = true;

                setEtiquetasSeleccionadas(cli.etiquetas || []);
                document.getElementById('input_notas').value = cli.notas || '';
                document.getElementById('switch_estado').checked = !!cli.estado;

                ocultarAlertaDuplicado();
                formularioModificado = false;
                bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-personales-btn')).show();
                modalClienteInstance.show();
            })
            .catch(err => {
                alert('No se pudo cargar la información del cliente.');
            });
    }

    /* ==========================================================================
       GUARDADO DEL FORMULARIO CON SPINNER Y REDIRECCIÓN
       ========================================================================== */
    function guardarClienteForm(accion) {
        // Validar campos obligatorios básicos
        const inputNombre = document.getElementById('input_nombre');
        const inputApellido = document.getElementById('input_apellido');
        const inputTel = document.getElementById('input_telefono');

        let valido = true;
        if (!inputNombre.value.trim()) {
            inputNombre.classList.add('is-invalid');
            document.getElementById('error_nombre').textContent = 'El nombre es obligatorio.';
            document.getElementById('error_nombre').style.display = 'block';
            bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-personales-btn')).show();
            inputNombre.focus();
            valido = false;
        }

        if (!inputApellido.value.trim()) {
            inputApellido.classList.add('is-invalid');
            document.getElementById('error_apellido').textContent = 'El apellido es obligatorio.';
            document.getElementById('error_apellido').style.display = 'block';
            if (valido) {
                bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-personales-btn')).show();
                inputApellido.focus();
            }
            valido = false;
        }

        const telVal = inputTel.value.replace(/\D/g, '');
        if (telVal.length !== 8 || (!telVal.startsWith('6') && !telVal.startsWith('7'))) {
            inputTel.classList.add('is-invalid');
            document.getElementById('error_telefono').textContent = 'El teléfono es obligatorio y debe ser un celular boliviano (8 dígitos que comiencen con 6 o 7).';
            document.getElementById('error_telefono').style.display = 'block';
            document.getElementById('badgeErrorContacto').style.display = 'inline';
            if (valido) {
                bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-contacto-btn')).show();
                inputTel.focus();
            }
            valido = false;
        } else {
            document.getElementById('badgeErrorContacto').style.display = 'none';
        }

        if (!valido) {
            return;
        }

        // Guardar la dirección actual que esté en el formulario
        guardarDireccionActualEnMemoria();

        // Construir datos del formulario
        const form = document.getElementById('formCliente');
        const formData = new FormData(form);
        formData.set('accion', accion);

        // Añadir las direcciones procesadas como JSON estructurado
        direccionesCliente.forEach((d, idx) => {
            formData.append(`direcciones[${idx}][id_direccion]`, d.id_direccion || '');
            formData.append(`direcciones[${idx}][alias]`, d.alias);
            formData.append(`direcciones[${idx}][ciudad]`, d.ciudad);
            formData.append(`direcciones[${idx}][zona]`, d.zona || '');
            formData.append(`direcciones[${idx}][calle]`, d.calle || '');
            formData.append(`direcciones[${idx}][referencia]`, d.referencia || '');
            if (d.latitud) formData.append(`direcciones[${idx}][latitud]`, d.latitud);
            if (d.longitud) formData.append(`direcciones[${idx}][longitud]`, d.longitud);
            formData.append(`direcciones[${idx}][es_principal]`, d.es_principal ? '1' : '0');
        });

        // Activar spinners
        const btnSubmit = (accion === 'guardar_y_pedido')
            ? document.getElementById('btnGuardarYPedido')
            : document.getElementById('btnGuardarCliente');
        const spinner = (accion === 'guardar_y_pedido')
            ? document.getElementById('spinnerGuardarPedido')
            : document.getElementById('spinnerGuardar');

        btnSubmit.disabled = true;
        spinner.style.display = 'inline-block';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw data;
            }
            return data;
        })
        .then(data => {
            formularioModificado = false;
            modalClienteInstance.hide();

            // Si se solicitó "Guardar y crear pedido", redirigir al POS
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            spinner.style.display = 'none';

            if (err.errors) {
                let msg = 'Errores de validación:\n';
                Object.values(err.errors).forEach(e => { msg += `• ${e.join(', ')}\n`; });
                alert(msg);
            } else {
                alert(err.message || 'Ocurrió un error al guardar el cliente.');
            }
        });
    }

    /* ==========================================================================
       CONFIRMACIÓN DE CIERRE CON CAMBIOS SIN GUARDAR
       ========================================================================== */
    function confirmarCerrarModal() {
        if (formularioModificado) {
            if (confirm('Tienes cambios sin guardar en el formulario. ¿Estás seguro de que deseas salir y descartarlos?')) {
                formularioModificado = false;
                modalClienteInstance.hide();
            }
        } else {
            modalClienteInstance.hide();
        }
    }
</script>
@endpush
