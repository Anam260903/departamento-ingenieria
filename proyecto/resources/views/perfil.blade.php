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

    <div class="d-flex" id="wrapper">

        @include('components._sidebar')

        <div id="page-content-wrapper">

            @include('components._navbar')

            <div class="container-fluid py-4">
                <h1 class="mb-4">MI PERFIL</h1>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm p-4">
                            <h5 class="card-title text-center mb-4">Editar perfil</h5>
                            <form action="#" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $usuario->nombre }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" value="{{ $usuario->apellido }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="cedula_user" class="form-label">Cédula</label>
                                    <input type="text" class="form-control" id="cedula_user" name="cedula_user" value="{{ $usuario->cedula_user }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="correo" name="correo" value="{{ $usuario->correo }}" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm p-4">
                            <h5 class="card-title text-center mb-4">Cambiar contraseña</h5>
                            <form action="#" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="password_actual" class="form-label">Contraseña actual</label>
                                    <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password_nueva" class="form-label">Nueva contraseña</label>
                                    <input type="password" class="form-control" id="password_nueva" name="password_nueva" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password_confirmacion" class="form-label">Confirma nueva contraseña</label>
                                    <input type="password" class="form-control" id="password_confirmacion" name="password_confirmacion" required>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>