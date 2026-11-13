@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado del Módulo -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1" style="font-size: 0.72rem;">Administración</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Gestión de Categorías</h2>
            </div>
            <p class="text-muted m-0">Administre las clasificaciones para prendas de vestir, accesorios, carteras y bisutería.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-boutique-rose" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria">
                <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
            </button>
        </div>
    </div>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="card-boutique p-3 mb-4">
        <form action="{{ route('categorias.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre o descripción..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <select name="estado" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos los Estados</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Solo Activas</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Solo Inactivas</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-boutique w-100">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
                @if(request()->hasAny(['buscar', 'estado']))
                    <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de Categorías -->
    <div class="card-boutique overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">#</th>
                        <th>Nombre y Descripción</th>
                        <th style="width: 170px;" class="text-center">Tipo / Tallas</th>
                        <th style="width: 150px;" class="text-center">Productos</th>
                        <th style="width: 130px;" class="text-center">Estado</th>
                        <th style="width: 160px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $cat)
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $cat->id_categoria }}
                            </td>
                            <td>
                                <div class="fw-bold" style="color: var(--color-text-dark); font-size: 0.95rem;">
                                    {{ $cat->nombre }}
                                </div>
                                @if($cat->descripcion)
                                    <div class="text-muted small text-truncate" style="max-width: 420px;" title="{{ $cat->descripcion }}">
                                        {{ $cat->descripcion }}
                                    </div>
                                @else
                                    <small class="text-muted fst-italic">Sin descripción</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($cat->usa_talla)
                                    <span class="badge badge-pink-soft" title="Aplica selección de tallas como XS, S, M, L, XL">
                                        <i class="bi bi-rulers me-1"></i> Ropa (Con Tallas)
                                    </span>
                                @else
                                    <span class="badge badge-boutique" title="Talla única o no aplicable (carteras, bisutería, accesorios)">
                                        <i class="bi bi-gem me-1"></i> Talla Única / Accesorio
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $cat->productos_count > 0 ? 'bg-light text-dark border' : 'text-muted' }}">
                                    <i class="bi bi-tag me-1"></i> {{ $cat->productos_count }} {{ Str::plural('producto', $cat->productos_count) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($cat->estado)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Activa
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-dash-circle me-1"></i> Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <!-- Botón Editar (Modal) -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary" 
                                            title="Editar categoría"
                                            onclick="abrirModalEditar({{ json_encode($cat) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Botón Toggle Estado -->
                                    <form action="{{ route('categorias.toggle', $cat->id_categoria) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="btn btn-sm {{ $cat->estado ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                title="{{ $cat->estado ? 'Desactivar categoría' : 'Activar categoría' }}">
                                            <i class="bi {{ $cat->estado ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Botón Eliminar Físicamente -->
                                    @if($cat->productos_count > 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary opacity-50" 
                                                title="No se puede eliminar: tiene {{ $cat->productos_count }} producto(s) asociado(s). Desactívela en su lugar."
                                                onclick="alert('Esta categoría no puede ser eliminada porque tiene {{ $cat->productos_count }} producto(s) vinculado(s). Puede desactivarla para ocultarla en nuevos registros.');">
                                            <i class="bi bi-trash text-muted"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('categorias.destroy', $cat->id_categoria) }}" 
                                              method="POST" 
                                              class="d-inline m-0"
                                              onsubmit="return confirm('¿Está seguro de eliminar definitivamente la categoría \'{{ $cat->nombre }}\'? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar categoría">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-2">
                                    <i class="bi bi-tags display-5 text-muted opacity-50"></i>
                                </div>
                                <div class="fw-semibold">No se encontraron categorías registradas</div>
                                <small>Haga clic en "+ Nueva Categoría" para crear la primera o ajuste los filtros de búsqueda.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categorias->hasPages())
            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    Mostrando {{ $categorias->firstItem() ?? 0 }} a {{ $categorias->lastItem() ?? 0 }} de {{ $categorias->total() }} categorías
                </span>
                <div>
                    {{ $categorias->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- =========================================================================
     MODAL: CREAR CATEGORÍA
     ========================================================================= -->
<div class="modal fade" id="modalCrearCategoria" tabindex="-1" aria-labelledby="modalCrearCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif fw-bold" id="modalCrearCategoriaLabel">
                    <i class="bi bi-tag-fill me-2" style="color: var(--fucsia-oscuro);"></i> Nueva Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="crearNombre" class="form-label fw-semibold small">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nombre" 
                               id="crearNombre" 
                               class="form-control" 
                               placeholder="ej. Carteras, Vestidos, Bisutería..." 
                               required 
                               maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="crearDescripcion" class="form-label fw-semibold small">Descripción (Opcional)</label>
                        <textarea name="descripcion" 
                                  id="crearDescripcion" 
                                  rows="3" 
                                  class="form-control" 
                                  placeholder="Detalle o características de los artículos pertenecientes a este grupo..." 
                                  maxlength="500"></textarea>
                    </div>

                    <!-- Switch: ¿Aplica tallas de ropa? -->
                    <div class="p-3 rounded" style="background-color: var(--color-bg-subtle); border: 1px solid var(--color-border);">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   name="usa_talla" 
                                   value="1" 
                                   id="crearUsaTalla" 
                                   checked>
                            <label class="form-check-label fw-semibold" for="crearUsaTalla" style="color: var(--color-text-dark);">
                                ¿Aplica tallas de ropa (XS, S, M, L, XL)?
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                            Mantenga activo para prendas de vestir. Desmárquelo para productos como <strong>carteras, bolsos, bisutería o accesorios</strong> que manejan talla única.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-boutique-rose px-4 fw-bold">
                        <i class="bi bi-check2 me-1"></i> Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: EDITAR CATEGORÍA
     ========================================================================= -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif fw-bold" id="modalEditarCategoriaLabel">
                    <i class="bi bi-pencil-square me-2" style="color: var(--fucsia-oscuro);"></i> Editar Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditarCategoria" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editarNombre" class="form-label fw-semibold small">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nombre" 
                               id="editarNombre" 
                               class="form-control" 
                               required 
                               maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="editarDescripcion" class="form-label fw-semibold small">Descripción (Opcional)</label>
                        <textarea name="descripcion" 
                                  id="editarDescripcion" 
                                  rows="3" 
                                  class="form-control" 
                                  maxlength="500"></textarea>
                    </div>

                    <!-- Switch: ¿Aplica tallas de ropa? -->
                    <div class="p-3 rounded mb-3" style="background-color: var(--color-bg-subtle); border: 1px solid var(--color-border);">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   name="usa_talla" 
                                   value="1" 
                                   id="editarUsaTalla">
                            <label class="form-check-label fw-semibold" for="editarUsaTalla" style="color: var(--color-text-dark);">
                                ¿Aplica tallas de ropa (XS, S, M, L, XL)?
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                            Desactivar si corresponde a carteras, accesorios o bisutería de talla única.
                        </small>
                    </div>

                    <!-- Switch: Estado Activo -->
                    <div class="p-3 rounded" style="background-color: var(--color-bg-subtle); border: 1px solid var(--color-border);">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   name="estado" 
                                   value="1" 
                                   id="editarEstado">
                            <label class="form-check-label fw-semibold" for="editarEstado" style="color: var(--color-text-dark);">
                                Categoría activa en catálogo
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                            Las categorías inactivas no se muestran al registrar nuevos productos.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-boutique-rose px-4 fw-bold">
                        <i class="bi bi-check2 me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function abrirModalEditar(categoria) {
        const form = document.getElementById('formEditarCategoria');
        form.action = `/categorias/${categoria.id_categoria}`;

        document.getElementById('editarNombre').value = categoria.nombre || '';
        document.getElementById('editarDescripcion').value = categoria.descripcion || '';
        document.getElementById('editarUsaTalla').checked = !!categoria.usa_talla;
        document.getElementById('editarEstado').checked = !!categoria.estado;

        const modal = new bootstrap.Modal(document.getElementById('modalEditarCategoria'));
        modal.show();
    }
</script>
@endpush

