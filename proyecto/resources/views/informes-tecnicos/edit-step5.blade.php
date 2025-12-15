<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Técnico - Paso 5</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/informes.css') }}">
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include ('components._sidebar')
        <div id="page-content-wrapper">
            @include('components._navbar')
            <div class="container-fluid py-4">

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

                <h1 class="mb-4 h3">INFORME TÉCNICO</h1>

                {{-- Barra de progreso--}}
                <div class="step-container">
                    <div class="step completed">1. Datos Generales</div>
                    <div class="step completed">2. Diagnóstico y observaciones</div>
                    <div class="step completed">3. Recomendaciones y mapa</div>
                    <div class="step completed">4. Materiales</div>
                    <div class="step active">5. Evidencia fotog.</div>
                </div>

                <div class="card shadow-sm p-4 mt-3">

                    <form action="{{ route('informes.update.step5', $informe->id_inf) }}" method="POST"
                        enctype="multipart/form-data" id="form-paso5">
                        @csrf
                        <input type="hidden" name="id_inf" value="{{ $informe->id_inf }}">

                        <div class="mb-4">
                            <label for="photos" class="form-label fw-bold">Seleccionar o Arrastrar Fotos</label>

                            <div class="p-4 border rounded text-center" id="drop-area-simple">
                                <p class="mb-2">Arrastra y suelta imágenes aquí, o haz clic para seleccionar.</p>
                                <input type="file" class="form-control" id="photos" name="photos[]" accept="image/*"
                                    multiple>
                            </div>

                            <div id="preview-container" class="row row-cols-2 row-cols-md-4 g-3 mt-3">
                            </div>

                            <small class="form-text text-muted">Acepta múltiples archivos de imagen (JPG, PNG).</small>
                            <?php if ($errors->has('photos')): ?>
                            <div class="text-danger"><?php    echo $errors->first('photos'); ?></div>
                            <?php elseif ($errors->has('photos.*')): ?>
                            <div class="text-danger">Asegúrate de que todos los archivos sean imágenes válidas.</div>
                            <?php endif; ?>
                        </div>
                    </form>

                    {{-- Mostrar vistas previas de las imágenes ya cargadas --}}
                    @if ($informe->imagenes->count() > 0)
                        <h6 class="mt-4 mb-3">Imágenes Cargadas:</h6>
                        <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                            @foreach ($informe->imagenes as $imagen)
                                <div class="col" id="imagen-{{ $imagen->id_evid }}">
                                    
                                    <div class="card h-100 shadow-sm border-success position-relative">

                                        {{-- Botón de Eliminación --}}
                                        <form action="{{ route('informes.delete.image', $imagen->id_evid) }}" method="POST"
                                            class="position-absolute top-0 end-0 m-1" style="z-index: 10;">
                                            @csrf
                                            @method('DELETE')
                        
                                            <button type="submit" class="btn-close m-1 bg-light p-1 rounded"
                                                aria-label="Eliminar" title="Eliminar imagen"
                                                onclick="return confirm('¿Estás seguro de que quieres eliminar la imagen?')">
                                            </button>
                                        </form>

                                        {{-- Imagen --}}
                                        <img src="{{ url('storage/' . $imagen->ruta_archivo) }}" class="card-img-top"
                                            style="height: 150px; object-fit: cover;" alt="Evidencia cargada">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('informes.edit.step4', $informe->id_inf) }}" class="btn btn-secondary">
                            Volver
                        </a>
                        <button type="submit" class="btn btn-primary w-auto" form="form-paso5">
                            Finalizar Informe
                        </button>
                    </div>



                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/informes.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Llamada a la función de inicialización del Paso 5
            initStep5PhotoUpload();
        });
    </script>

    @include('components._session-timeout')
</body>

</html>