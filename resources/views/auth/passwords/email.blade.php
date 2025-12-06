<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Recuperar Contraseña - Doggie's</title>
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

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #ff6b35;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-lock fa-3x mb-3"></i>
                <h2>Recuperar Contraseña</h2>
                <p>Ingresa tu correo electrónico</p>
            </div>
            
            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('password.send-code') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label" for="email">
                            <i class="fas fa-envelope me-1"></i> Correo Electrónico
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="form-control" 
                                placeholder="ejemplo@correo.com"
                                value="{{ old('email') }}"
                                required
                                autofocus
                            />
                            <i class="fas fa-envelope"></i>
                        </div>
                        <small class="text-muted">Recibirás un código de 6 dígitos en tu correo</small>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane me-2"></i> Enviar Código
                    </button>
                </form>

                <div class="back-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-arrow-left me-1"></i> Volver al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
