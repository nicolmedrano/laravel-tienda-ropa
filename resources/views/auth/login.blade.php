<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Bella Girls</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* =====================================================================
           PALETA OFICIAL DE TONOS VINO / ROSA PALO
           ===================================================================== */
        :root {
            --c-900: #4B0F1E;  /* vino muy oscuro */
            --c-800: #6D1D32;  /* vino oscuro */
            --c-700: #8E2B44;  /* vino */
            --c-600: #B23C59;  /* frambuesa */
            --c-500: #CC5671;  /* rosa frambuesa */
            --c-400: #E07A94;  /* rosa medio */
            --c-100: #F7D6DC;  /* rosa palo claro */

            --c-white: #FFFFFF;
            --c-shadow-wine: rgba(142, 43, 68, 0.16);
            --c-shadow-wine-sm: rgba(142, 43, 68, 0.08);
            --c-shadow-wine-lg: rgba(142, 43, 68, 0.14);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, var(--c-100) 0%, rgba(247, 214, 220, 0.45) 45%, var(--c-white) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            color: var(--c-900);
        }

        .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* Tarjeta del Login */
        .card-login {
            background: var(--c-white);
            border: 1px solid var(--c-100);
            border-radius: 22px;
            box-shadow: 0 16px 40px var(--c-shadow-wine-lg), 0 2px 10px var(--c-shadow-wine-sm);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .card-login:hover {
            box-shadow: 0 20px 48px var(--c-shadow-wine), 0 4px 12px var(--c-shadow-wine-sm);
        }

        /* Encabezado */
        .card-login-header {
            background: linear-gradient(180deg, var(--c-white) 0%, rgba(247, 214, 220, 0.25) 100%);
            border-bottom: 1px solid var(--c-100) !important;
            padding: 28px 24px 20px 24px;
        }

        /* Logo Bella Girls */
        .login-logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 14px;
        }

        .login-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 50%;
            box-shadow: 0 8px 22px rgba(142, 43, 68, 0.22), 0 0 0 3px rgba(245, 197, 66, 0.35);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: var(--c-white);
        }

        .login-logo:hover {
            transform: scale(1.04) rotate(1deg);
            box-shadow: 0 10px 26px rgba(142, 43, 68, 0.30), 0 0 0 4px rgba(245, 197, 66, 0.55);
        }

        .brand-title {
            color: var(--c-900);
            letter-spacing: -0.5px;
            font-size: 1.85rem;
            font-weight: 700;
        }

        .brand-subtitle {
            color: var(--c-700);
            font-size: 0.88rem;
        }

        /* Formulario y Etiquetas */
        .form-label {
            color: var(--c-800);
            font-weight: 600;
        }

        .input-group-text {
            background-color: #fff9fa !important;
            border-color: var(--c-100) !important;
            color: var(--c-600) !important;
            font-size: 1.05rem;
        }

        .form-control {
            border-color: var(--c-100) !important;
            color: var(--c-900);
            background-color: var(--c-white);
            border-radius: 10px;
            padding: 10px 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--c-500) !important;
            box-shadow: 0 0 0 0.25rem rgba(204, 86, 113, 0.22) !important;
            outline: none;
            color: var(--c-900);
        }

        /* Checkbox Recordar Sesión */
        .form-check-input {
            border-color: var(--c-400);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--c-600) !important;
            border-color: var(--c-600) !important;
        }

        .form-check-input:focus {
            border-color: var(--c-500) !important;
            box-shadow: 0 0 0 0.25rem rgba(204, 86, 113, 0.22) !important;
        }

        .form-check-label {
            color: var(--c-800) !important;
            cursor: pointer;
        }

        /* Botón Iniciar Sesión */
        .btn-login-submit {
            background: linear-gradient(135deg, var(--c-700) 0%, var(--c-500) 100%) !important;
            color: var(--c-white) !important;
            border: none;
            font-weight: 700;
            padding: 12px;
            border-radius: 12px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(142, 43, 68, 0.24);
            letter-spacing: 0.3px;
        }

        .btn-login-submit:hover {
            background: linear-gradient(135deg, var(--c-800) 0%, var(--c-600) 100%) !important;
            color: var(--c-white) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(142, 43, 68, 0.35);
        }

        .btn-login-submit:active {
            transform: translateY(0);
        }

        /* =====================================================================
           SECCIÓN: CAMBIAR RÁPIDO DE PERFIL (ACCESO INMEDIATO)
           ===================================================================== */
        .quick-switch-section {
            background: transparent;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid var(--c-100);
        }

        .quick-switch-title {
            color: var(--c-700);
            font-size: 0.74rem;
            letter-spacing: 0.6px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 12px;
        }

        /* Tarjeta de Perfil */
        .profile-card {
            background: var(--c-white);
            border: 1px solid var(--c-100);
            border-radius: 16px;
            padding: 12px 14px;
            text-align: left;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px var(--c-shadow-wine-sm);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .profile-card:hover {
            border-color: var(--c-500);
            box-shadow: 0 6px 16px var(--c-shadow-wine);
            transform: translateY(-2px);
        }

        /* Estado Activo / Seleccionado */
        .profile-card.active {
            border: 1.5px solid var(--c-600) !important;
            background: rgba(247, 214, 220, 0.35) !important;
            box-shadow: 0 4px 14px var(--c-shadow-wine-sm);
        }

        .profile-card-icon {
            font-size: 1.55rem;
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
            color: var(--c-900);
            font-weight: 700;
            font-size: 0.88rem;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .profile-card-role {
            color: var(--c-700);
            opacity: 0.75;
            font-size: 0.74rem;
            line-height: 1.2;
            white-space: nowrap;
        }

        /* Mensajes de Alerta Armonizados */
        .alert-custom-info {
            background-color: var(--c-100) !important;
            border: 1px solid var(--c-400) !important;
            color: var(--c-800) !important;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .alert-custom-danger {
            background-color: var(--c-100) !important;
            border: 1px solid var(--c-700) !important;
            color: var(--c-700) !important;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        /* Footer */
        .card-login-footer {
            background-color: rgba(247, 214, 220, 0.15);
            border-top: 1px solid var(--c-100) !important;
            color: var(--c-700);
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
<body>

    <div class="card-login">
        <!-- Encabezado de Marca con Logo Bella Girls -->
        <div class="text-center card-login-header">
            <div class="login-logo-container">
                <img src="{{ asset('images/logo-bella-girls.png') }}" alt="Logo Bella Girls" class="login-logo">
            </div>
            <h2 class="font-serif fw-bold mb-1 brand-title">Bella Girls</h2>
            <p class="brand-subtitle mb-0">Sistema de Gestión para Tienda de Ropa</p>
        </div>

        <div class="p-4">
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
</body>
</html>
