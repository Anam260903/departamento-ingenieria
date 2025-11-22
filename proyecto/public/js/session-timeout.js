document.addEventListener('DOMContentLoaded', function () {

    // 5 minutos (SESSION_LIFETIME)
    const SESSION_TIMEOUT_MS = 5 * 60 * 1000;
    // 1 minuto (Tiempo para advertir antes de la expiración)
    const WARNING_TIME_MS = 1 * 60 * 1000;

    // El modal se mostrará 4 minutos después de la última actividad
    const SHOW_WARNING_AT_MS = SESSION_TIMEOUT_MS - WARNING_TIME_MS;

    let warningTimer;
    let timeoutTimer;
    let countdownTimer;

    // --- Funciones de Control ---

    function resetTimers() {

        if (document.getElementById('sessionWarningModal').classList.contains('show')) {
            return;
        }

        clearTimeout(warningTimer);
        clearTimeout(timeoutTimer);
        clearInterval(countdownTimer);

        // Configura el temporizador para mostrar la advertencia
        warningTimer = setTimeout(showWarningModal, SHOW_WARNING_AT_MS);
    }

    function logoutUser() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/logout', { // Ruta de Laravel para cerrar sesión
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken
            },
            body: '_token=' + csrfToken
        })
            .then(response => {
                window.location.href = '/login';
            })
            .catch(error => {
                console.error('Error durante el cierre de sesión:', error);
                window.location.href = '/login';
            });
    }

    function extendSession() {
        // Llama a la ruta POST para extender la sesión
        fetch('/session/extend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Usa el token CSRF para seguridad
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                // Si la extensión es exitosa
                if (response.ok) {
                    hideWarningModal();
                    resetTimers(); // Reiniciar todo
                } else {
                    // Si falla, cerrar sesión
                    logoutUser();
                }
            })
            .catch(() => {
                // Error de red
                logoutUser();
            });
    }

    // --- Funciones del Modal ---

    function getModalInstance() {
        const modalElement = document.getElementById('sessionWarningModal');
        if (typeof bootstrap !== 'undefined' && modalElement) {
            return bootstrap.Modal.getOrCreateInstance(modalElement);
        }
        return null;
    }

    function showWarningModal() {
        const modal = getModalInstance();
        if (modal) {
            modal.show();
            // Configurar el temporizador para el cierre de sesión real
            timeoutTimer = setTimeout(logoutUser, WARNING_TIME_MS);
            startCountdown();
        }
    }

    function hideWarningModal() {
        const modal = getModalInstance();
        if (modal) {
            modal.hide();
        }
    }

    function startCountdown() {
        let timeLeft = WARNING_TIME_MS / 1000; // Segundos restantes
        const countdownDisplay = document.getElementById('countdownDisplay');

        if (!countdownDisplay) return;

        countdownDisplay.textContent = timeLeft;

        countdownTimer = setInterval(() => {
            timeLeft--;
            countdownDisplay.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
            }
        }, 1000);
    }

    // --- Inicialización y Event Listeners ---

    // Reiniciar temporizadores con la actividad del usuario
    ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'].forEach(eventName => {
        document.addEventListener(eventName, resetTimers, true);
    });

    // Event listener para el botón de extensión
    const extendButton = document.getElementById('extendSessionButton');
    if (extendButton) {
        extendButton.addEventListener('click', extendSession);
    }

    // Iniciar los temporizadores
    resetTimers();
});