document.addEventListener('DOMContentLoaded', function () {
    // Verificar si la variable calculosData existe
    if (typeof calculosData === 'undefined') {
        console.error("Error: La variable 'calculosData' no está definida. Asegúrate de pasarla desde el controlador a la vista.");
        return;
    }

    // Obtener los elementos del DOM
    const selectCalculo = document.getElementById('nombre_calculo');
    const textareaContenido = document.getElementById('contenido');
    const copyButton = document.getElementById('copyButton');
    const copyMessage = document.getElementById('copyMessage');

    if (!selectCalculo || !textareaContenido || !copyButton || !copyMessage) {
        console.error("Error: Faltan elementos DOM esenciales (select, textarea o botones de copiado).");
        return;
    }

    // LÓGICA DE SELECCIÓN Y CARGA DE CONTENIDO

    selectCalculo.addEventListener('change', function () {
        const selectedId = this.value; // Obtener el ID del cálculo seleccionado

        // Mostrar Mensaje de Carga Temporal
        textareaContenido.value = 'Cargando contenido. Por favor, espere...';

        setTimeout(() => {
            if (selectedId && calculosData[selectedId]) {
                const contenido = calculosData[selectedId].contenido;

                // Revisión de contenido nulo o vacío
                if (contenido) {
                    textareaContenido.value = contenido;
                } else {
                    textareaContenido.value = '⚠️ Este cálculo no tiene contenido de materiales definido aún.';
                }
            } else {
                textareaContenido.value = 'Seleccione una construcción para ver su contenido.';
            }
        }, 50);
    });

    // LÓGICA DE COPIAR AL PORTAPAPELES

    copyButton.addEventListener('click', function () {

        if (textareaContenido.value === '' || textareaContenido.value.includes('Seleccione una construcción') || textareaContenido.value.includes('Cargando contenido')) {
            alert('No hay contenido para copiar o la selección está vacía.');
            return;
        }

        // 1. Seleccionar el texto dentro del textarea
        textareaContenido.select();
        textareaContenido.setSelectionRange(0, 99999);

        // 2. Ejecutar el comando de copiado
        try {
            document.execCommand('copy');

            // 3. Mostrar el mensaje de éxito
            copyMessage.classList.remove('d-none');
            setTimeout(() => {
                copyMessage.classList.add('d-none');
            }, 2000); // Ocultar después de 2 segundos

        } catch (err) {
            console.error('Error al intentar copiar al portapapeles:', err);
            alert('Fallo al copiar el texto. Su navegador no soporta el comando "copy".');
        }
    });

    // Código para el dropdown de notificaciones
    // Selecciona el botón por su ID
    var toggleButton = document.getElementById('NotificacionToggle');

    if (toggleButton) {
        // Crear una nueva instancia de Dropdown de Bootstrap
        var dropdown = new bootstrap.Dropdown(toggleButton);

        // Agrega un listener de click para manejar el
        toggleButton.addEventListener('click', function (e) {
            e.preventDefault(); // Previene el comportamiento por defecto del enlace '#'
            dropdown.toggle();  // Fuerza la acción de mostrar/ocultar
        });
    }

});