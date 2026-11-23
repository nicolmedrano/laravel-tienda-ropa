<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bella Girls') - Boutique & Gestión</title>
    <!-- Script Anti-Parpadeo de Tema y Sidebar -->
    <script>
        (function() {
            // Tema Claro / Nocturno
            try {
                var storedTheme = localStorage.getItem('bg-theme');
                var theme = storedTheme;
                if (!theme || (theme !== 'light' && theme !== 'dark')) {
                    theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }

            // Estado Colapsado del Sidebar en Escritorio
            try {
                var storedSidebar = localStorage.getItem('bg-sidebar');
                if (storedSidebar === 'collapsed' && window.innerWidth >= 992) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Playfair Display & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Estilos Oficiales Bella Girls (Paleta Durazno, Crema, Rosa y Marfil) -->
    <link rel="stylesheet" href="{{ asset('css/boutique.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bella-theme.css') }}">
    
    <style>
        /* Ajustes de Header Global y Buscador */
        .top-navbar-search {
            position: relative;
            max-width: 380px;
            width: 100%;
        }

        .top-navbar-search .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted, #7D5863);
            pointer-events: none;
            font-size: 0.95rem;
        }

        .top-navbar-search .form-control {
            padding-left: 36px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid var(--peach-soft, #F3B5A0);
            font-size: 0.88rem;
            color: var(--wine-dark, #4A1F2A);
            height: 38px;
            transition: all 0.2s ease;
        }

        .top-navbar-search .form-control:focus {
            background: #FFFFFF;
            border-color: var(--coral-contrast, #D84C38);
            box-shadow: 0 0 0 3px rgba(254, 163, 142, 0.25);
        }

        /* Notificaciones Bell */
        .btn-notification-bell {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid var(--peach-soft, #F3B5A0);
            color: var(--wine-dark, #4A1F2A);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-notification-bell:hover {
            background: #FFFFFF;
            color: var(--coral-contrast, #D84C38);
            transform: translateY(-1px);
        }

        .notification-indicator-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 10px;
            height: 10px;
            background: var(--coral-contrast, #D84C38);
            border: 2px solid #FFFFFF;
            border-radius: 50%;
        }

        /* Saludo en Top Navbar */
        .navbar-greeting {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--wine-dark, #4A1F2A);
            line-height: 1.2;
        }

        .navbar-date-subtext {
            font-size: 0.78rem;
            color: var(--muted, #7D5863);
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- MENÚ LATERAL (SIDEBAR PALETA DURAZNO, CREMA Y ROSA) -->
    <aside class="sidebar" id="sidebar">
        <!-- Marca de la Tienda -->
        <div class="sidebar-brand">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-2 sidebar-brand-content text-truncate">
                    <img src="{{ asset('images/logo-bella-girls.png') }}" alt="Logo Bella Girls" class="sidebar-logo">
                    <h5 class="font-serif m-0 fw-bold sidebar-brand-title text-truncate" style="letter-spacing: 0.5px;">BELLA GIRLS</h5>
                </div>
                <button type="button" class="sidebar-toggle-btn sidebar-toggle-inside" id="sidebarToggleInside" aria-label="Mostrar u ocultar menú" aria-expanded="true" aria-controls="sidebar" title="Ocultar menú lateral">
                    <span class="hamburger-box">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                </button>
            </div>
        </div>

        <div class="py-2 flex-grow-1 overflow-auto">
            <div class="px-3 pb-2 sidebar-section-title">
                Menú Principal
            </div>

            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>

            <!-- 2. Clientes -->
            <a href="{{ route('clientes.index') }}" class="nav-link-custom {{ request()->routeIs('clientes.*') ? 'active' : '' }}" title="Clientes">
                <i class="bi bi-person-heart"></i>
                <span>Clientes</span>
            </a>

            <!-- 3. Pedidos & Ventas -->
            <a href="{{ route('ventas.pos') }}" class="nav-link-custom {{ request()->routeIs('ventas.pos') ? 'active' : '' }}" title="Venta (POS)">
                <i class="bi bi-cart3"></i>
                <span>Venta (POS)</span>
            </a>
            <a href="{{ route('ventas.index') }}" class="nav-link-custom nav-sublink ps-4 {{ request()->routeIs('ventas.index') ? 'active' : '' }}" title="Historial de Pedidos">
                <i class="bi bi-receipt"></i>
                <span>Historial de Pedidos</span>
            </a>

            <!-- 4. Productos & Catálogo -->
            <a href="{{ route('productos.index') }}" class="nav-link-custom {{ request()->routeIs('productos.*') ? 'active' : '' }}" title="Productos">
                <i class="bi bi-handbag"></i>
                <span>Productos</span>
            </a>
            @if(Auth::user() && Auth::user()->esAdmin())
            <a href="{{ route('categorias.index') }}" class="nav-link-custom nav-sublink ps-4 {{ request()->routeIs('categorias.*') ? 'active' : '' }}" title="Categorías">
                <i class="bi bi-tags"></i>
                <span>Categorías</span>
            </a>
            @endif

            <div class="px-3 pt-3 pb-2 sidebar-section-title">
                Inventario & Reportes
            </div>

            @if(Auth::user() && Auth::user()->esAdmin())
            <!-- 5. Inventario & Compras -->
            <a href="{{ route('compras.index') }}" class="nav-link-custom {{ request()->routeIs('compras.*') ? 'active' : '' }}" title="Inventario / Compras">
                <i class="bi bi-box-seam"></i>
                <span>Inventario & Compras</span>
            </a>

            <!-- 6. Proveedores -->
            <a href="{{ route('proveedores.index') }}" class="nav-link-custom {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" title="Proveedores">
                <i class="bi bi-truck"></i>
                <span>Proveedores</span>
            </a>

            <!-- 7. Arqueo & Reportes -->
            <a href="{{ route('pagos.index') }}" class="nav-link-custom {{ request()->routeIs('pagos.*') ? 'active' : '' }}" title="Arqueo de Pagos">
                <i class="bi bi-cash-coin"></i>
                <span>Arqueo de Pagos</span>
            </a>

            <!-- 8. Configuración & Usuarios -->
            <a href="{{ route('usuarios.index') }}" class="nav-link-custom {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" title="Usuarios del Sistema">
                <i class="bi bi-shield-lock"></i>
                <span>Usuarios & Roles</span>
            </a>
            @endif
        </div>

        <!-- Pie del Menú Lateral: Perfil del Usuario & Cerrar Sesión -->
        <div class="sidebar-user-footer">
            <div class="d-flex align-items-center gap-2 mb-2 sidebar-user-info">
                <div class="sidebar-user-avatar" style="background: var(--grad-coral); color: #FFFFFF;" title="{{ Auth::user()->nombre ?? 'Valentina' }} {{ Auth::user()->apellido ?? '' }}">
                    {{ mb_substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                </div>
                <div class="sidebar-user-meta text-truncate">
                    <div class="fw-bold sidebar-user-name text-truncate">
                        {{ Auth::user()->nombre ?? 'Valentina' }} {{ Auth::user()->apellido ?? '' }}
                    </div>
                    <span class="badge" style="background: rgba(254, 163, 142, 0.25); color: var(--coral-contrast, #D84C38); font-size: 0.70rem;">
                        {{ ucfirst(Auth::user()->rol ?? 'Administrador') }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form m-0">
                @csrf
                <button type="submit" class="btn btn-sidebar-logout w-100" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Fondo tenue para cerrar menú en móviles -->
    <div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop"></div>

    <!-- ÁREA PRINCIPAL -->
    <div class="main-wrapper">
        <!-- BARRA SUPERIOR (HEADER) CON BUSCADOR Y SALUDO -->
        <header class="top-navbar px-3 px-md-4 d-flex align-items-center justify-content-between" style="background: var(--card, #FFFFFF); border-bottom: 1.5px solid var(--peach-soft, #F3B5A0);">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="sidebar-toggle-btn top-navbar-toggle-btn" id="sidebarToggleNavbar" aria-label="Mostrar u ocultar menú" aria-expanded="false" aria-controls="sidebar" title="Mostrar menú lateral">
                    <span class="hamburger-box">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                </button>
                <div>
                    <div class="navbar-greeting">
                        ¡Bienvenido/a, {{ Auth::user()->nombre ?? 'Valentina' }}! 🌸
                    </div>
                    <div class="navbar-date-subtext d-none d-sm-block">
                        {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                    </div>
                </div>
            </div>

            <!-- Buscador Global y Acciones Rápidas -->
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <!-- Buscador global -->
                <form action="{{ route('productos.index') }}" method="GET" class="d-none d-md-block top-navbar-search">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar prendas, clientes o pedidos...">
                </form>

                <!-- Notificaciones (Alerta de stock bajo) -->
                <a href="{{ route('dashboard') }}#seccionAlertasStock" class="btn-notification-bell" title="Alertas de Inventario">
                    <i class="bi bi-bell"></i>
                    <span class="notification-indicator-badge"></span>
                </a>

                <!-- Selector de Tema (Claro / Nocturno) -->
                <button type="button" class="theme-toggle-btn theme-toggle-panel" aria-label="Cambiar tema" title="Cambiar tema">
                    <i class="bi bi-sun-fill theme-icon-sun"></i>
                    <i class="bi bi-moon-stars-fill theme-icon-moon"></i>
                </button>
            </div>
        </header>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="content-area p-3 p-md-4" style="background-color: var(--ivory, #F6E8D0);">
            <!-- Mensajes de Notificación -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background: var(--okbg, #E2F5E9); color: var(--ok, #2E7D52); border-left: 4px solid var(--ok, #2E7D52) !important;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background: var(--badbg, #FDEDEC); color: var(--bad, #C0392B); border-left: 4px solid var(--bad, #C0392B) !important;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background: var(--badbg, #FDEDEC); color: var(--bad, #C0392B); border-left: 4px solid var(--bad, #C0392B) !important;">
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
