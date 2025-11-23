<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>

<body>
    {{-- Barra lateral --}}
    <div class="bg-white" id="sidebar-wrapper">
        <div class="list-group list-group-flush pt-4">

            <a href="{{ route('dashboard') }}"
                class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('dashboard')) active @endif">
                <i class="bi bi-house me-2"></i> Inicio
            </a>

            <a href="{{ route('perfil') }}"
                class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('perfil')) active @endif">
                <i class="bi bi-person me-2"></i> Perfil
            </a>

            @if (auth()->check() && auth()->user()->id_rol === 1)
                <a href="{{ route('personal.index') }}"
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('personal.index') || request()->routeIs('personal.edit')) active @endif">
                    <i class="bi bi-file-earmark-person me-2"></i> Gestión de personal
                </a>
            @endif

            @if (auth()->check() && auth()->user()->id_rol === 1)
                <a href="{{ route('recursos.index') }}"
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('recursos.index') || request()->routeIs('recursos.create') || request()->routeIs('recursos.') || request()->routeIs('recursos.assignments.history')) active @endif">
                    <i class="bi bi-gear me-2"></i> Recursos
                </a>
            @endif

            @if (auth()->check() && auth()->user()->id_rol === 2)
                <a href="{{ route('recursos.assignments.history') }}"
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('recursos.assignments.history')) active @endif">
                    <i class="bi bi-gear me-2"></i> Recursos
                </a>
            @endif

            <a href="#" class="list-group-item list-group-item-action bg-white d-flex align-items-center">
                <i class="bi bi-calculator me-2"></i> Estimación de materiales
            </a>

            <a href="{{ route('inspecciones.index') }}"
                class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('inspecciones.index') || request()->routeIs('inspecciones.create') || request()->routeIs('inspecciones.edit')) active @endif">
                <i class="bi bi-card-checklist me-2"></i> Gestión de inspecciones
            </a>


            <a href="{{ route('informes.index') }}"
                class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('informes.index') || request()->routeIs('informes.seleccionar' || request()->routeIs('informes.create'))) active @endif">
                <i class="bi bi-file-earmark-text me-2"></i> Informes técnicos
            </a>


            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center w-100 border-0">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>