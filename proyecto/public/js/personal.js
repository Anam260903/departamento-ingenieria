document.addEventListener('DOMContentLoaded', function () {

    // Lógica para Asignar Inspecciones (assignInspectionModal)

    const modal = document.getElementById('assignInspectionModal');
    const select = document.getElementById('id_insp');
    const form = document.getElementById('assignInspectionForm');
    const userNameSpan = document.getElementById('modal-user-name');
    const noInspectionsAlert = document.getElementById('no-inspections-alert');
    const assignButton = document.getElementById('assign-btn');

    if (modal) { // Verificación de existencia del modal de inspecciones
        // Escucha el evento de Bootstrap
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            // 1. Resetear el estado
            select.innerHTML = '<option value="">Cargando inspecciones...</option>';
            noInspectionsAlert.classList.add('d-none');
            assignButton.setAttribute('disabled', 'true');

            // 2. Actualizar la interfaz y las URLs
            userNameSpan.textContent = userName;

            // Rutas dinámicas
            const fetchUrl = API_ROUTES.fetchInspections.replace(':userId', userId);
            const postUrl = API_ROUTES.postInspection.replace(':userId', userId);
            form.setAttribute('action', postUrl);

            form.setAttribute('action', postUrl);

            // 3. Petición AJAX para obtener las inspecciones disponibles
            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Error desconocido del servidor (Código: ' + response.status + ')');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    select.innerHTML = ''; // Limpiar select

                    if (data.inspecciones.length === 0) {
                        // No hay inspecciones
                        select.innerHTML = '<option value="">No hay disponibles</option>';
                        noInspectionsAlert.classList.remove('d-none');
                        assignButton.setAttribute('disabled', 'true');
                    } else {
                        // Rellenar el select
                        select.innerHTML += '<option value="">-- Seleccione una inspección --</option>';
                        data.inspecciones.forEach(inspeccion => {
                            const optionText = inspeccion.propietario_display;
                            select.innerHTML += `<option value="${inspeccion.id_insp}">${optionText}</option>`;
                        });

                        // Habilitar/Deshabilitar el botón según la selección en el select
                        select.onchange = function () {
                            if (this.value) {
                                assignButton.removeAttribute('disabled');
                            } else {
                                assignButton.setAttribute('disabled', 'true');
                            }
                        };
                    }
                })
                .catch(error => {
                    console.error('Error al cargar inspecciones', error);
                    select.innerHTML = '<option value="">ERROR: No se pudo cargar la lista.</option>';
                    assignButton.setAttribute('disabled', 'true');
                });
        });
    }



    // Lógica para Asignar Recursos (assignResourceModal)

    const resourceModal = document.getElementById('assignResourceModal');
    const resourceSelect = document.getElementById('id_recurso');
    const resourceForm = document.getElementById('assignResourceForm');
    const resourceUserNameSpan = document.getElementById('modal-resource-user-name');
    const noResourcesAlert = document.getElementById('no-resources-alert');
    const assignResourceButton = document.getElementById('assign-resource-btn-submit');

    if (resourceModal) { // Verificación de existencia del modal de recursos
        resourceModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');

            // 1. Resetear el estado
            resourceSelect.innerHTML = '<option value="">Cargando recursos...</option>';
            noResourcesAlert.classList.add('d-none');
            assignResourceButton.setAttribute('disabled', 'true');

            // 2. Actualizar la interfaz y las URLs
            resourceUserNameSpan.textContent = userName;

            // Rutas dinámicas para la asignación de recursos
            const fetchResourcesUrl = API_ROUTES.fetchResources; // Ya está completa
            const postResourceUrl = API_ROUTES.postResource.replace(':userId', userId);
            resourceForm.setAttribute('action', postResourceUrl);

            // 3. Petición AJAX para obtener los recursos disponibles
            fetch(fetchResourcesUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar recursos disponibles (Código: ' + response.status + ')');
                    }
                    return response.json();
                })
                .then(data => {
                    resourceSelect.innerHTML = ''; // Limpiar select

                    if (data.recursos.length === 0) {
                        // No hay recursos disponibles
                        resourceSelect.innerHTML = '<option value="">No hay disponibles</option>';
                        noResourcesAlert.classList.remove('d-none');
                        assignResourceButton.setAttribute('disabled', 'true');
                    } else {
                        // Rellenar el select
                        resourceSelect.innerHTML += '<option value="">-- Seleccione un recurso --</option>';
                        data.recursos.forEach(recurso => {
                            const optionText = `${recurso.codigo} - ${recurso.nombre_rec}`;
                            resourceSelect.innerHTML += `<option value="${recurso.id_recurso}">${optionText}</option>`;
                        });

                        // Habilitar/Deshabilitar el botón según la selección
                        resourceSelect.onchange = function () {
                            if (this.value) {
                                assignResourceButton.removeAttribute('disabled');
                            } else {
                                assignResourceButton.setAttribute('disabled', 'true');
                            }
                        };
                    }
                })
                .catch(error => {
                    console.error('Error al cargar recursos:', error);
                    resourceSelect.innerHTML = '<option value="">ERROR: No se pudo cargar la lista.</option>';
                    assignResourceButton.setAttribute('disabled', 'true');
                });
        });
    }

    // Lógica para filtrar por profesión y descargar PDF

    // Obtenemos las referencias a los elementos del DOM
    const selectElement = document.getElementById('profesion-select');
    const formElement = document.getElementById('form-pdf-filtro');
    const downloadButton = document.getElementById('btn-descargar-filtro');

    // Verificamos que todos los elementos existan antes de agregar el listener
    if (selectElement && formElement && downloadButton) {

        // Función que se ejecuta al cambiar la selección en el dropdown
        selectElement.addEventListener('change', function () {
            const selectedProfesion = this.value;

            if (selectedProfesion) {
                const urlBase = '/personal/exportar/pdf/__PROFESION__';

                // Construimos la URL final reemplazando el placeholder
                formElement.action = urlBase.replace('__PROFESION__', selectedProfesion);

                downloadButton.disabled = false; // Habilita el botón de descarga
            } else {
                downloadButton.disabled = true; // Deshabilita si no hay selección
            }
        });

        // Inicialmente, el botón está deshabilitado
        downloadButton.disabled = true;
    }
});