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
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
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
                            </div>
                            <div class="card-body p-4">
                                <canvas id="historicoChart" style="max-width: 400px; max-height: 350px;"></canvas>
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
    <script src="{{ asset('js/dashboard.js') }}"></script>

    <script
        src="[https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js](https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js)"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Datos pasados desde el controlador
            const historicoData = @json($historicoData);
            const cargaTrabajoData = @json($cargaTrabajoData);
            const disponibilidadData = @json($disponibilidadData);

            // GRÁFICO 1: Carga de trabajo 
            const cargaTrabajoCtx = document.getElementById('cargaTrabajoChart');
            new Chart(cargaTrabajoCtx, {
                type: 'bar',
                data: {
                    labels: cargaTrabajoData.labels,
                    datasets: [{
                        label: 'Inspecciones Pendientes Asignadas',
                        data: cargaTrabajoData.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Asignado'
                            }
                        }
                    }
                }
            });


            // GRÁFICO 2: Seguimiento histórico (Mes Actual vs. Mes Anterior)
            const historicoCtx = document.getElementById('historicoChart');

            new Chart(historicoCtx, {
                type: 'bar',
                data: {
                    labels: historicoData.labels, // Contiene las 3 etiquetas de meses
                    datasets: [
                        {
                            label: 'Inspecciones Completadas',
                            // Data contiene los 3 conteos de los meses
                            data: historicoData.data,
                            backgroundColor: [
                                '#adb5bd', // Gris para Mes - 2 (más antiguo)
                                '#0d6efd', // Azul para Mes - 1
                                '#28a745'  // Verde para Mes Actual
                            ],
                            borderColor: [
                                '#adb5bd',
                                '#0d6efd',
                                '#28a745'
                            ],
                            borderWidth: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            display: false,
                        },
                        title: {
                            display: false
                        },
                        tooltip: {
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Período Mensual'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Número de Inspecciones'
                            }
                        }
                    }
                }
            });

            // GRÁFICO 3: Disponibilidad de personal
            const disponibilidadCtx = document.getElementById('disponibilidadChart');

            new Chart(disponibilidadCtx, {
                type: 'doughnut', // Gráfico de Anillo
                data: {
                    // Los labels ya vienen con los conteos
                    labels: disponibilidadData.labels,
                    datasets: [{
                        label: 'Conteo de Personal',
                        data: disponibilidadData.data,
                        backgroundColor: disponibilidadData.backgroundColor,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });

            // Código para el dropdown de notificaciones
            var toggleButton = document.getElementById('NotificacionToggle');
            if (toggleButton) {
                // Crear una nueva instancia de Dropdown de Bootstrap
                var dropdown = new bootstrap.Dropdown(toggleButton);

                // Agrega un listener de click para manejar el toggle
                toggleButton.addEventListener('click', function (e) {
                    e.preventDefault(); // Previene el comportamiento por defecto del enlace '#'
                    dropdown.toggle();  // Fuerza la acción de mostrar/ocultar
                });
            }


        });
    </script>


    @include('components._session-timeout')
</body>

</html>