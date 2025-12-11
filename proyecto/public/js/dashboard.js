document.addEventListener("DOMContentLoaded", function () {
    // Lógica para mostrar/ocultar el sidebar
    const sidebarToggle = document.getElementById("sidebarToggle");
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function (event) {
            event.preventDefault();
            document.body.classList.toggle("sb-sidenav-toggled");
            localStorage.setItem(
                "sb-sidenav-toggle",
                document.body.classList.contains("sb-sidenav-toggled")
            );
        });
    }

    // Lógica para el gráfico de Chart.js
    const ctx = document.getElementById("informesChart");
    if (ctx) {
        const datos = JSON.parse(ctx.getAttribute("data-chart-data"));
        const labels = JSON.parse(ctx.getAttribute("data-chart-labels"));

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Número de informes",
                        data: datos,
                        backgroundColor: "#035aa6",
                        borderColor: "#035aa6",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                    },
                },
            },
        });
    }

    // Código para el dropdown de notificaciones
    // Selecciona el botón por su ID
    var toggleButton = document.getElementById('NotificacionToggle');

    if (toggleButton) {
        // Crear una nueva instancia de Dropdown de Bootstrap
        var dropdown = new bootstrap.Dropdown(toggleButton);

        // Agrega un listener de click para manejar el toggle
        toggleButton.addEventListener('click', function (e) {
            e.preventDefault(); // Previene el comportamiento por defecto del enlace '#'
            dropdown.toggle();  // Fuerza la acción de mostrar/ocultar
        });
    }


});