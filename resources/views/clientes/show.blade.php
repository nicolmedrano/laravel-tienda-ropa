@extends('layouts.app')

@section('title', "Perfil de {$cliente->nombre_completo} - Bella Girls")

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .show-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--peach-coral, #FEA38E), var(--coral-contrast, #D84C38));
        color: #ffffff;
    }
    #mapaShowCliente {
        height: 220px;
        border-radius: 12px;
        border: 1.5px solid var(--peach-soft, #F3B5A0);
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb / Volver -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver a Clientes
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('ventas.pos', ['cliente_id' => $cliente->id_cliente]) }}" class="btn btn-sm btn-boutique-rose fw-bold">
                <i class="bi bi-cart-plus me-1"></i> Nueva Venta para este Cliente
            </a>
        </div>
    </div>

    <!-- TARJETA CABECERA DEL CLIENTE -->
    <div class="card-boutique p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="show-avatar">
                    {{ mb_substr($cliente->nombre, 0, 1) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">{{ $cliente->nombre_completo }}</h3>
                        <span class="badge {{ $cliente->estado ? 'bg-success' : 'bg-secondary' }}">
                            {{ $cliente->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                        @if($cliente->tipo_cliente === 'empresa')
                            <span class="badge bg-dark">Empresa</span>
                        @endif
                    </div>
                    <div class="text-muted small mt-1 d-flex flex-wrap gap-3">
                        @if($cliente->ci_nit)
                            <span><i class="bi bi-card-heading me-1"></i> CI/NIT: {{ $cliente->ci_nit }}</span>
                        @endif
                        @if($cliente->fecha_nacimiento)
                            <span><i class="bi bi-cake2 me-1"></i> Cumpleaños: {{ $cliente->fecha_nacimiento->format('d/m/Y') }}</span>
                        @endif
                        <span><i class="bi bi-calendar-event me-1"></i> Registrado: {{ $cliente->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Etiquetas y canal -->
            <div class="d-flex flex-wrap gap-2 align-items-center">
                @if(!empty($cliente->etiquetas))
                    @foreach($cliente->etiquetas as $tag)
                        <span class="badge bg-warning text-dark border">{{ $tag }}</span>
                    @endforeach
                @endif
                @if($cliente->whatsapp_url)
                    <a href="{{ $cliente->whatsapp_url }}" target="_blank" class="btn btn-sm btn-success fw-bold">
                        <i class="bi bi-whatsapp me-1"></i> Abrir WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- COLUMNA IZQUIERDA: CONTACTO Y DIRECCIONES -->
        <div class="col-12 col-lg-5">
            <!-- Datos de contacto -->
            <div class="card-boutique p-3 mb-4">
                <h6 class="font-serif fw-bold mb-3 border-bottom pb-2" style="color: var(--title, #4A1F2A);">
                    <i class="bi bi-telephone-fill me-1" style="color: var(--coral-contrast, #D84C38);"></i> Información de Contacto
                </h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Teléfono / WhatsApp:</span>
                        <strong class="font-monospace">{{ $cliente->telefono ?? '—' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Teléfono Alternativo:</span>
                        <span>{{ $cliente->telefono_alternativo ?? '—' }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Correo Electrónico:</span>
                        <span>{{ $cliente->email ?? '—' }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Canal Preferido:</span>
                        <span class="badge bg-light text-dark border">{{ ucfirst($cliente->canal_preferido ?? 'WhatsApp') }}</span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Origen del Cliente:</span>
                        <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $cliente->origen ?? 'Tienda')) }}</span>
                    </li>
                </ul>

                @if($cliente->notas)
                    <div class="mt-3 p-2 bg-light rounded border">
                        <span class="small fw-semibold text-muted d-block mb-1"><i class="bi bi-chat-left-text me-1"></i> Notas del Cliente:</span>
                        <p class="m-0 small fst-italic">{{ $cliente->notas }}</p>
                    </div>
                @endif
            </div>

            <!-- Direcciones de entrega -->
            <div class="card-boutique p-3">
                <h6 class="font-serif fw-bold mb-3 border-bottom pb-2" style="color: var(--title, #4A1F2A);">
                    <i class="bi bi-geo-alt-fill me-1" style="color: var(--coral-contrast, #D84C38);"></i> Direcciones de Entrega
                </h6>

                @forelse($cliente->direcciones as $dir)
                    <div class="p-2 border rounded mb-2 bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge" style="{{ $dir->es_principal ? 'background: var(--badbg, #FDEDEC); color: var(--bad, #C0392B); border: 1px solid var(--bad, #C0392B);' : 'background: #f8f9fa; color: #6c757d; border: 1px solid #ced4da;' }}">{{ $dir->alias }}</span>
                            @if($dir->es_principal)
                                <small class="fw-bold" style="color: var(--coral-contrast, #D84C38);"><i class="bi bi-star-fill me-1"></i>Principal</small>
                            @endif
                        </div>
                        <div class="small fw-bold">{{ $dir->calle }}</div>
                        <div class="small text-muted">{{ $dir->zona ? "Zona {$dir->zona}, " : '' }}{{ $dir->ciudad }}</div>
                        @if($dir->referencia)
                            <div class="small text-muted fst-italic">Ref: {{ $dir->referencia }}</div>
                        @endif
                    </div>
                @empty
                    <p class="small text-muted m-0">{{ $cliente->direccion ?? 'Sin direcciones registradas.' }}</p>
                @endforelse
            </div>
        </div>

        <!-- COLUMNA DERECHA: HISTORIAL DE VENTAS Y COMPRAS -->
        <div class="col-12 col-lg-7">
            <div class="card-boutique p-3">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="font-serif fw-bold m-0" style="color: var(--title, #4A1F2A);">
                        <i class="bi bi-receipt me-1" style="color: var(--coral-contrast, #D84C38);"></i> Historial de Compras ({{ $cliente->ventas->count() }})
                    </h6>
                </div>

                @if($cliente->ventas->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Factura</th>
                                    <th>Fecha</th>
                                    <th>Prendas</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Ticket</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->ventas as $v)
                                    <tr>
                                        <td class="fw-bold">{{ $v->numero_factura }}</td>
                                        <td>{{ $v->fecha_venta ? $v->fecha_venta->format('d/m/Y H:i') : '' }}</td>
                                        <td>
                                            <small class="text-muted">{{ $v->detalles->sum('cantidad') }} prenda(s)</small>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            Bs. {{ number_format($v->total, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('ventas.ticket', $v->id_venta) }}" target="_blank" class="btn btn-xs btn-outline-secondary">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-bag-x display-6 d-block mb-2 text-muted"></i>
                        Este cliente aún no registra compras en la tienda.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
