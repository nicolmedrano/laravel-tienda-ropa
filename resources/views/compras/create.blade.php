@extends('layouts.app')

@section('title', 'Módulo Compra - Registrar Compra a Proveedor')

@section('content')
<div class="container-fluid p-0" style="max-width: 1000px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('compras.index') }}" class="btn btn-sm btn-light border py-1 px-2 text-muted">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Registrar Compra a Proveedor</h2>
            </div>
            <p class="text-muted m-0">El stock de las prendas y variantes seleccionadas se incrementará de manera inmediata y consistente.</p>
        </div>
    </div>

    <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
        @csrf

        <div class="row g-4 mb-4">
            <!-- Encabezado de la Compra -->
            <div class="col-12 col-md-6">
                <div class="card-boutique p-4 h-100">
                    <h6 class="fw-bold mb-3" style="color: var(--color-text-dark);">Datos del Proveedor & Comprobante</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Proveedor <span class="text-danger">*</span></label>
                        <select name="id_proveedor" class="form-select @error('id_proveedor') is-invalid @enderror" required>
                            <option value="">Seleccione un proveedor registrado...</option>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->id_proveedor }}" {{ old('id_proveedor') == $prov->id_proveedor ? 'selected' : '' }}>
                                    {{ $prov->nombre_empresa }} (Contacto: {{ $prov->contacto }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_proveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">N° Factura / Comprobante de Compra <span class="text-danger">*</span></label>
                        <input type="text" name="numero_comprobante" class="form-control @error('numero_comprobante') is-invalid @enderror" placeholder="ej. COM-2026-0005 o FAC-9821" value="{{ old('numero_comprobante') }}" required style="text-transform: uppercase;">
                        @error('numero_comprobante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card-boutique p-4 h-100">
                    <h6 class="fw-bold mb-3" style="color: var(--color-text-dark);">Observaciones de Entrega</h6>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Notas u Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="4" placeholder="Detalles de lote, número de guía de transporte, términos de crédito o pago...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETALLE DE PRENDAS A INGRESAR -->
        <div class="card-boutique p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold m-0" style="color: var(--color-text-dark);">Prendas y Variantes a Ingresar</h6>
                    <small class="text-muted">Indique la prenda, variante de talla/color, cantidad y costo unitario.</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-boutique" onclick="agregarLineaCompra()">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Línea
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="tablaLineasCompra">
                    <thead>
                        <tr style="font-size: 0.8rem;">
                            <th style="min-width: 280px;">Prenda & Variante (Talla/Color)</th>
                            <th style="width: 140px;" class="text-center">Cantidad</th>
                            <th style="width: 160px;" class="text-end">Costo Unit. (Bs.)</th>
                            <th style="width: 150px;" class="text-end">Subtotal</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCompra">
                        <!-- Línea inicial -->
                        <tr class="linea-compra">
                            <td>
                                <select name="items[0][id_variante]" class="form-select form-select-sm select-variante-compra" required onchange="actualizarCosto(this)">
                                    <option value="">Seleccione prenda...</option>
                                    @foreach($productos as $p)
                                        <optgroup label="{{ $p->codigo }} - {{ $p->nombre }}">
                                            @foreach($p->variantes as $v)
                                                <option value="{{ $v->id_variante }}" data-costo="{{ $p->costo_compra }}">
                                                    {{ $p->nombre }} (Talla: {{ $v->talla }}, Color: {{ $v->color }}) — Stock actual: {{ $v->stock_actual }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][cantidad]" class="form-control form-control-sm text-center input-cantidad" value="10" min="1" required oninput="calcularTotalCompra()">
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="number" step="0.50" name="items[0][costo_unitario]" class="form-control text-end input-costo" value="25.00" min="0" required oninput="calcularTotalCompra()">
                                </div>
                            </td>
                            <td class="text-end fw-bold celda-subtotal" style="color: var(--color-primary);">
                                Bs. 250.00
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger p-0" onclick="eliminarLinea(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total General de Compra -->
            <div class="d-flex justify-content-end align-items-center gap-3 pt-3 border-top" style="border-color: var(--color-border) !important;">
                <span class="fs-5 text-muted">Total Compra:</span>
                <span class="fs-4 fw-bold" id="totalGeneralCompra" style="color: var(--color-primary);">Bs. 250.00</span>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('compras.index') }}" class="btn btn-light border">Cancelar</a>
            <button type="submit" class="btn btn-boutique-rose px-4 fw-bold">
                <i class="bi bi-check2 me-1"></i> Guardar Compra & Actualizar Stock
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let indiceLinea = 1;

    function agregarLineaCompra() {
        const tbody = document.getElementById('tbodyCompra');
        const filaBase = document.querySelector('.linea-compra');
        const nuevaFila = filaBase.cloneNode(true);

        const select = nuevaFila.querySelector('select');
        select.name = `items[${indiceLinea}][id_variante]`;
        select.selectedIndex = 0;

        const inputCant = nuevaFila.querySelector('.input-cantidad');
        inputCant.name = `items[${indiceLinea}][cantidad]`;
        inputCant.value = 5;

        const inputCosto = nuevaFila.querySelector('.input-costo');
        inputCosto.name = `items[${indiceLinea}][costo_unitario]`;
        inputCosto.value = '0.00';

        const subtotal = nuevaFila.querySelector('.celda-subtotal');
        subtotal.textContent = 'Bs. 0.00';

        tbody.appendChild(nuevaFila);
        indiceLinea++;
        calcularTotalCompra();
    }

    function eliminarLinea(btn) {
        const filas = document.querySelectorAll('.linea-compra');
        if (filas.length <= 1) {
            alert('Debe ingresar al menos una prenda en la orden de compra.');
            return;
        }
        btn.closest('.linea-compra').remove();
        calcularTotalCompra();
    }

    function actualizarCosto(select) {
        const opt = select.options[select.selectedIndex];
        const costo = opt ? opt.getAttribute('data-costo') : '0.00';
        const fila = select.closest('.linea-compra');
        const inputCosto = fila.querySelector('.input-costo');
        if (costo && parseFloat(costo) > 0) {
            inputCosto.value = parseFloat(costo).toFixed(2);
        }
        calcularTotalCompra();
    }

    function calcularTotalCompra() {
        let total = 0;
        document.querySelectorAll('.linea-compra').forEach(fila => {
            const cant = parseFloat(fila.querySelector('.input-cantidad').value) || 0;
            const costo = parseFloat(fila.querySelector('.input-costo').value) || 0;
            const sub = cant * costo;
            fila.querySelector('.celda-subtotal').textContent = `Bs. ${sub.toFixed(2)}`;
            total += sub;
        });
        document.getElementById('totalGeneralCompra').textContent = `Bs. ${total.toFixed(2)}`;
    }
</script>
@endpush
