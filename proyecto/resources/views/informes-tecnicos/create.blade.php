<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Informe Técnico - Paso 1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/informes.css') }}">
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include ('components._sidebar')

        <div id="page-content-wrapper">
            @include('components._navbar')
            <div class="container-fluid py-4">

                {{-- Bloque para mostrar mensajes de éxito o error de sesión --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="container-fluid py-4">
                    <h1 class="mb-4 h3">NUEVO INFORME TÉCNICO</h1>

                    {{-- Barra de progreso --}}
                    <div class="step-container">
                        <div class="step active">1. Datos Generales</div>
                        <div class="step">2. Diagnóstico</div>
                        <div class="step">3. Recomendaciones</div>
                        <div class="step">4. Materiales</div>
                        <div class="step">4. Evidencia fotog.</div>
                    </div>

                    <div class="card shadow-sm p-4">
                        @php
                            // Detectamos si es una edición o una creación
                            $isEditing = isset($informe) && $informe->id_inf;

                            // Si es edición, apuntamos a la ruta de edición
                            $formAction = $isEditing
                                ? route('informes.update.step1', $informe->id_inf)
                                : route('informes.store.step1');
                        @endphp
                        <form action="{{ $formAction }}" method="POST">
                            @csrf

                            {{-- Si estamos editando, usamos el método PUT --}}
                            @if ($isEditing)
                                @method('PUT')
                                <input type="hidden" name="id_inf" value="{{ $informe->id_inf }}">
                            @endif

                            <input type="hidden" name="id_insp" value="{{ $inspeccion->id_insp }}">

                            {{-- DATOS GENERALES DEL INFORME (Paso 1 del informe técnico) --}}
                            <div class="row mb-4">

                                <div class="row mb-6">
                                    {{-- Campo: Fecha --}}
                                    <div class="col-md-6 mb-4">
                                        <label for="fecha_inf" class="form-label">Fecha de la Inspección</label>
                                        <input type="date" class="form-control @error('fecha_inf') is-invalid @enderror"
                                            id="fecha_inf" name="fecha_inf"
                                            value="{{ old('fecha_inf', $informe->fecha_inf ?? \Carbon\Carbon::now()->format('Y-m-d')) }}"
                                            required>
                                        @error('fecha_inf')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Campo: Ingeniero inspecctor --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Profesional asignado</label>
                                        <input type="text" class="form-control"
                                            value="{{ $inspeccion->usuario->nombre }} {{ $inspeccion->usuario->apellido }}"
                                            disabled>
                                    </div>
                                </div>

                                <div>

                                    {{-- Campo: Comunidad --}}
                                    <div class="col-md-12 mb-4">
                                        <label for="comunidad" class="form-label">Comunidad y/o proyecto</label>
                                        <input type="text" class="form-control @error('comunidad') is-invalid @enderror"
                                            id="comunidad" name="comunidad" placeholder="Comunidad y/o proyecto"
                                            maxlength="50" value="{{ old('comunidad', $informe->comunidad ?? '') }}"
                                            required>
                                        @error('comunidad')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>

                                </div>

                                <div class="row mb-4">
                                    {{-- Campos: Nombre y apellido del responsable de la vivienda --}}
                                    <h5>Responsable de la vivienda</h5>
                                    <div class="col-md-3 mb-3">
                                        <label for="propietario_nombre" class="form-label">Nombre</label>
                                        <input type="text"
                                            class="form-control @error('propietario_nombre') is-invalid @enderror"
                                            id="propietario_nombre" name="propietario_nombre"
                                            placeholder="Nombre del propietario" maxlength="30"
                                            value="{{ old('propietario_nombre', $inspeccion->vivienda->propietario->nombre_propie ?? '') }}"
                                            pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se permiten letras y espacios"
                                            required>
                                        @error('propietario_nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="propietario_apellido" class="form-label">Apellido</label>
                                        <input type="text"
                                            class="form-control @error('propietario_apellido') is-invalid @enderror"
                                            id="propietario_apellido" name="propietario_apellido"
                                            placeholder="Apellido del propietario" maxlength="30"
                                            value="{{ old('propietario_apellido', $inspeccion->vivienda->propietario->apellido_propie ?? '') }}"
                                            pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se permiten letras y espacios"
                                            required>
                                        @error('propietario_apellido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Campo: Cédula del responsable de la vivienda --}}
                                    <div class="col-md-3 mb-3">
                                        <label for="propietario_cedula" class="form-label">Cédula</label>
                                        <input type="text"
                                            class="form-control @error('propietario_cedula') is-invalid @enderror"
                                            id="propietario_cedula" name="propietario_cedula"
                                            placeholder="Cédula del propietario"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            maxlength="8"
                                            value="{{ old('propietario_cedula', $inspeccion->vivienda->propietario->cedula_propie ?? '') }}"
                                            required>
                                        @error('propietario_cedula')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Campo: Telefono del responsable de la vivienda --}}
                                    <div class="col-md-3 mb-3">
                                        <label for="propietario_telefono" class="form-label">Teléfono</label>
                                        <input type="text"
                                            class="form-control @error('propietario_telefono') is-invalid @enderror"
                                            id="propietario_telefono" name="propietario_telefono"
                                            placeholder="Teléfono del propietario"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            maxlength="11"
                                            value="{{ old('propietario_telefono', $inspeccion->vivienda->propietario->telefono ?? '') }}"
                                            required>
                                        @error('propietario_telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Campo: Dirección --}}
                                <div class="col-md-12 mb-4">
                                    <label for="direccion" class="form-label">Dirección de la vivienda</label>
                                    <input type="text" class="form-control @error('direccion') is-invalid @enderror"
                                        id="direccion" name="direccion" placeholder="Dirección de la vivienda"
                                        value="{{ old('direccion', $inspeccion->vivienda->direccion) }}" maxlength="100"
                                        required> @error('direccion') <div class="invalid-feedback">{{ $message }}
                                            </div>
                                        @enderror
                                </div>
                            </div>


                    </div>

                    {{-- Botones de Navegación --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary w-auto me-2">Guardar y Continuar
                        </button>
                        <a href="{{ route('informes.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/informes.js') }}"></script>
</body>

</html>