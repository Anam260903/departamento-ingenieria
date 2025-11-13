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
    <style>
        /* Estilos específicos para los badges de estado */
        .badge-pendiente {
            background-color: #ffc107;
        }

        .badge-completada {
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
                <h1 class="mb-4 h3">GESTIÓN DE INSPECCIONES</h1>

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
                    {{-- Botón de descarga PDF general--}}
                    <div class="col-auto">
                        <a href="{{ route('inspecciones.exportar.pdf') }}" class="btn btn-secondary text-nowrap"
                            title="Descargar PDF">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Descargar listado
                        </a>
                    </div>

                    {{--Botón de descarga PDF por mes--}}
                    <div class="col-auto">
                        <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal"
                            data-bs-target="#modalDescargaMensual" title="Descargar PDF por mes">
                            <i class="bi bi-calendar-check"></i> Descargar PDF Mensual
                        </button>
                    </div>


                    {{-- Botón nueva inspección --}}
                    <div class="col-auto">
                        {{-- Verifica si el usuario autenticado tiene id_rol igual a 1 (Administrador) --}}
                        @if (auth()->check() && auth()->user()->id_rol === 1)
                            <a href="{{ route('inspecciones.create') }}" class="btn btn-primary text-nowrap">
                                <i class="bi bi-plus-circle me-2"></i>Nueva inspección
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm p-4 mb-4">
                    <form action="{{ route('inspecciones.index') }}" method="GET">
                        <div class="row g-3">
                            {{-- Filtro por palabra clave --}}
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="keyword"
                                    placeholder="Buscar por Nombre, Dirección o Palabras Clave"
                                    value="{{ request('keyword') }}">
                            </div>

                            {{-- Filtro por estado --}}
                            <div class="col-md-3">
                                <select class="form-select" name="estado">
                                    <option value="">Filtrar por Estado</option>
                                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Completada
                                    </option>
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
                                <a href="{{ route('inspecciones.index') }}" class="btn btn-outline-secondary">
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
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Propietario</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inspecciones as $inspeccion)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($inspeccion->fecha_insp)->format('d-m-Y') }}</td>
                                        <td>
                                            @if($inspeccion->vivienda && $inspeccion->vivienda->propietario)
                                                {{ $inspeccion->vivienda->propietario->nombre_propie }}
                                                {{ $inspeccion->vivienda->propietario->apellido_propie }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($inspeccion->vivienda)
                                                {{ $inspeccion->vivienda->direccion }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($inspeccion->estado_insp == 0)
                                                <span class="badge badge-pendiente">Pendiente</span>
                                            @else
                                                <span class="badge badge-completada">Completada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- 1. Botón de ver detalles --}}
                                                <a href="#" class="btn btn-info btn-sm btn-ver-observacion"
                                                    title="Ver detalles" data-bs-toggle="modal"
                                                    data-bs-target="#observacionModal"
                                                    data-observacion="{{ json_encode($inspeccion->observacion) }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                {{-- 2. Botón de editar --}}
                                                <a href="{{ route('inspecciones.edit', $inspeccion->id_insp) }}"
                                                    class="btn btn-warning btn-sm" title="Editar inspección">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                @if($inspeccion->estado_insp == 0)

                                                    {{-- Formulario para mrcar como completada --}}
                                                    <form action="{{ route('inspecciones.complete', $inspeccion->id_insp) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="Marcar como Completada"
                                                            onclick="return confirm('¿Está seguro de que desea marcar esta inspección como COMPLETADA?')"
                                                            {{-- Se deshabilita el botón si ya está completada (estado 1) para
                                                            evitar clics innecesarios --}} @if ($inspeccion->estado_insp == 1)
                                                            disabled @endif>
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>

                                                {{-- 5. Botón de eliminar --}}
                                                <form action="{{ route('inspecciones.destroy', $inspeccion->id_insp) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        title="Eliminar inspección"
                                                        onclick="return confirm('¿Estás seguro de que quieres eliminar esta inspección?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No hay inspecciones registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <h5 class="modal-title" id="observacionModalLabel">Observaciones de la Inspección</h5>
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

    {{-- Modal de decargar PDF Mensual --}}
    <div class="modal fade" id="modalDescargaMensual" tabindex="-1" aria-labelledby="modalDescargaMensualLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDescargaMensualLabel">Descargar Reporte
                        Mensual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('inspecciones.exportar.mes') }}" method="GET">
                    <div class="modal-body">
                        <p>Seleccione el mes y año para filtrar el listado de inspecciones a descargar.</p>

                        <div class="row g-3">
                            {{-- Selector de Mes --}}
                            <div class="col-md-6">
                                <label for="mes_modal" class="form-label">Mes</label>
                                <select id="mes_modal" name="mes" class="form-select" required>
                                    <option value="">Seleccione Mes</option>
                                    @php
                                        \Carbon\Carbon::setLocale('es');
                                    @endphp
                                    {{-- Listar los 12 meses --}}
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">
                                            {{ ucfirst(\Carbon\Carbon::create()->month($m)->monthName) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Selector de año --}}
                            <div class="col-md-6">
                                <label for="ano_modal" class="form-label">Año</label>
                                <select id="ano_modal" name="ano" class="form-select" required>
                                    <option value="">Seleccione Año</option>
                                    {{-- Listar 5 años hacia atrás --}}
                                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-download"></i> Descargar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/inspecciones.js') }}"></script>
</body>

</html>

</html>