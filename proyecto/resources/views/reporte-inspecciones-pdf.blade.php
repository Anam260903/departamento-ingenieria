<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Inspecciones</title>
    <link href="{{ asset('css/pdf-inspecciones.css') }}" rel="stylesheet">
</head>

<body>

    {{-- ENCABEZADO --}}
    <div class="header-container">

        {{-- Logo Izquierdo --}}
        <div class="logo-col">
            <img src="{{ asset('images/logo2.jpg') }}" class="logo-img">
        </div>

        {{-- Texto Centrado --}}
        <div class="text-col">
            <p>REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
            <p>GOBIERNO BOLIVARIANO DEL ESTADO SUCRE</p>
            <p>CORPORACION DE VIVIENDA DEL ESTADO SUCRE (CORVISUCRE)</p>
            <p>RIF: G-200164492</p>
            <p>CARÚPANO - ESTADO SUCRE</p>
        </div>

        {{-- Logo Derecho --}}
        <div class="logo-col" style="text-align: right;">
            <img src="{{ asset('images/logo3.jpg') }}" class="logo-img">
        </div>
    </div>

    {{-- LÍNEA ROJA DE SEPARACIÓN --}}
    <div class="separator-line"></div>

    {{-- TÍTULOS DEL REPORTE --}}
    <div class="report-title">REPORTE DE INSPECCIONES</div>
    <div class="report-subtitle">Departamento de Ingeniería -
        @isset($fechaReporte)
            {{ $fechaReporte }}
        @else
            {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        @endisset
    </div>
    
    {{-- TABLA DE DATOS --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 25%;">Propietario</th>
                <th style="width: 10%;">Cédula</th>
                <th style="width: 35%;">Dirección</th>
                <th style="width: 20%;">Responsable</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inspecciones as $inspeccion)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($inspeccion->fecha_insp)->format('d-m-Y') }}</td>
                    <td>
                        {{-- Propietario --}}
                        {{ $inspeccion->vivienda->propietario->nombre_propie ?? 'N/A' }}
                        {{ $inspeccion->vivienda->propietario->apellido_propie ?? '' }}
                    </td>
                    <td>
                        {{-- Cédula --}}
                        {{ $inspeccion->vivienda->propietario->cedula_propie ?? 'N/A' }}
                    </td>
                    <td>
                        {{-- Dirección --}}
                        {{ $inspeccion->vivienda->direccion ?? 'N/A' }}
                    </td>
                    <td>
                        {{-- Responsable --}}
                        @if($inspeccion->usuario)
                            Ing. {{ $inspeccion->usuario->nombre }} {{ $inspeccion->usuario->apellido }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- PIE DE PÁGINA Y NÚMERO DE PÁGINA --}}
    <div class="footer">
        CORVISUCRE
        | Página
        <script type="text/php">echo $pdf->page_script('return $PAGE_NUM . " de " . $PAGE_COUNT;');</script>
    </div>

</body>

</html>