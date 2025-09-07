document.addEventListener('DOMContentLoaded', function () {
    const passwordField = document.getElementById('password-field');
    const togglePassword = document.getElementById('toggle-password');
    const eyeIcon = document.getElementById('eye-icon');

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            // Cambia el tipo del campo de entrada
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Cambia el ícono del ojo
            if (type === 'password') {
                eyeIcon.classList.remove('bi-eye-slash-fill');
                eyeIcon.classList.add('bi-eye-fill');
            } else {
                eyeIcon.classList.remove('bi-eye-fill');
                eyeIcon.classList.add('bi-eye-slash-fill');
            }
        });
    }
});