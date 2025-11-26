<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personal</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    <div class="d-flex" id="wrapper">

        @include('components._sidebar')

        <div id="page-content-wrapper">

            @include('components._navbar')

            <div class="container-fluid py-4">
                <h1 class="mb-4 h3">GESTIÓN DE PERSONAL</h1>

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <strong>Advertencia:</strong> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Mensajes de éxito o error --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card p-3 mb-4">

                    <div class="row align-items-center">

                        {{-- Botón de descarga listado PDF (General) --}}
                        <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
                            <a href="{{ route('personal.exportar.pdf') }}" class="btn btn-danger text-nowrap w-auto"
                                title="Descargar PDF General del Personal">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Descargar 
                            </a>
                        </div>

                        {{-- Formulario de descarga listado PDF (Filtrado) --}}
                        <div class="col-lg-8 col-md-6">
                            <form id="form-pdf-filtro" action="" method="GET" target="_blank">
                                <div class="input-group">
                                    <select class="form-select" id="profesion-select" name="profesion" required>
                                        <option value="" disabled selected>Seleccione una profesión</option>
                                        {{-- Iteramos sobre las profesiones obtenidas del controlador --}}
                                        @foreach ($profesionesUnicas as $profesion)
                                            <option value="{{ $profesion }}">{{ $profesion }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary" type="submit" id="btn-descargar-filtro" disabled>
                                        Descargar PDF Filtrado
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="card shadow-sm p-4 mb-4">
                    <form action="{{ route('personal.index') }}" method="GET">
                        <div class="row g-3">

                            {{-- Filtro por palabra clave --}}
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="keyword"
                                    placeholder="Buscar por Cédula, Nombre, Correo, Profesión o Rol"
                                    value="{{ request('keyword') }}">
                            </div>

                            {{-- Filtro por estado --}}
                            <div class="col-md-4">
                                <select class="form-select" name="estado">
                                    <option value="">Filtrar por Estado</option>
                                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>ACTIVO</option>
                                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>INACTIVO</option>
                                </select>
                            </div>

                            {{-- Botones de acción --}}
                            <div class="col-md-3 d-flex">
                                <button type="submit" class="btn btn-secondary w-100 me-2">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                                {{-- Botón para limpiar filtros --}}
                                <a href="{{ route('personal.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card shadow-sm p-4">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-header-custom">
                                <tr>
                                    <th scope="col">Cédula</th>
                                    <th scope="col">Nombre y Apellido</th>
                                    <th scope="col">Correo Electrónico</th>
                                    <th scope="col">Profesión</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($personal as $user)
                                    <tr>
                                        {{-- Cédula --}}
                                        <td class="align-middle text-sm">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->cedula_user ?? 'N/A' }}
                                            </p>
                                        </td>

                                        {{-- Nombre y apellido --}}
                                        <td class="align-middle text-sm">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->nombre }}
                                                {{ $user->apellido }}
                                            </p>
                                        </td>

                                        {{-- Correo --}}
                                        <td class="align-middle text-sm">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->correo }}</p>
                                        </td>

                                        {{-- Profesión --}}
                                        <td class="align-middle text-sm">
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $user->profesion ?? 'No Asignada' }}
                                            </p>
                                        </td>

                                        {{-- Estado (Activo/Inactivo) --}}
                                        <td class="align-middle text-center text-sm">
                                            @php
                                                $estado_numerico = $user->estado_user;

                                                $estado = ($estado_numerico == 1) ? 'ACIVO' : 'INACTIVO';

                                                $esActivo = ($estado_numerico === '1');
                                                $badgeClass = $esActivo ? 'bg-success' : 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} text-white text-uppercase">
                                                {{ $estado }}
                                            </span>
                                        </td>

                                        {{-- Acciones --}}
                                        <td class="align-middle">
                                            <div class="btn-group" role="group">

                                                {{-- 1. Botón de estado (Activar/Inactivar) --}}
                                                <form action="{{ route('personal.toggleStatus', $user->id_user) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-icon-only 
                                                                                                                                                                                                                {{ $esActivo ? 'btn-warning' : 'btn-success' }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ $esActivo ? 'Desactivar Personal' : 'Activar Personal' }}">
                                                        <i class="bi {{ $esActivo ? 'bi-lock' : 'bi-unlock' }}"></i>
                                                    </button>
                                                </form>

                                                {{-- 2. Botón de editar información --}}
                                                <a href="{{ route('personal.edit', $user->id_user) }}"
                                                    class="btn btn-sm btn-info btn-icon-only mx-1" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Editar Información">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                {{-- 3. Botón de asignar inspecciones --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-primary btn-icon-only assign-inspection-btn"
                                                    data-user-id="{{ $user->id_user }}"
                                                    data-user-name="{{ $user->nombre }} {{ $user->apellido }}"
                                                    data-bs-toggle="modal" data-bs-target="#assignInspectionModal"
                                                    data-bs-placement="top" title="Asignar Inspecciones">
                                                    <i class="bi bi-tools"></i>
                                                </button>

                                                {{-- 4. Botón de asignar recurso --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-icon-only assign-resource-btn"
                                                    data-user-id="{{ $user->id_user }}"
                                                    data-user-name="{{ $user->nombre }} {{ $user->apellido }}"
                                                    data-bs-toggle="modal" data-bs-target="#assignResourceModal"
                                                    data-bs-placement="top" title="Asignar Recurso">
                                                    <i class="bi bi-boxes"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal para asignación de inspecciones --}}
        <div class="modal fade" id="assignInspectionModal" tabindex="-1" aria-labelledby="assignInspectionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="assignInspectionModalLabel">
                            Asignar Inspección a: <span id="modal-user-name"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="assignInspectionForm" method="POST">
                        @csrf
                        <div class="modal-body">

                            <div class="alert alert-info d-none" id="no-inspections-alert">
                                No hay inspecciones disponibles para asignar en este momento.
                            </div>

                            <div class="mb-3">
                                <label for="id_insp" class="form-label">Inspección Disponible</label>
                                <select class="form-select" id="id_insp" name="id_insp" required>
                                    <option value="">Cargando inspecciones...</option>
                                </select>
                                {{-- Espacio para errores de validación si usaras AJAX, o si Laravel redirige --}}
                                <div class="text-danger mt-1" id="id_insp-error"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="assign-btn" disabled>
                                <i class="bi bi-person-fill-add me-2"></i> Asignar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal para asignación de recursos --}}
        <div class="modal fade" id="assignResourceModal" tabindex="-1" aria-labelledby="assignResourceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="assignResourceModalLabel">
                            Asignar Recurso a: <span id="modal-resource-user-name"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="assignResourceForm" method="POST">
                        @csrf
                        <div class="modal-body">

                            <div class="alert alert-warning d-none" id="no-resources-alert">
                                No hay recursos disponibles para asignar en este momento.
                            </div>

                            <div class="mb-3">
                                <label for="id_recurso" class="form-label">Recursos Disponibles</label>
                                <select class="form-select" id="id_recurso" name="id_recurso" required>
                                    <option value="">Cargando recursos...</option>
                                </select>
                                <div class="text-danger mt-1" id="id_recurso-error"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" id="assign-resource-btn-submit" disabled>
                                Asignar Recurso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/dashboard.js') }}"></script>
        <script>
            const API_ROUTES = {
                fetchInspections: '/personal/:userId/get-inspecciones', // Usar placeholder
                postInspection: '/personal/:userId/asignar-inspeccion',
                fetchResources: '{{ route('personal.getRecursosDisponibles') }}', // Ruta sin parámetros
                postResource: '/personal/:userId/asignar-recurso',
            };
        </script>
        <script src="{{ asset('js/personal.js') }}"></script>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    })
                });
            </script>
        @endpush



        {{-- Script para manejar la URL dinámica --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectElement = document.getElementById('profesion-select');
                const formElement = document.getElementById('form-pdf-filtro');
                const downloadButton = document.getElementById('btn-descargar-filtro');

                // Función que se ejecuta al cambiar la selección
                selectElement.addEventListener('change', function () {
                    const selectedProfesion = this.value;

                    if (selectedProfesion) {
                        // Genera la URL de la ruta nombrada, reemplazando el parámetro
                        // 'personal.exportar.pdf.filtro' es la ruta que creamos antes: personal/exportar/pdf/{profesion}
                        const url = '{{ route('personal.exportar.pdf.filtro', ['profesion' => '__PROFESION__']) }}';

                        // Reemplaza el placeholder por la profesión seleccionada en la acción del formulario
                        formElement.action = url.replace('__PROFESION__', selectedProfesion);
                        downloadButton.disabled = false; // Habilita el botón de descarga

                    } else {
                        downloadButton.disabled = true; // Deshabilita si no hay selección
                    }
                });
            });
        </script>

        @include('components._session-timeout')
</body>

</html>