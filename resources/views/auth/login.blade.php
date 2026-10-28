<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Boutique Elegance</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF7F4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }
        .card-login {
            background: #FFFFFF;
            border: 1px solid #EFE8E2;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .btn-rose {
            background-color: #E8B4B8;
            color: #1E1B18;
            border: none;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-rose:hover {
            background-color: #DCA0A5;
            color: #1E1B18;
        }
    </style>
</head>
<body>

    <div class="card-login">
        <!-- Encabezado de Marca -->
        <div class="p-4 text-center border-bottom" style="background-color: #FFFDFB;">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: #FAF0EE; color: #9E5664; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 12px;">
                <i class="bi bi-gem"></i>
            </div>
            <h3 class="font-serif fw-bold mb-1" style="color: #2A2421;">Boutique Elegance</h3>
            <p class="text-muted small mb-0">Sistema de Gestión para Tienda de Ropa (Laravel & PostgreSQL)</p>
        </div>

        <div class="p-4">
            @if($errors->any())
                <div class="alert alert-danger py-2 px-3 small mb-3 border-0" style="background-color: #FDF2F2; color: #991B1B;">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info py-2 px-3 small mb-3 border-0">
                    <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="usuario" class="form-control border-start-0" placeholder="admin o vendedor" value="{{ old('usuario', 'admin') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" value="admin123" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label small text-muted" for="remember">Recordar sesión</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-rose w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                </button>
            </form>

            <!-- Credenciales de Prueba -->
            <div class="p-3 rounded small" style="background-color: #FBF8F5; border: 1px dashed #E6DCD5;">
                <div class="fw-bold mb-1 text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase;">
                    Credenciales de prueba:
                </div>
                <div class="d-flex justify-content-between text-muted mb-1">
                    <span>👑 Administrador:</span>
                    <code>admin / admin123</code>
                </div>
                <div class="d-flex justify-content-between text-muted">
                    <span>🛍️ Vendedor:</span>
                    <code>vendedor / vendedor123</code>
                </div>
            </div>
        </div>

        <div class="p-3 text-center border-top text-muted small" style="background-color: #FAF8F6; font-size: 0.75rem;">
            Base de datos activa: <strong>PostgreSQL</strong> &bull; Boutique Elegance &copy; {{ date('Y') }}
        </div>
    </div>

</body>
</html>
