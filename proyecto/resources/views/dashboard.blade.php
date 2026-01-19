<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
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
            <div class="container-fluid">
                <h1 class="mt-4 h3">INICIO</h1>

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

                {{-- Panel de inicio--}}
                <div class="row mt-4 gy-4 gx-md-4">
                    <div class="col-12 col-md-6 col-lg-4">

                        {{-- Card inspecciones pendientes --}}
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <i class="bi bi-exclamation-circle-fill display-4 text-warning"></i>
                                <h5 class="card-title mt-3">INSPECCIONES PENDIENTES</h5>
                                <p class="display-4 fw-bold">{{ $inspeccionesPendientes }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Card inspeccioes completadas --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <i class="bi bi-check-circle-fill display-4 text-success"></i>
                                <h5 class="card-title mt-3">INSPECCIONES COMPLETADAS</h5>
                                <p class="display-4 fw-bold">{{ $inspeccionesCompletadas }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Card total de informes --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <i class="bi bi-journal-text display-4 text-primary"></i>
                                <h5 class="card-title mt-3">TOTAL DE INFORMES</h5>
                                <p class="display-4 fw-bold">{{ $totalInformes }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico informes por mes --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Informes realizados por mes</h5>

                                <div class="d-flex gap-2">
                                    <form action="{{ route('dashboard') }}" method="GET" id="yearForm">
                                        <select name="anio" class="form-select form-select-sm"
                                            onchange="document.getElementById('yearForm').submit()">
                                            @foreach($aniosDisponibles as $anio)
                                                <option value="{{ $anio }}" {{ $anioSeleccionado == $anio ? 'selected' : '' }}>
                                                    Año {{ $anio }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="descargarGraficoDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-download"></i> Descargar
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="descargarGraficoDropdown">
                                            <li><a class="dropdown-item" href="#" id="descargarPng">Descargar como PNG</a>
                                            </li>
                                            <li><a class="dropdown-item" href="#" id="descargarJpg">Descargar como JPG</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <canvas id="informesChart" data-chart-data="{{ json_encode($datosGrafico) }}"
                                data-chart-labels="{{ json_encode($meses) }}">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    @include('components._session-timeout')
</body>

</html>