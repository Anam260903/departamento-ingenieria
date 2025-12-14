<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Técnico - Paso 4</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
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

                <h1 class="mb-4 h3">INFORME TÉCNICO</h1>

                {{-- Barra de progreso --}}
                <div class="step-container">
                    <div class="step completed">1. Datos Generales</div>
                    <div class="step completed">2. Diagnóstico y observaciones</div>
                    <div class="step completed">3. Recomendaciones y mapa</div>
                    <div class="step active">4. Materiales</div>
                    <div class="step">5. Evidencia fotog.</div>
                </div>

                <div class="card shadow-sm p-4 mt-3">
                    <form action="{{ route('informes.update.step4', $informe->id_inf) }}" method="POST">
                        @csrf

                        <input type="hidden" name="id_inf" value="{{ $informe->id_inf }}">

                        <div class="mb-4">
                            <label for="calculos_codes" class="form-label fw-bold">Seleccionar Códigos de
                                Estimaciones
                            </label>

                            <?php $selectedId = old('calculos_codes', $informe->id_calculo);?>

                            <select class="form-select" id="calculos_codes" name="calculos_codes">
                                <option value="" disabled selected>Seleccione la construcción a realizar</option>

                                @forelse ($calculos as $calculo)
                                    <option value="{{ $calculo->id_calculo }}" @if(isset($selectedId) && $calculo->id_calculo == $selectedId) selected @endif>
                                        {{ $calculo->nombre_calculo }}
                                    </option>
                                @empty
                                    <option value="" disabled>No hay cálculos disponibles</option>
                                @endforelse
                            </select>

                            <small class="form-text text-muted">La información se mostrará en el campo "Materiales" a
                                continuación.</small>
                            <?php if ($errors->has('calculos_codes')): ?>
                            <div class="text-danger"><?php    echo $errors->first('calculos_codes'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label for="materials_info" class="form-label fw-bold">Materiales obtenidos de la estimación
                                (Editable)</label>
                            <textarea class="form-control" id="materials_info" name="materials_info"
                                rows="12"><?php echo old('materials_info', $informe->materials_info ?? ''); ?></textarea>
                            <small class="form-text text-muted">Este campo es editable. Puede modificar, agregar o
                                eliminar información manualmente.</small>
                            <?php if ($errors->has('materials_info')): ?>
                            <div class="text-danger"><?php    echo $errors->first('materials_info'); ?></div>
                            <?php endif; ?>
                        </div>


                        {{-- Botonos de navegación --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('informes.edit.step3', $informe->id_inf) }}" class="btn btn-secondary">
                                Volver
                            </a>
                            <button type="submit" class="btn btn-primary w-auto">
                                Guardar y Continuar
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    <script>
        window.CSRF_TOKEN = "{{ csrf_token() }}";
        window.CALCULOS_DATA = {!! $calculosJson !!};
        window.INITIAL_CONTENT = @json(old('materials_info', $informe->materials_info ?? ''));
    </script>

    <script src="{{ asset('js/informes.js') }}"></script>

    @include('components._session-timeout')
</body>

</html>