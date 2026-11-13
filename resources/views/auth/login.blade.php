<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Bella Girls</title>
    <!-- Script Anti-Parpadeo de Tema (Se ejecuta inmediatamente antes del CSS) -->
    <script>
        (function() {
            try {
                var stored = localStorage.getItem('bg-theme');
                var theme = stored;
                if (!theme || (theme !== 'light' && theme !== 'dark')) {
                    theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bella-theme.css') }}">
    
    <style>
        /* =====================================================================
           PALETA OFICIAL BELLA GIRLS
           Rosa Principal, Rosa Intenso, Fucsia Oscuro, Dorado, Champagne,
           Dorado Oscuro, Rojo Vino, Blanco.
           ===================================================================== */
        :root {
            --rosa-principal: #FD7EAF;
            --rosa-intenso: #EB5387;
            --fucsia-oscuro: #C01548;
            --dorado-principal: #FCDB72;
            --dorado-champagne: #FEF1B8;
            --dorado-oscuro: #E7B049;
            --rojo-vino: #821110;
            --blanco: #FFFFFF;

            --grad-accion: linear-gradient(135deg, var(--fucsia-oscuro) 0%, var(--rosa-intenso) 100%);
            --grad-accion-hover: linear-gradient(135deg, var(--rojo-vino) 0%, var(--fucsia-oscuro) 100%);
            --grad-header: linear-gradient(90deg, var(--rojo-vino) 0%, var(--fucsia-oscuro) 100%);
            --grad-gold: linear-gradient(135deg, var(--dorado-champagne) 0%, var(--dorado-principal) 60%, var(--dorado-oscuro) 100%);

            --c-shadow-fuchsia: rgba(192, 21, 72, 0.16);
            --c-shadow-fuchsia-sm: rgba(192, 21, 72, 0.08);
            --c-shadow-fuchsia-lg: rgba(192, 21, 72, 0.14);
            --c-border: #FBDCE8;
        }

        html {
            font-size: 14px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #FFF2F6 0%, var(--dorado-champagne) 45%, var(--blanco) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            color: var(--rojo-vino);
        }

        .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* Tarjeta del Login */
        .card-login {
            background: var(--blanco);
            border: 1px solid var(--c-border);
            border-radius: 18px;
            box-shadow: 0 12px 32px var(--c-shadow-fuchsia-lg), 0 2px 8px var(--c-shadow-fuchsia-sm);
            width: 100%;
            max-width: 395px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .card-login:hover {
            box-shadow: 0 16px 40px var(--c-shadow-fuchsia), 0 3px 10px var(--c-shadow-fuchsia-sm);
        }

        /* Encabezado */
        .card-login-header {
            background: linear-gradient(180deg, var(--blanco) 0%, #FFF5F8 100%);
            border-bottom: 1px solid var(--c-border) !important;
            padding: 18px 18px 14px 18px;
        }

        /* Logo Bella Girls */
        .login-logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 10px;
        }

        .login-logo {
            width: 95px;
            height: 95px;
            object-fit: contain;
            border-radius: 50%;
            box-shadow: 0 6px 18px rgba(192, 21, 72, 0.20), 0 0 0 2.5px var(--dorado-oscuro);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: var(--blanco);
        }

        .login-logo:hover {
            transform: scale(1.04) rotate(1deg);
            box-shadow: 0 8px 22px rgba(192, 21, 72, 0.28), 0 0 0 3.5px var(--dorado-principal);
        }

        .brand-title {
            color: var(--rojo-vino);
            letter-spacing: -0.5px;
            font-size: 1.55rem;
            font-weight: 700;
        }

        .brand-subtitle {
            color: var(--fucsia-oscuro);
            font-size: 0.80rem;
            font-weight: 500;
        }

        /* Formulario y Etiquetas */
        .form-label {
            color: var(--rojo-vino);
            font-weight: 600;
            font-size: 0.82rem;
        }

        .input-group-text {
            background-color: #FFF5F8 !important;
            border-color: var(--c-border) !important;
            color: var(--fucsia-oscuro) !important;
            font-size: 0.95rem;
            padding: 7px 11px;
        }

        .form-control {
            border-color: var(--c-border) !important;
            color: var(--rojo-vino);
            background-color: var(--blanco);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 0.88rem;
            min-height: 38px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--rosa-intenso) !important;
            box-shadow: 0 0 0 0.25rem rgba(235, 83, 135, 0.22) !important;
            outline: none;
            color: var(--rojo-vino);
        }

        /* Checkbox Recordar Sesión */
        .form-check-input {
            border-color: var(--rosa-principal);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--fucsia-oscuro) !important;
            border-color: var(--fucsia-oscuro) !important;
        }

        .form-check-input:focus {
            border-color: var(--rosa-intenso) !important;
            box-shadow: 0 0 0 0.25rem rgba(235, 83, 135, 0.22) !important;
        }

        .form-check-label {
            color: var(--rojo-vino) !important;
            cursor: pointer;
            font-size: 0.82rem;
        }

        /* Botón Iniciar Sesión */
        .btn-login-submit {
            background: var(--grad-accion) !important;
            color: var(--blanco) !important;
            border: none;
            font-weight: 700;
            padding: 9px 14px;
            font-size: 0.88rem;
            min-height: 40px;
            border-radius: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 3px 12px rgba(192, 21, 72, 0.22);
            letter-spacing: 0.3px;
        }

        .btn-login-submit:hover {
            background: var(--grad-accion-hover) !important;
            color: var(--blanco) !important;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(192, 21, 72, 0.32);
        }

        .btn-login-submit:active {
            transform: translateY(0);
        }

        /* =====================================================================
           SECCIÓN: CAMBIAR RÁPIDO DE PERFIL (ACCESO INMEDIATO)
           ===================================================================== */
        .quick-switch-section {
            background: transparent;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--c-border);
        }

        .quick-switch-title {
            color: var(--fucsia-oscuro);
            font-size: 0.68rem;
            letter-spacing: 0.6px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 9px;
        }

        /* Tarjeta de Perfil */
        .profile-card {
            background: var(--blanco);
            border: 1.5px solid var(--c-border);
            border-radius: 12px;
            padding: 8px 10px;
            text-align: left;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 9px;
            box-shadow: 0 2px 6px var(--c-shadow-fuchsia-sm);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .profile-card:hover {
            border-color: var(--rosa-intenso);
            box-shadow: 0 4px 12px var(--c-shadow-fuchsia);
            transform: translateY(-1px);
        }

        /* Estado Activo / Seleccionado */
        .profile-card.active {
            border: 2px solid var(--fucsia-oscuro) !important;
            background: #FFF2F6 !important;
            box-shadow: 0 3px 10px var(--c-shadow-fuchsia-sm);
        }

        .profile-card-icon {
            font-size: 1.30rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .profile-card-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .profile-card-name {
            color: var(--rojo-vino);
            font-weight: 700;
            font-size: 0.82rem;
            line-height: 1.2;
            margin-bottom: 1px;
        }

        .profile-card-role {
            color: var(--fucsia-oscuro);
            opacity: 0.85;
            font-size: 0.70rem;
            line-height: 1.2;
            white-space: nowrap;
        }

        /* Mensajes de Alerta Armonizados */
        .alert-custom-info {
            background-color: var(--dorado-champagne) !important;
            border: 1px solid var(--dorado-oscuro) !important;
            color: var(--rojo-vino) !important;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .alert-custom-danger {
            background-color: #FDF0F3 !important;
            border: 1px solid var(--rosa-intenso) !important;
            color: var(--rojo-vino) !important;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Footer */
        .card-login-footer {
            background-color: #FFF8FA;
            border-top: 1px solid var(--c-border) !important;
            color: var(--rojo-vino);
            opacity: 0.85;
            font-size: 0.76rem;
            padding: 14px 20px;
        }

        /* Celular muy pequeño (< 400px) */
        @media (max-width: 400px) {
            .profile-card {
                padding: 10px 12px;
            }
            .profile-card-icon {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body class="login-body">

    <!-- Botón Flotante para Alternar Modo Claro / Nocturno -->
    <button type="button" class="theme-toggle-btn theme-toggle-login" aria-label="Cambiar tema" title="Cambiar tema">
        <i class="bi bi-sun-fill theme-icon-sun"></i>
        <i class="bi bi-moon-stars-fill theme-icon-moon"></i>
    </button>

    <div class="card-login">
        <!-- Encabezado de Marca con Logo Bella Girls -->
        <div class="text-center card-login-header">
            <div class="login-logo-container">
                <img src="{{ asset('images/logo-bella-girls.png') }}" alt="Logo Bella Girls" class="login-logo">
            </div>
            <h2 class="font-serif fw-bold mb-1 brand-title">Bella Girls</h2>
            <p class="brand-subtitle mb-0">Sistema de Gestión para Tienda de Ropa</p>
        </div>

        <div class="p-3 p-sm-4">
            <!-- Mensajes de Notificación -->
            @if($errors->any())
                <div class="alert alert-custom-danger py-2 px-3 mb-3 border d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-custom-info py-2 px-3 mb-3 border d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <!-- Formulario de Autenticación -->
            <form action="{{ route('login') }}" method="POST" id="formLogin">
                @csrf
                <div class="mb-3">
                    <label for="inputUsuario" class="form-label small mb-1">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" name="usuario" id="inputUsuario" class="form-control border-start-0" placeholder="Ingresa tu usuario" value="{{ old('usuario') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="inputPassword" class="form-label small mb-1">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="inputPassword" class="form-control border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label small" for="remember">Recordar sesión</label>
                    </div>
                </div>

                <button type="submit" id="btnSubmitLogin" class="btn btn-login-submit w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                </button>
            </form>

            <!-- Sección: Cambiar Rápido de Perfil (Acceso Inmediato) -->
            <div class="quick-switch-section">
                <div class="quick-switch-title">
                    CAMBIAR RÁPIDO DE PERFIL (ACCESO INMEDIATO)
                </div>

                <div class="row g-2">
                    <!-- Tarjeta 1: Administrador -->
                    <div class="col-12 col-sm-6">
                        <button type="button" class="profile-card" data-user="admin" data-pass="admin123" title="Perfil Administrador">
                            <span class="profile-card-icon">👑</span>
                            <div class="profile-card-info">
                                <span class="profile-card-name">Administrador</span>
                                <span class="profile-card-role">Acceso total</span>
                            </div>
                        </button>
                    </div>

                    <!-- Tarjeta 2: Vendedor -->
                    <div class="col-12 col-sm-6">
                        <button type="button" class="profile-card" data-user="vendedor" data-pass="vendedor123" title="Perfil Vendedor">
                            <span class="profile-card-icon">🛍️</span>
                            <div class="profile-card-info">
                                <span class="profile-card-name">Vendedor</span>
                                <span class="profile-card-role">Ventas y clientes</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center card-login-footer">
            Bella Girls &copy; 2026 &bull; Sistema de Gestión
        </div>
    </div>

    <!-- Script de selección y autocompletado -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userInput = document.getElementById('inputUsuario');
            const passInput = document.getElementById('inputPassword');
            const btnSubmit = document.getElementById('btnSubmitLogin');
            const cards = document.querySelectorAll('.profile-card');

            cards.forEach(function(card) {
                card.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Marcar tarjeta activa y desmarcar las demás
                    cards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    // Rellenar usuario y contraseña desde data-*
                    const user = this.getAttribute('data-user');
                    const pass = this.getAttribute('data-pass');

                    if (userInput && passInput) {
                        userInput.value = user;
                        passInput.value = pass;

                        // Efecto de enfoque hacia el botón de iniciar sesión
                        if (btnSubmit) {
                            btnSubmit.focus();
                        }
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('js/boutique.js') }}"></script>
</body>
</html>
