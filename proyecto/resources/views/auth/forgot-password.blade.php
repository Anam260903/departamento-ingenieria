<!DOCTYPE html>
<html lang="es">

<head>
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row login-row">
            <div class="col-md-6 left-panel">
                <div class="login-card">
                    <h2 class="text-center mb-4">Recuperar Contraseña</h2>
                    <p class="text-center">Ingresa tu correo electrónico para verificar tu identidad mediante una
                        pregunta de seguridad.</p>

                    @if (session('errors'))
                        <div class="alert alert-danger" role="alert">
                            @foreach (session('errors')->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('identify.olvideContraeña')}}">
                        @csrf
                        <div class="mb-3">
                            <input type="email" class="form-control" name="correo"
                                value="{{ old('correo')}}" required placeholder="Correo electrónico">
                        </div>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6 right-panel d-none d-md-flex">
                <div class="logo-container">
                    <img src="{{ asset('images/logo3.jpg') }}"
                        alt="Logo Departamento de Ingeniería CORVISUCRE">
                </div>
            </div>
        </div>
    </div>
</body>

</html>