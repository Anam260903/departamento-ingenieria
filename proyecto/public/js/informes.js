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