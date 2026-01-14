<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Preguntas de Seguridad</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        .register-row {
            min-height: 100vh;
            background-color: #194680;
        }

        .register-card {
            padding: 30px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container-fluid register-container">

        <div class="row register-row justify-content-center align-items-center">

            <div class="col-12 col-sm-10 col-md-8 col-lg-7 security-card-wrapper">

                <div class="card shadow-sm p-4">

                    {{-- Registro de preguntas de seguridad --}}
                    <h2 class="mb-4">🔒 Registro de Preguntas de Seguridad</h2>
                    <p class="mb-4">Por favor, registra {{ $num_questions }} preguntas y respuestas para la recuperación
                        de
                        tu contraseña.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card-body p-0 pt-1">
                        <form action="{{ route('register.preguntasSeguridad') }}" method="POST">
                            @csrf

                            <h5 class="mb-3">Configuración de Seguridad</h5>

                            <div class="row mb-4">
                                @for ($i = 1; $i <= $num_questions; $i++)
                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <div class="border p-3 h-100">
                                            <h6 class="mb-3">Pregunta #{{ $i }}</h6>

                                            {{-- Campo Pregunta --}}
                                            <div class="mb-3">
                                                <label for="pregunta_{{ $i }}" class="form-label">Pregunta:</label>
                                                <input type="text"
                                                    class="form-control @error("pregunta_$i") is-invalid @enderror"
                                                    id="pregunta_{{ $i }}" name="pregunta_{{ $i }}"
                                                    value="{{ old("pregunta_$i") }}" required
                                                    placeholder="Ej: ¿Cuál es el nombre de tu primera mascota?">
                                                @error("pregunta_$i")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Campo Respuesta --}}
                                            <div>
                                                <label for="respuesta_{{ $i }}" class="form-label">Respuesta:</label>
                                                <input type="text"
                                                    class="form-control @error("respuesta_$i") is-invalid @enderror"
                                                    id="respuesta_{{ $i }}" name="respuesta_{{ $i }}" required>
                                                @error("respuesta_$i")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            {{-- Botón de acción --}}
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Preguntas de Seguridad
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>