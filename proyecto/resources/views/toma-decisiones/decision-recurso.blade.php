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
                <p class="text-muted">Información crítica para la gestión de equipos, inventario y planificación de
                    compras futuras.</p>

                {{-- Bloque de alertas --}}
                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <strong>Advertencia:</strong> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Recomendación --}}
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="card shadow-lg border-primary border-2 rounded-4">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-lightbulb-fill me-2"></i>
                                    Recomendación de Logística
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div id="recomendacion-texto">
                                    {!! nl2br(e($recomendacion)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico 4: Uso de recursos por inspector --}}
                <div class="row">
                    <div class="col-12 mb-5">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-dark fw-bold"><i
                                        class="bi bi-grid-3x3-gap-fill me-2"></i>Uso de Recursos por Inspector</h5>
                                <small class="text-muted">Distribución de los tipos de recursos asignados a cada
                                    ingeniero. Cada segmento representa un recurso
                                    ({{ count($usoRecursosPorInspector['datasets']) }} recursos diferentes
                                    registrados).</small>

                                {{-- Botón para descargar gráfico 4 --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="descargarUsoRecursosToggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download"></i> Descargar
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="descargarUsoRecursosToggle">
                                        <li><a class="dropdown-item" href="#" data-chart-id="usoRecursosChart"
                                                data-format="png">Descargar como PNG</a></li>
                                        <li><a class="dropdown-item" href="#" data-chart-id="usoRecursosChart"
                                                data-format="jpg">Descargar como JPG</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="card-body p-4">
                                <canvas id="usoRecursosChart" style="max-height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Gráfico 5: Recursos más solicitados (Top N) --}}
                    <div class="col-12 mb-5">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-dark fw-bold"><i class="bi bi-star-fill me-2"></i>
                                    Top 10 Recursos Más Solicitados</h5>
                                <small class="text-muted">Identifique los recursos de alta demanda para gestión de
                                    inventario y compras.</small>

                                {{-- Botón para descargar gráfico 5 --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="descargarTopRecursosToggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download"></i> Descargar
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="descargarTopRecursosToggle">
                                        <li><a class="dropdown-item" href="#" data-chart-id="topRecursosChart"
                                                data-format="png">Descargar como PNG</a></li>
                                        <li><a class="dropdown-item" href="#" data-chart-id="topRecursosChart"
                                                data-format=" jpg">Descargar como JPG</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="card-body p-4">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-4">
                                        <label for="mes-selector" class="form-label fw-bold">Seleccionar mes de
                                            análisis:</label>
                                        <select id="mes-selector" class="form-select shadow-sm">
                                            @foreach ($mesesDisponibles as $mes)
                                                <option value="{{ $mes['value'] }}" @if($mes['selected']) selected @endif>
                                                    {{ $mes['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted" id="mes-display-label">Datos de: {{ $mesActualTopN }}</small>
                                    </div>
                                </div>
                                <canvas id="topRecursosChart" style="max-height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        window.usoRecursosPorInspector = @json($usoRecursosPorInspector);
        window.topRecursosData = @json($topRecursosData);
        window.mesActualTopN = "{{ $mesActualTopN }}"; 
    </script>

    <script src="{{ asset('js/decisiones.js') }}"></script>

    @include('components._session-timeout')
</body>

</html>