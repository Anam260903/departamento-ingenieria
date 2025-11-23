<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Estilos específicos para los badges de estado */
        .badge-asignado {
            background-color: #ffc107;
        }

        .badge-disponible {
            background-color: #28a745;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        @include('components._sidebar')

        <div id="page-content-wrapper">

            @include('components._navbar')

            <div class="container-fluid py-4">
                <h1 class="mb-4 h3">LISTADO DE RECURSOS</h1>

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

                <div class="row justify-content-end mb-3">
                    {{-- Botón nuevo recurso --}}
                    <div class="col-auto">
                        {{-- Verifica si el usuario autenticado tiene id_rol igual a 1 (Administrador) --}}
                        @if (auth()->check() && auth()->user()->id_rol === 1)
                            <a href="{{ route('recursos.create') }}" class="btn btn-primary text-nowrap">
                                <i class="bi bi-plus-circle me-2"></i>Nuevo recurso
                            </a>
                        @endif
                    </div>
                    {{-- Botón asignaciones --}}
                    <div class="col-auto">
                        {{-- Verifica si el usuario autenticado tiene id_rol igual a 1 (Administrador) --}}
                        @if (auth()->check() && auth()->user()->id_rol === 1)
                            <a href="{{ route('recursos.assignments.history') }}" class="btn btn-primary text-nowrap">
                                <i class="bi bi-bookmark-check me-2"></i>Asignaciones
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm p-4 mb-4">
                    <form action="{{ route('recursos.index') }}" method="GET">
                        <div class="row g-3">

                            {{-- Filtro por palabra clave --}}
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="keyword"
                                    placeholder="Buscar por Código o Nombre del Recurso"
                                    value="{{ request('keyword') }}">
                            </div>

                            {{-- Filtro por estado (ASIGNADO / NO ASIGNADO) --}}
                            <div class="col-md-4">
                                <select class="form-select" name="estado">
                                    <option value="">Filtrar por Estado</option>
                                    {{-- '1' representa ASIGNADO (fecha_devolucion es NULL) --}}
                                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>ASIGNADO</option>
                                    {{-- '0' representa NO ASIGNADO (no tiene asignación activa) --}}
                                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>DISPONIBLE
                                    </option>
                                </select>
                            </div>

                            {{-- Botones de acción --}}
                            <div class="col-md-3 d-flex">
                                <button type="submit" class="btn btn-secondary w-100 me-2">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                                {{-- Botón para limpiar filtros --}}
                                <a href="{{ route('recursos.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="card shadow-sm p-4">

                    {{-- Tabla con datos de inspecciones --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-header-custom">
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Descripción</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recursos as $recurso)
                                                                    <tr>
                                                                        <td>{{ $recurso->codigo }}</td>
                                                                        <td>{{ $recurso->nombre_rec }}</td>
                                                                        <td>{{ $recurso->descripcion }}</td>
                                                                        <td>
                                                                            @php
                                    // Busca la última asignación que no tiene fecha de devolución
                                    $estaAsignado = $recurso->estaAsignado(); 
                                                                            @endphp

                                                                            @if ($estaAsignado)
                                                                                <span class="badge badge-asignado">Asignado</span>
                                                                            @else
                                                                                <span class="badge badge-disponible">Disponible</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex gap-2">

                                                                                {{-- 1. Botón de ver detalles --}}
                                                                                <a href="#" class="btn btn-info btn-sm btn-ver-observacion"
                                                                                    title="Ver detalles" data-bs-toggle="modal"
                                                                                    data-bs-target="#observacionModal"
                                                                                    data-observacion="{{ json_encode($recurso->observacion) }}">
                                                                                    <i class="bi bi-eye"></i>
                                                                                </a>

                                                                                {{-- 2. Botón de editar --}}
                                                                                <a href="{{ route('recursos.edit', $recurso->id_recurso) }}"
                                                                                    class="btn btn-warning btn-sm" title="Editar recurso">
                                                                                    <i class="bi bi-pencil"></i>
                                                                                </a>

                                                                                {{-- 2. Botón de eliminar --}}
                                                                                <form action="{{ route('recursos.destroy', $recurso->id_recurso) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                                        title="Eliminar recurso"
                                                                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este recurso?')">
                                                                                        <i class="bi bi-trash"></i>
                                                                                    </button>
                                                                                </form>

                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No hay recursos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de observación --}}
        <div class="modal fade" id="observacionModal" tabindex="-1" aria-labelledby="observacionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="observacionModalLabel">Observaciones del Recurso</h5>
                    </div>
                    <div class="modal-body">
                        <p id="modalObservacionContent"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/dashboard.js') }}"></script>
        <script src="{{ asset('js/recursos.js') }}"></script>


        @include('components._session-timeout')
</body>

</html>

</html>