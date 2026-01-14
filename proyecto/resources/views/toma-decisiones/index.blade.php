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

                {{-- Botones de acción --}}
                <div class="row justify-content-end mb-3">
                    {{-- Botón ver resumen de inspecciones --}}
                    <div class="col-auto">
                        {{-- Verifica si el usuario autenticado tiene id_rol igual a 1 (Administrador) --}}
                        @if (auth()->check() && auth()->user()->id_rol === 1)
                            <a href="{{ route('decisiones.resumenInspeccion') }}" class="btn btn-primary text-nowrap">
                                <i class="bi bi-list-columns-reverse me-2"></i> Resumen de Inspecciones
                            </a>
                        @endif
                    </div>
                    {{-- Botón para herramienta de comparación --}}
                    <div class="col-auto">
                        {{-- Verifica si el usuario autenticado tiene id_rol igual a 1 (Administrador) --}}
                        @if (auth()->check() && auth()->user()->id_rol === 1)
                            <a href="{{ route('decisiones.comparacion') }}" class="btn btn-success text-nowrap">
                                <i class="bi bi-tools me-2"></i> Herramienta de Comparación de Inspecciones
                            </a>
                        @endif
                    </div>
                    {{-- Botón para recursos --}}
                    <div class="col-auto">
                        {{-- Verifica si el usuario autenticado tiene id_rol igual a 1 (Administrador) --}}
                        @if (auth()->check() && auth()->user()->id_rol === 1)
                            <a href="{{ route('decisiones.recursos') }}" class="btn btn-danger text-nowrap">
                                <i class="bi bi-boxes me-2"></i> Recursos
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Gráfico 1: Carga de trabajo --}}
                <div class="row">
                    <div class="col-12 mb-5">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-dark fw-bold"><i
                                        class="bi bi-person-lines-fill me-2"></i> Carga de Trabajo por Inspector</h5>
                                <small class="text-muted">Inspecciones pendientes asignadas. Útil para
                                    reasignación.</small>

                                {{-- Botón para descargar gráfico1 --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="descargarCargaToggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download"></i> Descargar
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="descargarCargaToggle">
                                        <li><a class="dropdown-item" href="#" data-chart-id="cargaTrabajoChart"
                                                data-format="png">Descargar como PNG</a></li>
                                        <li><a class="dropdown-item" href="#" data-chart-id="cargaTrabajoChart"
                                                data-format="jpg">Descargar como JPG</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="card-body p-4">
                                <canvas id="cargaTrabajoChart" style="max-height: 350px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    {{-- Gráfico 2: Seguimiento histórico --}}
                    <div class="col-lg-6 col-md-12 mb-5">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-dark fw-bold"><i class="bi bi-graph-up me-2"></i>
                                    Inspecciones Completadas </h5>
                                <small class="text-muted">Comparación mensual vs. Período anterior.</small>

                                {{-- Botón para descargar gráfico 2 --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="descargarHistoricoToggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download"></i> Descargar
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="descargarHistoricoToggle">
                                        <li><a class="dropdown-item" href="#" data-chart-id="historicoChart"
                                                data-format="png">Descargar como PNG</a></li>
                                        <li><a class="dropdown-item" href="#" data-chart-id="historicoChart"
                                                data-format="jpg">Descargar como JPG</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="card-body p-4">
                                <canvas id="historicoChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Gráfico 3: Disponibilidad del personal --}}
                    <div class="col-lg-6 col-md-12 mb-5">
                        <div class="card shadow-lg border-0 rounded-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title mb-0 text-dark fw-bold"><i class="bi bi-speedometer me-2"></i>
                                    Disponibilidad de Personal</h5>
                                <small class="text-muted">Porcentaje de inspectores disponibles para nuevas
                                    asignaciones.</small>

                                {{-- Botón para descargar gráfico 3 --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="descargarDisponibilidadToggle" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bi bi-download"></i> Descargar
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="descargarDisponibilidadToggle">
                                        <li><a class="dropdown-item" href="#" data-chart-id="disponibilidadChart"
                                                data-format="png">Descargar como PNG</a></li>
                                        <li><a class="dropdown-item" href="#" data-chart-id="disponibilidadChart"
                                                data-format="jpg">Descargar como JPG</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="card-body p-4 d-flex justify-content-center">
                                <canvas id="disponibilidadChart" style="max-width: 400px; max-height: 350px;"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        window.historicoData = @json($historicoData);
        window.cargaTrabajoData = @json($cargaTrabajoData);
        window.disponibilidadData = @json($disponibilidadData);
    </script>

    <script src="{{ asset('js/decisiones.js') }}"></script>


    @include('components._session-timeout')
</body>

</html>