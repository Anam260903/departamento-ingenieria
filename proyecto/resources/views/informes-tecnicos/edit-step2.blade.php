<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Informe Técnico - Paso 2</title>
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

                {{-- Bloque para mostrar mensajes de Sesión --}}
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

                <h1 class="mb-4 h3">NUEVO INFORME TÉCNICO</h1>

                {{-- Barra de Progreso: El paso 2 debe estar 'active' --}}
                <div class="step-container">
                    <div class="step completed">1. Datos Generales</div>
                    <div class="step active">2. Diagnóstico y observaciones</div>
                    <div class="step">3. Recomendaciones</div>
                    <div class="step">4. Materiales</div>
                    <div class="step">5. Evidencia fotog.</div>
                </div>

                <div class="card shadow-sm p-4 mt-3">
                    {{-- Formulario apunta a la nueva ruta de actualización --}}
                    <form action="{{ route('informes.update.step2', $informe->id_inf) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Usamos PUT para actualizar el recurso existente --}}

                        {{-- ID del informe que estamos editando --}}
                        <input type="hidden" name="id_inf" value="{{ $informe->id_inf }}">

                        {{-- DIAGNÓSTICO Y OBSERVACIONES DEL INFORME (Segundo paso del formulario) --}}

                        <div class="row mb-4">
                            {{-- Campo: Antecedentes --}}
                            <div class="col-md-6 mb-12">
                                <label for="antecedentes" class="form-label">Antecedentes</label>
                                <textarea class="form-control @error('antecedentes') is-invalid @enderror"
                                    id="antecedentes" name="antecedentes" rows="10"
                                    placeholder="Antecedentes relevantes relacionados con la vivienda."
                                    required>{{ old('antecedentes', $informe->antecedentes) }}</textarea>

                                @error('antecedentes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Campo: Planteamiento --}}
                            <div class="col-md-6 mb-12">
                                <label for="planteamiento" class="form-label">Planteamiento del problema</label>
                                <textarea class="form-control @error('planteamiento') is-invalid @enderror" id="planteamiento" name="planteamiento"
                                    rows="10"
                                    placeholder="Planteamiento del problema relacionado con la vivienda."
                                    required>{{ old('planteamiento', $informe->planteamiento) }}</textarea>
                            
                                @error('planteamiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Campo: Caracteristicas --}}
                            <div class="col-md-6 mb-12" style="margin-top: 20px;">
                                <label for="caracteristicas" class="form-label">Características de la vivienda</label>
                                <textarea class="form-control @error('caracteristicas') is-invalid @enderror" id="caracteristicas" name="caracteristicas"
                                    rows="10" placeholder="Características relevantes de la vivienda."
                                    required>{{ old('caracteristicas', $informe->inspeccion->vivienda->caracteristicas) }}</textarea>
                            
                                @error('caracteristicas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Campo: Resultados --}}
                            <div class="col-md-6 mb-12" style="margin-top: 20px;">
                                <label for="resultados" class="form-label">Resultados de la inspección</label>
                                <textarea class="form-control @error('resultados') is-invalid @enderror" id="resultados" name="resultados"
                                    rows="10" placeholder="Resultados relevantes de la inspección."
                                    required>{{ old('resultados', $informe->resultados) }}</textarea>
                            
                                @error('resultados')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        {{-- Botones de Navegación --}}
                        <div class="d-flex justify-content-between mt-4">
                            {{-- Botón de retroceso al Paso 1 --}}
                            <a href="{{ route('informes.edit.step1', $informe->id_inf) }}" class="btn btn-secondary">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    {{-- No necesitamos el script de informes.js aquí, la funcionalidad está en el controlador --}}
</body>

</html>