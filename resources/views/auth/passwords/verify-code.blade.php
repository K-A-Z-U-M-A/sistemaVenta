<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Verificar Código - Doggie's</title>
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

        .code-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            font-size: 24px;
            text-align: center;
            letter-spacing: 10px;
            font-weight: 600;
            background: #fafafa;
            transition: border-color 0.2s;
        }

        .code-input:focus {
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
            border-left: 4px solid #28a745;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #0d47a1;
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
                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                <h2>Verificar Código</h2>
                <p>Ingresa el código de 6 dígitos</p>
            </div>
            
            <div class="login-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-success" style="background: #fff3cd; color: #856404; border-left: 4px solid #ffc107;">
                        <i class="fas fa-code me-2"></i>
                        <strong>{{ session('warning') }}</strong>
                        <p class="mb-0 mt-2" style="font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center;">
                            {{ session('debug_code') }}
                        </p>
                        <small class="d-block mt-2 text-center">
                            <i class="fas fa-info-circle"></i> Copia este código y pégalo abajo
                        </small>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <div class="info-box">
                    <p>
                        <i class="fas fa-info-circle me-1"></i>
                        Se envió un código a: <strong>{{ session('email') }}</strong>
                    </p>
                    <p class="mt-1">
                        <i class="fas fa-clock me-1"></i>
                        El código expira en 15 minutos
                    </p>
                </div>

                <form action="{{ route('password.verify-code.submit') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            class="code-input" 
                            placeholder="000000"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            autofocus
                        />
                        <small class="text-muted d-block text-center mt-2">Ingresa los 6 dígitos</small>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check me-2"></i> Verificar Código
                    </button>
                </form>

                <div class="back-link">
                    <a href="{{ route('password.request') }}">
                        <i class="fas fa-redo me-1"></i> Solicitar nuevo código
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-format: Solo permitir números
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
