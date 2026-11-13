@extends('layouts.app')

@section('title', 'Módulo Cliente - Clientes de la Tienda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1">Cliente</span>
                <h2 class="font-serif fw-bold m-0 page-title" style="color: var(--color-text-dark);">Gestión de Clientes</h2>
            </div>
            <p class="page-subtitle text-muted m-0">Registro, edición y trazabilidad de compras asociadas al módulo de Ventas.</p>
        </div>
        <div>
            <button type="button" class="btn btn-boutique-rose" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                <i class="bi bi-person-plus me-1"></i> Nuevo Cliente
            </button>
        </div>
    </div>

    <!-- Buscador -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('clientes.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre, apellido, teléfono o email..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-boutique w-100">Buscar</button>
                @if(request('buscar'))
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE CLIENTES -->
    <div class="card-boutique overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th class="text-center">Compras</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cli)
                        <tr>
                            <td><span class="text-muted">#{{ $cli->id_cliente }}</span></td>
                            <td>
                                <div class="fw-bold" style="color: var(--color-text-dark);">{{ $cli->nombre_completo }}</div>
                                <small class="text-muted">Registrado: {{ $cli->created_at ? $cli->created_at->format('d/m/Y') : '' }}</small>
                            </td>
                            <td>{{ $cli->telefono ?? '—' }}</td>
                            <td>{{ $cli->email ?? '—' }}</td>
                            <td><small class="text-muted">{{ $cli->direccion ?? '—' }}</small></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $cli->ventas_count }} venta(s)</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $cli->estado ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $cli->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light border" onclick="editarCliente({{ json_encode($cli) }})" title="Editar datos">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
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
                                No se encontraron clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $clientes->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- MODAL: REGISTRAR CLIENTE -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-serif fw-bold">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Teléfono / WhatsApp</label>
                        <input type="text" name="telefono" class="form-control form-control-sm" placeholder="ej. +591 71234567">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="ej. cliente@email.com">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Dirección de Entrega</label>
                        <input type="text" name="direccion" class="form-control form-control-sm" placeholder="Zona, Calle y N°">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-boutique-rose fw-bold">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR CLIENTE -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-serif fw-bold">Editar Datos del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditCliente" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido" id="edit_apellido" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" id="edit_telefono" class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Correo Electrónico</label>
                        <input type="email" name="email" id="edit_email" class="form-control form-control-sm">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Dirección</label>
                        <input type="text" name="direccion" id="edit_direccion" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-boutique-rose fw-bold">Actualizar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editarCliente(cli) {
        document.getElementById('formEditCliente').action = `/clientes/${cli.id_cliente}`;
        document.getElementById('edit_nombre').value = cli.nombre || '';
        document.getElementById('edit_apellido').value = cli.apellido || '';
        document.getElementById('edit_telefono').value = cli.telefono || '';
        document.getElementById('edit_email').value = cli.email || '';
        document.getElementById('edit_direccion').value = cli.direccion || '';
        new bootstrap.Modal(document.getElementById('modalEditarCliente')).show();
    }
</script>
@endpush
