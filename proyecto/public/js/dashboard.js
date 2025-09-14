document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const wrapper = document.getElementById('wrapper');
    
    if (sidebarToggle && wrapper) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            wrapper.classList.toggle('toggled');
        });
    }

    // Script para el gráfico de Chart.js
    const ctx = document.getElementById('informesChart');
    if (ctx) {
        // Obtenemos los datos del DOM (desde la vista Blade)
        const datos = JSON.parse(ctx.getAttribute('data-chart-data'));
        const labels = JSON.parse(ctx.getAttribute('data-chart-labels'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Número de informes',
                    data: datos,
                    backgroundColor: '#035aa6', // Color único para las barras
                    borderColor: '#035aa6',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});