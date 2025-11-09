<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Personal</title>
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

                <div class="card shadow-sm p-4">

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Cédula</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Nombre y Apellido</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Correo Electrónico</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Profesión</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        Estado</th>
                                    <th class="text-secondary opacity-7">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($personal as $user)
                                    <tr>
                                        {{-- Cédula --}}
                                        <td class="align-middle text-sm">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->cedula_user ?? 'N/A' }}</p>
                                        </td>

                                        {{-- Nombre y Apellido --}}
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
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->profesion ?? 'No Asignada' }}
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

                                                {{-- 1. Botón de Estado (Activar/Inactivar) --}}
                                                {{-- Usamos un formulario con PATCH para la acción de cambio de estado --}}
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

                                                {{-- 2. Botón de Editar Información --}}
                                                <a href="{{ route('personal.edit', $user->id_user) }}"
                                                    class="btn btn-sm btn-info btn-icon-only mx-1" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Editar Información">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                {{-- 3. Botón de Asignar Inspecciones --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-primary btn-icon-only assign-inspection-btn"
                                                    data-user-id="{{ $user->id_user }}"
                                                    data-user-name="{{ $user->nombre }} {{ $user->apellido }}"
                                                    data-bs-toggle="modal" data-bs-target="#assignInspectionModal"
                                                    data-bs-placement="top" title="Asignar Inspecciones">
                                                    <i class="bi bi-tools"></i>
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
    </div>


    {{-- ================================================= --}}
    {{-- DEFINICIÓN DE LA MODAL PARA ASIGNACIÓN --}}
    {{-- ================================================= --}}
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
                {{-- El 'action' del formulario se llena dinámicamente con JavaScript --}}
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

    <script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    {{-- Agrega esto en tu layout si no lo tienes para que los tooltips
    de Bootstrap funcionen --}}
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('assignInspectionModal');
            const select = document.getElementById('id_insp');
            const form = document.getElementById('assignInspectionForm');
            const userNameSpan = document.getElementById('modal-user-name');
            const noInspectionsAlert = document.getElementById('no-inspections-alert');
            const assignButton = document.getElementById('assign-btn');

            // Escucha el evento 'show.bs.modal' de Bootstrap
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userName = button.getAttribute('data-user-name');

                // 1. Resetear el estado
                select.innerHTML = '<option value="">Cargando inspecciones...</option>';
                noInspectionsAlert.classList.add('d-none');
                assignButton.setAttribute('disabled', 'true');

                // 2. Actualizar la interfaz y las URLs
                userNameSpan.textContent = userName;

                // Rutas dinámicas
                const fetchUrl = `/personal/${userId}/get-inspecciones`;
                const postUrl = `/personal/${userId}/asignar-inspeccion`;

                form.setAttribute('action', postUrl);

                // 3. Petición AJAX (fetch) para obtener las inspecciones disponibles
                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            // Si el servidor devuelve un error (ej. 500)
                            return response.json().then(err => {
                                // Intenta obtener el mensaje de error del JSON (si existe)
                                throw new Error(err.message || 'Error desconocido del servidor (Código: ' + response.status + ')');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        select.innerHTML = ''; // Limpiar select

                        if (data.inspecciones.length === 0) {
                            // No hay inspecciones
                            select.innerHTML = '<option value="">No hay disponibles</option>';
                            noInspectionsAlert.classList.remove('d-none');
                            assignButton.setAttribute('disabled', 'true');
                        } else {
                            // Rellenar el select
                            select.innerHTML += '<option value="">-- Seleccione una inspección --</option>';
                            data.inspecciones.forEach(inspeccion => {
                                // *** CLAVE: Usamos el campo propietario_display ***
                                const optionText = inspeccion.propietario_display;
                                select.innerHTML += `<option value="${inspeccion.id_insp}">${optionText}</option>`;
                            });

                            // Habilitar/Deshabilitar el botón según la selección en el select
                            select.onchange = function () {
                                if (this.value) {
                                    assignButton.removeAttribute('disabled');
                                } else {
                                    assignButton.setAttribute('disabled', 'true');
                                }
                            };
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar inspecciones (Detalle en Consola):', error);
                        // Mostrar mensaje de error claro al usuario
                        select.innerHTML = '<option value="">ERROR: No se pudo cargar la lista. Revise la consola del navegador.</option>';
                        assignButton.setAttribute('disabled', 'true');
                    });
            });
        });
    </script>
</body>

</html>

</html>