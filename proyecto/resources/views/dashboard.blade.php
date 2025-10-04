<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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

                <div class="row mt-4 gy-4 gx-md-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <i class="bi bi-exclamation-circle-fill display-4 text-warning"></i>
                                <h5 class="card-title mt-3">INSPECCIONES PENDIENTES</h5>
                                <p class="display-4 fw-bold">{{ $inspeccionesPendientes }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card text-center shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <i class="bi bi-check-circle-fill display-4 text-success"></i>
                                <h5 class="card-title mt-3">INSPECCIONES COMPLETADAS</h5>
                                <p class="display-4 fw-bold">{{ $inspeccionesCompletadas }}</p>
                            </div>
                        </div>
                    </div>
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

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm p-4">
                            <h5 class="text-center mb-4">Informes realizados por mes</h5>
                            <canvas id="informesChart">
                                data-chart-data="{{ json_encode($datosGrafico) }}"
                                data-chart-labels="{{ json_encode($meses) }}">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>