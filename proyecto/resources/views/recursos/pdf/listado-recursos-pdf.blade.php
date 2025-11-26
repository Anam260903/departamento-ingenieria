<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Recursos</title>
    {{-- Reutiliza el mismo CSS --}}
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
    <div class="report-title">LISTADO DE RECURSOS</div>
    <div class="report-subtitle">Departamento de Ingeniería
        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
    </div>

    {{-- Tabla de datos --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 30%;">Nombre del Recurso</th>
                <th style="width: 55%;">Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recursos as $recurso)
                <tr>
                    <td>{{ $recurso->codigo ?? 'N/A' }}</td>
                    <td>{{ $recurso->nombre_rec ?? 'N/A' }}</td>
                    <td>{{ $recurso->descripcion ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- NÚMERO DE PÁGINA --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Arial");
            $pdf->page_text(500, 800, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 9, array(0,0,0));
        }
    </script>

</body>

</html>