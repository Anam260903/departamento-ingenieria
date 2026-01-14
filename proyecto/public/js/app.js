document.addEventListener("DOMContentLoaded", function () {

     var toggleButton = document.getElementById('NotificacionToggle');

 // Código para el dropdown de notificaciones
    if (toggleButton) {
        // Crear una nueva instancia de dropdown de ootstrap
        var dropdown = new bootstrap.Dropdown(toggleButton);

        // Listener de click
        toggleButton.addEventListener('click', function (e) {
            e.preventDefault();
            dropdown.toggle(); 
        });
    }
    
    // Lógica para mostrar/ocultar contraseña
    document.querySelectorAll(".toggle-password").forEach((button) => {
        button.addEventListener("click", function () {
            
            const passwordInput = this.previousElementSibling;
            const icon = this.querySelector("i");
            
            if (passwordInput && passwordInput.tagName === 'INPUT') {
                
                // Alternar el tipo de input
                const type =
                    passwordInput.getAttribute("type") === "password"
                        ? "text"
                        : "password";
                passwordInput.setAttribute("type", type);

                // Alternar el ícono
                icon.classList.toggle("bi-eye");
                icon.classList.toggle("bi-eye-slash");
            }
        });
    });
        
    // Script para permitir solo letras en campos de texto
    document
        .querySelectorAll('[name="nombre"], [name="apellido"], [name="propietario_nombre"], [name="propietario_apellido"]')
        .forEach((input) => {
            input.addEventListener("input", function () {
                this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, "");
            });
        });

        // Script para inicializar Tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            // Inicializa Tooltips
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    }

});