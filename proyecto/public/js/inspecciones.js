document.addEventListener("DOMContentLoaded", function () {
    
    var toggleButton = document.getElementById('NotificacionToggle');

    // Obtenemos todos los botones de "Ver detalles"
    var verBotones = document.querySelectorAll(".btn-ver-observacion");
    var modalContent = document.getElementById("modalObservacionContent");

    verBotones.forEach(function (button) {
        button.addEventListener("click", function () {
            // 1. Obtener el valor del atributo y decodificarlo como JSON
            var observacionEncoded = this.getAttribute("data-observacion");
            var observacion = JSON.parse(observacionEncoded);

            // 2. Si la observación está vacía, mostrar un mensaje por defecto
            if (!observacion || observacion.trim() === "") {
                observacion =
                    "No se registraron observaciones para esta inspección.";
            }

            // 3. Inyectar el contenido en el modal
            modalContent.textContent = observacion;
        });
    });

    // Script para permitir solo letras en campos de texto
    document
        .querySelectorAll(
            '[name="propietario_nombre"], [name="propietario_apellido"]'
        )
        .forEach((input) => {
            input.addEventListener("input", function () {
                // Reemplaza cualquier carácter que no sea una letra (incluyendo ñ, tildes) o un espacio.
                this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, "");
            });
        });


});