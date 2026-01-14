document.addEventListener('DOMContentLoaded', function () {

    // --- 1. DEFINICIÓN DE UTILIDADES Y VARIABLES GLOBALES ---

    // Almacena las instancias de los gráficos
    let chartInstances = {};
    const margin = 20; // Margen para la descarga de imagen

    // Función para inicializar los dropdowns de bootstrap
    const initializeDropdown = (toggleId) => {
        const toggleButton = document.getElementById(toggleId);
        if (toggleButton && window.bootstrap && window.bootstrap.Dropdown) {
            const dropdown = new window.bootstrap.Dropdown(toggleButton);
            toggleButton.addEventListener('click', function (e) {
                dropdown.toggle();
            });
        }
    };

    // Helper para obtener datos desde las variables globales inyectadas por Blade 
    const getChartData = (varName) => window[varName] || { labels: [], data: [] }; // Retorna arrays vacíos si no hay datos

    // --- 2. FUNCIÓN DE DESCARGA GENÉRICA ---
    function descargarGrafico(chartId, formato) {
        const chartInstance = chartInstances[chartId];
        if (!chartInstance) {
            console.error('Instancia de gráfico no encontrada para la descarga:', chartId);
            return;
        }

        const canvasOriginal = chartInstance.canvas;

        // Crear un nuevo canvas temporal
        const canvasTemporal = document.createElement('canvas');
        canvasTemporal.width = canvasOriginal.width + 2 * margin;
        canvasTemporal.height = canvasOriginal.height + 2 * margin;

        const ctxTemp = canvasTemporal.getContext('2d');

        // Dibujar el fondo blanco en el canvas temporal completo
        ctxTemp.fillStyle = 'white';
        ctxTemp.fillRect(0, 0, canvasTemporal.width, canvasTemporal.height);

        // Dibujar el contenido del gráfico original
        ctxTemp.drawImage(canvasOriginal, margin, margin);

        // Obtener el Data URL de la imagen (PNG o JPG)
        let dataURL;
        let mimeType = (formato === 'png') ? 'image/png' : 'image/jpeg';
        let quality = (formato === 'jpg') ? 1.0 : undefined;

        dataURL = canvasTemporal.toDataURL(mimeType, quality);

        // Crear un enlace temporal para forzar la descarga
        const a = document.createElement('a');
        a.download = `${chartId}_${new Date().toISOString().split('T')[0]}.${formato}`;
        a.href = dataURL;

        // Simular el clic en el enlace
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    // --- 3. FUNCIÓN DE INICIALIZACIÓN DE GRÁFICOS ---
    function initializeCharts() {

        if (typeof window.Chart === 'undefined') {
            console.error('Error: La librería Chart.js (window.Chart) no se ha cargado correctamente.');
            return;
        }

        // GRÁFICO 1: Carga de trabajo 
        const cargaTrabajoCtx = document.getElementById('cargaTrabajoChart');
        if (cargaTrabajoCtx) {
            try {
                const data = getChartData('cargaTrabajoData');
                chartInstances['cargaTrabajoChart'] = new window.Chart(cargaTrabajoCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Inspecciones Pendientes Asignadas',
                            data: data.data,
                            backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)'],
                            borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true, title: { display: true, text: 'Total Asignado' } } }
                    }
                });
                console.log("Gráfico 1 (Carga de Trabajo) inicializado con éxito.");
            } catch (error) {
                console.error('ERROR FATAL AL INICIALIZAR GRÁFICO 1 (Carga de Trabajo):', error);
            }
        }

        // GRÁFICO 2: Seguimiento histórico (Mes Actual vs. Mes Anterior)
        const historicoCtx = document.getElementById('historicoChart');
        if (historicoCtx) {
            try {
                const data = getChartData('historicoData');
                chartInstances['historicoChart'] = new window.Chart(historicoCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Inspecciones Completadas',
                            data: data.data,
                            categoryPercentage: 0.7,
                            barPercentage: 0.9,
                            backgroundColor: ['#adb5bd', '#0d6efd', '#28a745'],
                            borderColor: ['#adb5bd', '#0d6efd', '#28a745'],
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: { display: false, position: 'top' },
                            title: { display: false }
                        },
                        animation: {
                            tooltip: { duration: 0 }
                        },
                        scales: {
                            x: {
                                distribution: 'linear',
                                title: { display: true, text: 'Período Mensual' }
                            },
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Número de Inspecciones' }
                            }
                        }
                    }
                });
                console.log("Gráfico 2 (Histórico) inicializado con éxito.");
            } catch (error) {
                console.error('ERROR FATAL AL INICIALIZAR GRÁFICO 2 (Histórico):', error);
            }
        }

        // GRÁFICO 3: Disponibilidad de personal
        const disponibilidadCtx = document.getElementById('disponibilidadChart');
        if (disponibilidadCtx) {
            try {
                const data = getChartData('disponibilidadData');
                chartInstances['disponibilidadChart'] = new window.Chart(disponibilidadCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Conteo de Personal',
                            data: data.data,
                            backgroundColor: data.backgroundColor,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom' }, title: { display: false } }
                    }
                });
                console.log("Gráfico 3 (Disponibilidad) inicializado con éxito.");
            } catch (error) {
                console.error('ERROR FATAL AL INICIALIZAR GRÁFICO 3 (Disponibilidad):', error);
            }
        }
    }

    // ---- GRÁFICOS DE RECURSOS ----

    let topRecursosChartInstance = null;

    // Helper para obtener datos desde las variables globales inyectadas por Blade
    const getChartDataRecursos = (varName) => window[varName] || {};


    function initializeRecursosCharts() {

        if (typeof window.Chart === 'undefined') {
            console.error('Error: La librería Chart.js no se ha cargado para los gráficos de recursos.');
            return;
        }

        // GRÁFICO 4: Uso de recursos por inspector
        const usoRecursosCtx = document.getElementById('usoRecursosChart');
        const usoRecursosData = getChartDataRecursos('usoRecursosPorInspector');

        if (usoRecursosCtx && usoRecursosData.labels) {
            try {
                // Almacenar instancia para descarga
                chartInstances['usoRecursosChart'] = new window.Chart(usoRecursosCtx, {
                    type: 'bar',
                    data: {
                        labels: usoRecursosData.labels,
                        datasets: usoRecursosData.datasets.map(dataset => ({
                            label: dataset.label,
                            data: dataset.data,
                            backgroundColor: dataset.backgroundColor,
                            borderColor: '#fff',
                            borderWidth: 1,
                        }))
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                        },
                        scales: {
                            x: {
                                stacked: true,
                                title: { display: true, text: 'Inspector' }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                title: { display: true, text: 'Total de Asignaciones' }
                            }
                        }
                    }
                });
                console.log("Gráfico 4 (Uso de Recursos) inicializado con éxito.");
            } catch (error) {
                console.error('ERROR FATAL AL INICIALIZAR GRÁFICO 4 (Uso de Recursos):', error);
            }
        }

        // GRÁFICO 5: Recursos más solicitados (Top N)
        const topRecursosDataInicial = getChartDataRecursos('topRecursosData');
        if (topRecursosDataInicial.labels) {
            renderTopRecursosChart(topRecursosDataInicial.labels, topRecursosDataInicial.data);
            console.log("Gráfico 5 (Top Recursos) inicializado con éxito.");
        }
    }


    // GRÁFICO 5: Función de renderizado reusable
    function renderTopRecursosChart(labels, data) {
        const topRecursosCtx = document.getElementById('topRecursosChart');
        if (!topRecursosCtx) return;

        if (topRecursosChartInstance) {
            topRecursosChartInstance.destroy();
        }

        try {
            topRecursosChartInstance = new window.Chart(topRecursosCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Número de Asignaciones',
                        data: data,
                        backgroundColor: 'rgba(255, 159, 64, 0.8)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    interaction: { mode: 'nearest', intersect: true, axis: 'y' },
                    hover: { mode: 'nearest', intersect: true },
                    plugins: {
                        legend: { display: false },
                        title: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: { display: true, text: 'Frecuencia de Uso' }
                        }
                    }
                }
            });
            // Almacenar instancia para descarga
            chartInstances['topRecursosChart'] = topRecursosChartInstance;

        } catch (error) {
            console.error('ERROR FATAL AL INICIALIZAR GRÁFICO 5 (Top Recursos):', error);
        }
    }

    // --- LÓGICA DE EVENTOS DE RECURSOS ---

    // Manejar el cambio de mes
    const mesSelector = document.getElementById('mes-selector');
    const mesDisplayLabel = document.getElementById('mes-display-label'); // Nuevo elemento
    const recomendacionElement = document.getElementById('recomendacion-texto');

    if (mesSelector) {
        mesSelector.addEventListener('change', function () {
            const mesAno = this.value;
            const url = `/decisiones/recursos/top?mes=${mesAno}`;

            // Obtener el texto del mes selccionado
            const selectedOption = this.options[this.selectedIndex];
            const selectedMonthText = selectedOption.textContent.trim();

            // Actualizar inmediatamente la etiqueta de datos
            if (mesDisplayLabel) {
                mesDisplayLabel.textContent = `Datos de: ${selectedMonthText}`;
            }

            recomendacionElement.innerHTML = 'Cargando...'; // Feedback de carga

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.labels && data.data) {
                        renderTopRecursosChart(data.labels, data.data);

                        // Actualizar la recomendación
                        recomendacionElement.innerHTML = data.recomendacion.replace(/\n/g, '<br>');

                    } else {
                        renderTopRecursosChart(['Sin datos'], [0]);
                        recomendacionElement.innerHTML = "Datos no disponibles para el mes seleccionado.";
                    }
                })
                .catch(error => {
                    console.error('Error al cargar datos de recursos:', error);
                    recomendacionElement.innerHTML = 'Error fatal al cargar datos. Consulte la consola.';
                });
        });
    }

    // --- 4. FUNCIÓN DE INICIALIZACIÓN DE LISTENERS ---
    function initializeDropdownsAndDownloadListeners() {
        // Inicializar los dropdowns de descarga
        initializeDropdown('descargarCargaToggle');
        initializeDropdown('descargarHistoricoToggle');
        initializeDropdown('descargarDisponibilidadToggle');
        initializeDropdown('descargarUsoRecursosToggle');
        initializeDropdown('descargarTopRecursosToggle');

        // Delega el evento de clic a todos los elementos del dropdown de descarga
        document.querySelectorAll('[data-chart-id]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const chartId = this.getAttribute('data-chart-id');
                const format = this.getAttribute('data-format');
                descargarGrafico(chartId, format);
            });
        });
    }

    // --- 5. LÓGICA DE EJECUCIÓN ---

    // 1. Ejecutar la lógica de inicialización de listeners y dropdowns inmediatamente
    try {
        initializeDropdownsAndDownloadListeners();
        initializeRecursosCharts();
        initializeRecursosDropdownsAndListeners();
        console.log("Dropdowns y listeners de descarga inicializados con éxito.");
    } catch (error) {
        console.error('ERROR FATAL AL INICIALIZAR DROPDOWNS/LISTENERS:', error);
    }

    // 2. Inicializar los gráficos
    initializeCharts();

    // Script para el modal de resumen de inspección
    // Obtener la URL Template del objeto global definido en Blade
    const url_template = window.AppConfig?.resumenUrlTemplate || '/api/inspeccion/resumen/PLACEHOLDER';
    const ID_PLACEHOLDER = 'PLACEHOLDER';

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
                // Manejo de errores
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
