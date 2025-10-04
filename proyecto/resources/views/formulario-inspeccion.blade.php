<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Inspección</title>
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
                <h1 class="mb-4 h3">GESTIÓN DE INSPECCIONES</h1>
                <h2 class="h4 mb-4">Nueva Inspección</h2>

                {{-- Muestra mensajes de éxito o error --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card shadow-sm p-4">
                    <form action="{{ route('inspecciones.store') }}" method="POST">
                        @csrf
                        
                        {{-- SECCIÓN DATOS DE LA INSPECCIÓN --}}
                        <h5 class="mb-3">Datos de la Inspección</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{ old('fecha') }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                    <option value="" disabled selected>Estado de la inspección</option>
                                    <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="1" {{ old('estado') == '1' ? 'selected' : '' }}>Completada</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SECCIÓN DATOS DEL PROPIETARIO --}}
                        <h5 class="mb-3">Datos del Propietario</h5>
                        <div class="row mb-4">
                            {{-- Nombre --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="propietario_nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control @error('propietario_nombre') is-invalid @enderror" id="propietario_nombre" name="propietario_nombre" placeholder="Nombre del propietario" maxlength="30" value="{{ old('propietario_nombre') }}" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se permiten letras y espacios">
                                @error('propietario_nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Apellido --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="propietario_apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control @error('propietario_apellido') is-invalid @enderror" id="propietario_apellido" name="propietario_apellido" placeholder="Apellido del propietario" maxlength="30" value="{{ old('propietario_apellido') }}" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se permiten letras y espacios">
                                @error('propietario_apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Cédula --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="propietario_cedula" class="form-label">Cédula</label>
                                <input type="text" class="form-control @error('propietario_cedula') is-invalid @enderror" id="propietario_cedula" name="propietario_cedula" placeholder="Cédula del propietario" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="8" value="{{ old('propietario_cedula') }}" required>
                                @error('propietario_cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Teléfono --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="propietario_telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('propietario_telefono') is-invalid @enderror" id="propietario_telefono" name="propietario_telefono" placeholder="Teléfono del propietario" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="11" value="{{ old('propietario_telefono') }}" required>
                                @error('propietario_telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SECCIÓN DATOS DE LA VIVIENDA / OBSERVACIÓN --}}
                        <h5 class="mb-3">Datos de la Vivienda y Observaciones</h5>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" placeholder="Ubicación de la vivienda" maxlength="100" value="{{ old('direccion') }}" required>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea class="form-control @error('observacion') is-invalid @enderror" id="observacion" name="observacion" rows="3" maxlength="250" placeholder="Observación" >{{ old('observacion') }}</textarea>
                            @error('observacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registrar Inspección</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/inspecciones.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script> 
</body>
</html>