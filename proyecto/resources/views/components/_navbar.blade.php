<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nav</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dasboard.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top navegacion">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo_icon.png') }}" alt="Logo" width="40" height="40" class="me-2">
        </a>

        <div class="d-flex flex-grow-1 justify-content-center">
            <div class="text-white text-center">
                <span class="d-block fw-bold">DEPARTAMENTO DE INGENIERÍA</span>
                <small class="d-block">CORVISUCRE CARÚPANO</small>
            </div>
        </div>

        <div class="ms-auto d-none d-lg-block">
            <span class="text-white d-inline-block">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</span> <br>
            <small class="text-white d-inline-block">{{ Auth::user()->correo }}</small>
        </div>
        
        <button class="btn btn-primary d-md-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
    </div>
</nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>