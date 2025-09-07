<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row login-row">
            <div class="col-md-6 left-panel">
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

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Correo electrónico" required autofocus>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Acceder</button>
                    </form>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('password.request') }}" class="text-decoration-none">¿Has olvidado tu contraseña?</a>
                        <p class="mb-0">¿No tienes cuenta? <a href="#">Regístrate</a></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 right-panel d-none d-md-flex">
                <div class="logo-container">
                    <img src="{{ asset('images/logo-corvisucre.png') }}" alt="Logo Departamento de Ingeniería CORVISUCRE">
                </div>
            </div>
        </div>
    </div>
</body>
</html>