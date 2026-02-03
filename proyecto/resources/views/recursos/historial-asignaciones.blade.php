<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaciones de Recursos</title>
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
                <h1 class="mb-4 h3">HISTORIAL DE ASIGNACIONES DE RECURSOS</h1>

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <strong>Advertencia:</strong> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Mensajes de sesión --}}
                @php
                    $mensaje = session('status') ?? session('success');
                @endphp

                @if($mensaje)
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $mensaje }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex justify-content-end mb-3 gap-2">
                    <div class="col-auto">
                        <a href="{{ route('recursos.asignaciones.exportar.pdf') }}" target="_blank"
                            class="btn btn-secondary text-nowrap" title="Descargar Historial de Asignaciones">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Descargar historial
                        </a>
                    </div>

                    <div class="col-auto">
                        <button type="button" class="btn btn-danger text-nowrap" data-bs-toggle="modal"
                            data-bs-target="#modalFiltroAsignaciones"
                            title="Descargar Historial de Asignaciones en un rango de fechas">
                            <i class="bi bi-calendar-date me-2"></i>Descargar historial por Fecha
                        </button>
                    </div>
                </div>

                {{-- Filtros --}}
                <div class=" card shadow-sm p-4 mb-4">
                    <form action="{{ route('recursos.assignments.history') }}" method="GET" id="filtroForm">
                        <div class="row g-3">

                            {{-- Filtro por palabra clave --}}
                            <div class="col-md-6 col-lg-3">
                                <input type="text" class="form-control" name="keyword"
                                    title="Buscar por recurso o usuario" placeholder="Nombre Recurso o Usuario"
                                    value="{{ request('keyword') }}">
                            </div>

                            {{-- Filtro por fecha de asignación (Inicio) --}}
                            <div class="col-md-6 col-lg-2">
                                <input type="date" class="form-control" name="fecha_asignacion_start"
                                    title="Fecha de Asignación (Desde)" value="{{ request('fecha_asignacion_start') }}">
                            </div>

                            {{-- Filtro por fecha de devolución (Fin) --}}
                            <div class="col-md-6 col-lg-2">
                                <input type="date" class="form-control" name="fecha_devolucion_end"
                                    title="Fecha de Devolución (Hasta)" value="{{ request('fecha_devolucion_end') }}">

                                @error('fecha_devolucion_end')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Filtro por estado (Pendiente/Devuelto) --}}
                            <div class="col-md-6 col-lg-3">
                                <select class=" form-select" name="estado">
                                    <option value="">Filtrar por Estado</option>
                                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>PENDIENTE (Sin
                                        Devolver)</option>
                                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>DEVUELTO
                                    </option>
                                </select>
                            </div>

                            {{-- Botones de acción --}}
                            <div class="col-md-12 col-lg-2 d-flex">
                                <button type=" submit" class="btn btn-secondary w-100 me-2">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                                {{-- Botón para limpiar filtros --}}
                                <a href="{{ route('recursos.assignments.history') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- Tabla con datos del historial --}}
                <div class="card shadow-sm p-4">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-header-custom">
                                <tr>
                                    <th scope="col">Nº</th>
                                    <th scope="col">Recurso</th>
                                    <th scope="col">Asignado a</th>
                                    <th scope="col">Fecha de Asignación</th>
                                    <th scope="col">Fecha de Devolución</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($asignaciones as $asignacion)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $asignacion->recurso->nombre_rec ?? 'Recurso No Encontrado' }}
                                        </td>
                                        <td>{{ $asignacion->usuario->nombre ?? 'Usuario' }}
                                            {{ $asignacion->usuario->apellido ?? 'Desconocido' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($asignacion->fecha_asignacion)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            @if ($asignacion->fecha_devolucion)
                                                <span
                                                    class="text">{{ \Carbon\Carbon::parse($asignacion->fecha_devolucion)->format('d-m-Y') }}</span>
                                            @else
                                                <span class="text-danger">Sin fecha</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($asignacion->fecha_devolucion)
                                                <span class="badge bg-primary">Devuelto</span>
                                            @else
                                                <span class="badge bg-danger">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!$asignacion->fecha_devolucion)
                                                <form
                                                    action="{{ route('recursos.assignments.mark-returned', $asignacion->id_asignacion) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        title="Marcar como Devuelto"
                                                        onclick="return confirm('¿Estás seguro de que deseas marcar este recurso como devuelto? Esto establecerá la fecha de devolución en la hora actual.')">
                                                        <i class="bi bi-arrow-return-left"></i> Devolver
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-outline-secondary btn-sm" disabled>Completo</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No hay asignaciones
                                            registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    <div class="mt-4 d-flex justify-content-center">
                        {{ $asignaciones->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para filtrar por rango de fechas --}}
    <div class="modal fade" id="modalFiltroAsignaciones" tabindex="-1" aria-labelledby="modalFiltroAsignacionesLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFiltroAsignacionesLabel">Filtrar Historial de Asignaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('recursos.asignaciones.exportar.pdf.fecha') }}" method="GET" 
                    target="_blank" id="exportarPDFForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fecha_desde" class="form-label">Fecha Desde:</label>
                            <input type="date" class="form-control @error('fecha_desde') is-invalid @enderror"
                                id="fecha_desde" name="fecha_desde" required value="{{ old('fecha_desde') }}">
                            @error('fecha_desde')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="fecha_hasta" class="form-label">Fecha Hasta:</label>
                            <input type="date" class="form-control @error('fecha_hasta') is-invalid @enderror"
                                id="fecha_hasta" name="fecha_hasta" required value="{{ old('fecha_hasta') }}">
                            @error('fecha_hasta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Descargar PDF Filtrado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/recursos.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Escucha el evento de envío del formulario de filtros
            $('#filtroForm').on('submit', function (e) {
                var fechaAsignacion = $('input[name="fecha_asignacion_start"]').val();
                var fechaDevolucion = $('input[name="fecha_devolucion_end"]').val();

                // Solo validamos si ambas fechas tienen un valor
                if (fechaAsignacion && fechaDevolucion) {
                    // Convertir las fechas a objetos Date para comparación
                    var start = new Date(fechaAsignacion);
                    var end = new Date(fechaDevolucion);

                    // Comparar. Si la fecha de devolución es menor que la de asignación, cancela el envío.
                    if (end < start) {
                        e.preventDefault(); // Detiene el envío del formulario

                        // Muestra un mensaje de error al usuario (puedes usar alertas, tooltips de Bootstrap, etc.)
                        alert('⚠️ Error: La Fecha de Devolución no puede ser anterior a la Fecha de Asignación.');

                        // Enfoca el campo de error
                        $('input[name="fecha_devolucion_end"]').focus();

                        return false;
                    }
                }
            });

            $('input[name="fecha_asignacion_start"]').on('change', function () {
                var minDate = $(this).val();
                $('input[name="fecha_devolucion_end"]').attr('min', minDate);
            });

            var currentStartDate = $('input[name="fecha_asignacion_start"]').val();
            if (currentStartDate) {
                $('input[name="fecha_devolucion_end"]').attr('min', currentStartDate);
            }

            // Verifica si hay algún error de validación para los campos del modal
            @if ($errors->has('fecha_desde') || $errors->has('fecha_hasta'))
                var modal = new bootstrap.Modal(document.getElementById('modalFiltroAsignaciones'));
                modal.show();
            @endif

            // Validación de rango de fechas para el modal
            $('#exportarPDFForm').on('submit', function (e) {
                var fechaDesde = $('#fecha_desde').val();
                var fechaHasta = $('#fecha_hasta').val();

                if (fechaDesde && fechaHasta) {
                    var start = new Date(fechaDesde);
                    var end = new Date(fechaHasta);

                    if (end < start) {
                        e.preventDefault(); // Detiene el envío

                        alert('⚠️ La "Fecha Hasta" no puede ser anterior a la "Fecha Desde".');

                        $('#fecha_hasta').focus();
                        return false;
                    }
                }
            });

            $('#fecha_desde').on('change', function () {
                var minDate = $(this).val();
                $('#fecha_hasta').attr('min', minDate);
            });

            var currentStartDate = $('#fecha_desde').val();
            if (currentStartDate) {
                $('#fecha_hasta').attr('min', currentStartDate);
            }
        });
    </script>

    @include('components._session-timeout')
</body>

</html>