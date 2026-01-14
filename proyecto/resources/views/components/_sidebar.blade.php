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
                class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('perfil') || request()->routeIs('seguridad.verificarContraseña') || request()->routeIs('seguridad.form.actualizarPreguntas') || request()->routeIs('seguridad.actualizarPreguntas')) active @endif">
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
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('recursos.index') || request()->routeIs('recursos.create') || request()->routeIs('recursos.edit') || request()->routeIs('recursos.assignments.history')) active @endif">
                    <i class="bi bi-gear me-2"></i> Recursos
                </a>
            @endif

            @if (auth()->check() && auth()->user()->id_rol === 2)
                <a href="{{ route('recursos.assignments.history') }}"
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('recursos.assignments.history')) active @endif">
                    <i class="bi bi-gear me-2"></i> Recursos
                </a>
            @endif

            @if (auth()->check() && auth()->user()->id_rol === 1)
                <a href="{{ route('decisiones.index') }}"
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('decisiones.index') || request()->routeIs('decisiones.recursos') || request()->routeIs('decisiones.recursos.top') || request()->routeIs('decisiones.resumenInspeccion') || request()->routeIs('api.inspeccion.resumen') || request()->routeIs('decisiones.comparacion')) active @endif">
                    <i class="bi bi-journal-check me-2"></i> Toma de decisiones
                </a>
            @endif

            <a href="{{ route('estimacion-materiales') }}" class="list-group-item list-group-item-action bg-white d-flex align-items-center @if(request()->routeIs('estimacion-materiales')) active @endif ">
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

            <a href="#" class="list-group-item list-group-item-action bg-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#helpCenterModal">    <i class="bi bi-question-octagon me-2"></i> Centro de ayuda</a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="list-group-item list-group-item-action bg-white d-flex align-items-center w-100 border-0">
                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>

    {{-- Modal para el centro de ayuda --}}
    <div class="modal fade" id="helpCenterModal" tabindex="-1" aria-labelledby="helpCenterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="helpCenterModalLabel">
                        <i class="bi bi-info-circle me-2"></i> Información del Sistema
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo3.jpg') }}" alt="Logo" style="width: 100px;"> 
                    <h4 class="mt-2">Departamento de Ingeniería</h4>
                    <h5 class="mt-2">CORVISUCRE - Circuito II</h5>
                    <p class="text-muted">Versión 1.0.0</p>
                </div>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Elaborado Por:</strong> Ana Marcano</li>
                    <li class="list-group-item"><strong>Desarrollado con:</strong> Laravel 12.26.3 & Bootstrap 5.3.8</li>
                    <li class="list-group-item"><strong>Base de Datos:</strong> MariaDB 10.4.32</li>
                    <li class="list-group-item"><strong>Última actualización:</strong> Enero 2026 </li>
                </ul>

                <div class="alert alert-light border">
                    <h6>¿Necesitas ayuda adicional?</h6>
                    <p class="small text-muted">Consulta el manual de usuario detallado para aprender a utilizar todas las funciones del sistema.</p>
                    
                    {{-- Botón para descargar manual de usuario--}}
                    <div class="d-grid gap-2">
                        <a href="{{ asset('documentos/manual-usuario.pdf') }}" 
                           download="Manual_de_Usuario.pdf" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Descargar Manual de Usuario (PDF)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>