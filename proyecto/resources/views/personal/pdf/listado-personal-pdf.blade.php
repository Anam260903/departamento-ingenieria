<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Listado de Personal</title>
    <link href="{{ asset('css/reportes-pdf.css') }}" rel="stylesheet"> 
</head>

<body>

    {{-- Encabezado --}}
    <div class="header-container">

        {{-- Logo izquierdo --}}
        <div class="logo-col">
            <img src="{{ asset('images/logo2.jpg') }}" class="logo-img">
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
            <img src="{{ asset('images/logo3.jpg') }}" class="logo-img">
        </div>
    </div>

    {{-- Línea roja de separación --}}
    <div class="separator-line"></div>

    {{-- Títulos del reporte --}}
    <div class="report-title">
        @isset($tituloReporte)
            {{ $tituloReporte }}
        @else
            REPORTE LISTADO DE PERSONAL
        @endisset
    </div>
    <div class="report-subtitle"> Departamento de Ingeniería
        @isset($fechaReporte)
           {{ $fechaReporte }}
        @else
            {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        @endisset
    </div>
    
    {{-- Tabla de datos --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 30%;">Nombre y Apellido</th>
                <th style="width: 15%;">Cédula</th>
                <th style="width: 35%;">Correo</th>
                <th style="width: 20%;">Profesión</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->nombre ?? 'N/A' }} {{ $usuario->apellido ?? '' }}</td>
                    <td>{{ $usuario->cedula_user ?? 'N/A' }}</td>
                    <td>{{ $usuario->correo ?? 'N/A' }}</td>
                    <td>{{ $usuario->profesion ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Número de página --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Arial");
            $pdf->page_text(500, 800, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 9, array(0,0,0));
        }
    </script>

</body>

</html>