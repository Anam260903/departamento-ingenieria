<!DOCTYPE html>
<html lang="es">
<head>
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row login-row">
            <div class="col-md-6 left-panel">
                <div class="login-card">
                    <h2 class="text-center mb-4">Restablecer Contraseña</h2>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $correo ?? old('email') }}" required placeholder="Correo electrónico">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Nueva Contraseña">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="password_confirmation" required placeholder="Confirmar Nueva Contraseña">
                        </div>
                        <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                    </form>
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