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

    // Lógica para mostrar/ocultar contraseñas
    document.querySelectorAll(".toggle-password").forEach((button) => {
        button.addEventListener("click", function () {
            const passwordInput = this.previousElementSibling;
            const icon = this.querySelector("i");

            const type =
                passwordInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordInput.setAttribute("type", type);

            icon.classList.toggle("bi-eye");
            icon.classList.toggle("bi-eye-slash");
        });
    });

    // Script para permitir solo letras en campos de texto
    document
        .querySelectorAll('[name="nombre"], [name="apellido"]')
        .forEach((input) => {
            input.addEventListener("input", function () {
                this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ\s]/g, "");
            });
        });
});


