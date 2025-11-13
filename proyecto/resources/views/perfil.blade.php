<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    @include('components._navbar')

    <div class="d-flex" id="wrapper">
        @include('components._sidebar')

        <div id="page-content-wrapper">

            <div class="container-fluid py-4">
                <h1 class="mb-4 h3">MI PERFIL</h1>
                
                {{-- Mensajes de sesión --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
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
                                    value="{{ $usuario->cedula_user }}" disabled>
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

                            <div class="mb-3">
                                <label for="password_actual" class="form-label">Contraseña actual</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_actual"
                                        name="password_actual" maxlength="10" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password_nueva" class="form-label">Nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_nueva"
                                        name="password_nueva" maxlength="10" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmacion" class="form-label">Confirma nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_nueva_confirmation"
                                        name="password_nueva_confirmation" maxlength="10" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>