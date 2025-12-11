<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Técnico - Paso 3</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/informes.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include ('components._sidebar')
        <div id="page-content-wrapper">
            @include('components._navbar')
            <div class="container-fluid py-4">

                {{-- Bloque para mostrar mensajes de Sesión --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <h1 class="mb-4 h3">NUEVO INFORME TÉCNICO</h1>

                {{-- Barra de Progreso --}}
                <div class="step-container">
                    <div class="step completed">1. Datos Generales</div>
                    <div class="step completed">2. Diagnóstico y observaciones</div>
                    <div class="step active">3. Recomendaciones y mapa</div>
                    <div class="step">4. Materiales</div>
                    <div class="step">5. Evidencia fotog.</div>
                </div>

                <div class="card shadow-sm p-4 mt-3">

                    <form action="{{ route('informes.update.step3', $informe->id_inf) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="id_inf" value="{{ $informe->id_inf }}">

                        <div class="row mb-4">
                            {{-- Columna para Recomendaciones --}}
                            <div class="col-md-6">
                                <label for="recomendaciones" class="form-label h5">Recomendaciones</label>
                                <textarea class="form-control @error('recomendaciones') is-invalid @enderror"
                                    id="recomendaciones" name="recomendaciones" rows="10"
                                    placeholder="Detalle las acciones correctivas recomendadas y las observaciones finales."
                                    required>{{ old('recomendaciones', $informe->recomendacion) }}</textarea>

                                @error('recomendaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Columna para mapa y carga de archivo --}}
                            <div class="col-md-6">
                                <label class="form-label h5">Ubicación de la Vivienda</label>

                                {{-- 1. Mapa --}}
                                <div id="map" class="mb-3"></div>

                                <p class="mt-2 mb-1 text-muted small">Arrastre el marcador, ingrese las coordenadas
                                    manualmente o use el buscador.</p>

                                {{-- 2. Campos visibles para entrada manual) --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label for="latitud_input" class="form-label small mb-1">Latitud</label>
                                        <input type="text" class="form-control" id="latitud_input" name="latitud" placeholder="Ej: 10.6698"
                                            value="{{ old('latitud', $informe->inspeccion->vivienda->latitud) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="longitud_input" class="form-label small mb-1">Longitud</label>
                                        <input type="text" class="form-control" id="longitud_input" name="longitud" placeholder="Ej: -63.2573"
                                            value="{{ old('longitud', $informe->inspeccion->vivienda->longitud) }}">
                                    </div>
                                </div>

                                {{-- 3. Campo para subir la imagen del mapa --}}
                                <label for="map_screenshot" class="form-label h5">Captura de Pantalla del Mapa</label>
                                <input type="file" class="form-control @error('map_screenshot') is-invalid @enderror"
                                    id="map_screenshot" name="map_screenshot" accept="image">
                                @if (!($informe->inspeccion->vivienda->map_image_file ?? false)) @endif
                                <p class="text-muted small">Por favor, suba una captura de pantalla del mapa para el
                                    informe PDF. (Archivo actual:
                                    {{ $informe->inspeccion->vivienda->map_image_file ?? 'Ninguno' }})
                                </p>

                                @error('map_screenshot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- 4. Campo oculto para enviar al controlador --}}
                                <input type="hidden" name="map_image_file" id="map_image_file_input"
                                    value="{{ old('map_image_file', $informe->inspeccion->vivienda->map_image_file) }}">
                            </div>

                            {{-- Botones de navegación --}}
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('informes.edit.step2', $informe->id_inf) }}"
                                    class="btn btn-secondary">
                                    Volver
                                </a>
                                <button type="submit" class="btn btn-primary w-auto">
                                    Guardar y Continuar
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="{{ asset('js/informes.js') }}"></script>


    @include('components._session-timeout')
</body>

</html>