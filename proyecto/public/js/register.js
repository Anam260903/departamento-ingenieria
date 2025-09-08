document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password-field');

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            // Alternar el tipo de input entre 'password' y 'text'
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Alternar el ícono del ojo
            this.querySelector('i').classList.toggle('bi-eye-fill');
            this.querySelector('i').classList.toggle('bi-eye-slash-fill');
        });
    }

    const togglePasswordConfirm = document.getElementById('toggle-password-confirm');
    const passwordConfirmField = document.getElementById('password-confirm-field');

    if (togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', function () {
            // Alternar el tipo de input entre 'password' y 'text'
            const type = passwordConfirmField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmField.setAttribute('type', type);

            // Alternar el ícono del ojo
            this.querySelector('i').classList.toggle('bi-eye-fill');
            this.querySelector('i').classList.toggle('bi-eye-slash-fill');
        });
    }
});