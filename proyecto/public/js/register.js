document.addEventListener("DOMContentLoaded", function () {
    const passwordField = document.getElementById('password-field');
    const togglePassword = document.getElementById("toggle-password");
    const passwordConfirmField = document.getElementById("password-confirm-field");
    const togglePasswordConfirm = document.getElementById("toggle-password-confirm");
    const cedulaField = document.getElementById("cedula-field"); // Nuevo ID para el campo de cédula

    // --- LÓGICA DE VISUALIZACIÓN DE CONTRASEÑAS (Toggle) ---
    
    // Función para alternar visibilidad de contraseña
    function togglePasswordVisibility(toggleElement, fieldElement) {
        if (toggleElement && fieldElement) {
            toggleElement.addEventListener("click", function () {
                // Alternar el tipo de input entre 'password' y 'text'
                const type =
                    fieldElement.getAttribute("type") === "password"
                        ? "text"
                        : "password";
                fieldElement.setAttribute("type", type);

                // Alternar el ícono del ojo
                this.querySelector("i").classList.toggle("bi-eye-fill");
                this.querySelector("i").classList.toggle("bi-eye-slash-fill");
            });
        }
    }

    // Aplicar a Contraseña
    togglePasswordVisibility(togglePassword, passwordField);
    
    // Aplicar a Confirmación de Contraseña
    togglePasswordVisibility(togglePasswordConfirm, passwordConfirmField);


    // --- LÓGICA DE VALIDACIÓN DE ENTRADA (Input Filtering) ---

    // Script para permitir solo letras en campos de texto (nombre, apellido)
    document
        .querySelectorAll('[name="nombre"], [name="apellido"]')
        .forEach((input) => {
            input.addEventListener("input", function () {
                // Reemplaza cualquier caracter que NO sea letra (incluyendo Ñ/ñ, acentos y espacios)
                this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, "");
            });
        });

    // Script para permitir solo números en el campo de cédula
    if (cedulaField) {
        cedulaField.addEventListener("keypress", function (event) {
            if (event.charCode < 48 || event.charCode > 57) {
                event.preventDefault();
            }
        });
    }

    // --- LÓGICA DEL TOOLTIP DE BOOTSTRAP ---

    // Inicialización del Tooltip
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Ocultar el Tooltip al hacer focus en el campo de contraseña
    if (passwordField && typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        // Buscamos el Tooltip asociado a passwordField
        const passwordTooltipInstance = bootstrap.Tooltip.getInstance(passwordField);

        if (passwordTooltipInstance) {
            const hideTooltipOnFocus = function () {
                passwordTooltipInstance.hide();
            };
            passwordField.addEventListener('focus', hideTooltipOnFocus);
        }
    }
});