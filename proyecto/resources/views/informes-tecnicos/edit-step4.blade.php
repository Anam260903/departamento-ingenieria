<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Informe Técnico - Paso 4</title>
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

                {{-- Bloque para mostrar mensajes de sesión --}}
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
                        <input type="hidden" name="calculos_codes" value="">

                        <div class="mb-4">
                            <label for="calculos_codes" class="form-label fw-bold">Seleccionar Códigos de
                                Estimaciones
                            </label>

                            <?php
                                // Obtenemos los IDs previamente seleccionados de la relación
                                $selectedIds = old('calculos_codes', $informe->calculos->pluck('id_calculo')->toArray() ?? []);
                            ?>

                            <select class="form-select" id="calculos_codes" name="calculos_codes[]" multiple size="5">
                                <option value="" disabled>Mantenga Ctrl/Cmd para seleccionar varios</option>

                                <?php foreach ($calculos as $calculo): ?>
                                <option value="<?php    echo $calculo->id_calculo; ?>" <?php    echo in_array($calculo->id_calculo, $selectedIds) ? 'selected' : ''; ?>>
                                    <?php    echo $calculo->codigo_calculo; ?>
                                    (<?php    echo \Illuminate\Support\Str::limit($calculo->contenido, 50); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Seleccione uno o más códigos. La información se
                                consolidará en el campo "Materiales" a continuación.</small>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calculosSelect = document.getElementById('calculos_codes');
            const materialsTextarea = document.getElementById('materials_info');

            calculosSelect.addEventListener('change', function () {
                const selectedCalculoIds = Array.from(this.selectedOptions).map(option => option.value);

                if (selectedCalculoIds.length > 0) {
                    // Petición AJAX para obtener el contenido de los IDs seleccionados
                    fetch("{{ route('calculos.getContenido') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({ calculos_ids: selectedCalculoIds })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error de red al cargar cálculos.');
                            }
                            return response.json();
                        })
                        .then(data => {
                            const consolidatedContent = data.contenido
                                .map((content, index) => `${index + 1}. ${content}`)
                                .join('\n');

                            materialsTextarea.value = consolidatedContent;
                        })
                        .catch(error => {
                            console.error('Error fetching materials:', error);
                            materialsTextarea.value = 'ERROR: No se pudo cargar la información de los cálculos.';
                        });
                } else {
                    materialsTextarea.value = '';
                }
            });
        });
    </script>

</body>

</html>