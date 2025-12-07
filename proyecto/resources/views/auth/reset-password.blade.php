<!DOCTYPE html>
<html lang="es">

<head>
    <title>Restablecer Contraseña</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row login-row">
            <div class="col-md-6 left-panel">
                <div class="login-card">
                    <h2 class="text-center mb-4">Restablecer Contraseña</h2>

                    <form method="POST" action="{{ route('update.contraseña')}}">
                        @csrf
                        <input type="hidden" name="reset_token" value="{{ $resetToken }}">

                        {{-- Nueva contraseña --}}
                        <div class="mb-3">
                            <label for="password-field" class="form-label">Nueva Contraseña</label>

                            {{-- Contenedor de la contraseña --}}
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password-field" name="password" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    data-bs-title="8-15 caracteres. Debe incluir: Una letra mayúscula (A-Z). Una letra minúscula (a-z). Un número (0-9). Un símbolo (! $ # % @, etc.)"
                                    minlength="8" maxlength="15" placeholder="Contraseña" required>
                                <span class="input-group-text" id="toggle-password">
                                    <i class="bi bi-eye-fill" id="eye-icon"></i>
                                </span>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirmación de contraseña --}}
                        <div class="mb-3">
                            <label for="password-confirm-field" class="form-label">Confirmar Nueva Contraseña</label>

                            <div class="input-group">
                                <input type="password" class="form-control" id="password-confirm-field"
                                    name="password_confirmation" minlength="8" maxlength="15"
                                    placeholder="Confirmar Contraseña" required>
                                <span class="input-group-text" id="toggle-password-confirm">
                                    <i class="bi bi-eye-fill" id="eye-icon-confirm"></i>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6 right-panel d-none d-md-flex">
                <div class="logo-container">
                    <img src="{{ asset('images/logo-corvisucre.png') }}"
                        alt="Logo Departamento de Ingeniería CORVISUCRE">
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/register.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('password-field');

            if (passwordField) {
                // 1. Creamos la instancia del tooltip
                const passwordTooltip = new bootstrap.Tooltip(passwordField);

                // 2. Definimos una función que solo oculte el tooltip.
                const hideTooltipOnFocus = function () {
                    // Ocultamos el tooltip
                    passwordTooltip.hide();
                };

                // 3. Asignamos el evento 'focus'
                passwordField.addEventListener('focus', hideTooltipOnFocus);
            }
        });
    </script>
</body>

</html>