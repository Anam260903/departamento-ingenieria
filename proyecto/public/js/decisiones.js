document.addEventListener('DOMContentLoaded', function () {
    // 1. Obtener la URL Template del objeto global definido en Blade
    const url_template = window.AppConfig?.resumenUrlTemplate || '/api/inspeccion/resumen/PLACEHOLDER';
    const ID_PLACEHOLDER = 'PLACEHOLDER'; // Definimos el marcador a reemplazar

    // Elementos del modal
    const resumenModal = document.getElementById('resumenModal');
    const loadingSpinner = document.getElementById('loading-spinner');
    const resumenContenido = document.getElementById('resumen-contenido');
    const mensajeError = document.getElementById('mensaje-error');
    const mensajeErrorTexto = document.getElementById('mensaje-error-texto');
    const btnPdf = document.getElementById('btn-ver-pdf');

    if (!resumenModal) return; // Salir si el modal no existe en la página actual

    // Evento que se dispara justo antes de que se muestre el modal
    resumenModal.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget;
        const id_insp = card.getAttribute('data-id-insp');

        // 1. Mostrar Spinner y ocultar todo lo demás al inicio
        loadingSpinner.style.display = 'block';
        resumenContenido.style.display = 'none';
        mensajeError.style.display = 'none';
        btnPdf.style.display = 'none';

        // 2. Construir la URL de la API
        const url = url_template.replace(ID_PLACEHOLDER, id_insp);

        // 3. Realizar la solicitud AJAX
        fetch(url)
            .then(response => {
                // Manejo de errores HTTP
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(JSON.stringify(errorData));
                    });
                }
                return response.json();
            })
            .then(data => {
                loadingSpinner.style.display = 'none';

                if (data.estado === 'pendiente' || data.estado === 'sin_informe') {
                    // Caso 1 y 2: Advertencia
                    mensajeError.classList.remove('alert-danger');
                    mensajeError.classList.add('alert-warning');

                    mensajeErrorTexto.textContent = data.mensaje;

                    mensajeError.style.display = 'block';
                    resumenContenido.style.display = 'none';

                } else if (data.estado === 'completado') {
                    // Caso 3: Éxito - Rellenar los campos
                    document.getElementById('info-ingeniero').textContent = data.ingeniero;
                    document.getElementById('info-comunidad').textContent = data.comunidad;
                    document.getElementById('info-antecedentes').textContent = data.antecedentes;
                    document.getElementById('info-resultados').textContent = data.resultados;
                    document.getElementById('info-recomendaciones').textContent = data.recomendacion;
                    document.getElementById('info-fecha').textContent = data.fecha_inf;

                    // Lógica del botón PDF
                    if (data.id_informe) {
                        const btnPdf = document.getElementById('btn-ver-pdf');
                        btnPdf.href = `${BASE_PDF_URL}/${data.id_informe}/pdf`;
                        btnPdf.style.display = 'inline-block'; // Mostrar el botón
                    }

                    // Mostrar el contenido del resumen
                    resumenContenido.style.display = 'block';
                    mensajeError.style.display = 'none';

                } else {
                    // Estado desconocido
                    mensajeError.classList.remove('alert-warning');
                    mensajeError.classList.add('alert-danger');
                    mensajeErrorTexto.textContent = 'Respuesta de servidor inesperada: ' + data.estado;
                    mensajeError.style.display = 'block';
                    resumenContenido.style.display = 'none';
                }
            })
            .catch(error => {
                // Manejo de errores de red o errores 400/500 lanzados
                loadingSpinner.style.display = 'none';
                let mensajeAMostrar = 'Ocurrió un error desconocido al cargar los datos. Por favor, inténtelo de nuevo.';

                try {
                    const errorData = JSON.parse(error.message);
                    if (errorData && errorData.mensaje) {
                        mensajeAMostrar = errorData.mensaje
                    } else {
                        console.error('Error de red/API:', error);
                    }
                } catch (e) {
                    console.error('Error de red o Servidor (500):', error);
                    mensajeAMostrar = 'Error fatal del servidor. Revise los logs de Laravel para más detalles.';
                }

                mensajeErrorTexto.textContent = mensajeAMostrar;
                mensajeError.classList.remove('alert-warning');
                mensajeError.classList.add('alert-danger');
                mensajeError.style.display = 'block';
                resumenContenido.style.display = 'none';
            });
    });


    // Script para la comparación de inspecciones

    // Verificar que las variables globales inyectadas por Blade estén disponibles
    if (typeof window.criterios === 'undefined' || 
        typeof window.urlDetalle === 'undefined' ||
        typeof window.urlProcesar === 'undefined') {
        
        console.error("Error: Las variables globales (criterios, rutas AJAX) no están definidas.");
        document.getElementById('btnComparar').disabled = true;
        return;
    }

    const criterios = window.criterios;
    
    const select1 = document.getElementById('inspeccion1_id');
    const select2 = document.getElementById('inspeccion2_id');
    const criteriosContainer1 = document.getElementById('criterios1');
    const criteriosContainer2 = document.getElementById('criterios2');
    const btnComparar = document.getElementById('btnComparar');
    const comparacionForm = document.getElementById('comparacionForm');
    
    let inspeccion1Seleccionada = null;
    let inspeccion2Seleccionada = null;
    
    /**
     * Hace la llamada AJAX para obtener los detalles de una inspección
     */
    async function fetchDetalleInspeccion(id) {
        try {
            const response = await fetch(`${window.urlDetalle}?id=${id}`);
            if (!response.ok) {
                console.error('Error: Inspección no encontrada en el servidor. ID:', id);
            }
            return await response.json();
        } catch (error) {
            console.error('Error al obtener detalle de inspección:', error);
            return null;
        }
    }

    /**
     * Renderiza los criterios de checklist en el contenedor especificado.
     */
    function renderCriterios(container, index) {
        container.innerHTML = ''; // Limpiar el contenedor
        let html = '<p class="fw-bold mb-3 border-bottom pb-1">Criterios de Decisión (Marque Sí o No):</p>';
        
        criterios.forEach(criterio => {
            html += `
                <div class="form-check mb-3">
                    <input class="form-check-input criterio-checkbox" 
                           type="checkbox" 
                           value="${criterio.ponderacion}" 
                           data-ponderacion="${criterio.ponderacion}"
                           data-id="${criterio.id}"
                           name="criterio_${index}_${criterio.id}" 
                           id="criterio_${index}_${criterio.id}">
                    <label class="form-check-label fw-medium" for="criterio_${index}_${criterio.id}">
                        ${criterio.pregunta} 
                        <span class="badge bg-primary ms-2">${criterio.ponderacion} pts</span>
                    </label>
                    <small class="text-muted d-block ms-4">${criterio.descripcion}</small>
                </div>
            `;
        });

        container.innerHTML = html;
        // Asignar el listener de validación a todos los checkboxes generados
        container.querySelectorAll('.criterio-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', validarHabilitarBoton);
        });
    }

    /**
     * Actualiza la información dinámica (nombre, fecha, comunidad) de la inspección
     */
    function updateDetalleDisplay(index, detalle) {
        // Actualizar el nombre de la vivienda y la fecha
        const nombreBase = 'VIVIENDA DE: ';
        document.getElementById(`nombre${index}_display`).innerHTML = nombreBase + 
            `<span class="text-secondary">${detalle.nombre}</span>`;
        document.getElementById(`fecha${index}_display`).textContent = detalle.fecha;
        document.getElementById(`comunidad${index}_display`).textContent = detalle.comunidad;
        
        // Guardar el nombre completo en un campo oculto para el envío
        document.getElementById(`nombre${index}_hidden`).value = detalle.nombre;
    }

    /**
     * Listener genérico para el cambio en los selectores
     */
    async function handleSelectChange(selectElement, index) {
        const id = selectElement.value;
        const nombre = selectElement.options[selectElement.selectedIndex].getAttribute('data-nombre');

        if (id) {
            // Prevenir la selección de la misma inspección
            const otroId = index === 1 ? inspeccion2Seleccionada : inspeccion1Seleccionada;
            if (id === otroId) {
                alert('No puede comparar una inspección consigo misma. Seleccione otra inspección.');
                selectElement.value = ""; // Restablecer la selección
                limpiarPanel(index);
                return;
            }

            const detalle = await fetchDetalleInspeccion(id);
            if (detalle) {
                updateDetalleDisplay(index, detalle);
                renderCriterios(document.getElementById(`criterios${index}`), index);
                
                // Actualizar el estado de la inspección
                if (index === 1) {
                    inspeccion1Seleccionada = id;
                } else {
                    inspeccion2Seleccionada = id;
                }
            } else {
                // Si falla la carga, limpiar y deshabilitar
                limpiarPanel(index);
            }
        } else {
            limpiarPanel(index);
        }
        validarHabilitarBoton();
    }
    
    /**
     * Limpia la información del panel si la selección es nula.
     */
    function limpiarPanel(index) {
        document.getElementById(`nombre${index}_display`).innerHTML = 'VIVIENDA DE: <span class="text-secondary">[Nombre del Propietario]</span>';
        document.getElementById(`fecha${index}_display`).textContent = '--/--/----';
        document.getElementById(`comunidad${index}_display`).textContent = '[Comunidad]';
        document.getElementById(`nombre${index}_hidden`).value = '';
        
        const container = document.getElementById(`criterios${index}`);
        container.innerHTML = '<p class="text-center text-muted">Seleccione una inspección para cargar el checklist.</p>';


        if (index === 1) {
            inspeccion1Seleccionada = null;
        } else {
            inspeccion2Seleccionada = null;
        }
    }

    /**
     * Valida si ambas inspecciones están seleccionadas y todos los checkboxes han sido marcados.
     */
    function validarHabilitarBoton() {
        const totalCriterios = criterios.length;

        // Comprueba si se han seleccionado ambas inspecciones (y son diferentes)
        const selectsDiferentes = inspeccion1Seleccionada && inspeccion2Seleccionada && (inspeccion1Seleccionada !== inspeccion2Seleccionada);
        
        // Comprueba si se han marcado todos los criterios para la Inspección 1
        const criteriosMarcados1 = criteriosContainer1.querySelectorAll('input[type="checkbox"]').length;
        const todosSeleccionados1 = criteriosMarcados1 === totalCriterios;

        // Comprueba si se han marcado todos los criterios para la Inspección 2
        const criteriosMarcados2 = criteriosContainer2.querySelectorAll('input[type="checkbox"]').length;
        const todosSeleccionados2 = criteriosMarcados2 === totalCriterios;
        
        // Habilitar si se han cargado los criterios y las selecciones son válidas
        btnComparar.disabled = !(selectsDiferentes && criteriosMarcados1 > 0 && criteriosMarcados2 > 0);
    }

    /**
     * Calcula la puntuación total de una inspección.
     */
    function calcularPuntuacion(container) {
        let puntuacion = 0;
        // Solo suma la ponderación si el checkbox está marcado (Respuesta SI)
        const checkboxes = container.querySelectorAll('input[type="checkbox"]:checked');
        checkboxes.forEach(cb => {
            // El valor del checkbox es la ponderación
            puntuacion += parseInt(cb.value); 
        });
        return puntuacion;
    }

    // Event Listeners para los selectores
    select1.addEventListener('change', () => handleSelectChange(select1, 1));
    select2.addEventListener('change', () => handleSelectChange(select2, 2));

    // Event Listener para el formulario de comparación
    comparacionForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // 1. Calcular las puntuaciones finales
        const puntuacion1 = calcularPuntuacion(criteriosContainer1);
        const puntuacion2 = calcularPuntuacion(criteriosContainer2);
        
        // 2. Obtener los nombres
        const nombre1 = document.getElementById('nombre1_hidden').value;
        const nombre2 = document.getElementById('nombre2_hidden').value;

        // Mostrar un estado de carga
        btnComparar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Calculando...';
        btnComparar.disabled = true;

        try {
            // 3. Enviar a Laravel para obtener el resultado y la recomendación
            const response = await fetch(window.urlProcesar, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    puntuacion1: puntuacion1,
                    puntuacion2: puntuacion2,
                    nombre1: nombre1,
                    nombre2: nombre2,
                })
            });
            
            const data = await response.json();

            // 4. Mostrar resultados en el modal
            document.getElementById('resultadoTexto').textContent = data.resultado;
            // Usamos innerHTML para permitir negritas del backend (**)
            document.getElementById('recomendacionTexto').innerHTML = data.recomendacion.replace(/\*\*/g, '<strong>').replace(/<\/?strong>/g, '');
            
            // Mostrar puntuaciones detalladas
            document.getElementById('puntuacionFinal1').textContent = puntuacion1;
            document.getElementById('nombreFinal1').textContent = nombre1;
            document.getElementById('puntuacionFinal2').textContent = puntuacion2;
            document.getElementById('nombreFinal2').textContent = nombre2;

            // Abrir el modal
            const modal = new bootstrap.Modal(document.getElementById('resultadoModal'));
            modal.show();

        } catch (error) {
            console.error('Error al procesar la comparación:', error);
            document.getElementById('resultadoTexto').textContent = 'Error en el cálculo.';
            document.getElementById('recomendacionTexto').textContent = 'Ocurrió un error al comunicarse con el servidor. Por favor, intente de nuevo.';
            const modal = new bootstrap.Modal(document.getElementById('resultadoModal'));
            modal.show();
        } finally {
            // Restaurar el botón
            btnComparar.innerHTML = '<i class="bi bi-check-circle me-2"></i> Comparar Prioridades';
            validarHabilitarBoton(); // Revalidar el estado por si se puede volver a presionar
        }
    });

    // Inicializar listeners al cargar la página
    validarHabilitarBoton();

});
