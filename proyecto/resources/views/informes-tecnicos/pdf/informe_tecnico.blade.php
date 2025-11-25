<!DOCTYPE html>
<html>

<head>
    <title>Informe Técnico N° {{ $informe->id_inf }}</title>
    <link href="{{public_path('css/informes.css')}}" rel="stylesheet">
</head>

<body>

    {{-- PÁGINA PRINCIPAL --}}

    {{-- Encabezado --}}
    <div class="header-container">

        {{-- Logo izquierdo --}}
        <div class="logo-col">
            <img src="{{ public_path('images/logo2.jpg') }}" class="logo-img">
        </div>

        {{-- Texto centrado --}}
        <div class="text-col">
            <p>REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
            <p>GOBIERNO BOLIVARIANO DEL ESTADO SUCRE</p>
            <p>CORPORACION DE VIVIENDA DEL ESTADO SUCRE</p>
            <p>(CORVISUCRE)</p>
            <p>RIF: G-200164492</p>
            <p>CARÚPANO - ESTADO SUCRE</p>
        </div>

        {{-- Logo derecho --}}
        <div class="logo-col" style="text-align: right;">
            <img src="{{ public_path('images/logo3.jpg') }}" class="logo-img">
        </div>
    </div>


    {{-- Línea roja de separación --}}
    <div class="separator-line"></div>

    {{-- Títulos del informe --}}

    <div class="report-title">INFORME DE INSPECCIÓN TÉCNICA N° {{ $informe->id_inf }}</div>
    <div class="report-subtitle">VIVIENDA {{ $informe->inspeccion->vivienda->propietario->nombre_propie ?? 'N/A' }}
        {{ $informe->inspeccion->vivienda->propietario->apellido_propie ?? 'N/A' }}

    </div>

    {{-- Datos generales --}}
    <table class="data-table">
        <tr>
            <td class="label">PROFESIONAL(ES) ASIGNADO(S):</td>
            <td class="data-content">
                Ing. {{ $informe->inspeccion->usuario->nombre ?? 'N/A' }}
                {{ $informe->inspeccion->usuario->apellido ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td class="label">FECHA DE LA INSPECCIÓN:</td>
            <td class="data-content">
                {{ \Carbon\Carbon::parse($informe->inspeccion->fecha_insp)->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <td class="full-row-label">NOMBRE DE LA COMUNIDAD Y/O PROYECTO:</td>
            <td colspan="3" class="full-row-content">
                {{ $informe->comunidad ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td class="label">RESPONSABLE:</td>
            <td class="data-content">
                {{ $informe->inspeccion->vivienda->propietario->nombre_propie ?? 'N/A' }}
                {{ $informe->inspeccion->vivienda->propietario->apellido_propie ?? 'N/A' }}
            </td>
            <td class="label">C.I:</td>
            <td class="data-content">{{ $informe->inspeccion->vivienda->propietario->cedula_propie ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">DIRECCIÓN:</td>
            <td colspan="2" class="data-content">
                {{ $informe->inspeccion->vivienda->direccion ?? 'N/A' }}
            </td>
            <td class="label">TLF:</td>
            <td class="data-content">{{ $informe->inspeccion->vivienda->propietario->telefono ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Contenido de la inspección --}}
    <div class="section-title">1. ANTECEDENTES</div>
    <div class="content-box">
        {{ $informe->antecedentes ?? 'No se proporcionaron antecedentes.' }}
    </div>

    <div class="section-title">2. PLANTEAMIENTO DEL PROBLEMA</div>
    <div class="content-box">
        {{ $informe->planteamiento ?? 'No se proporcionó planteamiento del problema.' }}
    </div>

    <div class="section-title">3. CARACTERÍSTICAS DE LA VIVIENDA</div>
    <div class="content-box">
        {{ $informe->inspeccion->vivienda->caracteristicas ?? 'No se proporcionaron características de la vivienda.' }}
    </div>

    <div class="section-title">4. RESULTADOS DE LA INSPECCIÓN</div>
    <div class="content-box">
        {{ $informe->resultados ?? 'No se proporcionaron resultados de la inspección.' }}
    </div>

    <div class="section-title">5. RECOMENDACIONES</div>
    <div class="content-box">
        {{ $informe->recomendacion ?? 'No se proporcionaron recomendaciones.' }}
    </div>

    <div class="section-title">6. MATERIALES</div>
    <div class="content-box">
        {{ $informe->materials_info ?? 'No se proporcionaron materiales.' }}
    </div>

    {{-- NÚMERO DE PÁGINA --}}


    {{-- MEMORIA FOTOGRÁFICA --}}

    <div class="page-break"></div>

    {{-- Encabezado --}}
    <div class="header-container">

        {{-- Logo izquierdo --}}
        <div class="logo-col">
            <img src="{{ public_path('images/logo2.jpg') }}" class="logo-img">
        </div>

        {{-- Texto cntrado --}}
        <div class="text-col">
            <p>REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
            <p>GOBIERNO BOLIVARIANO DEL ESTADO SUCRE</p>
            <p>CORPORACION DE VIVIENDA DEL ESTADO SUCRE</p>
            <p>(CORVISUCRE)</p>
            <p>RIF: G-200164492</p>
            <p>CARÚPANO - ESTADO SUCRE</p>
        </div>

        {{-- Logo derecho --}}
        <div class="logo-col" style="text-align: right;">
            <img src="{{ public_path('images/logo3.jpg') }}" class="logo-img">
        </div>
    </div>


    {{-- Línea roja de separación --}}
    <div class="separator-line"></div>

    {{-- Título --}}

    <div class="report-title">MEMORIA FOTOGRÁFICA</div>

    {{-- Imagénes --}}
    @if ($informe->imagenes->count() > 0)
        <div class="photo-grid">
            @foreach ( $informe->imagenes as $index => $imagen)
                        @php

                $storagePath = storage_path('app/public/' . $imagen->ruta_archivo);
                $imageData = '';
                if (file_exists($storagePath)) {
                    $imageData = 'data:image/' . pathinfo($storagePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($storagePath));
                }
                        @endphp

                        <div class="photo-item">
                            @if ($imageData)
                                <img src="{{ $imageData }}" alt="Foto de Evidencia">
                            @else
                                <p>[Imagen no encontrada]</p>
                            @endif
                        </div>

                        {{-- ⚠️ Lógica para forzar el corte de página después de la imagen #6, #12, #18, etc. --}}
                        @if (($index + 1) % 6 === 0)
                            <div class="page-break"></div>
                        @endif
            @endforeach
            <div style="clear: both;"></div>
        </div>
    @else
        <p>No se adjuntaron imágenes a este informe.</p>
    @endif

    {{-- NÚMERO DE PÁGINA --}}


    {{-- MAPA --}}

    <div class="page-break"></div>

    {{-- Encabezado --}}
    <div class="header-container">

        {{-- Logo izquierdo --}}
        <div class="logo-col">
            <img src="{{ public_path('images/logo2.jpg') }}" class="logo-img">
        </div>

        {{-- Texto centrado --}}
        <div class="text-col">
            <p>REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
            <p>GOBIERNO BOLIVARIANO DEL ESTADO SUCRE</p>
            <p>CORPORACION DE VIVIENDA DEL ESTADO SUCRE</p>
            <p>(CORVISUCRE)</p>
            <p>RIF: G-200164492</p>
            <p>CARÚPANO - ESTADO SUCRE</p>
        </div>

        {{-- Logo derecho --}}
        <div class="logo-col" style="text-align: right;">
            <img src="{{ public_path('images/logo3.jpg') }}" class="logo-img">
        </div>
    </div>


    {{-- Línea roja de separación --}}
    <div class="separator-line"></div>

    {{-- Titulo --}}
    <div class="report-title">CROQUIS DE UBICACIÓN DEL TERRENO</div>

    @php
// Accedemos a los datos de vivienda a través de la relación de inspección
$vivienda = $informe->inspeccion->vivienda;
$lat = $vivienda->latitud ?? 'N/A';
$long = $vivienda->longitud ?? 'N/A';

// Obtenemos la imagen del mapa
$mapStoragePath = storage_path('app/public/' . $vivienda->map_image_file);
$mapImageData = '';
if (file_exists($mapStoragePath)) {
    $mapImageData = 'data:image/' . pathinfo($mapStoragePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($mapStoragePath));
}


    @endphp

    {{-- Datos relacionados con el mapa--}}

    <div class="section-title">DETALLES DE UBICACIÓN</div>
    <p style="font-size: 10pt;">
        <strong>Dirección:</strong> {{ $vivienda->direccion ?? 'N/A' }}<br>
        <strong>Comunidad:</strong> {{ $informe->comunidad ?? 'N/A' }}<br>
        <strong>Coordenadas:</strong> {{ $lat }}, {{ $long }}
    </p>

    <div class="section-title">MAPA</div>
    <div style="text-align: center; margin-top: 15px;">
        @if ($mapImageData)
            <img src="{{ $mapImageData }}" alt="Mapa de Ubicación"
                style="max-width: 90%; height: auto; border: 1px solid #ccc;">
        @else
            <div style="border: 1px solid #ccc; padding: 50px; background-color: #f9f9f9;">
                <p>Mapa de ubicación no disponible.</p>
            </div>
        @endif
    </div>

    {{-- NÚMERO DE PÁGINA --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Arial");                        
            $pdf->page_text(500, 800, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 9, array(0,0,0));
        }
    </script>

</body>

</html>