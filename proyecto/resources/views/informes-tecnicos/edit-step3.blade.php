<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Informe Técnico - Paso 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/informes.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAo9TchM4yS15fF7aHqL7YhR0S2T5S7bL2K0Lw0=" crossorigin="" />
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
                    <div class="step active">2. Diagnóstico y observaciones</div>
                    <div class="step">3. Recomendaciones</div>
                    <div class="step">4. Materiales y calc.</div>
                    <div class="step">5. Evidencia fotog.</div>
                </div>

                <div class="card shadow-sm p-4 mt-3">
                    {{-- CRÍTICO: Añadir enctype para permitir la subida de archivos --}}
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

                                <div id="map" class="mb-3"></div>

                                <p class="mt-2 mb-1 text-muted small">Arrastre el marcador o haga clic en el mapa para
                                    fijar la
                                    ubicación.</p>

                                {{-- Campos para mostrar y enviar Coordenadas --}}
                                <div class="input-group mt-2 mb-3">
                                    <span class="input-group-text">Lat/Lon</span>
                                    <input type="text" class="form-control" id="latitud_display" readonly>
                                    <input type="text" class="form-control" id="longitud_display" readonly>
                                </div>

                                {{-- Campo para subir la imagen del mapa --}}
                                <label for="map_screenshot" class="form-label h5">Captura de Pantalla del Mapa</label>
                                <input type="file" class="form-control @error('map_screenshot') is-invalid @enderror"
                                    id="map_screenshot" name="map_screenshot" accept="image/*" required>
                                <p class="text-muted small">Por favor, suba una captura de pantalla del mapa para el
                                    informe PDF.</p>

                                @error('map_screenshot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Campos OCULTOS para enviar al controlador --}}
                                <input type="hidden" name="latitud" id="latitud_input"
                                    value="{{ old('latitud', $informe->inspeccion->vivienda->latitud) }}">
                                <input type="hidden" name="longitud" id="longitud_input"
                                    value="{{ old('longitud', $informe->inspeccion->vivienda->longitud) }}">
                            </div>
                        </div>

                        {{-- Botones de Navegación --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('informes.edit.step2', $informe->id_inf) }}" class="btn btn-secondary">
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-2KTo0W2Fv1E8tWv+Yk2r8V1v8n/E0A7C2B4z6U01R0=" crossorigin=""></script>

    <script>
        // Variables globales para el mapa y el marcador
        let map;
        let marker;

        // Coordenadas iniciales (si ya existen o por defecto a Carúpano, Venezuela)
        const latInput = document.getElementById('latitud_input');
        const lonInput = document.getElementById('longitud_input');

        const defaultLat = parseFloat(latInput.value) || 10.6698;
        const defaultLon = parseFloat(lonInput.value) || -63.2573;

        const initialLocation = [defaultLat, defaultLon];

        function initMap() {
            map = L.map('map').setView(initialLocation, 15); // Inicializa el mapa

            // Agrega la capa base de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Agrega el marcador
            marker = L.marker(initialLocation, { draggable: true }).addTo(map);

            // Función para actualizar las coordenadas
            const updateCoordinates = (lat, lng) => {
                latInput.value = lat.toFixed(8);
                lonInput.value = lng.toFixed(8);
                document.getElementById('latitud_display').value = 'Lat: ' + lat.toFixed(8);
                document.getElementById('longitud_display').value = 'Lon: ' + lng.toFixed(8);
            };

            // Evento al arrastrar el marcador
            marker.on('dragend', function (e) {
                const coords = marker.getLatLng();
                updateCoordinates(coords.lat, coords.lng);
            });

            // Evento al hacer clic en el mapa (opcional: mueve el marcador al punto de clic)
            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });

            // Inicializar los displays con las coordenadas por defecto/guardadas
            updateCoordinates(defaultLat, defaultLon);

            setTimeout(function () {
                map.invalidateSize();
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>

</html>