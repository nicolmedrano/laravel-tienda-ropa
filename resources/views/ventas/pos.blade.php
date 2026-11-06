@extends('layouts.app')

@section('title', 'Módulo Venta - Punto de Venta POS')

@section('content')
<div class="container-fluid p-0">
    <!-- Encabezado de Venta POS -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge badge-module px-2 py-1" style="font-size: 0.72rem;">Módulo 2 de 6</span>
                <h2 class="font-serif fw-bold m-0" style="color: var(--color-text-dark);">Punto de Venta (POS)</h2>
            </div>
            <p class="text-muted m-0 small">Seleccione las prendas y variantes disponibles para emitir la factura y procesar el cobro.</p>
        </div>
        <div>
            <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-outline-boutique">
                <i class="bi bi-clock-history me-1"></i> Historial de Ventas
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- COLUMNA IZQUIERDA: CATÁLOGO RÁPIDO DE PRENDAS -->
        <div class="col-12 col-lg-7 col-xl-8">
            <div class="card-boutique p-3 mb-3">
                <div class="input-group">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="filtroPrendas" class="form-control border-start-0" placeholder="Buscar por nombre o código de prenda...">
                </div>
            </div>

            <!-- Grilla de prendas para seleccionar -->
            <div class="row g-3" id="grillaPrendas" style="max-height: 720px; overflow-y: auto;">
                @foreach($productos as $p)
                    <div class="col-12 col-sm-6 col-xl-4 prenda-item" data-nombre="{{ strtolower($p->nombre) }}" data-codigo="{{ strtolower($p->codigo) }}">
                        <div class="card-boutique h-100 p-2 d-flex flex-column">
                            <div style="height: 140px; background: var(--color-bg-subtle); border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 8px; border: 1px solid var(--color-border);">
                                @if($p->imagen_url)
                                    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                @else
                                    <i class="bi bi-handbag fs-1 text-muted"></i>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">{{ $p->codigo }}</span>
                                <span class="fw-bold" style="font-size: 0.95rem; color: var(--color-primary);">Bs. {{ number_format($p->precio_venta, 2) }}</span>
                            </div>

                            <h6 class="fw-semibold text-truncate mb-2" title="{{ $p->nombre }}" style="font-size: 0.88rem; color: var(--color-text-dark);">{{ $p->nombre }}</h6>

                            <!-- Selector de Variante (Talla y Color) -->
                            <div class="mt-auto">
                                <label class="form-label text-muted" style="font-size: 0.72rem; margin-bottom: 2px;">Talla y Color disponible:</label>
                                <select class="form-select form-select-sm mb-2 selector-variante" id="select_variante_{{ $p->id_producto }}">
                                    @php $tieneStock = false; @endphp
                                    @foreach($p->variantes as $v)
                                        @if($v->stock_actual > 0)
                                            @php $tieneStock = true; @endphp
                                            <option value="{{ $v->id_variante }}" 
                                                    data-sku="{{ $v->codigo_sku }}" 
                                                    data-talla="{{ $v->talla }}" 
                                                    data-color="{{ $v->color }}" 
                                                    data-stock="{{ $v->stock_actual }}"
                                                    data-precio="{{ $p->precio_venta }}"
                                                    data-nombre="{{ $p->nombre }}">
                                                {{ $v->talla }} / {{ $v->color }} (Stock: {{ $v->stock_actual }})
                                            </option>
                                        @else
                                            <option disabled>{{ $v->talla }} / {{ $v->color }} (Agotado)</option>
                                        @endif
                                    @endforeach
                                </select>

                                @if($tieneStock)
                                    <button type="button" class="btn btn-boutique-rose btn-sm w-100 fw-semibold" onclick="agregarAlCarrito({{ $p->id_producto }})">
                                        <i class="bi bi-cart-plus me-1"></i> Agregar a la venta
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary btn-sm w-100 disabled">Sin existencias</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- COLUMNA DERECHA: CARRITO, CLIENTE Y FINALIZAR VENTA -->
        <div class="col-12 col-lg-5 col-xl-4">
            <div class="card-boutique p-3 d-flex flex-column" style="min-height: 650px;">
                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3" style="border-color: var(--color-border) !important;">
                    <h5 class="font-serif fw-bold m-0" style="color: var(--color-primary);"><i class="bi bi-bag-check me-1"></i> Comprobante de Venta</h5>
                    <button type="button" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" onclick="limpiarCarrito()">
                        <i class="bi bi-trash"></i> Vaciar
                    </button>
                </div>

                <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
                    @csrf

                    <!-- 1. Módulo Cliente: Selección de Cliente -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-1">Cliente Asociado</label>
                        <select name="id_cliente" class="form-select form-select-sm">
                            <option value="">Cliente Ocasional / Mostrador</option>
                            @foreach($clientes as $cli)
                                <option value="{{ $cli->id_cliente }}">{{ $cli->nombre_completo }} ({{ $cli->telefono ?? 'Sin tel.' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Ítems en Carrito -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-1">Prendas Seleccionadas</label>
                        <div id="listaCarrito" style="max-height: 250px; overflow-y: auto; background-color: var(--color-bg-subtle);" class="border rounded p-2">
                            <div id="carritoVacio" class="text-center py-4 text-muted small">
                                <i class="bi bi-cart3 display-6 d-block mb-2 text-muted"></i>
                                El carrito está vacío. Agregue prendas desde el catálogo.
                            </div>
                        </div>
                    </div>

                    <!-- 3. Totales y Descuento -->
                    <div class="border-top pt-2 mb-3" style="border-color: var(--color-border) !important;">
                        <div class="d-flex justify-content-between mb-1 small text-muted">
                            <span>Subtotal:</span>
                            <span class="fw-semibold text-dark" id="displaySubtotal">Bs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                            <span>Descuento (Bs.):</span>
                            <input type="number" step="0.50" min="0" name="descuento" id="inputDescuento" value="0.00" class="form-control form-control-sm text-end" style="width: 100px;" oninput="calcularTotales()">
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top fs-5 fw-bold" style="color: var(--color-primary); border-color: var(--color-border) !important;">
                            <span>Total a Cobrar:</span>
                            <span id="displayTotal">Bs. 0.00</span>
                        </div>
                    </div>

                    <!-- 4. Registro de Pago (Forma de cobro) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small mb-1">Método de Pago <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_efectivo" value="efectivo" checked>
                                <label class="btn btn-outline-secondary btn-sm w-100" for="pago_efectivo">
                                    <i class="bi bi-cash me-1"></i> Efectivo
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_qr" value="qr">
                                <label class="btn btn-outline-secondary btn-sm w-100" for="pago_qr">
                                    <i class="bi bi-qr-code me-1"></i> QR Simple
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_tarjeta" value="tarjeta">
                                <label class="btn btn-outline-secondary btn-sm w-100" for="pago_tarjeta">
                                    <i class="bi bi-credit-card me-1"></i> Tarjeta
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="metodo_pago" id="pago_transferencia" value="transferencia">
                                <label class="btn btn-outline-secondary btn-sm w-100" for="pago_transferencia">
                                    <i class="bi bi-bank me-1"></i> Transferencia
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="referencia_pago" class="form-control form-control-sm" placeholder="Referencia / N° Autorización / Comprobante (opcional)">
                    </div>

                    <!-- Botón procesar venta -->
                    <div class="mt-auto">
                        <button type="submit" id="btnProcesarVenta" class="btn btn-boutique-rose w-100 py-3 fw-bold fs-6" disabled>
                            <i class="bi bi-check2-circle me-1"></i> Procesar Venta & Emitir Factura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let carrito = [];

    // Filtro en vivo del catálogo
    document.getElementById('filtroPrendas').addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.prenda-item').forEach(item => {
            const nombre = item.getAttribute('data-nombre');
            const codigo = item.getAttribute('data-codigo');
            if (nombre.includes(query) || codigo.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    function agregarAlCarrito(idProducto) {
        const select = document.getElementById(`select_variante_${idProducto}`);
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption || selectedOption.disabled) return;

        const idVariante = parseInt(selectedOption.value);
        const sku = selectedOption.getAttribute('data-sku');
        const talla = selectedOption.getAttribute('data-talla');
        const color = selectedOption.getAttribute('data-color');
        const stock = parseInt(selectedOption.getAttribute('data-stock'));
        const precio = parseFloat(selectedOption.getAttribute('data-precio'));
        const nombre = selectedOption.getAttribute('data-nombre');

        const existente = carrito.find(item => item.id_variante === idVariante);

        if (existente) {
            if (existente.cantidad + 1 > stock) {
                alert(`Stock máximo disponible para esta talla/color: ${stock} unidades.`);
                return;
            }
            existente.cantidad += 1;
        } else {
            carrito.push({
                id_variante: idVariante,
                sku: sku,
                nombre: nombre,
                talla: talla,
                color: color,
                stock: stock,
                precio_unitario: precio,
                cantidad: 1,
            });
        }

        renderCarrito();
    }

    function cambiarCantidad(idVariante, delta) {
        const item = carrito.find(i => i.id_variante === idVariante);
        if (!item) return;

        const nuevaCantidad = item.cantidad + delta;
        if (nuevaCantidad <= 0) {
            carrito = carrito.filter(i => i.id_variante !== idVariante);
        } else if (nuevaCantidad > item.stock) {
            alert(`Stock máximo disponible para esta prenda: ${item.stock} unidades.`);
            return;
        } else {
            item.cantidad = nuevaCantidad;
        }

        renderCarrito();
    }

    function eliminarItem(idVariante) {
        carrito = carrito.filter(i => i.id_variante !== idVariante);
        renderCarrito();
    }

    function limpiarCarrito() {
        if (carrito.length > 0 && confirm('¿Desea vaciar el carrito actual?')) {
            carrito = [];
            renderCarrito();
        }
    }

    function renderCarrito() {
        const container = document.getElementById('listaCarrito');
        const btnProcesar = document.getElementById('btnProcesarVenta');

        if (carrito.length === 0) {
            container.innerHTML = `
                <div id="carritoVacio" class="text-center py-4 text-muted small">
                    <i class="bi bi-cart3 display-6 d-block mb-2 text-muted"></i>
                    El carrito está vacío. Agregue prendas desde el catálogo.
                </div>
            `;
            btnProcesar.disabled = true;
            calcularTotales();
            return;
        }

        btnProcesar.disabled = false;
        let html = '';

        carrito.forEach((item, index) => {
            const subtotal = item.cantidad * item.precio_unitario;
            html += `
                <div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-white border rounded" style="border-color: var(--color-border) !important;">
                    <div style="flex-grow: 1; padding-right: 8px;">
                        <div class="fw-semibold small" style="color: var(--color-text-dark);">${item.nombre}</div>
                        <small class="text-muted d-block" style="font-size: 0.72rem;">
                            ${item.talla} / ${item.color} &bull; Bs. ${item.precio_unitario.toFixed(2)} c/u
                        </small>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm btn-light border py-0 px-2" onclick="cambiarCantidad(${item.id_variante}, -1)">-</button>
                        <span class="fw-bold px-1" style="min-width: 22px; text-align: center; font-size: 0.85rem; color: var(--color-text-dark);">${item.cantidad}</span>
                        <button type="button" class="btn btn-sm btn-light border py-0 px-2" onclick="cambiarCantidad(${item.id_variante}, 1)">+</button>
                    </div>

                    <div class="text-end ps-2" style="min-width: 80px;">
                        <span class="fw-bold small" style="color: var(--color-primary);">Bs. ${subtotal.toFixed(2)}</span>
                        <button type="button" class="btn btn-link text-danger p-0 ms-1" onclick="eliminarItem(${item.id_variante})" title="Quitar">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>

                    <!-- Campos ocultos para el request de Laravel -->
                    <input type="hidden" name="items[${index}][id_variante]" value="${item.id_variante}">
                    <input type="hidden" name="items[${index}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="items[${index}][precio_unitario]" value="${item.precio_unitario}">
                </div>
            `;
        });

        container.innerHTML = html;
        calcularTotales();
    }

    function calcularTotales() {
        const subtotal = carrito.reduce((sum, item) => sum + (item.cantidad * item.precio_unitario), 0);
        const inputDesc = document.getElementById('inputDescuento');
        let descuento = parseFloat(inputDesc.value) || 0;
        if (descuento < 0) descuento = 0;
        if (descuento > subtotal) descuento = subtotal;

        const total = Math.max(0, subtotal - descuento);

        document.getElementById('displaySubtotal').textContent = `Bs. ${subtotal.toFixed(2)}`;
        document.getElementById('displayTotal').textContent = `Bs. ${total.toFixed(2)}`;
    }
</script>
@endpush
