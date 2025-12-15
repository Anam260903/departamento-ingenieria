document.addEventListener("DOMContentLoaded", function () {
    // Obtenemos todos los botones de "Ver detalles"
    var verBotones = document.querySelectorAll(".btn-ver-observacion");
    var modalContent = document.getElementById("modalObservacionContent");
    var toggleButton = document.getElementById('NotificacionToggle');

    verBotones.forEach(function (button) {
        button.addEventListener("click", function () {
            // 1. Obtener el valor del atributo y decodificarlo como JSON.
            var observacionEncoded = this.getAttribute("data-observacion");
            var observacion = JSON.parse(observacionEncoded);

            // 2. Si la observación está vacía, mostrar un mensaje por defecto
            if (!observacion || observacion.trim() === "") {
                observacion =
                    "No se registraron observaciones para este recurso.";
            }

            // 3. Inyectar el contenido en el modal
            modalContent.textContent = observacion;
        });
    });

});
