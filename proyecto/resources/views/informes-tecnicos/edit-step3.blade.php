<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Informe Técnico - Paso 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

                {{-- Barra de Progreso: El paso 3 debe estar 'active' --}}
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

                            {{-- Columna para Mapa y Carga de Archivo --}}
                            <div class="col-md-6">
                                <label class="form-label h5">Ubicación de la Vivienda</label>

                                {{-- 1. Mapa --}}
                                <div id="map" class="mb-3"></div>

                                <p class="mt-2 mb-1 text-muted small">Arrastre el marcador, ingrese las coordenadas
                                    manualmente o use el buscador.</p>

                                {{-- 2. Campos Visibles para Entrada Manual (y Display) --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label for="latitud_manual" class="form-label small mb-1">Latitud</label>
                                        <input type="text" class="form-control" id="latitud_manual"
                                            placeholder="Ej: 10.6698"
                                            value="{{ old('latitud', $informe->inspeccion->vivienda->latitud) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="longitud_manual" class="form-label small mb-1">Longitud</label>
                                        <input type="text" class="form-control" id="longitud_manual"
                                            placeholder="Ej: -63.2573"
                                            value="{{ old('longitud', $informe->inspeccion->vivienda->longitud) }}">
                                    </div>
                                </div>

                                {{-- 3. Campo para subir la imagen del mapa --}}
                                <label for="map_screenshot" class="form-label h5">Captura de Pantalla del Mapa</label>
                                <input type="file" class="form-control @error('map_screenshot') is-invalid @enderror"
                                    id="map_screenshot" name="map_screenshot" accept="image/*" {{-- Hacemos la subida de
                                    archivo opcional si ya existe uno guardado --}} @if (!($informe->inspeccion->vivienda->map_image_file ?? false)) required @endif>
                                <p class="text-muted small">Por favor, suba una captura de pantalla del mapa para el
                                    informe PDF. (Archivo actual:
                                    {{ $informe->inspeccion->vivienda->map_image_file ?? 'Ninguno' }})
                                </p>

                                @error('map_screenshot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- 4. Campos OCULTOS para enviar al controlador (siempre deben estar) --}}
                                <input type="hidden" name="latitud" id="latitud_input"
                                    value="{{ old('latitud', $informe->inspeccion->vivienda->latitud) }}">
                                <input type="hidden" name="longitud" id="longitud_input"
                                    value="{{ old('longitud', $informe->inspeccion->vivienda->longitud) }}">
                                <input type="hidden" name="map_image_file" id="map_image_file_input"
                                    value="{{ old('map_image_file', $informe->inspeccion->vivienda->map_image_file) }}">
                            </div>

                            {{-- Botones de Navegación --}}
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        // Variables globales (accesibles por todas las funciones)
        let map;
        let marker;

        // Referencias a los campos HTML
        const latInput = document.getElementById('latitud_input');
        const lonInput = document.getElementById('longitud_input');
        const latManual = document.getElementById('latitud_manual');
        const lonManual = document.getElementById('longitud_manual');

        // Coordenadas iniciales (usando las guardadas o por defecto a Carúpano)
        const defaultLat = parseFloat(latInput.value) || 10.6698;
        const defaultLon = parseFloat(lonInput.value) || -63.2573;
        const initialLocation = [defaultLat, defaultLon];

        // =======================================================
        // FUNCIÓN DE UTILIDAD: Sincroniza el mapa con los inputs HTML
        // =======================================================
        const updateCoordinates = (lat, lng) => {
            // 1. Actualiza los campos visibles (manuales)
            latManual.value = lat.toFixed(8);
            lonManual.value = lng.toFixed(8);

            // 2. Actualiza los campos ocultos (los que se envían al servidor)
            latInput.value = lat.toFixed(8);
            lonInput.value = lng.toFixed(8);
        };

        // =======================================================
        // FUNCIÓN: Mueve el mapa al ingresar coordenadas manualmente
        // =======================================================
        const updateMapFromManualInput = () => {
            const lat = parseFloat(latManual.value);
            const lng = parseFloat(lonManual.value);

            // Validación básica
            if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                console.error("Coordenadas ingresadas no válidas.");
                return;
            }

            const newLocation = [lat, lng];

            // 1. Mover el marcador y centrar el mapa
            marker.setLatLng(newLocation);

            // 2. Centrar el mapa (Mantiene el zoom si es alto, sino usa 18)
            map.setView(newLocation, map.getZoom() > 10 ? map.getZoom() : 18);

            // 3. Actualizar los campos ocultos
            updateCoordinates(lat, lng);
        };

        // =======================================================
        // FUNCIÓN PRINCIPAL: Inicializa el mapa y todos sus controles
        // =======================================================
        function initMap() {
            // Inicializa el mapa y lo centra en la ubicación inicial con un buen zoom (18)
            map = L.map('map').setView(initialLocation, 18);

            // DEFINICIÓN DE CAPAS BASE (Calles y Satelital)
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 22,
                attribution: '© OpenStreetMap contributors'
            });

            const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 22,
                attribution: 'Tiles © Esri &mdash; Source: Esri...'
            });

            // Añadir la capa de Calles (OSM) por defecto
            osmLayer.addTo(map);

            // OBJETO DE MAPAS BASE PARA EL CONTROL
            const baseMaps = {
                "Calles (OSM)": osmLayer,
                "Satélite (Esri)": esriLayer
            };

            // MARCACIÓN Y EVENTOS
            marker = L.marker(initialLocation, { draggable: true }).addTo(map);

            // Evento al arrastrar el marcador
            marker.on('dragend', function (e) {
                const coords = marker.getLatLng();
                updateCoordinates(coords.lat, coords.lng);
            });

            // Evento al hacer clic en el mapa
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });

            // Inicializar los displays con las coordenadas por defecto/guardadas
            updateCoordinates(defaultLat, defaultLon);

            // CONTROL DE GEOCODER (Buscador)
            L.Control.geocoder({
                defaultMarkGeocode: false,
                geocoder: L.Control.Geocoder.nominatim(),
                position: 'topleft',
            })
                .on('markgeocode', function (e) {
                    const center = e.geocode.center;
                    map.fitBounds(e.geocode.bbox);
                    marker.setLatLng(center);
                    updateCoordinates(center.lat, center.lng);
                })
                .addTo(map);

            // CONTROL DE CAPAS (Selector Satélite/Calles)
            L.control.layers(baseMaps).addTo(map);

            // Asegura que el mapa se renderice correctamente al cargar la página
            setTimeout(function () {
                map.invalidateSize();
            }, 300);
        }

        // LISTENER PRINCIPAL: Inicializa el mapa y agrega listeners a los inputs cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function () {
            initMap(); // Inicializa el mapa

            // Agregar listeners para la entrada manual
            latManual.addEventListener('change', updateMapFromManualInput);
            lonManual.addEventListener('change', updateMapFromManualInput);
        });
    </script>
</body>

</html>