<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nav</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dasboard.css') }}" rel="stylesheet">
</head>

<body>
    {{-- Barra de navegación --}}
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top navegacion">
        <div class="container-fluid">

            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}" title="Inicio">
                <img src="{{ asset('images/logo4.png') }}" alt="Logo" width="180" height="45">
            </a>

            <div class="d-flex flex-grow-1 justify-content-center">
                <div class="text-white text-center">
                    <span class="d-block fw-bold">DEPARTAMENTO DE INGENIERÍA</span>
                    <small class="d-block">CORVISUCRE CARÚPANO</small>
                </div>
            </div>

            <div class="nav-item dropdown me-4">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false" title="Notificaciones">
                    <i class="bi bi-bell-fill fs-4 text-white"></i>

                    {{-- Contador de no leídas --}}
                    @if (isset($unreadCount) && $unreadCount > 0)
                        <span class="position-absolute translate-middle badge rounded-pill bg-danger"
                            style="top: 10px; right: 15px;">
                            {{ $unreadCount > 9 ? '+9' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="width: 300px;">
                    <li class="dropdown-header text-center fw-bold">Notificaciones
                        ({{ isset($unreadCount) ? $unreadCount : 0 }} sin leer)</li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @if (isset($notificaciones) && $notificaciones->count() > 0)
                        @foreach ($notificaciones as $notificacion)
                            <a href="{{ route('notifications.markAsRead', $notificacion->id_notificacion) }}"
                                class="dropdown-item d-flex align-items-start {{ $notificacion->leida ? 'text-muted' : 'fw-bold' }}"
                                style="white-space: normal; line-height: 1.4;">
                                <i
                                    class="bi {{ $notificacion->tipo == 'inspeccion_asignada' ? 'bi-clipboard-check-fill text-primary' : 'bi-tools text-warning' }} me-2 mt-1"></i>
                                <div>
                                    {{ $notificacion->mensaje }}
                                    <small
                                        class="d-block text-muted fw-normal">{{ $notificacion->created_at->diffForHumans() }}</small>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <li class="dropdown-item text-center text-muted">No tienes notificaciones recientes.</li>
                    @endif

                    @if (isset($notificaciones) && $notificaciones->count() > 0)
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center" href="{{ route('notifications.index') }}">Ver todas</a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="me-2 d-none d-lg-block">
                <span class="text-white d-inline-block">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</span>
                <br>
                <small class="text-white d-inline-block">{{ Auth::user()->correo }}</small>
            </div>

            <div class="me-2 d-none d-lg-block">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('logout') }}" title="Cerrar sesión">
                    <img src="{{ asset('images/logo1.png') }}" alt="Logo" width="50" height="40">
                </a>
            </div>


            <div class="col-auto d-md-none">
                <button class="btn btn-primary" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>

        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicialización de Dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>