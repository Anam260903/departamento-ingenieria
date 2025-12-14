<!DOCTYPE html>
<html lang="es">

<head>
    <title>Desafío de Seguridad</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row login-row">
            <div class="col-md-6 left-panel">
                <div class="login-card">
                    <h2 class="text-center mb-4">Pregunta de Seguridad</h2>

                    @if (session('errors'))
                        <div class="alert alert-danger" role="alert">
                            @foreach (session('errors')->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pregunta de seguridad para restablecer la contraseña --}}
                    <form method="POST" action="{{ route('validate.preguntas') }}">
                        @csrf
                        <input type="hidden" name="challenge_token" value="{{ $challengeToken }}">

                        <p class="lead">Pregunta: {{ $pregunta }}</p>

                        <div class="mb-3">
                            <label for="respuesta" class="form-label">Tu Respuesta:</label>
                            <input type="text" class="form-control @error('respuesta') is-invalid @enderror"
                                id="respuesta" name="respuesta" required>
                            @error('respuesta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Botón de acción --}}
                        <button type="submit" class="btn btn-primary">Verificar Respuesta</button>
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