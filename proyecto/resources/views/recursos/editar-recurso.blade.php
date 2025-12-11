<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Recurso</title>
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
                <h1 class="mb-4 h3">RECURSOS</h1>
                <h2 class="h4 mb-4">Editar Recurso #{{ $recurso->id_recurso }}</h2>

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card shadow-sm p-4">

                    <form action="{{ route('recursos.update', $recurso->id_recurso) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Sección Datos del Recurso --}}
                        <h5 class="mb-3">Datos del Recurso</h5>
                        <div class="row mb-4">
                            {{-- Campo código --}}
                            <div class="col-md-6 mb-3">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                                    id="codigo" name="codigo" placeholder="Código único del recurso" maxlength="20"
                                    value="{{ old('codigo', $recurso->codigo) }}" required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo nombre --}}
                            <div class="col-md-6 mb-3">
                                <label for="nombre_rec" class="form-label">Nombre</label>
                                <input type="text" class="form-control @error('nombre_rec') is-invalid @enderror"
                                    id="nombre_rec" name="nombre_rec" placeholder="Ej:..."
                                    maxlength="30" value="{{ old('nombre_rec', $recurso->nombre_rec) }}" required>
                                @error('nombre_rec')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            {{-- Campo descripción --}}
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                    id="descripcion" name="descripcion" rows="3" maxlength="255"
                                    placeholder="Detalles técnicos o características principales" required>{{ old('descripcion', $recurso->descripcion) }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            {{-- Campo observación --}}
                            <div class="col-md-12 mb-3">
                                <label for="observacion" class="form-label">Observación</label>
                                <textarea class="form-control @error('observacion') is-invalid @enderror"
                                    id="observacion" name="observacion" rows="3"
                                    placeholder="Condición del recurso, ubicación inicial, etc.">{{ old('observacion', $recurso->observacion) }}</textarea>
                                @error('observacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="d-flex justify-content-end gap-2">
                            {{-- Botón cancelar: Vuelve al listado de recursos --}}
                            <a href="{{ route('recursos.index') }}" class="btn btn-secondary">Cancelar</a>
                            {{-- Botón guardar --}}
                            <button type="submit" class="btn btn-primary w-auto">Guardar Cambios</button>
                        </div>
                    </form>

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