<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informes Técnicos</title>
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

                <h1 class="mb-4 h3">INFORMES TÉCNICOS</h1>

                {{-- Bloque de alertas --}}
                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <strong>Advertencia:</strong> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row justify-content-end mb-3">

                    {{-- Botón nuevo informe --}}
                    <div class="col-auto">
                        <a href="#" class="btn btn-primary text-nowrap" data-bs-toggle="modal"
                            data-bs-target="#modalSeleccionarInspeccion">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo informe
                        </a>
                    </div>
                </div>

                {{-- Filtros --}}
                <div class="card shadow-sm p-4 mb-4">
                    <form action="{{ route('informes.index') }}" method="GET">
                        <div class="row g-3">
                            {{-- Filtro por palabra clave --}}
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="keyword"
                                    placeholder="Buscar por Propietario, Dirección o Ingeniero"
                                    value="{{ request('keyword') }}">
                            </div>
                            {{-- Filtro por ingeniero --}}
                            <div class="col-md-3">
                                <select class="form-select" name="ingeniero">
                                    <option value="">Filtrar por Ingeniero</option>
                                </select>
                            </div>
                            {{-- Filtro por fecha --}}
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label visually-hidden">Fecha Desde</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                    title="Fecha Desde" value="{{ request('fecha_inicio') }}">
                            </div>
                            {{-- Botones de acción --}}
                            <div class="col-md-2 d-flex">
                                <button type="submit" class="btn btn-secondary w-100 me-2">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                                {{-- Botón para limpiar filtros --}}
                                <a href="{{ route('informes.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Tabla --}}
                <div class="card shadow-sm p-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-header-custom">
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Ingeniero asignado</th>
                                    <th scope="col">Propietario de la vivienda</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($informes as $informe)
                                    <tr>
                                        {{-- Fecha --}}
                                        <td>{{ \Carbon\Carbon::parse($informe->fecha_inf)->format('d-m-Y') }}</td>

                                        {{-- Ingeniero asignado --}}
                                        <td>
                                            @php $usuario = $informe->inspeccion->usuario ?? null; @endphp
                                            {{ $usuario ? ($usuario->nombre . ' ' . $usuario->apellido) : 'N/A' }}
                                        </td>

                                        {{-- Propietario --}}
                                        <td>
                                            @php $propietario = $informe->inspeccion->vivienda->propietario ?? null; @endphp
                                            {{ $propietario ? ($propietario->nombre_propie . ' ' . $propietario->apellido_propie) : 'N/A' }}
                                        </td>

                                        {{-- Dirección --}}
                                        <td>
                                            {{ $informe->inspeccion->vivienda->direccion ?? 'N/A' }}
                                        </td>

                                        {{-- Acciones --}}
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- 1. Botón de editar --}}
                                                <a href="{{ route('informes.edit.step1', $informe->id_inf) }}"
                                                    class="btn btn-warning btn-sm" title="Editar informe">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                {{-- 2. Botón de descargar PDF --}}
                                                <a href="{{ route('informes.generatePdf', $informe->id_inf) }}"
                                                    class="btn btn-danger btn-sm" title="Descargar PDF">
                                                    <i class="bi bi-file-pdf-fill"></i>
                                                </a>

                                                {{-- 3. Botón de eliminar --}}
                                                {{-- Verifica si el usuario autenticado tiene id_rol igual a 1
                                                (Administrador) --}}
                                                @if (auth()->check() && auth()->user()->id_rol === 1)


                                                    <form action="{{ route('informes.destroy', $informe->id_inf) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-info btn-sm"
                                                            title="Eliminar informe"
                                                            onclick="return confirm('¿Estás seguro de que quieres eliminar este informe?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No hay informes técnicos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Enlaces de paginación --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $informes->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal para seleccionar inspección al crear nuevo informe --}}

    <div class="modal fade" id="modalSeleccionarInspeccion" tabindex="-1"
        aria-labelledby="modalSeleccionarInspeccionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSeleccionarInspeccionLabel">Seleccionar Inspección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" id="formSeleccionarInspeccion">
                    <div class="modal-body">
                        <p>Seleccione la vivienda del propietario cuya inspección requiere un informe
                            técnico.</p>

                        <div class="mb-3">
                            <label for="id_insp_select" class="form-label">Propietario / Vivienda</label>
                            <select id="id_insp_select" name="id_insp" class="form-select" required>
                                <option value="">Seleccione una inspección...</option>

                                {{-- Llenar el Dropdown con inspecciones disponibles --}}
                                @php

                                    $informesController = new App\Http\Controllers\InformesController();
                                    $inspeccionesDisponibles = $informesController->obtenerInspeccionesDisponibles();
                                    $limiteTexto = 55;
                                @endphp

                                @forelse ($inspeccionesDisponibles as $insp)
                                    @php
                                        $propietario = $insp->vivienda->propietario;
                                        $texto = ($propietario ? $propietario->nombre_propie . ' ' . $propietario->apellido_propie : 'N/A')
                                            . ' - ' . ($insp->vivienda->direccion ?? 'N/A');
                                        if (strlen($texto) > $limiteTexto) {
                                            $textoMostrar = substr($texto, 0, $limiteTexto) . '...';
                                        } else {
                                            $textoMostrar = $texto;
                                        }
                                    @endphp
                                    <option value="{{ $insp->id_insp }}">{{ $textoMostrar }}</option>
                                @empty
                                    <option value="" disabled>No hay inspecciones pendientes de informe.
                                    </option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnContinuarInforme"
                            data-base-url="{{ url('informes/crear') }}/">
                            Iniciar informe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

      <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnContinuar = document.getElementById('btnContinuarInforme');

            if (btnContinuar) {
                btnContinuar.addEventListener('click', function () {

                    const selectElement = document.getElementById('id_insp_select');
                    const id_insp = selectElement.value;
                    const baseUrl = btnContinuar.getAttribute('data-base-url');

                    // Validamos que el ID exista y no sea la cadena vacía del placeholder
                    if (id_insp && id_insp !== "") {

                        // Redirección
                        window.location.href = baseUrl + id_insp;

                    } else {
                        alert('Por favor, seleccione una inspección válida para continuar.');
                        selectElement.focus();
                    }
                });
            }
        });
    </script>
    
    <script src="{{ asset('js/informes.js') }}"></script>

    @include('components._session-timeout')
</body>

</html>