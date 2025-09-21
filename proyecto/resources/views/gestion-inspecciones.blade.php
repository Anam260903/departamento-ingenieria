<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inspecciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    <div class="d-flex" id="wrapper">

        @include('components._sidebar')

        <div id="page-content-wrapper">

            @include('components._navbar')

            <div class="container-fluid py-4">
                <h1 class="mb-4">GESTIÓN DE INSPECCIONES</h1>

                <div class="row justify-content-end mb-3">
        <div class="col-auto">
            <a href="#" class="btn btn-primary text-nowrap">
                <i class="bi bi-plus-circle me-2"></i>Nueva inspección
            </a>
        </div>
    </div>

                <div class="table-responsive">
                    <table class="table table-hover shadow-sm">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Propietario de la vivienda</th>
                                <th scope="col">Dirección</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>24-08-2025</td>
                                <td>Ana Marcano</td>
                                <td>Valle nuevo, san martín, municipio bermúdez</td>
                                <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-info text-white" title="Ver detalles"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-sm btn-warning text-white" title="Editar"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm btn-success" title="Crear informe"><i class="bi bi-plus-circle"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>