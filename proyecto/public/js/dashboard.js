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

    // Variable para almecenar la instancia del gráfico
    let informesChartInstance = null;

    // Lógica para el gráfico de Chart.js
    const ctx = document.getElementById("informesChart");
    if (ctx) {
        const datos = JSON.parse(ctx.getAttribute("data-chart-data"));
        const labels = JSON.parse(ctx.getAttribute("data-chart-labels"));

        // Crea el gráfico y almacena la instancia
        informesChartInstance = new Chart(ctx, {
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
                // Asegurar que el fondo blanco esté disponible para JPG/PNG
                plugins: {
                    legend: {
                        display: true
                    },
                    // Añadir un fondo blanco al exportar para evitar transparencia
                    beforeDraw: function (chart) {
                        if (chart.canvas.getContext) {
                            var ctx = chart.canvas.getContext('2d');
                            ctx.fillStyle = 'white'; // Fondo blanco
                            ctx.fillRect(0, 0, chart.width, chart.height);
                        }
                    }
                }
            },
        });

        // Lógica de descargar de imagen
        const descargarPngBtn = document.getElementById('descargarPng');
        const descargarJpgBtn = document.getElementById('descargarJpg');

        // Función genérica para descargar el gráfico
        function descargarGrafico(formato) {
            if (!informesChartInstance) return;

            // Obtener el elemento canvas original y definir el margen
            const canvasOriginal = informesChartInstance.canvas;
            const margin = 20;
            
            // Crear un nuevo canvas temporal, aumentando el tamaño para el margen
            const canvasTemporal = document.createElement('canvas');
            canvasTemporal.width = canvasOriginal.width + 2 * margin; // Añadir margen a izquierda y derecha
            canvasTemporal.height = canvasOriginal.height + 2 * margin; // Añadir margen arriba y abajo
            
            const ctxTemp = canvasTemporal.getContext('2d');
            
            // 3. Dibujar el fondo blanco en el canvas temporal completo
            ctxTemp.fillStyle = 'white'; 
            ctxTemp.fillRect(0, 0, canvasTemporal.width, canvasTemporal.height);

            // 4. Dibujar el contenido del gráfico original con el desfase del margen
            ctxTemp.drawImage(canvasOriginal, margin, margin);

            // 5. Obtener el Data URL de la imagen (PNG o JPEG/JPG)
            let dataURL;
            let mimeType;
            
            if (formato === 'png') {
                mimeType = 'image/png';
                dataURL = canvasTemporal.toDataURL(mimeType);
            } else if (formato === 'jpg') {
                mimeType = 'image/jpeg';
                // Usamos toDataURL para obtener la imagen JPEG
                dataURL = canvasTemporal.toDataURL(mimeType, 1.0);
            } else {
                return; // Formato no soportado
            }
            
            // 6. Crear un enlace temporal para forzar la descarga
            const a = document.createElement('a');
            a.download = `informes_por_mes_${new Date().toISOString().split('T')[0]}.${formato}`;
            a.href = dataURL;

            // 7. Simular el clic en el enlace
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Event Listeners para los botones
        if (descargarPngBtn) {
            descargarPngBtn.addEventListener('click', function (e) {
                e.preventDefault();
                descargarGrafico('png');
            });
        }

        if (descargarJpgBtn) {
            descargarJpgBtn.addEventListener('click', function (e) {
                e.preventDefault();
                descargarGrafico('jpg');
            });
        }
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

    // Código para el dropdown de descarga de gráficos
    var descargarToggle = document.getElementById('descargarGraficoDropdown');

    if (descargarToggle) {
        // Crear una nueva instancia de Dropdown de Bootstrap para la descarga
        var descargaDropdown = new bootstrap.Dropdown(descargarToggle);

        // Agrega un listener de click para manejar el toggle
        descargarToggle.addEventListener('click', function (e) {
            descargaDropdown.toggle();  // Fuerza la acción de mostrar/ocultar
        });
    }

});