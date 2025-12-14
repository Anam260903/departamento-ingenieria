<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Preguntas de Seguridad</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    </head>

<body>
    @include('components._navbar')

    <div class="d-flex" id="wrapper">
        @include('components._sidebar')

        <div id="page-content-wrapper">
            <div class="container-fluid py-4">
                <h1 class="mt-4 h3">ACTUALIZAR PREGUNTAS DE SEGURIDAD</h1>
                <p>Estás actualizando tus preguntas. Por favor, registra {{ $num_questions }} preguntas y respuestas nuevas.</p>

                {{-- Mensajes de sesión --}}
                @php
                    $mensaje = session('status') ?? session('success');
                @endphp

                @if($mensaje)
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $mensaje }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif 
                
                {{-- Formulario para actualizar preguntas --}}
                <div class="card shadow-sm p-4 mt-4">
                    <form method="POST" action="{{ route('seguridad.actualizarPreguntas') }}">
                        @csrf
                        
                        @for ($i = 1; $i <= $num_questions; $i++)
                            <div class="mb-4">
                                <h5 class="mb-3">Pregunta #{{ $i }}</h5>
                                <div class="mb-3">
                                    <label for="pregunta_{{ $i }}" class="form-label">Pregunta:</label>
                                    <input type="text" class="form-control @error("pregunta_$i") is-invalid @enderror"
                                        id="pregunta_{{ $i }}" name="pregunta_{{ $i }}" required>
                                    @error("pregunta_$i")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="respuesta_{{ $i }}" class="form-label">Respuesta:</label>
                                    <input type="text" class="form-control @error("respuesta_$i") is-invalid @enderror"
                                        id="respuesta_{{ $i }}" name="respuesta_{{ $i }}" required>
                                    @error("respuesta_$i")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                        @endfor

                        <div class="d-flex justify-content-end gap-2">
                            {{-- Botón para volver al perfil --}}
                            <a href="{{ route('perfil') }}" class="btn btn-secondary">Cancelar</a>
                        
                            {{-- Botón Guardar --}}
                            <button type="submit" class="btn btn-primary w-auto">Guardar Nuevas Preguntas de Seguridad</button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    @include('components._session-timeout')
</body>
</html>