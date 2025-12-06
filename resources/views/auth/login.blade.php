<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Doggie's - Inicio de sesión" />
    <meta name="author" content="Doggie's" />
    <title>Doggie's - Login</title>
    <link rel="icon" href="{{ asset('img/image.png') }}" type="image/png">
    
    <!-- Preconnect para recursos externos - mejora significativa de velocidad -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://use.fontawesome.com">
    
    <!-- Fuentes optimizadas con display=swap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous" defer></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 15px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(255, 107, 53, 0.15);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            padding: 35px 25px;
            text-align: center;
            color: white;
        }

        .login-logo {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            margin-bottom: 12px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.5);
        }

        .login-header h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 13px;
            opacity: 0.92;
            margin: 0;
        }

        .login-body {
            padding: 35px 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff6b35;
            font-size: 15px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            font-size: 14px;
            background: #fafafa;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b35;
            background: white;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-login:hover {
            opacity: 0.9;
        }

        .btn-login:active {
            opacity: 1;
        }

        .alert {
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            border: none;
        }

        .alert-danger {
            background: #ffe5e0;
            color: #d63031;
        }
        
        .alert-warning {
            background: #fff4e5;
            color: #e17055;
            border-left: 4px solid #ff6b35;
        }
        
        .btn-force-login {
            width: 100%;
            padding: 10px;
            background: #e17055;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }
        
        .btn-force-login:hover {
            background: #d63031;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #777;
            font-size: 12px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }

        .password-toggle:hover {
            color: #ff6b35;
        }

        /* Loading animation */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.7;
            position: relative;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            top: 50%;
            left: 50%;
            margin-left: -7px;
            margin-top: -7px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 12px;
            }
            
            .login-header {
                padding: 28px 18px;
            }
            
            .login-body {
                padding: 28px 18px;
            }

            .login-logo {
                width: 65px;
                height: 65px;
            }

            .login-header h2 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('img/image.png') }}" alt="Logo" class="login-logo">
                <h2>Doggie's</h2>
                <p>Ingresa tus credenciales para continuar</p>
            </div>
            
            <div class="login-body">
                @if ($errors->any())
                    @foreach ($errors->all() as $item)
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $item }}
                        </div>
                    @endforeach
                @endif
                
                @if(session('warning') && session('show_force_login'))
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Sesión Activa Detectada</strong>
                        <p class="mb-2 mt-1">{{ session('warning') }}</p>
                        <p class="mb-2"><strong>¿Deseas cerrar la sesión activa y continuar?</strong></p>
                        <form action="/login" method="post">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email_to_force') ?? old('email') }}">
                            <input type="hidden" name="password" value="{{ old('password') }}">
                            <input type="hidden" name="force_login" value="1">
                            <button type="submit" class="btn-force-login">
                                <i class="fas fa-sign-out-alt me-2"></i>Sí, cerrar sesión anterior e iniciar aquí
                            </button>
                        </form>
                    </div>
                @endif

                <form action="/login" method="post" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="inputEmail">
                            <i class="fas fa-envelope me-1"></i> Correo Electrónico
                        </label>
                        <div class="input-wrapper">
                            <input 
                                autofocus 
                                autocomplete="email" 
                                class="form-control" 
                                name="email" 
                                id="inputEmail" 
                                type="email" 
                                placeholder="ejemplo@correo.com"
                                required
                            />
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="inputPassword">
                            <i class="fas fa-lock me-1"></i> Contraseña
                        </label>
                        <div class="input-wrapper">
                            <input 
                                class="form-control" 
                                name="password" 
                                id="inputPassword" 
                                type="password" 
                                placeholder="Ingresa tu contraseña"
                                required
                            />
                            <i class="fas fa-lock"></i>
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>

                    <button class="btn-login" type="submit" id="loginBtn">
                        <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}" style="color: #ff6b35; text-decoration: none; font-size: 14px;">
                            <i class="fas fa-question-circle me-1"></i> ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>

                <div class="footer-text">
                    <p>&copy; 2025 Doggie's. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('inputPassword');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Form submission animation
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        loginForm.addEventListener('submit', function(e) {
            // Prevent double submission
            if (loginBtn.classList.contains('loading')) {
                e.preventDefault();
                return;
            }
            
            loginBtn.classList.add('loading');
            // Keep the width to prevent layout shift
            loginBtn.style.width = loginBtn.offsetWidth + 'px';
            loginBtn.innerHTML = ''; // Clear text to show spinner clearly
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>