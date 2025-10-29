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
    <style>
        /* Estilo para la barra de progreso */
        .step-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            color: #6c757d;
            /* Gris para pasos pendientes */
        }

        .step.active {
            color: #007bff;
            /* Azul para el paso actual */
            border-bottom: 3px solid #007bff;
        }

        .step.completed {
            color: #28a745;
            /* Verde para pasos completados (TO DO) */
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include ('components._sidebar')

        <div id="page-content-wrapper">
            @include('components._navbar')

            <div class="container-fluid py-4">
                <h1 class="mb-4 h3">NUEVO INFORME TÉCNICO</h1>

                {{-- Barra de Progreso --}}
                <div class="step-container">
                    <div class="step active">1. Datos Generales</div>
                    <div class="step">2. Antecedentes</div>
                    <div class="step">3. Planteamiento</div>
                    <div class="step">4. Resultados y Rec.</div>
                </div>

                <div class="card shadow-sm p-4">
                    <form action="#" method="POST"> {{-- La acción será para guardar el primer paso --}}
                        @csrf

                        {{-- 1. IDENTIFICACIÓN DE LA INSPECCIÓN (Datos NO EDITABLES) --}}
                        <h5 class="mb-3 text-primary">Información de la Inspección Seleccionada</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Propietario</label>
                                <input type="text" class="form-control"
                                    value="{{ $inspeccion->vivienda->propietario->nombre_propie }} {{ $inspeccion->vivienda->propietario->apellido_propie }}"
                                    disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cédula</label>
                                <input type="text" class="form-control"
                                    value="{{ $inspeccion->vivienda->propietario->cedula_propie }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Inspeccionado por (Ingeniero)</label>
                                <input type="text" class="form-control"
                                    value="{{ $inspeccion->usuario->nombre }} {{ $inspeccion->usuario->apellido }}"
                                    disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Inspección</label>
                                <input type="text" class="form-control"
                                    value="{{ \Carbon\Carbon::parse($inspeccion->fecha_insp)->format('d-m-Y') }}" disabled>
                                <input type="hidden" name="id_insp" value="{{ $inspeccion->id_insp }}">
                            </div>
                        </div>

                        {{-- 2. DATOS GENERALES DEL INFORME (Primer paso del formulario) --}}
                        <h5 class="mb-3 text-primary">1. Datos Generales del Informe</h5>
                        <div class="row">

                            {{-- Campo: Fecha del Informe --}}
                            <div class="col-md-6 mb-4">
                                <label for="fecha_inf" class="form-label">Fecha del Informe (*)</label>
                                <input type="date" class="form-control @error('fecha_inf') is-invalid @enderror"
                                    id="fecha_inf" name="fecha_inf" value="{{ old('fecha_inf', $fecha_inf) }}" required>
                                @error('fecha_inf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Campo: Comunidad (Prellenado con dirección de vivienda, editable) --}}
                            <div class="col-md-6 mb-4">
                                <label for="comunidad" class="form-label">Comunidad/Dirección (*)</label>
                                <input type="text" class="form-control @error('comunidad') is-invalid @enderror"
                                    id="comunidad" name="comunidad" placeholder="Comunidad/Dirección de la vivienda"
                                    value="{{ old('comunidad', $inspeccion->vivienda->direccion) }}" maxlength="100"
                                    required>
                                @error('comunidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Botones de Navegación --}}
                        <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success me-2">Guardar y Continuar</button>
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