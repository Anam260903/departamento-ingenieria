<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/register.css') }}" rel="stylesheet"> </head>
<body>
    <div class="container-fluid register-container">
        <div class="row register-row">

            <div class="col-md-5 left-panel-register d-none d-md-flex">
                <div class="logo-container">
                    <img src="{{ asset('images/logo-corvisucre.png') }}" alt="Logo Departamento de Ingeniería CORVISUCRE">
                </div>
            </div>

            <div class="col-md-7 right-panel-register">
                <div class="register-card">
                    <h2 class="text-center mb-3">¡Bienvenido!</h2>
                    <h5 class="text-center mb-4 text-secondary">REGISTRO DE USUARIO</h5>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <input type="email" class="form-control" name="correo" maxlength="40" placeholder="Correo electrónico" value="{{ old('correo') }}" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <input type="text" class="form-control" name="nombre" maxlength="30" placeholder="Nombre" value="{{ old('nombre') }}" required>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" name="apellido" maxlength="30" placeholder="Apellido" value="{{ old('apellido') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
        <div class="col-6">
            <input type="text" class="form-control" name="cedula_user" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="8" placeholder="Cédula" value="{{ old('cedula_user') }}" required>
        </div>
        <div class="col-6">
            <div class="input-group">
                <input type="password" class="form-control" id="password-field" name="password" maxlength="10" placeholder="Contraseña" required>
                <span class="input-group-text" id="toggle-password">
                    <i class="bi bi-eye-fill" id="eye-icon"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="input-group">
            <input type="password" class="form-control" id="password-confirm-field" name="password_confirmation" maxlength="10" placeholder="Confirmar Contraseña" required>
            <span class="input-group-text" id="toggle-password-confirm">
                <i class="bi bi-eye-fill" id="eye-icon-confirm"></i>
            </span>
        </div>
    </div>

                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </form>

                    <div class="text-center mt-3">
                        <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Accede</a></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>