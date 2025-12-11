<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma de Decisiones</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/decisiones.css') }}">
</head>

<body>
    <div class="d-flex" id="wrapper">

        @include('components._sidebar')

        <div id="page-content-wrapper">

            @include('components._navbar')

            <div class="container-fluid py-4">

                <h1 class="mb-4 h3">TOMA DE DECISIONES</h1>
                <p class="text-muted">Seleccione dos inspecciones para compararlas basándose en criterios de riesgo y
                    logística.</p>

                {{-- Bloque de alertas --}}
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

                {{-- Comparación de inspecciones --}}
                <div id="comparison-tool">

                    <div class="row">

                        {{-- Columna de inspección 1 --}}
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header bg-primary text-white py-3 rounded-top-4">
                                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-1-circle me-2"></i> Inspección 1
                                    </h5>
                                </div>
                                <div class="card-body">

                                    {{-- Selector de inspección --}}
                                    <div class="mb-3">
                                        <label for="inspeccion1_id" class="form-label fw-bold">Seleccionar
                                            Inspección:</label>
                                        <select class="form-select" id="inspeccion1_id" name="inspeccion1_id">
                                            <option value="" selected>Seleccione aquí</option>
                                            @foreach($inspecciones as $inspeccion)
                                                <option value="{{ $inspeccion['id'] }}"
                                                    data-fecha="{{ $inspeccion['fecha'] }}"
                                                    data-comunidad="{{ $inspeccion['comunidad'] }}"
                                                    data-propietario="{{ $inspeccion['propietario'] }}">
                                                    ID: {{ $inspeccion['id'] }} - {{ $inspeccion['propietario'] }}
                                                    ({{ $inspeccion['comunidad'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Información de la vivienda 1 --}}
                                    <div id="vivienda_info_1" class="mb-4" style="display: none;">
                                        <h6 class="text-secondary fw-bold">VIVIENDA DE: <span id="propietario_1"
                                                class="text-dark"></span></h6>
                                        <p class="mb-1"><small>Fecha de Inspección: <span id="fecha_1"></span></small>
                                        </p>
                                        <p class="mb-3"><small>Comunidad: <span id="comunidad_1"></span></small></p>

                                        <hr>
                                        <p class="fw-bold text-muted">Checklist de Criterios (Marcar según sea el caso):
                                        </p>

                                        {{-- Checklist para la inspección 1 --}}
                                        {{-- Ponderaciones: 3+5+4+2+1 = Máx 15 --}}
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q1_1" data-weight="3">
                                            <label class="form-check-label" for="q1_1">¿El Informe Técnico ha sido
                                                aprobado?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q2_1" data-weight="5">
                                            <label class="form-check-label" for="q2_1">¿El informe clasifica la vivienda
                                                como de Alto Riesgo Estructural (urgencia)?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q3_1" data-weight="4">
                                            <label class="form-check-label" for="q3_1">¿Los recursos/materiales críticos
                                                requeridos están disponibles?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q4_1" data-weight="2">
                                            <label class="form-check-label" for="q4_1">¿Hay personal con la especialidad
                                                requerida disponible?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-1" type="checkbox" value="1"
                                                id="q5_1" data-weight="1">
                                            <label class="form-check-label" for="q5_1">¿La ubicación de la vivienda se
                                                encuentra en una zona de alta prioridad de ejecución actual?</label>
                                        </div>
                                        <p class="mt-3"><small class="text-info fw-bold">Puntaje: <span
                                                    id="current_score_1">0</span>/15</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Columna de inspección 2 --}}
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-header bg-secondary text-white py-3 rounded-top-4">
                                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-2-circle me-2"></i> Inspección 2
                                    </h5>
                                </div>
                                <div class="card-body">

                                    {{-- Selector de inspección --}}
                                    <div class="mb-3">
                                        <label for="inspeccion2_id" class="form-label fw-bold">Seleccionar
                                            Inspección:</label>
                                        <select class="form-select" id="inspeccion2_id" name="inspeccion2_id">
                                            <option value="" selected>Seleccione aquí</option>
                                            @foreach($inspecciones as $inspeccion)
                                                <option value="{{ $inspeccion['id'] }}"
                                                    data-fecha="{{ $inspeccion['fecha'] }}"
                                                    data-comunidad="{{ $inspeccion['comunidad'] }}"
                                                    data-propietario="{{ $inspeccion['propietario'] }}">
                                                    ID: {{ $inspeccion['id'] }} - {{ $inspeccion['propietario'] }}
                                                    ({{ $inspeccion['comunidad'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Información de la vivienda 2 --}}
                                    {{-- Ponderaciones: 3+5+4+2+1 = Máx 15 --}}
                                    <div id="vivienda_info_2" class="mb-4" style="display: none;">
                                        <h6 class="text-secondary fw-bold">VIVIENDA DE: <span id="propietario_2"
                                                class="text-dark"></span></h6>
                                        <p class="mb-1"><small>Fecha de Inspección: <span id="fecha_2"></span></small>
                                        </p>
                                        <p class="mb-3"><small>Comunidad: <span id="comunidad_2"></span></small></p>

                                        <hr>
                                        <p class="fw-bold text-muted">Checklist de Criterios (Marcar según sea el caso):
                                        </p>

                                        {{-- Checklist para la inspección 2 --}}
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q1_2" data-weight="3">
                                            <label class="form-check-label" for="q1_2">¿El Informe Técnico ha sido
                                                aprobado?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q2_2" data-weight="5">
                                            <label class="form-check-label" for="q2_2">¿El informe clasifica la vivienda
                                                como de Alto Riesgo Estructural (urgencia)?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q3_2" data-weight="4">
                                            <label class="form-check-label" for="q3_2">¿Los recursos/materiales críticos
                                                requeridos están disponibles?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q4_2" data-weight="2">
                                            <label class="form-check-label" for="q4_2">¿Hay personal con la especialidad
                                                requerida disponible?</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input check-inspeccion-2" type="checkbox" value="1"
                                                id="q5_2" data-weight="1">
                                            <label class="form-check-label" for="q5_2">¿La ubicación de la vivienda se
                                                encuentra en una zona de alta prioridad de ejecución actual?</label>
                                        </div>
                                        <p class="mt-3"><small class="text-info fw-bold">Puntaje: <span
                                                    id="current_score_2">0</span>/15</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Resultados y botón --}}
                    <div class="row mt-4">
                        <div class="col-12 text-center mb-4">
                            <button type="button" class="btn btn-success btn-lg shadow w-auto" id="btn-comparar">
                                <i class="bi bi-check-circle-fill me-2"></i>Comparar y Obtener Recomendación
                            </button>
                        </div>

                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-4">
                                <div class="card-body p-4">
                                    <h4 class="fw-bold">Resultado de Prioridad: <span id="comparison-result"
                                            class="text-secondary">Pendiente</span></h4>
                                    <hr>
                                    <h4 class="fw-bold">Recomendación de Ejecución:</h4>
                                    <p id="comparison-recommendation">Seleccione dos inspecciones, complete el checklist
                                        y presione el botón para realizar la comparación de prioridad.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select1 = document.getElementById('inspeccion1_id');
            const select2 = document.getElementById('inspeccion2_id');
            const btnComparar = document.getElementById('btn-comparar');
            const resultSpan = document.getElementById('comparison-result');
            const recommendationP = document.getElementById('comparison-recommendation');
            const maxScore = 15;

            // Resetear el panel de resultados
            function resetResults() {
                resultSpan.classList.remove('text-success', 'text-danger', 'text-warning', 'text-secondary');
                resultSpan.classList.add('text-secondary');
                resultSpan.textContent = 'Pendiente';
                recommendationP.textContent = 'Seleccione dos inspecciones, complete el checklist y presione el botón para realizar la comparación de prioridad.';
            }

            // Calcular la prioridad actual y actualizar el display
            function calculatePriorityScore(panelNumber) {
                let score = 0;
                const checkboxes = document.querySelectorAll(`.check-inspeccion-${panelNumber}`);

                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const weight = parseInt(checkbox.dataset.weight);
                        score += weight;
                    }
                });
                document.getElementById(`current_score_${panelNumber}`).textContent = score;
                return score;
            }

            // Recalcular puntajes al cambiar un checkbox
            function recalculateOnCheck(panelNumber) {
                calculatePriorityScore(panelNumber);
                resetResults();
            }

            /**
             * Validar que no se seleccione la misma inspección en ambos selects.
             */
            function validateSelection(changedSelect, otherSelect, panelNumber) {
                const changedValue = changedSelect.value;
                const otherValue = otherSelect.value;

                if (changedValue && changedValue === otherValue) {
                    // Si la ID es la misma y no es la opción vacía
                    alert('¡Atención! No puedes seleccionar la misma inspección en los dos paneles. Por favor, elige una inspección diferente.');

                    // Restablecer la selección y la UI del panel que se acaba de modificar
                    changedSelect.value = '';
                    document.getElementById(`vivienda_info_${panelNumber}`).style.display = 'none';
                    document.querySelectorAll(`.check-inspeccion-${panelNumber}`).forEach(cb => cb.checked = false);
                    document.getElementById(`current_score_${panelNumber}`).textContent = '0';

                    resetResults();
                    return false;
                }
                return true;
            }

            // Actualizar la información de la vivienda 
            function updateViviendaInfo(selectElement, panelNumber) {

                // Si la validación falla, se detiene la ejecución
                if (!validateSelection(selectElement, (panelNumber === 1 ? select2 : select1), panelNumber)) {
                    return;
                }

                const infoDiv = document.getElementById(`vivienda_info_${panelNumber}`);
                const selectedOption = selectElement.options[selectElement.selectedIndex];

                if (selectedOption.value) {
                    document.getElementById(`propietario_${panelNumber}`).textContent = selectedOption.dataset.propietario;
                    document.getElementById(`fecha_${panelNumber}`).textContent = selectedOption.dataset.fecha;
                    document.getElementById(`comunidad_${panelNumber}`).textContent = selectedOption.dataset.comunidad;
                    infoDiv.style.display = 'block';
                } else {
                    infoDiv.style.display = 'none';
                    document.querySelectorAll(`.check-inspeccion-${panelNumber}`).forEach(cb => cb.checked = false);
                    document.getElementById(`current_score_${panelNumber}`).textContent = '0';
                }

                resetResults();
            }

            // Eventos para actualizar la información al cambiar el select
            select1.addEventListener('change', () => updateViviendaInfo(select1, 1));
            select2.addEventListener('change', () => updateViviendaInfo(select2, 2));

            // Eventos para recalcular el puntaje en tiempo real al marcar/desmarcar
            document.querySelectorAll('.check-inspeccion-1').forEach(cb => {
                cb.addEventListener('change', () => recalculateOnCheck(1));
            });
            document.querySelectorAll('.check-inspeccion-2').forEach(cb => {
                cb.addEventListener('change', () => recalculateOnCheck(2));
            });


            // Lógica de comparación al presionar el botón
            btnComparar.addEventListener('click', function () {
                const id1 = select1.value;
                const id2 = select2.value;

                if (!id1 || !id2) {
                    alert('Debe seleccionar ambas inspecciones para realizar la comparación.');
                    return;
                }

                // Ya validamos que no sean iguales al cambiar, pero se puede hacer una doble verificación
                if (id1 === id2) {
                    alert('Error de comparación: Las inspecciones seleccionadas son la misma. Por favor, elige dos diferentes.');
                    return;
                }

                // 1. Obtener los puntajes de prioridad
                const score1 = calculatePriorityScore(1);
                const score2 = calculatePriorityScore(2);

                const propietario1 = document.getElementById('propietario_1').textContent;
                const propietario2 = document.getElementById('propietario_2').textContent;

                // 2. Aplicar la lógica de comparación
                let resultText = '';
                let recommendationText = '';

                resultSpan.classList.remove('text-success', 'text-danger', 'text-warning', 'text-secondary');

                // Restricción para puntuación 0
                if (score1 === 0 && score2 === 0) {
                    resultText = `Baja Prioridad. | Puntaje: 0/${maxScore}`;
                    recommendationText = `Ninguna de las inspecciones (${propietario1} y ${propietario2}) marcó criterios críticos o de alta prioridad. Se recomienda asignar una prioridad baja o diferida en el cronograma de ejecución, o reevaluar la necesidad basándose en otros factores externos.`;
                    resultSpan.classList.add('text-success'); // Verde para indicar que no hay urgencia

                }

                // Lógica de empate (Puntuación > 0)
                else if (score1 === score2) {
                    // Empate (sólo si score1 y score2 son > 0)
                    resultText = `¡Empate Técnico! | Puntaje: ${score1}/${maxScore}`;
                    recommendationText = `Ambas inspecciones, ${propietario1} y ${propietario2}, obtuvieron la misma puntuación de ${score1} puntos. Se recomienda una revisión detallada de los factores de riesgo (Alto Riesgo Estructural) y la Disponibilidad de Recursos para desempatar la prioridad.`;
                    resultSpan.classList.add('text-warning');
                }

                // Lógica de Mayor/Menos prioridad
                else {
                    let ganador = (score1 > score2) ? propietario1 : propietario2;
                    let perdedor = (score1 > score2) ? propietario2 : propietario1;
                    let puntuacionGanador = Math.max(score1, score2);
                    let puntuacionPerdedor = Math.min(score1, score2);
                    let diferencia = Math.abs(score1 - score2);

                    resultText = `Prioridad Clara: El proyecto de ${ganador} (${puntuacionGanador} pts) tiene mayor prioridad sobre ${perdedor} (${puntuacionPerdedor} pts).`;

                    // Adaptando la lógica de recomendación a la escala de 15 puntos:
                    if (diferencia >= 5) { // Crítica: 5 puntos o más (33% del máximo)
                        recommendationText = `Existe una diferencia crítica de ${diferencia} puntos. La inspección de ${ganador} debe ser ejecutada de inmediato. Concentre los esfuerzos logísticos y de personal en esta ubicación.`;
                        resultSpan.classList.add('text-danger');
                    } else if (diferencia >= 3) { // Moderada: 3 o 4 puntos
                        recommendationText = `Existe una diferencia moderada de ${diferencia} puntos. Se sugiere priorizar a ${ganador}. Verifique si la diferencia se debe al factor de Alto Riesgo o la Disponibilidad de Recursos.`;
                        resultSpan.classList.add('text-warning');
                    } else { // Pequeña: 1 o 2 puntos
                        recommendationText = `La diferencia de ${diferencia} puntos es muy pequeña. Aunque ${ganador} tiene una ligera ventaja, es vital reevaluar si un cambio en la disponibilidad de recursos (personal/materiales) podría cambiar rápidamente la prioridad.`;
                        resultSpan.classList.add('text-success');
                    }
                }

                // 3. Mostrar el resultado y la recomendación
                resultSpan.textContent = resultText;
                recommendationP.innerHTML = recommendationText;
            });

            // Código para el dropdown de notificaciones
            var toggleButton = document.getElementById('NotificacionToggle');
            if (toggleButton) {
                // Crear una nueva instancia de Dropdown de Bootstrap
                var dropdown = new bootstrap.Dropdown(toggleButton);

                // Agrega un listener de click para manejar el toggle
                toggleButton.addEventListener('click', function (e) {
                    e.preventDefault(); // Previene el comportamiento por defecto del enlace '#'
                    dropdown.toggle();  // Fuerza la acción de mostrar/ocultar
                });
            }
        });
    </script>

    @include('components._session-timeout')
</body>

</html>