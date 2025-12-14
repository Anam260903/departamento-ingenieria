// public/js/decisiones.js

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
        // Se usa querySelectorAll para obtener los elementos con el prefijo de clase
        const checkboxes = document.querySelectorAll(`.check-inspeccion-${panelNumber}`);

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                // El peso se toma del atributo data-weight
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
            alert('¡Atención! No puedes seleccionar la misma inspección en los dos paneles. Por favor, elige una inspección diferente.');

            // Restablecer la selección y la UI del panel que se acaba de modificar
            changedSelect.value = '';
            document.getElementById(`vivienda_info_${panelNumber}`).style.display = 'none';
            // Restablece el estado de los checkboxes
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
            // Actualizar la información de la vivienda en el panel
            document.getElementById(`propietario_${panelNumber}`).textContent = selectedOption.dataset.propietario;
            document.getElementById(`fecha_${panelNumber}`).textContent = selectedOption.dataset.fecha;
            document.getElementById(`comunidad_${panelNumber}`).textContent = selectedOption.dataset.comunidad;
            infoDiv.style.display = 'block';

            // El contenido del checklist NO debe limpiarse o reiniciarse aquí
            // Simplemente se asegura de que el score se muestre si ya hay una selección
            calculatePriorityScore(panelNumber);

        } else {
            // Limpiar y ocultar si se selecciona la opción vacía
            infoDiv.style.display = 'none';
            document.querySelectorAll(`.check-inspeccion-${panelNumber}`).forEach(cb => cb.checked = false);
            document.getElementById(`current_score_${panelNumber}`).textContent = '0';
        }

        resetResults();
    }

    // Eventos para actualizar la información al cambiar el select
    if (select1) select1.addEventListener('change', () => updateViviendaInfo(select1, 1));
    if (select2) select2.addEventListener('change', () => updateViviendaInfo(select2, 2));

    // Eventos para recalcular el puntaje en tiempo real al marcar/desmarcar
    document.querySelectorAll('.check-inspeccion-1').forEach(cb => {
        cb.addEventListener('change', () => recalculateOnCheck(1));
    });
    document.querySelectorAll('.check-inspeccion-2').forEach(cb => {
        cb.addEventListener('change', () => recalculateOnCheck(2));
    });


    // Lógica de comparación al presionar el botón
    if (btnComparar) {
        btnComparar.addEventListener('click', function () {
            const id1 = select1.value;
            const id2 = select2.value;

            if (!id1 || !id2) {
                alert('Debe seleccionar ambas inspecciones para realizar la comparación.');
                return;
            }

            // Doble verificación para asegurar que no sean iguales
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
                if (puntuacionGanador >= 8 && diferencia >= 3) { // Alta Puntuación general y diferencia notable
                    recommendationText = `La inspección de ${ganador} con ${puntuacionGanador} puntos se considera de **Alta Prioridad**. Existe una diferencia crítica de ${diferencia} puntos. Concentre los esfuerzos logísticos y de personal en esta ubicación.`;
                    resultSpan.classList.add('text-danger');
                } else if (diferencia >= 5) { // Crítica: 5 puntos o más (33% del máximo)
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
    }

    // Código para el dropdown de notificaciones (se mantiene fuera de la lógica principal)
    var toggleButton = document.getElementById('NotificacionToggle');
    if (toggleButton) {
        // Asumiendo que `bootstrap` está cargado globalmente (como se ve en el Blade)
        var dropdown = new bootstrap.Dropdown(toggleButton);

        toggleButton.addEventListener('click', function (e) {
            e.preventDefault();
            dropdown.toggle();
        });
    }
});