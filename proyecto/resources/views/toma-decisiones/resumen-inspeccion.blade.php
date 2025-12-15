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
                <p class="text-muted">Resumen de las inspecciones registradas.</p>

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

                <div class="row g-4">
                    @forelse ($inspecciones as $inspeccion)
                        {{-- Card de Inspección --}}
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm card-insp-clickable" data-id-insp="{{ $inspeccion->id_insp }}"
                                data-bs-toggle="modal" data-bs-target="#resumenModal">

                                <div class="card-body">
                                    {{-- Cabecera con Estado y Fecha --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span
                                            class="badge {{ $inspeccion->estado_insp == 1 ? 'badge-insp-completada' : 'badge-insp-pendiente' }}">
                                            {{ $inspeccion->estado_insp == 1 ? 'Completada' : 'Pendiente' }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i>
                                            {{ \Carbon\Carbon::parse($inspeccion->fecha_insp)->format('d-m-Y') }}
                                        </small>
                                    </div>

                                    {{-- Dirección --}}
                                    <h6 class="card-title text-primary fw-bold mb-1">
                                        <i class="bi bi-house-door me-2"></i>
                                        {{ $inspeccion->vivienda->direccion ?? 'Dirección N/A' }}
                                    </h6>

                                    {{-- Propietario --}}
                                    <p class="card-text small mb-1">
                                        <span class="text-dark fw-bolder"> Propietario:
                                        </span>{{ $inspeccion->vivienda->propietario->nombre_propie ?? 'N/A' }}
                                        {{ $inspeccion->vivienda->propietario->apellido_propie ?? '' }}
                                    </p>

                                </div>
                                {{-- Pie de la Card --}}
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <p class="card-footer-text mb-0">Haga clic para ver el resumen del informe</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                No hay inspecciones registradas para mostrar en el resumen.
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- Modal para mostrar el resumen del informe --}}
            <div class="modal fade" id="resumenModal" tabindex="-1" aria-labelledby="resumenModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="resumenModalLabel">
                                <i class="bi bi-file-text me-2"></i> Resumen del Informe Técnico
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modal-resumen-body">

                            {{-- Spinner de Carga --}}
                            <div id="loading-spinner" class="text-center py-5" style="display:none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>

                            {{-- Mensaje de Error/Advertencia (Pendiente/Sin Informe) --}}
                            <div id="mensaje-error" class="alert alert-warning" style="display:none;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> <span
                                    id="mensaje-error-texto"></span>
                            </div>

                            {{-- Contenido del Informe (estado: completado) --}}
                            <div id="resumen-contenido" style="display:none;">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="bi bi-person-circle me-2"></i>
                                        Ingeniero: <span id="info-ingeniero" class="text-success fw-bold"></span>
                                    </h5>
                                    <small class="text-muted"><i class="bi bi-calendar-check me-1"></i> Fecha de
                                        Informe: <span id="info-fecha"></span></small>
                                </div>

                                <p class="mb-4">
                                    <i class="bi bi-building me-2"></i>
                                    Comunidad: <span id="info-comunidad" class="fw-bold text-dark"></span>
                                </p>

                                <h6 class="text-primary border-bottom pb-1 mt-4">1. Antecedentes</h6>
                                <div class="card card-body bg-light">
                                    <pre id="info-antecedentes" class="mb-0 pre-wrap"></pre>
                                </div>

                                <h6 class="text-primary border-bottom pb-1 mt-4">2. Resultados / Hallazgos</h6>
                                <div class="card card-body bg-light">
                                    <pre id="info-resultados" class="mb-0 pre-wrap"></pre>
                                </div>

                                <h6 class="text-primary border-bottom pb-1 mt-4">3. Recomendaciones</h6>
                                <div class="card card-body bg-light">
                                    <pre id="info-recomendaciones" class="mb-0 pre-wrap"></pre>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            {{-- Botón de Ver PDF Completo --}}
                            <a id="btn-ver-pdf" href="#" target="_blank" class="btn btn-danger" style="display:none;"
                                title="Descargar Informe Completo">
                                <i class="bi bi-file-pdf"></i> Ver PDF Completo
                            </a>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    {{-- Definir la variable global de URL --}}
    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.resumenUrlTemplate = '{{ route('api.inspeccion.resumen', ['inspeccion' => 'PLACEHOLDER']) }}';
        const BASE_PDF_URL = '{{ url('informes/exportar') }}';
    </script>
    <script src="{{ asset('js/decisiones.js') }}"></script>


    @include('components._session-timeout')
</body>

</html>