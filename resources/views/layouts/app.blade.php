<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bella Girls') - Sistema de Gestión</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Playfair Display & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Estilos Oficiales Bella Girls (Paleta Vino / Rosa Palo) -->
    <link rel="stylesheet" href="{{ asset('css/boutique.css') }}">
    @stack('styles')
</head>
<body>

    <!-- MENÚ LATERAL (6 Módulos Principales del Diagrama de Componentes) -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/logo-bella-girls.png') }}" alt="Logo Bella Girls" class="sidebar-logo">
                <h5 class="font-serif m-0 fw-bold sidebar-brand-title">BELLA GIRLS</h5>
            </div>
        </div>

        <div class="py-3 flex-grow-1 overflow-auto">
            <div class="px-3 pb-2 sidebar-section-title">
                Módulos Principales
            </div>

            <!-- Panel General -->
            <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span>Panel Principal</span>
            </a>

            <!-- 1. MÓDULO PRODUCTO -->
            <a href="{{ route('productos.index') }}" class="nav-link-custom {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                <i class="bi bi-handbag"></i>
                <span>1. Producto</span>
            </a>

            <!-- 2. MÓDULO VENTA -->
            <a href="{{ route('ventas.pos') }}" class="nav-link-custom {{ request()->routeIs('ventas.pos') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>
                <span>2. Venta (POS)</span>
            </a>
            <a href="{{ route('ventas.index') }}" class="nav-link-custom ps-4 {{ request()->routeIs('ventas.index') ? 'active' : '' }}" style="font-size: 0.85rem;">
                <i class="bi bi-receipt"></i>
                <span>Historial de Ventas</span>
            </a>

            <!-- 3. MÓDULO COMPRA -->
            <a href="{{ route('compras.index') }}" class="nav-link-custom {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>3. Compra</span>
            </a>

            <!-- 4. MÓDULO CLIENTE -->
            <a href="{{ route('clientes.index') }}" class="nav-link-custom {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                <i class="bi bi-person-heart"></i>
                <span>4. Cliente</span>
            </a>

            <!-- 5. MÓDULO PROVEEDOR -->
            <a href="{{ route('proveedores.index') }}" class="nav-link-custom {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i>
                <span>5. Proveedor</span>
            </a>

            <!-- 6. MÓDULO USUARIO -->
            <a href="{{ route('usuarios.index') }}" class="nav-link-custom {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i>
                <span>6. Usuario</span>
            </a>

            <div class="px-3 pt-3 pb-2 sidebar-section-title">
                Operaciones Auxiliares
            </div>

            <!-- PAGOS -->
            <a href="{{ route('pagos.index') }}" class="nav-link-custom {{ request()->routeIs('pagos.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Arqueo de Pagos</span>
            </a>
        </div>

        <!-- Pie del menú lateral -->
        <div class="p-3 sidebar-footer">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-bold" style="font-size: 0.85rem;">PostgreSQL 14+</div>
                    <small style="color: var(--color-primary); font-size: 0.75rem;"><i class="bi bi-check-circle-fill"></i> DB Conectada</small>
                </div>
                <span class="badge badge-boutique">v1.0</span>
            </div>
        </div>
    </aside>

    <!-- ÁREA PRINCIPAL -->
    <div class="main-wrapper">
        <!-- BARRA SUPERIOR -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-sidebar-toggle d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="navbar-subtext fw-normal" style="font-size: 0.88rem;">Tienda:</span>
                    <strong class="ms-1 navbar-brand-text">Bella Girls — Sucursal Central</strong>
                </div>
            </div>

            <!-- Usuario activo y sesión -->
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar-badge">
                        {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                    </div>
                    <div class="text-end d-none d-sm-block text-white">
                        <div class="fw-semibold" style="font-size: 0.88rem; line-height: 1.2;">
                            {{ Auth::user()->nombre ?? 'Valentina' }} {{ Auth::user()->apellido ?? 'Nicol' }}
                        </div>
                        <span class="{{ (Auth::user()->rol ?? 'administrador') === 'administrador' ? 'badge-admin' : 'badge-vendedor' }}">
                            {{ ucfirst(Auth::user()->rol ?? 'administrador') }}
                        </span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-logout btn-sm" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </button>
                </form>
            </div>
        </header>

        <!-- CONTENIDO DE LA PÁGINA -->
        <main class="content-area">
            <!-- Mensajes de Notificación -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i> Por favor verifique los siguientes errores:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/boutique.js') }}"></script>
    @stack('scripts')
</body>
</html>
