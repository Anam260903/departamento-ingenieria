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

                <h1 class="mb-4 h3">COMPARACIÓN DE PRIORIDADES DE INSPECCIÓN</h1>
                <p class="text-muted">Seleccione dos inspecciones para compararlas basándose en criterios de riesgo y
                    logística.</p>

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

                <div class="row g-4">
                    <form id="comparacionForm" class="bg-white p-4 rounded-4 shadow-lg">
                        @csrf

                        <div class="row mb-5">
                            {{-- Columna 1: Inspección A --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <h3 class="mb-3 text-primary fw-bold">Inspección A (Proyecto 1)</h3>

                                <label for="inspeccion1_id" class="form-label fw-bold">Seleccionar Inspección 1:</label>
                                <select id="inspeccion1_id" name="inspeccion1_id" class="form-select mb-4 shadow-sm"
                                    required>
                                    <option value="" disabled selected>Seleccione aquí</option>
                                    @foreach ($inspecciones as $inspeccion)
                                        <option value="{{ $inspeccion['id'] }}" data-nombre="{{ $inspeccion['vivienda_nombre'] }}">
                                        {{ $inspeccion['vivienda_nombre'] }} ({{ $inspeccion['codigo_inspeccion'] }})</option>
                                    @endforeach
                                </select>

                                <div id="detalle1"
                                    class="mb-4 text-muted border-start border-3 ps-3 p-2 bg-light rounded">
                                    <p class="fw-bold mb-1 fs-5 text-dark" id="nombre1_display">VIVIENDA DE: <span
                                            class="text-secondary">...</span></p>
                                    <p class="mb-0">Fecha: <span id="fecha1_display">--/--/----</span></p>
                                    <p>Comunidad: <span id="comunidad1_display">...</span></p>
                                </div>

                                <input type="hidden" id="nombre1_hidden" name="nombre1">

                                <div id="criterios1" class="p-3 border rounded">
                                    <p class="text-center text-muted">Seleccione una inspección para cargar el
                                        checklist.</p>
                                </div>
                            </div>

                            {{-- Columna 1: Inspección B --}}
                            <div class="col-md-6 ps-md-4">
                                <h3 class="mb-3 text-success fw-bold">Inspección B (Proyecto 2)</h3>

                                <label for="inspeccion2_id" class="form-label fw-bold">Seleccionar Inspección 2:</label>
                                <select id="inspeccion2_id" name="inspeccion2_id" class="form-select mb-4 shadow-sm"
                                    required>
                                    <option value="" disabled selected>Seleccione aquí</option>
                                    @foreach ($inspecciones as $inspeccion)
                                        <option value="{{ $inspeccion['id'] }}" data-nombre="{{ $inspeccion['vivienda_nombre'] }}">
                                        {{ $inspeccion['vivienda_nombre'] }} ({{ $inspeccion['codigo_inspeccion'] }})</option>
                                    @endforeach
                                </select>

                                <div id="detalle2"
                                    class="mb-4 text-muted border-start border-3 ps-3 p-2 bg-light rounded">
                                    <p class="fw-bold mb-1 fs-5 text-dark" id="nombre2_display">VIVIENDA DE: <span
                                            class="text-secondary">...</span></p>
                                    <p class="mb-0">Fecha: <span id="fecha2_display">--/--/----</span></p>
                                    <p>Comunidad: <span id="comunidad2_display">...</span></p>
                                </div>

                                <input type="hidden" id="nombre2_hidden" name="nombre2">

                                <div id="criterios2" class="p-3 border rounded">
                                    <p class="text-center text-muted">Seleccione una inspección para cargar el
                                        checklist.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Botón de comparación y secciones de resultado --}}
                        <div class="text-center mt-4 pt-4 border-top">
                            <button type="submit" id="btnComparar" class="btn btn-primary  px-5 shadow-lg"
                                disabled>
                                <i class="bi bi-check-circle me-2"></i> Comparar Prioridades
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Modal de resultados y recomendación --}}
                <div class="modal fade" id="resultadoModal" tabindex="-1" aria-labelledby="resultadoModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content rounded-4 shadow-lg">
                            <div class="modal-header bg-dark text-white rounded-top-4">
                                <h5 class="modal-title" id="resultadoModalLabel"><i
                                        class="bi bi-bar-chart-fill me-2"></i> Resultado de Priorización</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body p-4">
                                <h4 class="text-primary mb-3" id="resultadoTexto">Resultado:</h4>

                                <div id="detallePuntuacion" class="mt-4 p-3 bg-light rounded border">
                                    <p class="mb-1 fw-bold text-primary">Puntuación Inspección A: <span
                                            id="nombreFinal1"></span> (<span id="puntuacionFinal1"
                                            class="badge bg-primary fs-6">0</span> pts)</p>
                                    <p class="mb-0 fw-bold text-success">Puntuación Inspección B: <span
                                            id="nombreFinal2"></span> (<span id="puntuacionFinal2"
                                            class="badge bg-success fs-6">0</span> pts)</p>
                                </div>

                                <p class="fw-bold border-bottom pb-2 mt-4 text-dark">Recomendación:</p>
                                <p id="recomendacionTexto" class="alert alert-info border-info"></p>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    <script>
        // Pasar los criterios para el renderizado dinámico y el cálculo
        window.criterios = @json($criterios);
        // Definir las rutas AJAX
        window.urlDetalle = "{{ route('decisiones.comparar.detalle') }}";
        window.urlProcesar = "{{ route('decisiones.comparar.procesar') }}";
    </script>

    <script src="{{ asset('js/decisiones.js') }}"></script>


    @include('components._session-timeout')
</body>

</html>