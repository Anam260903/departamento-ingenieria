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

});