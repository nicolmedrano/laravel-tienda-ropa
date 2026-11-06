@extends('layouts.app')

@section('title', 'Editar Prenda: ' . $producto->nombre)

@section('content')
<div class="container-fluid p-0" style="max-width: 950px;">
    <!-- Encabezado -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-light border py-1 px-2 text-muted">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Editar Prenda: {{ $producto->nombre }}</h2>
            </div>
            <p class="text-muted m-0">Actualice la fotografía, datos comerciales o conserve la imagen actual.</p>
        </div>
        <span class="badge badge-module px-3 py-2">Código: {{ $producto->codigo }}</span>
    </div>

    <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- COLUMNA IZQUIERDA: GESTIÓN DE FOTOGRAFÍA -->
            <div class="col-12 col-md-5">
                <div class="card-boutique p-4 mb-4">
                    <label class="form-label fw-bold mb-2">Fotografía de la Prenda</label>
                    <p class="text-muted small mb-3">Formatos: <strong>JPG, JPEG, PNG, WEBP</strong> (máx. 5 MB). Puede cambiarla o conservarla.</p>

                    <!-- CONTENEDOR DE VISTA PREVIA (ACTUAL / NUEVA) -->
                    <div id="previewContainer" class="border rounded p-2 text-center mb-3" style="min-height: 220px; background-color: var(--color-bg-subtle); border-color: var(--color-border) !important; display: flex; align-items: center; justify-content: center;">
                        @if($producto->imagen_url)
                            <img id="imgPreview" src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" style="max-height: 200px; max-width: 100%; object-fit: contain; border-radius: 6px;">
                            <div id="placeholderPreview" class="text-muted py-4" style="display: none;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                                <div class="small mt-1">Sin fotografía</div>
                            </div>
                        @else
                            <img id="imgPreview" src="" alt="Vista previa" style="max-height: 200px; max-width: 100%; object-fit: contain; display: none; border-radius: 6px;">
                            <div id="placeholderPreview" class="text-muted py-4">
                                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--color-bg-light); border: 1px solid var(--color-accent); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                    <i class="bi bi-handbag" style="font-size: 1.8rem; color: var(--color-primary);"></i>
                                </div>
                                <div class="fw-semibold small" style="color: var(--color-text-dark);">Sin fotografía cargada</div>
                            </div>
                        @endif
                    </div>

                    <!-- BOTÓN SELECCIONAR NUEVA IMAGEN -->
                    <div class="d-grid gap-2">
                        <label for="inputImagenEdit" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2" style="cursor: pointer;">
                            <i class="bi bi-arrow-repeat text-primary"></i> Cambiar imagen
                        </label>
                        <input type="file" name="imagen" id="inputImagenEdit" class="d-none" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <small id="fileNameDisplayEdit" class="text-muted text-center" style="font-size: 0.78rem;">
                            {{ $producto->imagen ? 'Conservando imagen actual' : 'Ningún archivo nuevo' }}
                        </small>
                        <div id="fileErrorEdit" class="text-danger small text-center" style="display: none;"></div>
                    </div>

                    @if($producto->imagen)
                        <div class="form-check mt-3 pt-2 border-top" style="border-color: var(--color-border) !important;">
                            <input class="form-check-input" type="checkbox" name="conservar_imagen" value="1" id="conservarImagen" checked>
                            <label class="form-check-label small" for="conservarImagen">
                                Conservar fotografía actual si no se selecciona una nueva
                            </label>
                        </div>
                    @endif
                </div>

                <!-- Estado del producto -->
                <div class="card-boutique p-4">
                    <label class="form-label fw-bold mb-2">Estado del Producto</label>
                    <select name="estado" class="form-select">
                        <option value="1" {{ $producto->estado ? 'selected' : '' }}>Activo (Visible en Catálogo)</option>
                        <option value="0" {{ !$producto->estado ? 'selected' : '' }}>Inactivo (Oculto del Catálogo)</option>
                    </select>
                </div>
            </div>

            <!-- COLUMNA DERECHA: DATOS DEL FORMULARIO -->
            <div class="col-12 col-md-7">
                <div class="card-boutique p-4 mb-4">
                    <h6 class="fw-bold mb-3" style="color: var(--color-text-dark);">Información General</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-5">
                            <label class="form-label fw-semibold small">Código <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $producto->codigo) }}" required style="text-transform: uppercase;">
                        </div>
                        <div class="col-12 col-sm-7">
                            <label class="form-label fw-semibold small">Nombre de la Prenda <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Categoría <span class="text-danger">*</span></label>
                        <select name="id_categoria" class="form-select" required>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id_categoria }}" {{ old('id_categoria', $producto->id_categoria) == $cat->id_categoria ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small">Precio de Venta (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" step="0.50" name="precio_venta" class="form-control" value="{{ old('precio_venta', $producto->precio_venta) }}" required min="0">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small">Costo de Compra (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" step="0.50" name="costo_compra" class="form-control" value="{{ old('costo_compra', $producto->costo_compra) }}" required min="0">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold small">Stock Mínimo de Alerta</label>
                        <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required min="0">
                    </div>
                </div>

                <!-- Variantes existentes -->
                <div class="card-boutique p-4 mb-4">
                    <h6 class="fw-bold mb-2" style="color: var(--color-text-dark);">Variantes de Stock Actual</h6>
                    <p class="text-muted small mb-3">Tallas y colores registrados para esta prenda:</p>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr style="font-size: 0.78rem;">
                                    <th>SKU</th>
                                    <th>Talla</th>
                                    <th>Color</th>
                                    <th class="text-center">Stock Actual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($producto->variantes as $v)
                                    <tr>
                                        <td><code style="color: var(--color-primary);">{{ $v->codigo_sku }}</code></td>
                                        <td><span class="badge bg-light text-dark border">{{ $v->talla }}</span></td>
                                        <td>{{ $v->color }}</td>
                                        <td class="text-center fw-bold" style="color: var(--color-primary);">{{ $v->stock_actual }} un.</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('productos.index') }}" class="btn btn-light border">Cancelar</a>
                    <button type="submit" class="btn btn-boutique-rose px-4 fw-bold">
                        <i class="bi bi-check2 me-1"></i> Actualizar Cambios
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const inputImagenEdit = document.getElementById('inputImagenEdit');
    const imgPreview = document.getElementById('imgPreview');
    const placeholderPreview = document.getElementById('placeholderPreview');
    const fileNameDisplayEdit = document.getElementById('fileNameDisplayEdit');
    const fileErrorEdit = document.getElementById('fileErrorEdit');

    inputImagenEdit.addEventListener('change', function() {
        fileErrorEdit.style.display = 'none';
        fileErrorEdit.textContent = '';

        if (!this.files || !this.files[0]) {
            return;
        }

        const file = this.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const allowedExtensions = ['.jpg', '.jpeg', '.png', '.webp'];
        const fileExt = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();

        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExt)) {
            fileErrorEdit.textContent = 'Solo se admiten fotografías JPG, JPEG, PNG y WEBP.';
            fileErrorEdit.style.display = 'block';
            this.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            fileErrorEdit.textContent = 'La imagen excede el límite máximo de 5 Megabytes.';
            fileErrorEdit.style.display = 'block';
            this.value = '';
            return;
        }

        fileNameDisplayEdit.textContent = `Nueva foto: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        const reader = new FileReader();
        reader.onload = function(evt) {
            imgPreview.src = evt.target.result;
            imgPreview.style.display = 'block';
            if (placeholderPreview) placeholderPreview.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
