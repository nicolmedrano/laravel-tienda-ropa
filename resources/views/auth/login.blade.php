<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Bella Girls Boutique</title>
    <!-- Script Anti-Parpadeo de Tema -->
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
    <!-- Google Fonts: Playfair Display & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bella-theme.css') }}">
    
    <style>
        /* =====================================================================
           TEMA SAKURA & GLASSMORPHISM (Paleta Durazno, Crema, Rosa y Marfil)
           ===================================================================== */
        :root {
            --peach-coral:  #FEA38E;
            --cream:        #FFDFC3;
            --pink:         #FBA2A8;
            --ivory:        #F6E8D0;
            --peach-soft:   #F3B5A0;
            --wine-dark:    #4A1F2A;
            --coral-btn:    #D84C38;
            --coral-hover:  #BF3B28;
            --grad-sakura:  linear-gradient(135deg, #FFDFC3 0%, #F3B5A0 35%, #FEA38E 70%, #FBA2A8 100%);
            --grad-coral:   linear-gradient(135deg, #FEA38E 0%, #D84C38 100%);
            --grad-coral-hover: linear-gradient(135deg, #D84C38 0%, #BF3B28 100%);
        }

        * {
            box-sizing: border-box;
        }

        body.login-sakura-body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--grad-sakura);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            margin: 0;
            position: relative;
            overflow-x: hidden;
            color: var(--wine-dark);
        }

        /* Formas Orgánicas Decorativas en Fondo (Estilo Sakura / Pétalos sin Copyright) */
        .sakura-bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(55px);
            opacity: 0.65;
            pointer-events: none;
            z-index: 0;
            animation: floatShape 16s ease-in-out infinite alternate;
        }

        .shape-1 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, #FBA2A8 0%, #FEA38E 100%);
            top: -60px;
            left: -80px;
        }

        .shape-2 {
            width: 440px;
            height: 440px;
            background: radial-gradient(circle, #FFDFC3 0%, #F3B5A0 100%);
            bottom: -90px;
            right: -90px;
            animation-duration: 22s;
        }

        .shape-3 {
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, #FFFFFF 0%, #FBA2A8 80%);
            top: 40%;
            left: 75%;
            filter: blur(40px);
            opacity: 0.5;
        }

        /* Ilustración SVG Floral Decorativa */
        .floral-backdrop-svg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 900px;
            max-width: 95vw;
            height: 900px;
            max-height: 95vh;
            opacity: 0.22;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes floatShape {
            0% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(25px) rotate(8deg) scale(1.05); }
            100% { transform: translateY(-20px) rotate(-6deg) scale(0.96); }
        }

        /* TARJETA GLASSMORPHISM CENTRAL */
        .glass-card-login {
            background: rgba(255, 255, 255, 0.76);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1.5px solid rgba(255, 255, 255, 0.88);
            border-radius: 30px;
            box-shadow: 0 25px 60px rgba(74, 31, 42, 0.14), 0 6px 20px rgba(254, 163, 142, 0.22);
            width: 100%;
            max-width: 440px;
            padding: 38px 34px 30px 34px;
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        [data-theme="dark"] .glass-card-login {
            background: rgba(43, 20, 30, 0.80);
            backdrop-filter: blur(26px);
            -webkit-backdrop-filter: blur(26px);
            border: 1.5px solid rgba(243, 181, 160, 0.25);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.50);
            color: #FFDFC3;
        }

        /* Encabezado */
        .login-header-logo {
            width: 82px;
            height: 82px;
            object-fit: contain;
            border-radius: 50%;
            padding: 4px;
            background: #FFFFFF;
            border: 2px solid var(--peach-coral);
            box-shadow: 0 6px 16px rgba(254, 163, 142, 0.35);
            transition: transform 0.3s ease;
        }

        .login-header-logo:hover {
            transform: scale(1.05) rotate(2deg);
        }

        .login-title {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--wine-dark);
            font-size: 1.85rem;
            font-weight: 700;
            margin: 12px 0 3px 0;
        }

        [data-theme="dark"] .login-title {
            color: #FFDFC3;
        }

        .login-subtitle {
            color: var(--muted, #7D5863);
            font-size: 0.88rem;
            font-weight: 400;
            margin-bottom: 24px;
        }

        [data-theme="dark"] .login-subtitle {
            color: #F3B5A0;
        }

        /* INPUTS CON ESTILO DE LÍNEA INFERIOR (REFERENCIA IMAGEN 1) */
        .bottom-line-group {
            position: relative;
            margin-bottom: 22px;
            text-align: left;
        }

        .bottom-line-group .input-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--wine-dark);
            margin-bottom: 4px;
        }

        [data-theme="dark"] .bottom-line-group .input-label {
            color: #F3B5A0;
        }

        .bottom-line-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 2px;
            font-size: 1.15rem;
            color: var(--peach-coral);
            transition: color 0.25s ease;
            pointer-events: none;
        }

        .form-control-bottom-line {
            width: 100%;
            background: transparent !important;
            border: none;
            border-bottom: 2px solid var(--peach-soft);
            border-radius: 0;
            padding: 9px 34px 9px 30px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--wine-dark);
            outline: none;
            transition: all 0.25s ease;
        }

        [data-theme="dark"] .form-control-bottom-line {
            color: #FFDFC3;
            border-bottom-color: rgba(243, 181, 160, 0.35);
        }

        .form-control-bottom-line:focus {
            border-bottom-color: var(--coral-btn);
            box-shadow: 0 2px 0 0 var(--coral-btn);
        }

        .form-control-bottom-line:focus ~ .input-icon-left {
            color: var(--coral-btn);
        }

        .toggle-password-btn {
            position: absolute;
            right: 2px;
            background: none;
            border: none;
            color: var(--peach-soft);
            font-size: 1.1rem;
            padding: 0;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .toggle-password-btn:hover {
            color: var(--coral-btn);
        }

        /* Error bajo el campo */
        .invalid-feedback-bottom {
            font-size: 0.78rem;
            color: var(--danger-warm, #C0392B);
            margin-top: 5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Checkbox y Olvidaste tu contraseña */
        .form-check-input {
            border-color: var(--peach-soft);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--coral-btn);
            border-color: var(--coral-btn);
        }

        .form-check-label {
            font-size: 0.85rem;
            color: var(--wine-dark);
            cursor: pointer;
            user-select: none;
        }

        [data-theme="dark"] .form-check-label {
            color: #FFDFC3;
        }

        .forgot-link {
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--coral-btn);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--coral-hover);
            text-decoration: underline;
        }

        /* BOTÓN PRINCIPAL REDONDEADO CON EL CORAL */
        .btn-sakura-login {
            background: var(--grad-coral);
            color: #FFFFFF !important;
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 6px 18px rgba(216, 76, 56, 0.35);
            transition: all 0.25s ease;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-sakura-login:hover {
            background: var(--grad-coral-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(216, 76, 56, 0.45);
            color: #FFFFFF;
        }

        .btn-sakura-login:active {
            transform: translateY(0);
        }

        .btn-sakura-login:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        /* SECCIÓN RÁPIDA DE PERFILES DE DEMO */
        .demo-roles-divider {
            display: flex;
            align-items: center;
            margin: 24px 0 14px 0;
            color: var(--muted, #7D5863);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .demo-roles-divider::before,
        .demo-roles-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--peach-soft);
            opacity: 0.6;
        }

        .demo-roles-divider span {
            padding: 0 10px;
        }

        .btn-pill-role {
            background: rgba(255, 255, 255, 0.6);
            border: 1.5px solid var(--peach-soft);
            border-radius: 50px;
            padding: 8px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--wine-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        [data-theme="dark"] .btn-pill-role {
            background: rgba(43, 20, 30, 0.6);
            color: #FFDFC3;
            border-color: rgba(243, 181, 160, 0.3);
        }

        .btn-pill-role:hover {
            border-color: var(--coral-btn);
            background: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(216, 76, 56, 0.15);
        }

        /* Botón de tema flotante */
        .theme-toggle-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.85);
            color: var(--wine-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(74, 31, 42, 0.1);
            transition: all 0.2s ease;
            z-index: 99;
        }

        .theme-toggle-floating:hover {
            transform: scale(1.08);
            color: var(--coral-btn);
        }

        [data-theme="dark"] .theme-toggle-floating {
            background: rgba(43, 20, 30, 0.8);
            border-color: rgba(243, 181, 160, 0.25);
            color: #FFDFC3;
        }

        /* Responsive móvil */
        @media (max-width: 480px) {
            .glass-card-login {
                padding: 28px 22px 22px 22px;
                border-radius: 24px;
            }
            .login-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body class="login-sakura-body">

    <!-- Formas orgánicas decorativas en el fondo (Sakura / Durazno) -->
    <div class="sakura-bg-shape shape-1"></div>
    <div class="sakura-bg-shape shape-2"></div>
    <div class="sakura-bg-shape shape-3"></div>

    <!-- Ilustración SVG botánica / sakura de fondo sin derechos de autor -->
    <svg class="floral-backdrop-svg" viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M250 50C265 140 340 160 410 180C330 210 320 280 320 370C270 300 210 320 150 400C160 310 100 270 40 250C120 220 150 160 170 80C190 150 240 180 250 50Z" fill="url(#gradFloral)" />
        <circle cx="110" cy="120" r="35" fill="#FEA38E" fill-opacity="0.3"/>
        <circle cx="390" cy="340" r="45" fill="#FBA2A8" fill-opacity="0.3"/>
        <circle cx="370" cy="110" r="28" fill="#FFDFC3" fill-opacity="0.4"/>
        <defs>
            <linearGradient id="gradFloral" x1="40" y1="50" x2="410" y2="400" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFDFC3" stop-opacity="0.6"/>
                <stop offset="0.5" stop-color="#FEA38E" stop-opacity="0.4"/>
                <stop offset="1" stop-color="#FBA2A8" stop-opacity="0.5"/>
            </linearGradient>
        </defs>
    </svg>

    <!-- Botón Flotante para Alternar Modo Claro / Nocturno -->
    <button type="button" class="theme-toggle-floating theme-toggle-btn" aria-label="Cambiar tema" title="Cambiar tema">
        <i class="bi bi-sun-fill theme-icon-sun"></i>
        <i class="bi bi-moon-stars-fill theme-icon-moon"></i>
    </button>

    <!-- TARJETA CENTRAL GLASSMORPHISM -->
    <div class="glass-card-login text-center">
        <!-- Logo y Nombre de Tienda -->
        <div>
            <img src="{{ asset('images/logo-bella-girls.png') }}" alt="Bella Girls Logo" class="login-header-logo">
            <h1 class="login-title">Iniciar sesión</h1>
            <p class="login-subtitle">Bella Girls &bull; Panel de Gestión Boutique</p>
        </div>

        <!-- Alerta de sesión o error general -->
        @if(session('info'))
            <div class="alert alert-info py-2 px-3 mb-3 border-0 rounded-3 small text-start d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-primary"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->has('usuario') && !old('usuario'))
            <div class="alert alert-danger py-2 px-3 mb-3 border-0 rounded-3 small text-start d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                <span>{{ $errors->first('usuario') }}</span>
            </div>
        @endif

        <!-- Formulario -->
        <form action="{{ route('login') }}" method="POST" id="formLogin">
            @csrf

            <!-- Campo 1: Usuario con línea inferior -->
            <div class="bottom-line-group">
                <label for="inputUsuario" class="input-label">Usuario</label>
                <div class="bottom-line-wrapper">
                    <i class="bi bi-person input-icon-left"></i>
                    <input type="text" name="usuario" id="inputUsuario" class="form-control-bottom-line" placeholder="ej. admin o vendedor" value="{{ old('usuario') }}" required autofocus autocomplete="username">
                </div>
                @error('usuario')
                    <div class="invalid-feedback-bottom">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Campo 2: Contraseña con línea inferior y botón toggle ver/ocultar -->
            <div class="bottom-line-group">
                <label for="inputPassword" class="input-label">Contraseña</label>
                <div class="bottom-line-wrapper">
                    <i class="bi bi-lock input-icon-left"></i>
                    <input type="password" name="password" id="inputPassword" class="form-control-bottom-line" placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="toggle-password-btn" id="btnTogglePassword" aria-label="Mostrar u ocultar contraseña">
                        <i class="bi bi-eye" id="iconoEye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback-bottom">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Recordarme & Olvidaste tu contraseña -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check text-start">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>
                <a href="#" onclick="alert('Para restablecer tu contraseña o crear una nueva cuenta, comunícate con la administración de Bella Girls.'); return false;" class="forgot-link">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <!-- Botón principal redondeado -->
            <button type="submit" id="btnSubmitLogin" class="btn btn-sakura-login">
                <span class="spinner-border spinner-border-sm" id="loginSpinner" style="display: none;" role="status"></span>
                <span id="textoBtnLogin">Ingresar al Sistema</span>
                <i class="bi bi-arrow-right" id="iconoArrowLogin"></i>
            </button>
        </form>

        <!-- Acceso Rápido para Pruebas / Demostración -->
        <div class="demo-roles-divider">
            <span>Acceso rápido</span>
        </div>

        <div class="row g-2">
            <div class="col-6">
                <button type="button" class="btn-pill-role" onclick="autoCompletarCredenciales('admin', 'admin123')">
                    <span>👑</span> <span>Admin</span>
                </button>
            </div>
            <div class="col-6">
                <button type="button" class="btn-pill-role" onclick="autoCompletarCredenciales('vendedor', 'vendedor123')">
                    <span>🛍️</span> <span>Ventas</span>
                </button>
            </div>
        </div>

        <div class="mt-4 pt-2 text-center text-muted" style="font-size: 0.76rem;">
            &copy; {{ date('Y') }} Bella Girls &bull; Todos los derechos reservados
        </div>
    </div>

    <!-- Scripts de Interacción: Toggle Password, Spinner y Autocompletar -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle de mostrar/ocultar contraseña
            const btnToggle = document.getElementById('btnTogglePassword');
            const passInput = document.getElementById('inputPassword');
            const eyeIcon = document.getElementById('iconoEye');

            if (btnToggle && passInput) {
                btnToggle.addEventListener('click', function () {
                    const isPassword = passInput.type === 'password';
                    passInput.type = isPassword ? 'text' : 'password';
                    eyeIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                });
            }

            // Spinner en el botón de submit
            const form = document.getElementById('formLogin');
            const btnSubmit = document.getElementById('btnSubmitLogin');
            const spinner = document.getElementById('loginSpinner');
            const arrow = document.getElementById('iconoArrowLogin');
            const textoBtn = document.getElementById('textoBtnLogin');

            if (form && btnSubmit) {
                form.addEventListener('submit', function () {
                    btnSubmit.disabled = true;
                    if (spinner) spinner.style.display = 'inline-block';
                    if (arrow) arrow.style.display = 'none';
                    if (textoBtn) textoBtn.textContent = 'Verificando...';
                });
            }
        });

        // Autocompletar credenciales de demostración
        function autoCompletarCredenciales(usuario, password) {
            const userInput = document.getElementById('inputUsuario');
            const passInput = document.getElementById('inputPassword');
            const btnSubmit = document.getElementById('btnSubmitLogin');

            if (userInput && passInput) {
                userInput.value = usuario;
                passInput.value = password;
                btnSubmit.focus();
            }
        }
    </script>
    <script src="{{ asset('js/boutique.js') }}"></script>
</body>
</html>
