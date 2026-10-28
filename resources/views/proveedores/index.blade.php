@extends('layouts.app')

@section('title', 'Módulo Proveedor - Proveedores de Mercadería')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark text-white px-2 py-1" style="font-size: 0.72rem;">Módulo 5 de 6</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--boutique-dark);">Gestión de Proveedores</h2>
            </div>
            <p class="text-muted m-0">Empresas fabricantes y distribuidoras asociadas al módulo de Compras.</p>
        </div>
        <div>
            <button type="button" class="btn btn-boutique-rose" data-bs-toggle="modal" data-bs-target="#modalNuevoProveedor">
                <i class="bi bi-truck me-1"></i> Registrar Proveedor
            </button>
        </div>
    </div>

    <!-- Buscador -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('proveedores.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por razón social, contacto, teléfono o correo..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-boutique w-100">Buscar</button>
                @if(request('buscar'))
                    <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE PROVEEDORES -->
    <div class="card-boutique overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr style="font-size: 0.82rem;">
                        <th>ID</th>
                        <th>Razón Social / Empresa</th>
                        <th>Contacto Comercial</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th class="text-center">Órdenes</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $prov)
                        <tr>
                            <td><span class="text-muted">#{{ $prov->id_proveedor }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $prov->nombre_empresa }}</div>
                                <small class="text-muted">{{ $prov->direccion ?? 'Sin dirección' }}</small>
                            </td>
                            <td>{{ $prov->contacto }}</td>
                            <td>{{ $prov->telefono }}</td>
                            <td>{{ $prov->email ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $prov->compras_count }} compra(s)</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $prov->estado ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $prov->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light border" onclick="editarProveedor({{ json_encode($prov) }})" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('proveedores.toggle', $prov->id_proveedor) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-light border {{ $prov->estado ? 'text-warning' : 'text-success' }}" title="{{ $prov->estado ? 'Desactivar' : 'Activar' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No se encontraron proveedores registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $proveedores->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- MODAL: REGISTRAR PROVEEDOR -->
<div class="modal fade" id="modalNuevoProveedor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-serif fw-bold">Registrar Empresa Proveedora</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('proveedores.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nombre de Empresa / Razón Social <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_empresa" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Persona de Contacto <span class="text-danger">*</span></label>
                            <input type="text" name="contacto" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control form-control-sm">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Dirección de Despacho / Fábrica</label>
                        <input type="text" name="direccion" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-boutique-rose fw-bold">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR PROVEEDOR -->
<div class="modal fade" id="modalEditarProveedor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-serif fw-bold">Editar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditProveedor" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Empresa / Razón Social <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_empresa" id="edit_empresa" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Contacto <span class="text-danger">*</span></label>
                            <input type="text" name="contacto" id="edit_contacto" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" id="edit_tel" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" id="edit_mail" class="form-control form-control-sm">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Dirección</label>
                        <input type="text" name="direccion" id="edit_dir" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-boutique-rose fw-bold">Actualizar Proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editarProveedor(prov) {
        document.getElementById('formEditProveedor').action = `/proveedores/${prov.id_proveedor}`;
        document.getElementById('edit_empresa').value = prov.nombre_empresa || '';
        document.getElementById('edit_contacto').value = prov.contacto || '';
        document.getElementById('edit_tel').value = prov.telefono || '';
        document.getElementById('edit_mail').value = prov.email || '';
        document.getElementById('edit_dir').value = prov.direccion || '';
        new bootstrap.Modal(document.getElementById('modalEditarProveedor')).show();
    }
</script>
@endpush
