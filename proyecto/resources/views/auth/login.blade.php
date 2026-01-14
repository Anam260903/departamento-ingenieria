<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">

</head>

<body>
    <div class="container-fluid">
        <div class="row login-row">
            <div class="col-md-7 left-panel">
                <div class="login-card">
                    <h2 class="text-center mb-3">¡Bienvenido!</h2>
                    <h5 class="text-center mb-3">DEPARTAMENTO DE INGENIERÍA</h5>
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

                    @php
                        $mensaje = session('status') ?? session('success');
                    @endphp

                    @if($mensaje)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $mensaje }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Credenciales de acceso --}}
                        <div class="mb-3">
                            <input type="email" class="form-control" name="correo" maxlength="40"
                                placeholder="Correo electrónico" required autofocus>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password-field" name="password"
                                    minlength="8" maxlength="15" placeholder="Contraseña" required>
                                <span class="input-group-text" id="toggle-password">
                                    <i class="bi bi-eye-fill" id="eye-icon"></i>
                                </span>
                            </div>
                        </div>
                        {{-- Botón de acción --}}
                        <button type="submit" class="btn btn-primary">Acceder</button>
                    </form>

                    {{-- Enlaces de navegación --}}
                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('form.olvideContraseña') }}" class="text-decoration-none">¿Has olvidado tu contraseña?</a>
                        <p class="mb-0">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-decoration-none">Regístrate</a></p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ asset('documentos/manual-usuario.pdf') }}" download="Manual_de_Usuario.pdf" class="text-decoration-none" title="Descarga el manual de usuario del sistema">¿Cómo usar el sistema?</a>
                    </div>
                </div>
            </div>

            <div class="col-md-5 right-panel d-none d-md-flex">
                <div class="logo-container">
                    <img src="{{ asset('images/logo3.jpg') }}" alt="Logo Departamento de Ingeniería CORVISUCRE">
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>