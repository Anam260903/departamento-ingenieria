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
                <h1 class="mb-4">GESTIÓN DE INSPECCIONES</h1>
                <h2 class="h4 mb-4">Nueva Inspección</h2>

                <div class="card shadow-sm p-4">
                    <form action="{{ route('inspecciones.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="propietario_nombre" class="form-label">Propietario</label>
                                <input  type="text" class="form-control @error('propietario_nombre') is-invalid @enderror" id="propietario_nombre" name="propietario_nombre" placeholder="Nombre del propietario" maxlength="30" required>
                                 @error('propietario_nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="propietario_apellido" class="form-label">Propietario (Apellido)</label>
                                <input type="text" class="form-control @error('propietario_apellido') is-invalid @enderror" id="propietario_apellido" name="propietario_apellido" placeholder="Apellido del propietario" maxlength="30" required>
                                @error('propietario_apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="propietario_cedula" class="form-label">Cédula</label>
                                <input type="text" class="form-control @error('propietario_cedula') is-invalid @enderror" id="propietario_cedula" name="propietario_cedula" placeholder="Cédula del propietario" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="8" required>
                                @error('propietario_cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="propietario_telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('propietario_telefono') is-invalid @enderror" id="propietario_telefono" name="propietario_telefono" placeholder="Teléfono del propietario" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="11" required>
                                @error('propietario_telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror                                    
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" placeholder="Ubicación de la vivienda" maxlength="50" required>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                <option selected disabled>Estado de la inspección</option>
                                <option value="0">Pendiente</option>
                                <option value="1">Completada</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea class="form-control @error('observacion') is-invalid @enderror" id="observacion" name="observacion" rows="3"></textarea>
                            @error('observacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>