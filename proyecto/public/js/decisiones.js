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

});
