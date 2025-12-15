<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimación de Materiales</title>
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
                <h1 class="mb-4 h3">Estimación de Materiales</h1>

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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm p-4 mt-3">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="nombre_calculo" class="form-label fw-bold">
                                Seleccione una construcción para ver su estimación
                            </label>

                            <select class="form-select" id="nombre_calculo">
                                <option value="" disabled selected>Seleccione la construcción a realizar</option>

                                @forelse ($calculos as $calculo)
                                    <option value="{{ $calculo->id_calculo }}">
                                        {{ $calculo->nombre_calculo }}
                                    </option>
                                    @empty
                                    <option value="" disabled>No hay cálculos disponibles</option>
                                @endforelse
                            </select>

                            <small class="form-text text-muted">El contenido del cálculo se mostrará en el cuadro de texto de abajo.</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="contenido" class="form-label fw-bold">
                            Contenido del cálculo
                        </label>

                        <button type="button" class="btn btn-sm btn-outline-secondary mb-2" id="copyButton">
                            <i class="bi bi-clipboard"></i>
                        </button>
                        <span id="copyMessage" class="ms-2 text-success fw-bold d-none">¡Copiado!</span>
      
                        <textarea class="form-control" id="contenido" name="contenido" 
                        rows="15" readonly 
                        style="background-color: #f8f9fa; resize: none;" 
                        placeholder="Seleccione una construcción para ver su contenido aquí."></textarea>
            
                        <small class="form-text text-danger">Este campo es de solo lectura.</small>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        const calculosData = {!! $calculosJson !!};
    </script>
    
    <script src="{{ asset('js/estimaciones.js') }}"></script>
    

    
    @include('components._session-timeout')
</body>

</html>