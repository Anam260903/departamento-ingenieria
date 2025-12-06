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
                                        <small class="text-muted">Datos de: {{ $mesActualTopN }}</small>
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

    {{-- Scripts para Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Datos pasados desde el controlador
            const usoRecursosPorInspector = @json($usoRecursosPorInspector);
            let topRecursosData = @json($topRecursosData);

            let topRecursosChartInstance = null; // Instancia global para el gráfico 5 (Top N)

            // GRÁFICO 4: Uso de recursos por inspector
            const usoRecursosCtx = document.getElementById('usoRecursosChart');
            new Chart(usoRecursosCtx, {
                type: 'bar',
                data: {
                    labels: usoRecursosPorInspector.labels,
                    datasets: usoRecursosPorInspector.datasets.map(dataset => ({
                        label: dataset.label,
                        data: dataset.data,
                        backgroundColor: dataset.backgroundColor,
                        borderColor: '#fff',
                        borderWidth: 1,
                    }))
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right', // Colocar la leyenda a la derecha
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Inspector'
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total de Asignaciones'
                            }
                        }
                    }
                }
            });

            // GRÁFICO 5: Recursos más solicitados (Top N)
            function renderTopRecursosChart(labels, data) {
                const topRecursosCtx = document.getElementById('topRecursosChart');

                if (topRecursosChartInstance) {
                    topRecursosChartInstance.destroy();
                }

                topRecursosChartInstance = new Chart(topRecursosCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Asignaciones',
                            data: data,
                            // Un color consistente para todas las barras del Top N
                            backgroundColor: 'rgba(255, 159, 64, 0.8)',
                            borderColor: 'rgb(255, 159, 64)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: false,
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Frecuencia de Uso'
                                }
                            }
                        }
                    }
                });
            }

            // Renderizar el gráfico inicial (mes actual)
            renderTopRecursosChart(topRecursosData.labels, topRecursosData.data);


            // Manejar el cambio de mes
            document.getElementById('mes-selector').addEventListener('change', function () {
                const mesAno = this.value;
                // Usar la ruta dinámica para obtener los datos del mes seleccionado
                const url = `/decisiones/recursos/top?mes=${mesAno}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const recomendacionElement = document.getElementById('recomendacion-texto');

                        if (data.labels && data.data) {
                            // Actualizar la instancia del gráfico
                            topRecursosChartInstance.data.labels = data.labels;
                            topRecursosChartInstance.data.datasets[0].data = data.data;
                            topRecursosChartInstance.update();

                            // Actualizar la recomendación
                            recomendacionElement.innerHTML = data.recomendacion.replace(/\n/g, '<br>');

                        } else {
                            // Manejo de error si los datos no vienen correctos
                            topRecursosChartInstance.data.labels = ['Sin datos'];
                            topRecursosChartInstance.data.datasets[0].data = [0];
                            topRecursosChartInstance.update();
                            recomendacionElement.innerHTML = "Error al cargar los datos o datos no disponibles para el mes seleccionado.";
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar datos de recursos:', error);
                        alert('Error al cargar datos. Verifique la consola para más detalles.');
                    });
            });

        });
    </script>

    @include('components._session-timeout')
</body>

</html>