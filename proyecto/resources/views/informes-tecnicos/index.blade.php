<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informes Técnicos</title>
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
            
                <h1 class="mb-4 h3">INFORMES TÉCNICOS</h1>
                
                {{-- Bloque de Alertas --}}
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

                    {{-- Botón "Nuevo informe" --}}
                    <div class="col-auto">
                        <a href="{{ route('informes.create') }}" class="btn btn-primary text-nowrap">
                            <i class="bi bi-plus-circle me-2"></i>Nuevo informe
                        </a>
                    </div>
                </div>
                
                {{-- Filtros --}}
                <div class="card shadow-sm p-4 mb-4">
                    <form action="{{ route('informes.index') }}" method="GET">
                        <div class="row g-3">
                            {{-- Filtro por Palabra Clave --}}
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="keyword"
                                    placeholder="Buscar por Propietario, Dirección o Ingeniero"
                                    value="{{ request('keyword') }}">
                            </div>
                            {{-- Filtro por Ingeniero 
                            <div class="col-md-3">
                                <select class="form-select" name="ingeniero">
                                    <option value="">Filtrar por Ingeniero</option>
                                </select>
                            </div> --}}
                            {{-- Filtro por Fecha --}}
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label visually-hidden">Fecha Desde</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                    title="Fecha Desde" value="{{ request('fecha_inicio') }}">
                            </div>
                            {{-- Botones de Acción --}}
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
                                                {{-- 1. Botón de Editar --}}
                                                <a href="#" class="btn btn-warning btn-sm" title="Editar informe">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                {{-- 2. Botón de Descargar PDF --}}
                                                <a href="#" class="btn btn-danger btn-sm" title="Descargar PDF">
                                                    <i class="bi bi-file-pdf-fill"></i>
                                                </a>
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
                    
                    {{-- Enlaces de Paginación --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $informes->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/informes.js') }}"></script>
</body>
</html>