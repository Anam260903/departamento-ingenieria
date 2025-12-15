<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma de Decisiones</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/decisiones.css') }}">
</head>

<body>
    <div class="d-flex" id="wrapper">

        @include('components._sidebar')

        <div id="page-content-wrapper">

            @include('components._navbar')

            <div class="container-fluid py-4">

                <h1 class="mb-4 h3">TOMA DE DECISIONES</h1>
                <p class="text-muted">Seleccione dos inspecciones para compararlas basándose en criterios de riesgo y
                    logística.</p>

                {{-- Bloque de alertas --}}
                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <strong>Advertencia:</strong> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
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

                {{-- Comparación de inspecciones --}}
                <div id="comparison-tool">

                    <div class="row">

                        {{-- Columna de inspección 1 --}}
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-1-circle me-2"></i> Inspección 1
                                    </h5>
                                </div>
                                <div class="card-body">

                                    {{-- Selector de inspección --}}
                                    <div class="mb-3">
                                        <label for="inspeccion1_id" class="form-label fw-bold">Seleccionar
                                            Inspección:</label>
                                        <select class="form-select" id="inspeccion1_id" name="inspeccion1_id">
                                            <option value="" selected>Seleccione aquí</option>
                                            @foreach($inspecciones as $inspeccion)
                                                <option value="{{ $inspeccion['id'] }}"
                                                    data-fecha="{{ $inspeccion['fecha'] }}"
                                                    data-comunidad="{{ $inspeccion['comunidad'] }}"
                                                    data-propietario="{{ $inspeccion['propietario'] }}">
                                                    ID: {{ $inspeccion['id'] }} - {{ $inspeccion['propietario'] }}
                                                    ({{ $inspeccion['comunidad'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Información de la vivienda 1 --}}
                                    <div id="vivienda_info_1" class="mb-4" style="display: none;">
                                        <h6 class="text-secondary fw-bold">VIVIENDA DE: <span id="propietario_1"
                                                class="text-dark"></span></h6>
                                        <p class="mb-1"><small>Fecha de Inspección: <span id="fecha_1"></span></small>
                                        </p>
                                        <p class="mb-3"><small>Comunidad: <span id="comunidad_1"></span></small></p>

                                        <hr>
                                        <p class="fw-bold text-muted">Checklist de Criterios (Marcar según sea el caso):
                                        </p>

                                        {{-- Checklist para la inspección 1 --}}
                                        {{-- Ponderaciones: 3+5+4+2+1 = Máx 15 --}}
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q1_1" data-weight="3">
                                            <label class="form-check-label" for="q1_1">¿El Informe Técnico ha sido
                                                aprobado?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q2_1" data-weight="5">
                                            <label class="form-check-label" for="q2_1">¿El informe clasifica la vivienda
                                                como de Alto Riesgo Estructural (urgencia)?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q3_1" data-weight="4">
                                            <label class="form-check-label" for="q3_1">¿Los recursos/materiales críticos
                                                requeridos están disponibles?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q4_1" data-weight="2">
                                            <label class="form-check-label" for="q4_1">¿Hay personal con la especialidad
                                                requerida disponible?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q5_1" data-weight="1">
                                            <label class="form-check-label" for="q5_1">¿La ubicación de la vivienda se
                                                encuentra en una zona de alta prioridad de ejecución actual?</label>
                                        </div>
                                        <p class="mt-3"><small class="text-info fw-bold">Puntaje: <span
                                                    id="current_score_1">0</span>/15</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Columna de inspección 2 --}}
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header bg-secondary text-white py-3 rounded-top-4">
                                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-2-circle me-2"></i> Inspección 2
                                    </h5>
                                </div>
                                <div class="card-body">

                                    {{-- Selector de inspección --}}
                                    <div class="mb-3">
                                        <label for="inspeccion2_id" class="form-label fw-bold">Seleccionar
                                            Inspección:</label>
                                        <select class="form-select" id="inspeccion2_id" name="inspeccion2_id">
                                            <option value="" selected>Seleccione aquí</option>
                                            @foreach($inspecciones as $inspeccion)
                                                <option value="{{ $inspeccion['id'] }}"
                                                    data-fecha="{{ $inspeccion['fecha'] }}"
                                                    data-comunidad="{{ $inspeccion['comunidad'] }}"
                                                    data-propietario="{{ $inspeccion['propietario'] }}">
                                                    ID: {{ $inspeccion['id'] }} - {{ $inspeccion['propietario'] }}
                                                    ({{ $inspeccion['comunidad'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Información de la vivienda 2 --}}
                                    {{-- Ponderaciones: 3+5+4+2+1 = Máx 15 --}}
                                    <div id="vivienda_info_2" class="mb-4" style="display: none;">
                                        <h6 class="text-secondary fw-bold">VIVIENDA DE: <span id="propietario_2"
                                                class="text-dark"></span></h6>
                                        <p class="mb-1"><small>Fecha de Inspección: <span id="fecha_2"></span></small>
                                        </p>
                                        <p class="mb-3"><small>Comunidad: <span id="comunidad_2"></span></small></p>

                                        <hr>
                                        <p class="fw-bold text-muted">Checklist de Criterios (Marcar según sea el caso):
                                        </p>

                                        {{-- Checklist para la inspección 2 --}}
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q1_2" data-weight="3">
                                            <label class="form-check-label" for="q1_2">¿El Informe Técnico ha sido
                                                aprobado?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q2_2" data-weight="5">
                                            <label class="form-check-label" for="q2_2">¿El informe clasifica la vivienda
                                                como de Alto Riesgo Estructural (urgencia)?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q3_2" data-weight="4">
                                            <label class="form-check-label" for="q3_2">¿Los recursos/materiales críticos
                                                requeridos están disponibles?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q4_2" data-weight="2">
                                            <label class="form-check-label" for="q4_2">¿Hay personal con la especialidad
                                                requerida disponible?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q5_2" data-weight="1">
                                            <label class="form-check-label" for="q5_2">¿La ubicación de la vivienda se
                                                encuentra en una zona de alta prioridad de ejecución actual?</label>
                                        </div>
                                        <p class="mt-3"><small class="text-info fw-bold">Puntaje: <span
                                                    id="current_score_2">0</span>/15</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Resultados y botón --}}
                    <div class="row mt-4">
                        <div class="col-12 text-center mb-4">
                            <button type="button" class="btn btn-success btn-lg shadow w-auto" id="btn-comparar">
                                <i class="bi bi-check-circle-fill me-2"></i>Comparar y Obtener Recomendación
                            </button>
                        </div>

                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-4">
                                <div class="card-body p-4">
                                    <h4 class="fw-bold">Resultado de Prioridad: <span id="comparison-result"
                                            class="text-secondary">Pendiente</span></h4>
                                    <hr>
                                    <h4 class="fw-bold">Recomendación de Ejecución:</h4>
                                    <p id="comparison-recommendation">Seleccione dos inspecciones, complete el checklist
                                        y presione el botón para realizar la comparación de prioridad.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/comparacion-inspecciones.js') }}"></script>

    @include('components._session-timeout')
</body>

</html>