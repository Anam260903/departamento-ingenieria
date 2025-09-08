<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    
</head>
<body>
    <div class="container-fluid">
        <div class="row login-row">
            <div class="col-md-7 left-panel">
                <div class="login-card">
                    <h2 class="text-center mb-3">¡Bienvenido!</h2>
                    <h5 class="text-center mb-4 text-secondary">INGRESO AL SISTEMA</h5>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="email" class="form-control" name="correo" maxlength="40" placeholder="Correo electrónico" required autofocus>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password-field" name="password" maxlength="10" placeholder="Contraseña" required>
                                <span class="input-group-text" id="toggle-password">
                                    <i class="bi bi-eye-fill" id="eye-icon"></i>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Acceder</button>
                    </form>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('password.request') }}" class="text-decoration-none">¿Has olvidado tu contraseña?</a>
                        <p class="mb-0">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
                    </div>
                </div>
            </div>

            <div class="col-md-5 right-panel d-none d-md-flex">
                <div class="logo-container">
                    <img src="{{ asset('images/logo-corvisucre.png') }}" alt="Logo Departamento de Ingeniería CORVISUCRE">
                </div>
            </div>
        </div>
    </div>
<script src="{{ asset('js/login.js') }}"></script>
</body>
</html>