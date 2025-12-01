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
    let sessionModalInstance = null;

    // --- Funciones de Control ---

    function resetTimers() {

        // Si el modal está visible, no reiniciamos el warningTimer ni el timeoutTimer.
        if (document.getElementById('sessionWarningModal') &&
            document.getElementById('sessionWarningModal').classList.contains('show')) {
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

        clearTimeout(timeoutTimer);
        clearInterval(countdownTimer);

        hideWarningModal();

        fetch('/session/extend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (response.ok) {
                    resetTimers(); // Reiniciar todo
                } else {
                    logoutUser();
                }
            })
            .catch(() => {
                logoutUser();
            });
    }

    // --- Funciones del Modal ---

    function getModalInstance() {
        const modalElement = document.getElementById('sessionWarningModal');

        if (!modalElement || typeof bootstrap === 'undefined') {
            return null;
        }

        if (sessionModalInstance) {
            return sessionModalInstance;
        }

        // Creamos la instancia si no existe
        try {
            sessionModalInstance = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            return sessionModalInstance;
        } catch (e) {
            console.error("Error al crear la instancia del Modal de Bootstrap:", e);
            return null;
        }
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
            try {
                modal.hide();
            } catch (e) {
                console.warn("Error seguro: El modal fue removido o destruido previamente. Ignorando error de Bootstrap.", e);
            }
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