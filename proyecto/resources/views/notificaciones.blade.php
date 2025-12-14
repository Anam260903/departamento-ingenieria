<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    @include('components._navbar')

    <div class="d-flex" id="wrapper">
        @include('components._sidebar')

        <div id="page-content-wrapper">

            <div class="container-fluid py-4">
                <h1 class="mb-4 h3">CENTRO DE NOTIFICACIONES</h1>

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
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-8 mx-auto">

                        {{-- Botón Marcar todas como leídas --}}
                        @if ($notificaciones->where('leida', false)->count() > 0)
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                Tienes {{ $notificaciones->where('leida', false)->count() }} notificaciones sin leer.
                                <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info">Marcar todas como
                                        leídas</button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-secondary">
                                Todas las notificaciones han sido leídas.
                            </div>
                        @endif

                        <div class="card shadow-sm">
                            <div class="list-group list-group-flush">
                                @forelse ($notificaciones as $notificacion)
                                                            {{-- Estilo para destacar las no leídas --}}
                                                            <div
                                                                class="list-group-item d-flex justify-content-between align-items-center {{ $notificacion->leida ? 'text-muted' : 'list-group-item-light fw-bold' }}">

                                                                <div class="d-flex align-items-center">
                                                                    {{-- Ícono según el tipo --}}
                                                                    <i
                                                                        class="bi {{ 
                                                                                                                                                                                                                                                                    $notificacion->tipo == 'inspeccion_asignada' ? 'bi-clipboard-check-fill text-success' :
        ($notificacion->tipo == 'recurso_asignado' ? 'bi-tools text-warning' : 'bi-info-circle-fill text-primary') 
                                                                                                                                                                                                                                                                }} me-3 fs-5"></i>

                                                                    <div>
                                                                        <span class="d-block">{{ $notificacion->mensaje }}</span>
                                                                        <small class="text-secondary fw-normal">
                                                                            {{ $notificacion->created_at->diffForHumans() }}
                                                                            @if (!$notificacion->leida)
                                                                                <span class="badge bg-primary ms-2">NUEVA</span>
                                                                            @endif
                                                                        </small>
                                                                    </div>
                                                                </div>

                                                                {{-- Botón para marcar individualmente --}}
                                                                @if (!$notificacion->leida)
                                                                    <form
                                                                        action="{{ route('notifications.markAsRead', $notificacion->id_notificacion) }}"
                                                                        method="POST" class="d-inline-block">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-3"
                                                                            title="Marcar como leída">
                                                                            <i class="bi bi-check-lg"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                @empty
                                    <div class="list-group-item text-center py-4">
                                        No hay notificaciones históricas para mostrar.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Enlaces de Paginación --}}
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $notificaciones->links() }}
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>


    @include('components._session-timeout')
</body>

</html>