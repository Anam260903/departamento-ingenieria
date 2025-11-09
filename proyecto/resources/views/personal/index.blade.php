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
                                                {{ $user->apellido }}</p>
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
                                                <a href="{{ route('personal.assignInspections', $user->id_user) }}"
                                                    class="btn btn-sm btn-primary btn-icon-only" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Asignar Inspecciones">
                                                    <i class="bi bi-tools"></i>
                                                </a>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    {{-- Agrega esto en tu layout si no lo tienes para que los tooltips de Bootstrap funcionen --}}
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
</body>

</html>

</html>