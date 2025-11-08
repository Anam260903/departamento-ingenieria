// Archivo: public/js/informes.js 

document.addEventListener('DOMContentLoaded', function() {
    
    console.log('Script informes.js cargado. Configurando delegación de eventos...'); 
    
    // Delegación: Escuchamos clics en todo el documento.
    document.addEventListener('click', function(e) {
        
        // Verificamos si el clic fue en el botón #btnContinuarInforme o en un descendiente
        const btnContinuar = e.target.closest('#btnContinuarInforme');
        
        // Si el clic NO fue en nuestro botón, salimos
        if (!btnContinuar) return;

        // Si llegamos aquí, el botón fue encontrado y pulsado
        e.preventDefault(); 
        
        const selectElement = document.getElementById('id_insp_select');
        const selectContainer = selectElement ? selectElement.closest('.mb-3') : null;
        
        // Función para mostrar feedback visual de error (la mantenemos)
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
                    if(feedback) feedback.remove();
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
            // Reemplazo del alert() con feedback visual
            showErrorFeedback('⚠️ Por favor, seleccione una inspección válida.');
            selectElement.focus();
        }
    });

    // Se mantiene esta parte solo para propósitos de logging
    if (!document.getElementById('btnContinuarInforme')) {
        console.warn('ADVERTENCIA: Botón #btnContinuarInforme no encontrado inicialmente, pero la delegación de eventos lo manejará cuando aparezca.');
    }
});

/**
 * Inicializa la funcionalidad de arrastrar y soltar (Drag & Drop)
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

    // Almacena la lista actual de archivos (FileList es de solo lectura, necesitamos un Array)
    let filesArray = [];

    // ===================================================
    // FUNCIÓN DE PREVISUALIZACIÓN Y MANEJO DE ARCHIVOS
    // ===================================================

    /**
     * Procesa una nueva lista de archivos, los añade al array principal y actualiza la vista.
     */
    function handleFiles(newFiles) {
        // Al seleccionar o arrastrar nuevos archivos, los agregamos al array existente.
        // Convertimos FileList a Array y lo concatenamos
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
                
                reader.onload = function(e) {
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

    // ===================================================
    // FUNCIÓN DE ELIMINACIÓN
    // ===================================================
    
    /**
     * Maneja la eliminación de una miniatura de la lista.
     */
    function handleDelete(event) {
        // Busca el elemento más cercano con la clase .btn-close (para manejo delegado)
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


    // ===================================================
    // FUNCIÓN PARA ACTUALIZAR EL INPUT (CLAVE DE LA ELIMINACIÓN)
    // ===================================================
    
    /**
     * Crea un nuevo objeto FileList y lo asigna al input de archivos.
     */
    function updateFileInput() {
        // Se utiliza DataTransfer para crear un FileList modificable
        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => {
            dataTransfer.items.add(file);
        });
        
        // Asignar la nueva lista de archivos al input original
        photosInput.files = dataTransfer.files;
    }


    // ===================================================
    // EVENTO CHANGE (PARA SELECCIÓN POR CLIC)
    // ===================================================
    photosInput.addEventListener('change', (e) => {
        // Llama a la función principal de manejo de archivos
        handleFiles(e.target.files);
        // Limpiamos el valor del input para que el mismo archivo pueda ser seleccionado de nuevo si se elimina
        e.target.value = ''; 
    });


    // ===================================================
    // LÓGICA DE DRAG AND DROP
    // ===================================================
    
    // Función genérica para prevenir el comportamiento por defecto del navegador
    function preventDefaults (e) {
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
        
        // 1. Asignar los archivos arrastrados al input file (Necesario para el evento change, aunque lo manejaremos directo)
        photosInput.files = files;
        
        // 2. Ejecutar la función principal de manejo de archivos
        handleFiles(files);
    }
}

// Opcional: Si tienes más scripts de pasos anteriores, agrégales aquí...
// function initStep4Calculos() { ... }