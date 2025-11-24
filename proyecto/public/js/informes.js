// Archivo: public/js/informes.js 

document.addEventListener('DOMContentLoaded', function () {

    console.log('Script informes.js cargado. Configurando delegación de eventos...');

    document.addEventListener('click', function (e) {

        // Verificamos si el clic fue en el botón #btnContinuarInforme o en un descendiente
        const btnContinuar = e.target.closest('#btnContinuarInforme');

        // Si el clic no fue en nuestro botón, salimos
        if (!btnContinuar) return;

        // Si llegamos aquí, el botón fue encontrado y pulsado
        e.preventDefault();

        const selectElement = document.getElementById('id_insp_select');
        const selectContainer = selectElement ? selectElement.closest('.mb-3') : null;

        // Función para mostrar feedback visual de error
        function showErrorFeedback(message) {
            if (selectContainer) {
                let feedback = document.getElementById('select-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.id = 'select-feedback';
                    feedback.classList.add('invalid-feedback', 'd-block');
                    selectContainer.appendChild(feedback);
                }
                feedback.textContent = message;
                selectElement.classList.add('is-invalid');

                setTimeout(() => {
                    selectElement.classList.remove('is-invalid');
                    if (feedback) feedback.remove();
                }, 3000);
            }
        }

        // Ejecución de la lógica
        const id_insp = selectElement ? selectElement.value : null;
        const baseUrl = btnContinuar.getAttribute('data-base-url');

        if (id_insp && id_insp !== "") {

            if (baseUrl) {
                // Redirección
                window.location.href = baseUrl + id_insp;
            } else {
                console.error('URL base no encontrada. Verifique el atributo data-base-url en el botón.');
            }

        } else {
            // Feedback visual
            showErrorFeedback('⚠️ Por favor, seleccione una inspección válida.');
            selectElement.focus();
        }
    });

    if (!document.getElementById('btnContinuarInforme')) {
        console.warn('ADVERTENCIA: Botón #btnContinuarInforme no encontrado inicialmente, pero la delegación de eventos lo manejará cuando aparezca.');
    }
});


// Función auxiliar para obtener el token CSRF de un input oculto
function getCsrfToken() {
    // Busca el token CSRF en la página (usando la convención de Laravel)
    const tokenElement = document.querySelector('input[name="_token"]');
    return tokenElement ? tokenElement.value : null;
}

/**
 * Encapsulación de la lógica del mapa
 */
(function() {

    // 1. Variables y referencias globales del módulo
    let map;
    let marker;

    // Referencias a los campos HTML
    const latInput = document.getElementById('latitud_input');
    const lonInput = document.getElementById('longitud_input');
    const latManual = document.getElementById('latitud_manual');
    const lonManual = document.getElementById('longitud_manual');

    // Manejo de valores iniciales
    const defaultLat = parseFloat(latInput?.value) || 10.6698;
    const defaultLon = parseFloat(lonInput?.value) || -63.2573;
    const initialLocation = [defaultLat, defaultLon];


    // 2. Funciones de utilidad

    /**
     * Sincroniza las coordenadas entre el marcador/mapa y los campos de entrada HTML.
     */
    const updateCoordinates = (lat, lng) => {
        // Validación para asegurar que los elementos existen antes de intentar actualizarlos
        if (latManual && lonManual && latInput && lonInput) {
            // 1. Actualiza los campos visibles (manuales)
            latManual.value = lat.toFixed(8);
            lonManual.value = lng.toFixed(8);

            // 2. Actualiza los campos ocultos (los que se envían al servidor)
            latInput.value = lat.toFixed(8);
            lonInput.value = lng.toFixed(8);
        }
    };

    /**
     * Mueve el marcador y el mapa basándose en las coordenadas ingresadas manualmente.
     */
    const updateMapFromManualInput = () => {
        if (!map || !marker || !latManual || !lonManual) return; // Salir si el mapa no está inicializado

        const lat = parseFloat(latManual.value);
        const lng = parseFloat(lonManual.value);

        // Validación
        if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            console.error("Coordenadas ingresadas no válidas.");
            return;
        }

        const newLocation = [lat, lng];

        // Mover el marcador
        marker.setLatLng(newLocation);

        // Centrar el mapa
        map.setView(newLocation, map.getZoom() > 10 ? map.getZoom() : 18);

        // Actualizar los campos ocultos
        updateCoordinates(lat, lng);
    };

    // 3. Función de inicialización principal

    /**
     * Inicializa el mapa y todos sus controles.
     */
    function initMap() {
        if (!document.getElementById('map') || !latInput || !lonInput) {
            console.error("El contenedor 'map' o los campos de coordenadas no existen. El mapa no puede inicializarse.");
            return;
        }

        // Inicializa el mapa
        map = L.map('map').setView(initialLocation, 18);

        // Definición de capas base
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 22,
            attribution: '© OpenStreetMap contributors'
        });

        const esriLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 22,
            attribution: 'Tiles © Esri &mdash; Source: Esri...'
        });

        // Añadir la capa de calles (OSM) por defecto
        osmLayer.addTo(map);

        const baseMaps = {
            "Calles (OSM)": osmLayer,
            "Satélite (Esri)": esriLayer
        };

        // Marcación y eventos
        marker = L.marker(initialLocation, { draggable: true }).addTo(map);

        // Evento al arrastrar el marcador
        marker.on('dragend', function (e) {
            const coords = marker.getLatLng();
            updateCoordinates(coords.lat, coords.lng);
        });

        // Evento al hacer clic en el mapa
        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        // Inicializar los displays con las coordenadas por defecto/guardadas
        updateCoordinates(defaultLat, defaultLon);

        // Control de Geocoder (Buscador)
        L.Control.geocoder({
            defaultMarkGeocode: false,
            geocoder: L.Control.Geocoder.nominatim(),
            position: 'topleft',
        })
        .on('markgeocode', function (e) {
            const center = e.geocode.center;
            map.fitBounds(e.geocode.bbox);
            marker.setLatLng(center);
            updateCoordinates(center.lat, center.lng);
        })
        .addTo(map);

        // Control de capas (Selector Satélite/Calles)
        L.control.layers(baseMaps).addTo(map);

        // Agregar listeners para la entrada manual
        if (latManual && lonManual) {
            latManual.addEventListener('change', updateMapFromManualInput);
            lonManual.addEventListener('change', updateMapFromManualInput);
        }

        // Asegurar que el mapa se renderice correctamente
        setTimeout(() => map.invalidateSize(), 300);
    }


    // 4. Ejecución / Punto de Entrada (Asegura la inicialización)

    // Inicializa el mapa cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', initMap);

})();

/**
 * Lógica de eventos para el paso 4 de informes técnicos
 */


// Función Única para la Carga del Contenido 
function loadCalculoContent(id, materialsTextarea) {
    
    // Obtenemos los datos inyectados por Blade.
    const calculosData = window.CALCULOS_DATA || {}; 

    // Si el ID es nulo, vacío o no hay textarea, limpiar y salir.
    if (!id || id === "" || !materialsTextarea) {
        if (materialsTextarea) materialsTextarea.value = '';
        return;
    }

    // 1. Buscamos el objeto de cálculo por su ID
    const calculo = calculosData[id];

    if (calculo && calculo.contenido) {
        // 2. Si se encuentra el cálculo y tiene contenido, lo insertamos directamente.
        const contenido = calculo.contenido;
        materialsTextarea.value = contenido;
        console.log(`[INFO] Contenido del cálculo ID ${id} cargado exitosamente desde el caché de la página.`);
    } else {
        // 3. Si no se encuentra (o no tiene contenido), limpiamos.
        materialsTextarea.value = '';
        console.warn(`[INFO] Cálculo ID ${id} no encontrado en los datos locales o el contenido está vacío.`);
    }
}


// Lógica de Eventos DOM

document.addEventListener('DOMContentLoaded', function () {
    console.log("Script informes.js cargado. Configurando manejo de datos locales.");
    
    const calculosSelect = document.getElementById('calculos_codes');
    const materialsTextarea = document.getElementById('materials_info');
    const initialContent = window.INITIAL_CONTENT || '';
    
    if (calculosSelect && materialsTextarea) {
        
        // 1. Carga inicial
        materialsTextarea.value = initialContent; 
        const initialId = calculosSelect.value;
        
        // Solo cargamos el contenido del cálculo por defecto si el campo de texto está completamente vacío.
        // Si el usuario ya había agregado o editado información, la mantenemos.
        if (initialContent === '' && initialId) {
            console.log("[INFO] Contenido inicial vacío. Cargando cálculo por defecto.");
            loadCalculoContent(initialId, materialsTextarea);
        } else if (initialContent !== '') {
            console.log("[INFO] Contenido editado del informe cargado. Se mantiene la edición.");
        }


        // 2. Escuchar el evento de cambio
        calculosSelect.addEventListener('change', function () {
            const selectedCalculoId = this.value;
            
            // Verificamos si el usuario ha editado algo antes de sobrescribir.
            if (materialsTextarea.value.trim() !== '') {
                
                // Preguntar antes de borrar lo editado
                const confirmReplace = confirm("ADVERTENCIA: El campo de materiales contiene información editada. ¿Desea reemplazar el contenido actual con el nuevo cálculo? (Presione Cancelar para mantener su edición)");

                if (!confirmReplace) {
                    console.log("[INFO] Sobrescritura cancelada por el usuario. Manteniendo el contenido editado.");
                    return; // Mantiene el contenido editado y detiene la función.
                }
            }
            
            // Si el campo estaba vacío O el usuario confirmó la sobrescritura:
            loadCalculoContent(selectedCalculoId, materialsTextarea);
        });

    } else {
        console.error("ERROR: No se pudo iniciar el script. 'calculos_codes' o 'materials_info' no encontrados en el DOM.");
    }
});

/**
 * Inicializa la funcionalidad de arrastrar y soltar
 * y la previsualización de imágenes, incluyendo la eliminación.
 */
function initStep5PhotoUpload() {
    // 1. Obtener elementos del DOM
    const dropArea = document.getElementById('drop-area-simple');
    const photosInput = document.getElementById('photos');
    const previewContainer = document.getElementById('preview-container');

    if (!dropArea || !photosInput || !previewContainer) {
        console.error('ERROR: No se encontraron todos los elementos DOM necesarios para el Paso 5.');
        return;
    }

    // Almacena la lista actual de archivos
    let filesArray = [];

    /**
     * Procesa una nueva lista de archivos, los añade al array principal y actualiza la vista.
     */
    function handleFiles(newFiles) {
        // Al seleccionar o arrastrar nuevos archivos, los agregamos al array existente.
        filesArray = filesArray.concat(Array.from(newFiles));

        // Redibujamos la lista completa
        renderPreviews(filesArray);

        // Actualizamos el input para asegurar que los archivos correctos se envíen al servidor
        updateFileInput();
    }

    /**
     * Dibuja las miniaturas de todos los archivos en el filesArray.
     */
    function renderPreviews(files) {
        previewContainer.innerHTML = '';

        files.forEach((file, index) => {
            if (file.type && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const colDiv = document.createElement('div');
                    colDiv.classList.add('col');
                    colDiv.innerHTML = `
                        <div class="card h-100 shadow-sm border-success position-relative" data-file-index="${index}">
                            
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-1 bg-light p-1 rounded" 
                                aria-label="Eliminar" data-file-index="${index}"></button>

                            <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;" alt="Preview">
                            <div class="card-body p-2">
                                <p class="card-text small text-truncate m-0">${file.name}</p>
                            </div>
                        </div>
                    `;
                    previewContainer.appendChild(colDiv);
                }

                // Lee el archivo como una URL de datos para la previsualización
                reader.readAsDataURL(file);
            }
        });
    }

    /**
     * Maneja la eliminación de una miniatura de la lista.
     */
    function handleDelete(event) {
        // Busca el elemento más cercano con la clase .btn-close
        const button = event.target.closest('.btn-close');
        if (!button) return; // No fue un clic en el botón de cierre

        const indexToRemove = parseInt(button.dataset.fileIndex);

        if (!isNaN(indexToRemove)) {
            // Eliminar el archivo del array filesArray
            filesArray.splice(indexToRemove, 1);

            // Redibujar la previsualización con el archivo eliminado
            renderPreviews(filesArray);

            // Actualizar el input con la nueva lista de archivos
            updateFileInput();
        }
    }

    // Asignar el listener al contenedor padre para manejar los clics del botón de cierre
    previewContainer.addEventListener('click', handleDelete);

    /**
     * Crea un nuevo objeto y lo asigna al input de archivos.
     */
    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => {
            dataTransfer.items.add(file);
        });

        // Asignar la nueva lista de archivos al input original
        photosInput.files = dataTransfer.files;
    }

    photosInput.addEventListener('change', (e) => {
        // Llama a la función principal de manejo de archivos
        handleFiles(e.target.files);
        e.target.value = '';
    });


    // Función genérica para prevenir el comportamiento por defecto del navegador
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Aplicar preventDefaults a los eventos de arrastre
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    // Control de estilos visuales
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('border-primary'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('border-primary'), false);
    });

    // Maneja la acción de soltar los archivos
    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        // 1. Asignar los archivos arrastrados al input file  evento change
        photosInput.files = files;

        // 2. Ejecutar la función principal de manejo de archivos
        handleFiles(files);
    }
}