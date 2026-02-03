<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

            {{-- Notificaciones --}}
            <div class="nav-item dropdown me-4">
                <a class="nav-link" href="#" id="NotificacionToggle" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false" data-bs-auto-close="outside" title="Notificaciones">
                    <i class="bi bi-bell-fill fs-4 text-white"></i>

                    {{-- Contador de no leídas --}}
                    @if (isset($unreadCount) && $unreadCount > 0)
                        <span class="position-absolute translate-middle badge rounded-pill bg-danger"
                            style="top: 10px; right: 15px;">
                            {{ $unreadCount > 9 ? '+9' : $unreadCount }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="NotificacionToggle" id="menuNotificaciones"
                    style="width: 300px;">
                    <li class="dropdown-header text-center fw-bold">Notificaciones
                        ({{ isset($unreadCount) ? $unreadCount : 0 }} sin leer)</li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    @if (isset($notificaciones) && $notificaciones->count() > 0)
                        @foreach ($notificaciones as $notificacion)
                            {{-- Contenedor principal --}}
                            <div class="dropdown-item d-flex align-items-start {{ $notificacion->leida ? 'text-muted' : 'fw-bold' }}"
                                style="white-space: normal; line-height: 1.4;">

                                @if (!$notificacion->leida)
                                    <form action="{{ route('notifications.markAsRead', $notificacion->id_notificacion) }}"
                                        method="POST" class="w-100" id="mark-read-{{ $notificacion->id_notificacion }}" >
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-link dropdown-item text-start w-100 d-flex align-items-start {{ $notificacion->leida ? 'text-muted' : 'fw-bold' }}"
                                            style="white-space: normal; line-height: 1.4; text-decoration: none; padding: 0.5rem 1rem;">
                                            <i
                                                class="bi {{ $notificacion->tipo == 'inspeccion_asignada' ? 'bi-clipboard-check-fill text-primary' : 'bi-tools text-warning' }} me-2 mt-1"></i>
                                            <div>
                                                {{ $notificacion->mensaje }}
                                                <small
                                                    class="d-block text-muted fw-normal">{{ $notificacion->created_at->diffForHumans() }}</small>
                                            </div>
                                        </button>
                                    </form>
                                @else
                                    <div class="d-flex w-100 align-items-start" style="padding: 0.5rem 1rem;">
                                        <i
                                            class="bi {{ $notificacion->tipo == 'inspeccion_asignada' ? 'bi-clipboard-check-fill text-primary' : 'bi-tools text-warning' }} me-2 mt-1"></i>
                                        <div>
                                            {{ $notificacion->mensaje }}
                                            <small
                                                class="d-block text-muted fw-normal">{{ $notificacion->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Aviso si hay más notificaciones de las que se muestran --}}
                        @if($unreadCount > $notificaciones->where('leida', false)->count() && $unreadCount > 4)
                            <li>
                                <div class="dropdown-item text-center small text-muted italic">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Hay {{ $unreadCount - $notificaciones->where('leida', false)->count() }} más sin leer
                                </div>
                            </li>
                        @endif
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

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <a class="navbar-brand d-flex align-items-center" href="{{ route('logout') }}" title="Cerrar sesión"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">

                <img src="{{ asset('images/logo1.png') }}" alt="Logo" width="50" height="40">
            </a>

            <div class="col-auto d-md-none">
                <button class="btn btn-primary" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>

        </div>
    </nav>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

</body>

</html>