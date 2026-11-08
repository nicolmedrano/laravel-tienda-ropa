@extends('layouts.app')

@section('title', 'Registrar Prenda de Ropa')

@section('content')
<div class="container-fluid p-0" style="max-width: 1000px;">
    <!-- Encabezado -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-light border py-1 px-2 text-muted">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Registrar Nueva Prenda</h2>
            </div>
            <p class="text-muted m-0">Complete la información de la prenda, fotografía y genere sus variantes de inventario.</p>
        </div>
    </div>

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" id="formProducto">
        @csrf

        <div class="row g-4">
            <!-- COLUMNA IZQUIERDA: DATOS GENERALES Y FOTOGRAFÍA -->
            <div class="col-12 col-md-5">
                <!-- Tarjeta de Carga de Fotografía -->
                <div class="card-boutique p-4 mb-4">
                    <label class="form-label fw-bold mb-2">Fotografía de la Prenda</label>
                    <p class="text-muted small mb-3">Formatos permitidos: <strong>JPG, JPEG, PNG, WEBP</strong>. Máximo 5 MB.</p>

                    <!-- CONTENEDOR DE VISTA PREVIA -->
                    <div id="previewContainer" class="border rounded p-2 text-center mb-3" style="min-height: 220px; background-color: var(--color-bg-subtle); border-color: var(--color-border) !important; display: flex; align-items: center; justify-content: center; position: relative;">
                        <img id="imgPreview" src="" alt="Vista previa de la prenda" style="max-height: 200px; max-width: 100%; object-fit: contain; display: none; border-radius: 6px;">
                        
                        <div id="placeholderPreview" class="text-muted py-4">
                            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--color-bg-light); border: 1px solid var(--color-accent); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                <i class="bi bi-image" style="font-size: 1.8rem; color: var(--color-primary);"></i>
                            </div>
                            <div class="fw-semibold small" style="color: var(--color-text-dark);">Sin fotografía seleccionada</div>
                            <small class="text-muted">Haga clic en el botón inferior para elegir un archivo</small>
                        </div>
                    </div>

                    <!-- BOTÓN SELECCIONAR IMAGEN -->
                    <div class="d-grid gap-2">
                        <label for="inputImagen" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2" style="cursor: pointer;">
                            <i class="bi bi-upload text-primary"></i> Seleccionar imagen
                        </label>
                        <input type="file" name="imagen" id="inputImagen" class="d-none" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <small id="fileNameDisplay" class="text-muted text-center" style="font-size: 0.78rem;">Ningún archivo seleccionado</small>
                        <div id="fileError" class="text-danger small text-center" style="display: none;"></div>
                    </div>
                </div>

                <!-- Tarjeta de Categoría y Stock Mínimo -->
                <div class="card-boutique p-4">
                    <h6 class="fw-bold mb-3" style="color: var(--color-text-dark);">Clasificación & Alertas</h6>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold small m-0">Categoría <span class="text-danger">*</span></label>
                            @if(Auth::user() && Auth::user()->esAdmin())
                                <a href="{{ route('categorias.index') }}" target="_blank" class="text-decoration-none" style="font-size: 0.78rem; color: var(--fucsia-oscuro); font-weight: 600;" title="Administrar catálogo de categorías">
                                    <i class="bi bi-gear-fill me-1"></i> Administrar categorías
                                </a>
                            @endif
                        </div>
                        <div class="input-group">
                            <select name="id_categoria" id="selectCategoria" class="form-select @error('id_categoria') is-invalid @enderror" required onchange="actualizarModoTallas()">
                                <option value="" data-usa-talla="1">Seleccione una categoría...</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id_categoria }}" 
                                            data-usa-talla="{{ $cat->usa_talla ? '1' : '0' }}" 
                                            {{ old('id_categoria') == $cat->id_categoria ? 'selected' : '' }}>
                                        {{ $cat->nombre }} {{ !$cat->usa_talla ? ' (Talla única / Accesorio)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if(Auth::user() && Auth::user()->esAdmin())
                                <a href="{{ route('categorias.index') }}" target="_blank" class="btn btn-outline-secondary" title="Crear o administrar categorías">
                                    <i class="bi bi-plus-lg"></i>
                                </a>
                            @endif
                        </div>
                        @error('id_categoria')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div id="categoriaAlertaTalla" class="text-muted small mt-1" style="font-size: 0.76rem;"></div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Stock Mínimo de Seguridad</label>
                        <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', 3) }}" min="0" required>
                        <small class="text-muted">Generará una alerta cuando las existencias sean iguales o inferiores.</small>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: INFORMACIÓN COMERCIAL Y VARIANTES -->
            <div class="col-12 col-md-7">
                <div class="card-boutique p-4 mb-4">
                    <h6 class="fw-bold mb-3" style="color: var(--color-text-dark);">Información Comercial</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-sm-5">
                            <label class="form-label fw-semibold small">Código Único <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" placeholder="ej. VEST-010" value="{{ old('codigo') }}" required style="text-transform: uppercase;">
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-sm-7">
                            <label class="form-label fw-semibold small">Nombre de la Prenda <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" placeholder="ej. Vestido Cocktail Escote V" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Descripción & Tejido</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de corte, composición textil, acabados o sugerencias de cuidado...">{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small">Precio de Venta (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" step="0.50" name="precio_venta" class="form-control @error('precio_venta') is-invalid @enderror" placeholder="0.00" value="{{ old('precio_venta') }}" required min="0">
                            </div>
                            @error('precio_venta')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label fw-semibold small">Costo de Compra (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" step="0.50" name="costo_compra" class="form-control @error('costo_compra') is-invalid @enderror" placeholder="0.00" value="{{ old('costo_compra') }}" required min="0">
                            </div>
                            @error('costo_compra')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- VARIANTES (Tallas, Colores y Stock Inicial) -->
                <div class="card-boutique p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="fw-bold m-0" style="color: var(--color-text-dark);">Variantes de Prenda (Tallas & Colores)</h6>
                            <small class="text-muted">Evita mezclar existencias de diferentes tallas o colores.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-boutique" onclick="agregarVariante()">
                            <i class="bi bi-plus-lg"></i> Agregar Variante
                        </button>
                    </div>

                    <!-- Paleta Oficial Bella Girls (Colores Centralizados) -->
                    <div class="p-2 mb-3 rounded" style="background-color: var(--color-bg-subtle); border: 1px solid var(--color-border);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-bold text-uppercase" style="font-size: 0.72rem; color: var(--fucsia-oscuro); letter-spacing: 0.5px;">
                                <i class="bi bi-palette-fill me-1"></i> Paleta Oficial Bella Girls (clic para rellenar color):
                            </small>
                            <small class="text-muted" style="font-size: 0.7rem;">7 tonos oficiales</small>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-sm py-1 px-2 border-0 fw-bold d-flex align-items-center gap-1" style="background-color: var(--rosa-principal, #FD7EAF); color: var(--rojo-vino, #821110); border-radius: 6px; font-size: 0.74rem;" onclick="asignarColor('Rosa principal (#FD7EAF)')">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--rojo-vino); display: inline-block;"></span> Rosa principal
                            </button>
                            <button type="button" class="btn btn-sm py-1 px-2 border-0 fw-semibold text-white d-flex align-items-center gap-1" style="background-color: var(--rosa-intenso, #EB5387); border-radius: 6px; font-size: 0.74rem;" onclick="asignarColor('Rosa intenso (#EB5387)')">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #fff; display: inline-block;"></span> Rosa intenso
                            </button>
                            <button type="button" class="btn btn-sm py-1 px-2 border-0 fw-semibold text-white d-flex align-items-center gap-1" style="background-color: var(--fucsia-oscuro, #C01548); border-radius: 6px; font-size: 0.74rem;" onclick="asignarColor('Fucsia oscuro (#C01548)')">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #fff; display: inline-block;"></span> Fucsia oscuro
                            </button>
                            <button type="button" class="btn btn-sm py-1 px-2 border-0 fw-bold d-flex align-items-center gap-1" style="background-color: var(--dorado-principal, #FCDB72); color: var(--rojo-vino, #821110); border-radius: 6px; font-size: 0.74rem;" onclick="asignarColor('Dorado (#FCDB72)')">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--rojo-vino); display: inline-block;"></span> Dorado
                            </button>
                            <button type="button" class="btn btn-sm py-1 px-2 border-0 fw-bold d-flex align-items-center gap-1" style="background-color: var(--dorado-champagne, #FEF1B8); color: var(--rojo-vino, #821110); border-radius: 6px; font-size: 0.74rem;" onclick="asignarColor('Champagne (#FEF1B8)')">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--dorado-oscuro); display: inline-block;"></span> Champagne
                            </button>
                            <button type="button" class="btn btn-sm py-1 px-2 border-0 fw-bold d-flex align-items-center gap-1" style="background-color: var(--dorado-oscuro, #E7B049); color: var(--rojo-vino, #821110); border-radius: 6px; font-size: 0.74rem;" onclick="asignarColor('Dorado oscuro (#E7B049)')">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #fff; display: inline-block;"></span> Dorado oscuro
                            </button>
                            <button type="button" class="btn btn-sm py-1 px-2 border-0 fw-semibold text-white d-flex align-items-center gap-1" style="background-color: var(--rojo-vino, #821110); border-radius: 6px; font-size: 0.74rem;" onclick="asignarColor('Rojo vino (#821110)')">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--dorado-principal); display: inline-block;"></span> Rojo vino
                            </button>
                        </div>
                    </div>

                    <datalist id="coloresBoutique">
                        <option value="Rosa principal (#FD7EAF)"></option>
                        <option value="Rosa intenso (#EB5387)"></option>
                        <option value="Fucsia oscuro (#C01548)"></option>
                        <option value="Dorado (#FCDB72)"></option>
                        <option value="Champagne (#FEF1B8)"></option>
                        <option value="Dorado oscuro (#E7B049)"></option>
                        <option value="Rojo vino (#821110)"></option>
                        <option value="Negro Clásico"></option>
                        <option value="Blanco Puro"></option>
                        <option value="Beige Natural"></option>
                    </datalist>

                    <div id="variantesContainer">
                        <!-- Fila 1 de variante inicial -->
                        <div class="row g-2 align-items-center mb-2 variante-fila">
                            <div class="col-3">
                                <select name="variantes[0][talla]" class="form-select form-select-sm" required>
                                    <option value="XS">XS</option>
                                    <option value="S" selected>S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="Única">Única</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="text" name="variantes[0][color]" list="coloresBoutique" class="form-control form-select-sm" placeholder="Color" value="Rosa principal (#FD7EAF)" required>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Stock</span>
                                    <input type="number" name="variantes[0][stock]" class="form-control form-control-sm" value="5" min="0">
                                </div>
                            </div>
                            <div class="col-1 text-center">
                                <button type="button" class="btn btn-sm text-danger" onclick="eliminarFila(this)" title="Eliminar variante">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Fila 2 de variante inicial -->
                        <div class="row g-2 align-items-center mb-2 variante-fila">
                            <div class="col-3">
                                <select name="variantes[1][talla]" class="form-select form-select-sm" required>
                                    <option value="XS">XS</option>
                                    <option value="S">S</option>
                                    <option value="M" selected>M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="Única">Única</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <input type="text" name="variantes[1][color]" list="coloresBoutique" class="form-control form-select-sm" placeholder="Color" value="Dorado (#FCDB72)" required>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Stock</span>
                                    <input type="number" name="variantes[1][stock]" class="form-control form-control-sm" value="8" min="0">
                                </div>
                            </div>
                            <div class="col-1 text-center">
                                <button type="button" class="btn btn-sm text-danger" onclick="eliminarFila(this)" title="Eliminar variante">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('productos.index') }}" class="btn btn-light border">Cancelar</a>
                    <button type="submit" class="btn btn-boutique-rose px-4 fw-bold">
                        <i class="bi bi-check2 me-1"></i> Guardar Prenda en Base de Datos
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Validar y previsualizar imagen en tiempo real antes de guardar
    const inputImagen = document.getElementById('inputImagen');
    const imgPreview = document.getElementById('imgPreview');
    const placeholderPreview = document.getElementById('placeholderPreview');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const fileError = document.getElementById('fileError');

    inputImagen.addEventListener('change', function(e) {
        fileError.style.display = 'none';
        fileError.textContent = '';

        if (!this.files || !this.files[0]) {
            imgPreview.style.display = 'none';
            placeholderPreview.style.display = 'block';
            fileNameDisplay.textContent = 'Ningún archivo seleccionado';
            return;
        }

        const file = this.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const allowedExtensions = ['.jpg', '.jpeg', '.png', '.webp'];
        const fileExt = file.name.substring(file.name.lastIndexOf('.')).toLowerCase();

        // Validación de tipo de archivo
        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExt)) {
            fileError.textContent = 'Formato no permitido. Solo se aceptan archivos JPG, JPEG, PNG y WEBP.';
            fileError.style.display = 'block';
            this.value = '';
            imgPreview.style.display = 'none';
            placeholderPreview.style.display = 'block';
            fileNameDisplay.textContent = 'Archivo rechazado';
            return;
        }

        // Validación de tamaño (5MB = 5 * 1024 * 1024 bytes)
        if (file.size > 5 * 1024 * 1024) {
            fileError.textContent = 'El archivo supera el tamaño máximo permitido de 5 Megabytes.';
            fileError.style.display = 'block';
            this.value = '';
            imgPreview.style.display = 'none';
            placeholderPreview.style.display = 'block';
            fileNameDisplay.textContent = 'Archivo demasiado pesado';
            return;
        }

        // Mostrar nombre y vista previa
        fileNameDisplay.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
        const reader = new FileReader();
        reader.onload = function(evt) {
            imgPreview.src = evt.target.result;
            imgPreview.style.display = 'block';
            placeholderPreview.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    // Adaptar modo de tallas según categoría seleccionada
    function actualizarModoTallas() {
        const selectCat = document.getElementById('selectCategoria');
        const alerta = document.getElementById('categoriaAlertaTalla');
        if (!selectCat) return;

        const opt = selectCat.options[selectCat.selectedIndex];
        const usaTalla = opt && opt.getAttribute('data-usa-talla') === '0' ? false : true;

        if (!usaTalla) {
            alerta.innerHTML = '<span class="badge badge-boutique py-1 px-2"><i class="bi bi-info-circle me-1"></i> Categoría sin tallas: las variantes se asignarán con <strong>Talla Única</strong> automáticamente.</span>';
        } else {
            alerta.innerHTML = '';
        }

        // Si es categoría que no usa tallas, cambiar los selects de talla a Única
        if (!usaTalla) {
            document.querySelectorAll('#variantesContainer select[name$="[talla]"]').forEach(sel => {
                sel.value = 'Única';
            });
        }
    }

    // Agregar dinámicamente filas de variantes
    let contadorVariantes = 2;
    function agregarVariante() {
        const selectCat = document.getElementById('selectCategoria');
        const opt = selectCat ? selectCat.options[selectCat.selectedIndex] : null;
        const usaTalla = opt && opt.getAttribute('data-usa-talla') === '0' ? false : true;
        const tallaDefault = usaTalla ? 'M' : 'Única';

        const container = document.getElementById('variantesContainer');
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 variante-fila';
        row.innerHTML = `
            <div class="col-3">
                <select name="variantes[${contadorVariantes}][talla]" class="form-select form-select-sm">
                    <option value="XS" ${tallaDefault === 'XS' ? 'selected' : ''}>XS</option>
                    <option value="S" ${tallaDefault === 'S' ? 'selected' : ''}>S</option>
                    <option value="M" ${tallaDefault === 'M' ? 'selected' : ''}>M</option>
                    <option value="L" ${tallaDefault === 'L' ? 'selected' : ''}>L</option>
                    <option value="XL" ${tallaDefault === 'XL' ? 'selected' : ''}>XL</option>
                    <option value="Única" ${tallaDefault === 'Única' ? 'selected' : ''}>Única</option>
                </select>
            </div>
            <div class="col-4">
                <input type="text" name="variantes[${contadorVariantes}][color]" list="coloresBoutique" class="form-control form-select-sm" placeholder="Color" value="Fucsia oscuro (#C01548)" required>
            </div>
            <div class="col-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Stock</span>
                    <input type="number" name="variantes[${contadorVariantes}][stock]" class="form-control form-control-sm" value="5" min="0">
                </div>
            </div>
            <div class="col-1 text-center">
                <button type="button" class="btn btn-sm text-danger" onclick="eliminarFila(this)" title="Eliminar">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        contadorVariantes++;
    }

    document.addEventListener('DOMContentLoaded', function() {
        actualizarModoTallas();
    });

    function eliminarFila(btn) {
        const rows = document.querySelectorAll('.variante-fila');
        if (rows.length <= 1) {
            alert('La prenda debe conservar al menos una variante de talla y color.');
            return;
        }
        btn.closest('.variante-fila').remove();
    }

    function asignarColor(colorTexto) {
        const rows = document.querySelectorAll('.variante-fila');
        if (rows.length > 0) {
            const ultimoInput = rows[rows.length - 1].querySelector('input[name$="[color]"]');
            if (ultimoInput) {
                ultimoInput.value = colorTexto;
                ultimoInput.focus();
            }
        }
    }
</script>
@endpush
