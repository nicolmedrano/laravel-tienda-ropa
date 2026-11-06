@extends('layouts.app')

@section('title', 'Módulo Usuario - Cuentas de Acceso y Roles')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1" style="font-size: 0.72rem;">Módulo 6 de 6</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Gestión de Usuarios</h2>
            </div>
            <p class="text-muted m-0">Control de acceso según el rol (administrador / vendedor) y trazabilidad de operaciones.</p>
        </div>
        <div>
            <button type="button" class="btn btn-boutique-rose" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                <i class="bi bi-shield-plus me-1"></i> Crear Usuario
            </button>
        </div>
    </div>

    <!-- TABLA DE USUARIOS -->
    <div class="card-boutique overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr style="font-size: 0.82rem;">
                        <th>ID</th>
                        <th>Nombre y Apellido</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol Asignado</th>
                        <th class="text-center">Estado</th>
                        <th>Fecha Creación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usr)
                        <tr>
                            <td><span class="text-muted">#{{ $usr->id_usuario }}</span></td>
                            <td>
                                <div class="fw-bold" style="color: var(--color-text-dark);">{{ $usr->nombre_completo }}</div>
                            </td>
                            <td><code style="color: var(--color-primary); background-color: var(--color-bg-subtle); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--color-border);">{{ $usr->usuario }}</code></td>
                            <td>
                                <span class="{{ $usr->rol === 'administrador' ? 'badge-admin' : 'badge-vendedor' }}">
                                    {{ ucfirst($usr->rol) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $usr->estado ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $usr->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>{{ $usr->created_at ? $usr->created_at->format('d/m/Y') : '—' }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-light border" onclick="editarUsuario({{ json_encode($usr) }})" title="Editar usuario">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    @if($usr->id_usuario !== 1)
                                        <form action="{{ route('usuarios.toggle', $usr->id_usuario) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-light border {{ $usr->estado ? 'text-warning' : 'text-success' }}" title="{{ $usr->estado ? 'Desactivar cuenta' : 'Activar cuenta' }}">
                                                <i class="bi bi-power"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No se encontraron usuarios en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: CREAR USUARIO -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-serif fw-bold">Crear Nueva Cuenta de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
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
                        <label class="form-label small fw-semibold">Nombre de Usuario (Login) <span class="text-danger">*</span></label>
                        <input type="text" name="usuario" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-sm" minlength="6" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Rol Asignado <span class="text-danger">*</span></label>
                            <select name="rol" class="form-select form-select-sm" required>
                                <option value="vendedor" selected>Vendedor</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">La contraseña será hasheada con bcrypt en PostgreSQL.</small>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-boutique-rose fw-bold">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR USUARIO -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title font-serif fw-bold">Editar Cuenta de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditUsuario" method="POST">
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
                        <label class="form-label small fw-semibold">Nombre de Usuario (Login) <span class="text-danger">*</span></label>
                        <input type="text" name="usuario" id="edit_usuario" class="form-control form-control-sm" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Nueva Contraseña (Opcional)</label>
                            <input type="password" name="password" class="form-control form-control-sm" minlength="6" placeholder="Dejar en blanco para no cambiar">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Rol Asignado <span class="text-danger">*</span></label>
                            <select name="rol" id="edit_rol" class="form-select form-select-sm" required>
                                <option value="vendedor">Vendedor</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-boutique-rose fw-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editarUsuario(usr) {
        document.getElementById('formEditUsuario').action = `/usuarios/${usr.id_usuario}`;
        document.getElementById('edit_nombre').value = usr.nombre || '';
        document.getElementById('edit_apellido').value = usr.apellido || '';
        document.getElementById('edit_usuario').value = usr.usuario || '';
        document.getElementById('edit_rol').value = usr.rol || 'vendedor';
        new bootstrap.Modal(document.getElementById('modalEditarUsuario')).show();
    }
</script>
@endpush
