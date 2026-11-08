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
    <!-- Estilos Oficiales Bella Girls (Paleta Rosa, Fucsia y Dorado) -->
    <link rel="stylesheet" href="{{ asset('css/boutique.css') }}">
    @stack('styles')
</head>
<body>

    <!-- MENÚ LATERAL -->
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

            <!-- MÓDULO PRODUCTO -->
            <a href="{{ route('productos.index') }}" class="nav-link-custom {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                <i class="bi bi-handbag"></i>
                <span>Producto</span>
            </a>
            @if(Auth::user() && Auth::user()->esAdmin())
            <a href="{{ route('categorias.index') }}" class="nav-link-custom ps-4 {{ request()->routeIs('categorias.*') ? 'active' : '' }}" style="font-size: 0.85rem;">
                <i class="bi bi-tags"></i>
                <span>Categorías</span>
            </a>
            @endif

            <!-- MÓDULO VENTA -->
            <a href="{{ route('ventas.pos') }}" class="nav-link-custom {{ request()->routeIs('ventas.pos') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>
                <span>Venta (POS)</span>
            </a>
            <a href="{{ route('ventas.index') }}" class="nav-link-custom ps-4 {{ request()->routeIs('ventas.index') ? 'active' : '' }}" style="font-size: 0.85rem;">
                <i class="bi bi-receipt"></i>
                <span>Historial de Ventas</span>
            </a>

            <!-- MÓDULO COMPRA -->
            <a href="{{ route('compras.index') }}" class="nav-link-custom {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Compra</span>
            </a>

            <!-- MÓDULO CLIENTE -->
            <a href="{{ route('clientes.index') }}" class="nav-link-custom {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                <i class="bi bi-person-heart"></i>
                <span>Cliente</span>
            </a>

            <!-- MÓDULO PROVEEDOR -->
            <a href="{{ route('proveedores.index') }}" class="nav-link-custom {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i>
                <span>Proveedor</span>
            </a>

            <!-- MÓDULO USUARIO -->
            <a href="{{ route('usuarios.index') }}" class="nav-link-custom {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i>
                <span>Usuario</span>
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

        <!-- Pie del menú lateral: Información del Usuario Autenticado -->
        <div class="sidebar-user-footer">
            <div class="d-flex align-items-center gap-2 mb-2 sidebar-user-info">
                <div class="sidebar-user-avatar">
                    {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                </div>
                <div class="sidebar-user-meta text-truncate">
                    <div class="fw-bold sidebar-user-name text-truncate" title="{{ Auth::user()->nombre ?? 'Usuario' }} {{ Auth::user()->apellido ?? '' }}">
                        {{ Auth::user()->nombre ?? 'Valentina' }} {{ Auth::user()->apellido ?? 'Nicol' }}
                    </div>
                    <span class="badge-role-gold">
                        {{ ucfirst(Auth::user()->rol ?? 'Administrador') }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form m-0">
                @csrf
                <button type="submit" class="btn btn-sidebar-logout w-100" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Salir</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ÁREA PRINCIPAL -->
    <div class="main-wrapper">
        <!-- BARRA SUPERIOR (HEADER) -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-sidebar-toggle d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-flex align-items-center">
                    <span class="navbar-subtext fw-normal me-2" style="font-size: 0.88rem;">Tienda:</span>
                    <strong class="navbar-brand-text">Bella Girls — Sucursal Central</strong>
                </div>
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
