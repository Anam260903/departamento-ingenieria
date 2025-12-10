<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>

<body>

    @include('components._navbar')

    <div class="d-flex" id="wrapper">
        @include('components._sidebar')

        <div id="page-content-wrapper">

            <div class="container-fluid py-4">
                <h1 class="mb-4 h3">MI PERFIL</h1>

                {{-- Mensajes de sesión --}}
                @php
                    $mensaje = session('status') ?? session('success');
                @endphp

                @if($mensaje)
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $mensaje }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm p-4">

                        <h5 class="card-title text-center mb-4">Editar perfil</h5>
                        <form action="{{ route('perfil.update') }}" method="POST">
                            @csrf

                            {{-- Datos personales --}}
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="30"
                                    value="{{ $usuario->nombre }}" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+"
                                    title="Solo se permiten letras y espacios">
                            </div>
                            <div class="mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" maxlength="30"
                                    value="{{ $usuario->apellido }}" required pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+"
                                    title="Solo se permiten letras y espacios">
                            </div>
                            <div class="mb-3">
                                <label for="cedula_user" class="form-label">Cédula</label>
                                <input type="text" class="form-control" id="cedula_user" name="cedula_user"
                                    value="{{ $usuario->cedula_user }}"
                                    title="Para cambiar este dato contacta a la administración" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="correo" name="correo" maxlength="40"
                                    value="{{ $usuario->correo }}" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    {{-- Cambio de contraseña--}}
                    <div class="card shadow-sm p-4">
                        <h5 class="card-title text-center mb-4">Cambiar contraseña</h5>
                        <form action="{{ route('perfil.change-password') }}" method="POST">
                            @csrf

                            {{-- Contraseña actual --}}
                            <div class="mb-3">
                                <label for="password_actual" class="form-label">Contraseña actual</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_actual"
                                        name="password_actual" maxlength="10" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i> </button>
                                </div>
                            </div>


                            {{-- Contraseña nueva --}}
                            <div class="mb-3">
                                <label for="password_nueva" class="form-label">Nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_nueva"
                                        data-bs-toggle="tooltip" data-bs-trigger="hover"
                                        data-bs-title="8-15 caracteres. Debe incluir: Una letra mayúscula (A-Z). Una letra minúscula (a-z). Un número (0-9). Un símbolo (! $ # % @ .)"
                                        name="password_nueva" minlength="8" maxlength="15" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i> </button>
                                </div>
                            </div>

                            {{-- Confirmación de contraseña nueva --}}
                            <div class="mb-3">
                                <label for="password_confirmacion" class="form-label">Confirma nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_nueva_confirmation"
                                        name="password_nueva_confirmation" minlength="8" maxlength="15" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i> </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Actualizar preguntas de seguridad--}}
            <div class="card shadow-sm p-4 mt-4">
                <h5 class="card-title text-center mb-4">Preguntas de Seguridad</h5>
                <p class="text-center text-muted">Utiliza tus preguntas para recuperar tu contraseña si la olvidas.
                </p>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#confirmPasswordModal">
                        <i class="bi bi-shield-lock-fill"></i> Actualizar Preguntas
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal para confirmar identidad con contraseña --}}
    <div class="modal fade" id="confirmPasswordModal" tabindex="-1" aria-labelledby="confirmPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPasswordModalLabel">Confirma tu Identidad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="{{ route('seguridad.verificarContraseña') }}">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted">Ingresa tu contraseña actual para poder modificar tus preguntas de
                            seguridad.</p>

                        <div class="mb-3">
                            <label for="password_modal" class="form-label">Contraseña actual</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_modal" name="password"
                                    minlength="8" maxlength="15" placeholder="Contraseña actual" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            @error('password', 'confirmQuestions')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Inicializar Tooltips y Popovers
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializa Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // Verificamos si hay cualquier error dentro de Error Bag.
            @if ($errors->confirmQuestions->any())
                // 1. Crear una nueva instancia del Modal de Bootstrap
                var confirmModal = new bootstrap.Modal(document.getElementById('confirmPasswordModal'));

                // 2. Mostrar el modal
                confirmModal.show();

                // 3. Enfocar el campo para que el usuario pueda corregir inmediatamente
                var passwordField = document.getElementById('password_modal');
                if (passwordField) {
                    passwordField.focus();
                }
            @endif
        });
    </script>

    @include('components._session-timeout')
</body>

</html>