<!DOCTYPE html>
<html>

<head>
    <title>Informe Técnico N° {{ $informe->id_inf }}</title>
    <style>
        <?php echo file_get_contents(public_path('css/informes.css')); ?>
    </style>
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
            <p>CORPORACIÓN DE VIVIENDA DEL ESTADO SUCRE</p>
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

    <div style="font-size: 10pt; line-height: 1.5; margin-bottom: 15px;">

        {{-- Fila 1: PROFESIONAL ASIGNADO --}}
        <span style="font-weight: bold;">PROFESIONAL ASIGNADO:</span>
        <span style="display: inline-block; width: 10px;"></span>
        <span style="font-weight: normal;">Ing. {{ $informe->inspeccion->usuario->nombre ?? 'N/A' }}
            {{ $informe->inspeccion->usuario->apellido ?? 'N/A' }}</span>
        <br />

        {{-- Fila 2: FECHA DE LA INSPECCIÓN --}}
        <span style="font-weight: bold;">FECHA DE LA INSPECCIÓN:</span>
        <span style="display: inline-block; width: 10px;"></span>
        <span
            style="font-weight: normal;">{{ \Carbon\Carbon::parse($informe->inspeccion->fecha_insp)->format('d/m/Y') }}</span>
        <br />

        {{-- Fila 3: NOMBRE DE LA COMUNIDAD Y/O PROYECTO --}}
        <span style="font-weight: bold;">NOMBRE DE LA COMUNIDAD Y/O PROYECTO:</span>
        <span style="display: inline-block; width: 10px;"></span>
        <span style="font-weight: normal;">{{ $informe->comunidad ?? 'N/A' }}</span>
        <br />

        {{-- Fila 4 : RESPONSABLE --}}
        <span style="font-weight: bold;">RESPONSABLE:</span>
        <span style="display: inline-block; width: 10px;"></span>
        <span style="font-weight: normal;">{{ $informe->inspeccion->vivienda->propietario->nombre_propie ?? 'N/A' }}
            {{ $informe->inspeccion->vivienda->propietario->apellido_propie ?? 'N/A' }}</span>
        <br />

        {{-- Fila 5: CÉDULA DE IDENTIDAD --}}
        <span style="font-weight: bold;">CÉDULA DE IDENTIDAD:</span>
        <span style="display: inline-block; width: 10px;"></span>
        <span style="font-weight: normal;">
            {{ number_format($informe->inspeccion->vivienda->propietario->cedula_propie ?? 0, 0, ',', '.') }}
        </span>
        <br />

        {{-- Fila 6: TELÉFONO --}}
        <span style="font-weight: bold;">TELÉFONO:</span>
        <span style="display: inline-block; width: 10px;"></span>
        <span style="font-weight: normal;">{{ $informe->inspeccion->vivienda->propietario->telefono ?? 'N/A' }}</span>

        <br />

        {{-- Fila 7: DIRECCIÓN --}}
        <span style="font-weight: bold;">DIRECCIÓN:</span>
        <span style="display: inline-block; width: 10px;"></span>
        <span style="font-weight: normal;">{{ $informe->inspeccion->vivienda->direccion ?? 'N/A' }}</span>
        <br />

    </div>

    {{-- Contenido de la inspección --}}
    <div class="section-title">1. ANTECEDENTES</div>
    <div class="content-box">
        {!! nl2br(e($informe->antecedentes ?? 'No se proporcionaron antecedentes.')) !!}
    </div>

    <div class="section-title">2. PLANTEAMIENTO DEL PROBLEMA</div>
    <div class="content-box">
        {!! nl2br(e($informe->planteamiento ?? 'No se proporcionó planteamiento del problema.')) !!}
    </div>

    <div class="section-title">3. CARACTERÍSTICAS DE LA VIVIENDA</div>
    <div class="content-box">
        {!! nl2br(e($informe->inspeccion->vivienda->caracteristicas ?? 'No se proporcionaron características de la vivienda.')) !!}

    </div>

    <div class="section-title">4. RESULTADOS DE LA INSPECCIÓN</div>
    <div class="content-box">
        {!! nl2br(e($informe->resultados ?? 'No se proporcionaron resultados de la inspección.')) !!}
    </div>

    <div class="section-title">5. RECOMENDACIONES</div>
    <div class="content-box">
        {!! nl2br(e($informe->recomendacion ?? 'No se proporcionaron recomendaciones.')) !!}
    </div>

    <div class="section-title">6. MATERIALES</div>
    <div class="content-box">
        {!! nl2br(e($informe->materials_info ?? 'No se proporcionaron materiales.')) !!}
    </div>

    {{-- ESPACIO PARA FIRMA DEL INSPECTOR --}}
    <div class="footer-signature">
        <div style="height: 150px;"></div> {{-- Crea un espacio elástico --}}
        <div class="signature-box">
            <p>Ing. {{ $informe->inspeccion->usuario->nombre ?? '' }} {{ $informe->inspeccion->usuario->apellido ?? '' }}</p>
            <p>C.I: {{ number_format($informe->inspeccion->usuario->cedula_user ?? 0, 0, ',', '.') }}</p>
        </div>
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

    {{-- Título --}}

    <div class="report-title">MEMORIA FOTOGRÁFICA</div>

    {{-- Imagénes --}}
    @if ($informe->imagenes->count() > 0)

        @php
            $img_count = 0; // Contador de imagen dentro de la página
            $imagenes_por_fila = 2;
            $imagenes_por_pagina = 6;

            // Abrimos la primera tabla
            echo '<table class="photo-table">';
        @endphp

        @foreach ($informe->imagenes as $index => $imagen)

            {{-- 1. Si es la primera imagen de una fila, abrimos una fila de tabla --}}
            @if ($img_count % $imagenes_por_fila === 0)
                <tr>
            @endif

                @php
                    // Lógica de Manejo de Archivos
                    $storagePath = storage_path('app/public/' . $imagen->ruta_archivo);
                    $imageData = '';
                    if (file_exists($storagePath)) {
                        $imageData = 'data:image/' . pathinfo($storagePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($storagePath));
                    }
                @endphp

                {{-- 2. Insertamos la celda con la imagen --}}
                <td class="photo-cell">
                    @if ($imageData)
                        <img src="{{ $imageData }}" alt="Foto de Evidencia">
                    @else
                        <p>[Imagen no encontrada]</p>
                    @endif
                </td>

                {{-- 3. Si es la última imagen de una fila, cerramos la fila --}}
                @if (($img_count + 1) % $imagenes_por_fila === 0)
                    </tr>
                @endif

            {{-- 4. Verificacion para el corte de página --}}
            @if (($img_count + 1) % $imagenes_por_pagina === 0)
                @php
                    // Cerramos la tabla actual
                    echo '</table>';

                    echo '<div class="page-break"></div>';

                    // Abrimos una nueva tabla para la siguiente página
                    echo '<table class="photo-table">';
                @endphp
            @endif

            @php
                $img_count++; // Incrementamos el contador
            @endphp
        @endforeach

        @php
            // Cerramos la tabla final si el total de imágenes no fue múltiplo de 4
            if ($img_count % $imagenes_por_pagina !== 0) {
                // Aseguramos que la última fila se cierre si el conteo fue impar
                if ($img_count % $imagenes_por_fila !== 0) {
                    echo '</tr>';
                }
                echo '</table>';
            }
        @endphp

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

        // Inicializamos la data del mapa vacía
        $mapImageData = '';

        // Verificar si existe un nombre de archivo para el mapa
        if (!empty($vivienda->map_image_file)) {

            // Obtenemos la imagen del mapa
            $mapStoragePath = storage_path('app/public/' . $vivienda->map_image_file);

            // Lógica de verificación de archivo y codificación
            if (file_exists($mapStoragePath)) {
                $mapImageData = 'data:image/' . pathinfo($mapStoragePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($mapStoragePath));
            }
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