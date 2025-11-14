<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Personal</title>
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
                <h1 class="mb-4 h3">GESTIÓN DE PERSONAL</h1>
                <h2 class="h4 mb-4">Editar Usuario: {{ $personal->nombre }} {{ $personal->apellido }}</h2>

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Manejo de errores de validación de Laravel --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm p-4">
                    {{-- Formulario de edición de personal --}}
                    <form action="{{ route('personal.update', $personal->id_user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Sección de datos personales --}}
                        <h5 class="mb-3">Datos de Identificación</h5>
                        <div class="row mb-4">

                            {{-- Cédula --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="cedula_user" class="form-label">Cédula</label>
                                <input type="text" class="form-control @error('cedula_user') is-invalid @enderror"
                                    id="cedula_user" name="cedula_user" placeholder="Cédula de identidad"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57" minlength="7" maxlength="8"
                                    value="{{ old('cedula_user', $personal->cedula_user) }}" required>
                                @error('cedula_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nombre --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                    id="nombre" name="nombre" placeholder="Nombre" maxlength="30"
                                    value="{{ old('nombre', $personal->nombre) }}" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+"
                                    title="Solo se permiten letras y espacios" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Apellido --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror"
                                    id="apellido" name="apellido" placeholder="Apellido" maxlength="30"
                                    value="{{ old('apellido', $personal->apellido) }}" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+"
                                    title="Solo se permiten letras y espacios" required>
                                @error('apellido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            
                            {{-- Campo de rol --}}
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="id_rol" class="form-label">Rol del Usuario</label>
                                <select class="form-select @error('id_rol') is-invalid @enderror" id="id_rol" name="id_rol" required>
                                    <option value="">Seleccione un Rol</option>
                                    {{-- Recorremos la lista de roles que vienen del controlador --}}
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id_rol }}"  @selected(old('id_rol', $personal->id_rol) == $rol->id_rol)>
                                            {{ $rol->nombre_rol }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_rol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> 

                        {{-- Sección de contacto y profesión --}}
                        <h5 class="mb-3">Contacto y Profesión</h5>
                        <div class="row mb-4">
                            {{-- Correo --}}
                            <div class="col-lg-6 col-md-6 mb-3">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control @error('correo') is-invalid @enderror"
                                    id="correo" name="correo" placeholder="ejemplo@gmail.com" maxlength="40"
                                    value="{{ old('correo', $personal->correo) }}" required>
                                @error('correo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Profesión --}}
                            <div class="col-lg-6 col-md-6 mb-3">
                                <label for="profesion" class="form-label">Profesión (Opcional)</label>
                                <input type="text" class="form-control @error('profesion') is-invalid @enderror"
                                    id="profesion" name="profesion" placeholder="Ingeniero Civil, Topografo, etc."
                                    maxlength="50" value="{{ old('profesion', $personal->profesion) }}">
                                @error('profesion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- sección cambio de contraseña --}}
                        <h5 class="mb-3">Cambio de Contraseña (Opcional)</h5>
                        <p class="text-muted small">Solo llena estos campos si deseas cambiar la contraseña del usuario.
                        </p>
                        <div class="row mb-4">

                            {{-- Nueva Contraseña --}}
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" minlength="8" maxlength="15" placeholder="Nueva contraseña">
                                    
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Confirmar Contraseña --}}
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Repite la nueva contraseña">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            {{-- Botón Cancelar: Vuelve al listado de personal --}}
                            <a href="{{ route('personal.index') }}" class="btn btn-secondary">Cancelar</a>

                            {{-- Botón Guardar --}}
                            <button type="submit" class="btn btn-primary w-auto">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>