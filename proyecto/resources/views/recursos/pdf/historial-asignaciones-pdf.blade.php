<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Historial de Asignaciones</title>
    <link href="{{ asset('css/reportes-pdf.css') }}" rel="stylesheet">
</head>

<body>

    {{-- Encabezado --}}
    <div class="header-container">
        <div class="logo-col">
            <img src="{{ asset('images/logo2.jpg') }}" class="logo-img">
        </div>
        <div class="text-col">
            <p>REPÚBLICA BOLIVARIANA DE VENEZUELA</p>
            <p>GOBIERNO BOLIVARIANO DEL ESTADO SUCRE</p>
            <p>CORPORACIÓN DE VIVIENDA DEL ESTADO SUCRE</p>
            <p>(CORVISUCRE)</p>
            <p>RIF: G-200164492</p>
            <p>CARÚPANO - ESTADO SUCRE</p>
        </div>
        <div class="logo-col" style="text-align: right;">
            <img src="{{ asset('images/logo3.jpg') }}" class="logo-img">
        </div>
    </div>

    {{-- Línea roja de separación --}}
    <div class="separator-line"></div>

    {{-- Títulos del reporte --}}
    <div class="report-title">HISTORIAL DE ASIGNACIONES DE RECURSOS</div>
    <div class="report-subtitle">
        @if (isset($rangoFechas))
            {{-- Muestra el rango de fechas si fue enviado desde el controlador --}}
            Filtrado desde {{ $rangoFechas['desde'] }} hasta {{ $rangoFechas['hasta'] }}
        @else
            {{-- Si no hay rango, es el listado general --}}
            Listado General
        @endif
    </div>
    <div class="report-subtitle">
        Fecha de Reporte: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    {{-- Tabla de datos --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 20%;">Recurso</th>
                <th style="width: 10%;">Código</th>
                <th style="width: 25%;">Asignado a</th>
                <th style="width: 15%;">Fecha de Asignación</th>
                <th style="width: 15%;">Fecha de Devolución</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asignaciones as $asignacion)
                <tr>
                    <td>
                        {{-- Recurso --}}
                        {{ $asignacion->recurso->nombre_rec ?? 'Recurso Eliminado' }}
                    </td>
                    <td>
                        {{-- Código --}}
                        {{ $asignacion->recurso->codigo ?? 'N/A' }}
                    </td>
                    <td>
                        {{-- Usuario --}}
                        {{ $asignacion->usuario->nombre ?? 'Usuario Eliminado' }} 
                        {{ $asignacion->usuario->apellido ?? '' }} 
                    </td>
                    <td>
                        {{-- Fecha de asignación --}}
                        {{ $asignacion->fecha_asignacion->format('d-m-Y') }}
                    </td>
                    <td>
                        {{-- Fecha de devolución --}}
                        @if ($asignacion->fecha_devolucion)
                            {{ $asignacion->fecha_devolucion->format('d-m-Y') }}
                        @else
                            <span >No devuelto</span>
                        @endif
                    </td>
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