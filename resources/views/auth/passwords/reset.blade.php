<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Nueva Contraseña - Doggie's</title>
    <link rel="icon" href="{{ asset('img/image.png') }}" type="image/png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    
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

        .login-header h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 13px;
            opacity: 0.92;
        }

        .login-body {
            padding: 35px 25px;
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

        .btn-primary {
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

        .btn-primary:hover {
            opacity: 0.9;
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

        .password-requirements {
            background: #f8f9fa;
            border-left: 4px solid #6c757d;
            padding: 10px;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 12px;
        }

        .password-requirements ul {
            margin: 5px 0 0 0;
            padding-left: 20px;
        }

        .password-requirements li {
            margin: 3px 0;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-key fa-3x mb-3"></i>
                <h2>Nueva Contraseña</h2>
                <p>Ingresa tu nueva contraseña</p>
            </div>
            
            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0" style="padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.reset.submit') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock me-1"></i> Nueva Contraseña
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control" 
                                placeholder="Mínimo 8 caracteres"
                                required
                                autofocus
                            />
                            <i class="fas fa-lock"></i>
                            <i class="fas fa-eye password-toggle" id="togglePassword1"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">
                            <i class="fas fa-lock me-1"></i> Confirmar Contraseña
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                class="form-control" 
                                placeholder="Repite la contraseña"
                                required
                            />
                            <i class="fas fa-lock"></i>
                            <i class="fas fa-eye password-toggle" id="togglePassword2"></i>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <strong>Requisitos de la contraseña:</strong>
                        <ul>
                            <li>Mínimo 8 caracteres</li>
                            <li>Ambas contraseñas deben coincidir</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-primary mt-3">
                        <i class="fas fa-save me-2"></i> Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword1 = document.getElementById('togglePassword1');
        const togglePassword2 = document.getElementById('togglePassword2');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        togglePassword1.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        togglePassword2.addEventListener('click', function() {
            const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmation.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
