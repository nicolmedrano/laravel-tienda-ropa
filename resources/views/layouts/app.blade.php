<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Boutique Elegance') - Sistema de Gestión</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Playfair Display & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --boutique-bg: #FDFBF9;
            --boutique-card: #FFFFFF;
            --boutique-text: #2A2421;
            --boutique-text-muted: #79716B;
            --boutique-border: #EFE9E4;
            --boutique-rose: #E8B4B8;
            --boutique-rose-hover: #DCA0A5;
            --boutique-soft-rose: #FDF2F4;
            --boutique-dark: #1E1B18;
            --boutique-beige: #F4EBE2;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--boutique-bg);
            color: var(--boutique-text);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #FFFFFF;
            border-right: 1px solid var(--boutique-border);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid var(--boutique-border);
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: var(--boutique-text-muted);
            font-weight: 500;
            font-size: 0.92rem;
            text-decoration: none;
            border-radius: 8px;
            margin: 2px 12px;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            color: var(--boutique-dark);
            background-color: var(--boutique-soft-rose);
        }

        .nav-link-custom.active {
            color: var(--boutique-dark);
            background-color: #F8E7E9;
            font-weight: 600;
        }

        .nav-link-custom i {
            font-size: 1.15rem;
        }

        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            height: 68px;
            background: #FFFFFF;
            border-bottom: 1px solid var(--boutique-border);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-area {
            padding: 32px;
            flex-grow: 1;
        }

        .card-boutique {
            background: #FFFFFF;
            border: 1px solid var(--boutique-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .btn-boutique {
            background-color: var(--boutique-dark);
            color: #FFFFFF;
            border-radius: 8px;
            padding: 9px 18px;
            font-weight: 500;
            font-size: 0.9rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-boutique:hover {
            background-color: #38332F;
            color: #FFFFFF;
        }

        .btn-boutique-rose {
            background-color: var(--boutique-rose);
            color: #1E1B18;
            border-radius: 8px;
            padding: 9px 18px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-boutique-rose:hover {
            background-color: var(--boutique-rose-hover);
            color: #1E1B18;
        }

        .badge-admin {
            background-color: #F4EBE2;
            color: #7A5333;
            border: 1px solid #E8D3C0;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .badge-vendedor {
            background-color: #EBF3ED;
            color: #2F6A3D;
            border: 1px solid #D2E4D6;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- MENÚ LATERAL (6 Módulos Principales del Diagrama de Componentes) -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 38px; height: 38px; background: #FAF0EE; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9E5664;">
                    <i class="bi bi-gem"></i>
                </div>
                <div>
                    <h5 class="font-serif m-0 fw-bold" style="letter-spacing: -0.5px;">Boutique Elegance</h5>
                    <small class="text-muted" style="font-size: 0.75rem;">Sistema de Gestión (Laravel & PostgreSQL)</small>
                </div>
            </div>
        </div>

        <div class="py-3 flex-grow-1 overflow-auto">
            <div class="px-3 pb-2 text-uppercase text-muted fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">
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

            <div class="px-3 pt-3 pb-2 text-uppercase text-muted fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                Operaciones Auxiliares
            </div>

            <!-- PAGOS -->
            <a href="{{ route('pagos.index') }}" class="nav-link-custom {{ request()->routeIs('pagos.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Arqueo de Pagos</span>
            </a>
        </div>

        <!-- Pie del menú lateral -->
        <div class="p-3 border-top" style="border-color: var(--boutique-border) !important; background-color: #FAF8F6;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-semibold" style="font-size: 0.85rem;">PostgreSQL 14+</div>
                    <small class="text-success" style="font-size: 0.75rem;"><i class="bi bi-check-circle-fill"></i> DB Conectada</small>
                </div>
                <span class="badge bg-white text-dark border">v1.0</span>
            </div>
        </div>
    </aside>

    <!-- ÁREA PRINCIPAL -->
    <div class="main-wrapper">
        <!-- BARRA SUPERIOR -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <span class="text-muted fw-normal" style="font-size: 0.88rem;">Tienda:</span>
                    <strong class="ms-1">Boutique Elegance — Sucursal Central</strong>
                </div>
            </div>

            <!-- Usuario activo y sesión -->
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #FAF0EE; color: #9E5664; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                        {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                    </div>
                    <div class="text-end d-none d-sm-block">
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
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </button>
                </form>
            </div>
        </header>

        <!-- CONTENIDO DE LA PÁGINA -->
        <main class="content-area">
            <!-- Mensajes de Notificación -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #EBF7EE; color: #1E6B34;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #FDF2F2; color: #991B1B;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #FDF2F2; color: #991B1B;">
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
    @stack('scripts')
</body>
</html>
