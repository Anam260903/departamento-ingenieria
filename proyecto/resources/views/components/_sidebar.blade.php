<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body>
    <div class="bg-white" id="sidebar-wrapper">
    <div class="list-group list-group-flush pt-4">
        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-white active d-flex align-items-center">
            <i class="bi bi-house me-2"></i> Inicio
        </a>
        <a href="#" class="list-group-item list-group-item-action bg-white d-flex align-items-center">
            <i class="bi bi-person me-2"></i> Perfil
        </a>
        <a href="#" class="list-group-item list-group-item-action bg-white d-flex align-items-center">
            <i class="bi bi-card-checklist me-2"></i> Gestión de inspecciones
        </a>
        <a href="#" class="list-group-item list-group-item-action bg-white d-flex align-items-center">
            <i class="bi bi-file-earmark-text me-2"></i> Informes técnicos
        </a>
        <a href="#" class="list-group-item list-group-item-action bg-white d-flex align-items-center">
            <i class="bi bi-calculator me-2"></i> Estimación de materiales
        </a>
        <a href="#" class="list-group-item list-group-item-action bg-white d-flex align-items-center">
            <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
        </a>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>