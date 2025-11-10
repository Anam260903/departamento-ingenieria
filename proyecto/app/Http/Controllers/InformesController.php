<?php

namespace App\Http\Controllers;
use App\Models\Informe;
use App\Models\Inspeccion;
use App\Models\Propietario;
use App\Models\Vivienda;
use App\Models\Calculos;
use App\Models\evidencia_fotografica;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class InformesController extends BaseController
{
    use AuthorizesRequests;

    // AÑADA ESTE CONSTRUCTOR
    public function __construct()
    {
        // Esto asegura que todos los métodos del controlador requieren autenticación
        $this->middleware('auth');
    }
    
    /**
     * Mostrar el listado de informes técnicos.
     */
    public function index()
    {
        // 1. Autorización: Verifica si el usuario puede acceder a la lista.
        // La Policy ('viewAny') garantiza que id_rol=1 (admin) pase o id_rol=2 (normal) pase.
        $this->authorize('viewAny', Informe::class);

        // 2. Obtener los informes
        $query = Informe::with([
            'inspeccion.vivienda.propietario',
            'inspeccion.usuario'
        ]);

        // NUEVO: FILTRADO DE INFORMES PARA USUARIOS NORMALES (id_rol === 2)
        $user = Auth::user();
        if ($user->id_rol === 2) {
            // Si es usuario normal, filtra los informes por su id_user
            $query->whereHas('inspeccion', function ($q) use ($user) {
                // Se une a la relación 'inspeccion' y se filtra por el ID del usuario logueado
                $q->where('id_user', $user->id_user);
            });
        }

        $informes = $query
            ->orderBy('fecha_inf', 'desc')
            ->paginate(10); // Paginación

        return view('informes-tecnicos.index', compact('informes'));
    }

    /**
     * Devuelve una lista de inspecciones completadas que aún no tienen un informe.
     * Esto se usa para poblar el modal de selección.
     */
    public function obtenerInspeccionesDisponibles()
    {
        // 1. Obtener los IDs de las inspecciones que ya tienen un informe
        $inspeccionesConInforme = Informe::pluck('id_insp');

        // 2. Obtener las inspecciones que están 'Completadas' y no tienen un informe
        $query = Inspeccion::where('estado_insp', 1)
            ->whereNotIn('id_insp', $inspeccionesConInforme)
            ->with('vivienda.propietario');

        // NUEVO: FILTRAR INSPECCIONES PARA USUARIOS NORMALES
        $user = Auth::user();
        if ($user->id_rol === 2) {
            $query->where('id_user', $user->id_user);
        }

        $inspecciones = $query->get();
        return $inspecciones;
    }

    /**
     * Muestra el formulario de creación del Informe Técnico.
     * @param int $id_insp
     */
    public function create($id_insp)
    {
        // 1. Cargar la inspección y sus relaciones para prellenar el formulario
        $inspeccion = Inspeccion::with([
            'vivienda.propietario',
            'usuario'
        ])->findOrFail($id_insp);

        // 2. Prepara la fecha del informe con la fecha de la inspección
        $fecha_inf = Carbon::parse($inspeccion->fecha_insp)->format('Y-m-d');

        // El paso actual es 1 (Datos Generales)
        $pasoActual = 1;

        // 3. Pasar los datos a la vista
        return view('informes-tecnicos.create', compact('inspeccion', 'fecha_inf', 'pasoActual'));
    }

    public function storeStep1(Request $request)
    {
        // 1. Validación de los datos del Paso 1
        $validatedData = $request->validate([
            'id_insp' => [
                'required',
                'integer',
                // Asegurar que la inspección exista
                Rule::exists('inspecciones', 'id_insp'),
                // Asegurar que NO haya ya un informe asociado (Creación inicial)
                Rule::unique('informes', 'id_insp')
            ],
            // Datos del Informe (fecha_inf y comunidad)
            'fecha_inf' => 'required|date',
            'comunidad' => 'required|string|max:50',

            // Datos del Propietario (Responsable) - Se actualizarán si cambian
            'propietario_cedula' => 'required|string|max:8|regex:/^[0-9]+$/',
            'propietario_nombre' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_apellido' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_telefono' => 'required|string|max:11|regex:/^[0-9]+$/',

            // Datos de la Vivienda (Dirección) - Se actualizarán si cambian
            'direccion' => 'required|string|max:100',
        ]);

        $informe = null;

        try {
            // Usamos una transacción para asegurar que todas las operaciones se completen
            $informe = DB::transaction(function () use ($validatedData) {

                // 2. Obtener la Inspección y sus relaciones para obtener IDs
                $inspeccion = Inspeccion::with('vivienda.propietario')->findOrFail($validatedData['id_insp']);
                $vivienda = $inspeccion->vivienda;
                $propietario = $vivienda->propietario;

                // 3. Actualizar datos del Propietario (Responsable)
                $propietario->update([
                    'cedula_propie' => $validatedData['propietario_cedula'],
                    'nombre_propie' => $validatedData['propietario_nombre'],
                    'apellido_propie' => $validatedData['propietario_apellido'],
                    'telefono' => $validatedData['propietario_telefono'],
                ]);

                // 4. Actualizar datos de la Vivienda (Dirección)
                $vivienda->update([
                    'direccion' => $validatedData['direccion'],
                ]);

                // 5. Crear el registro inicial del informe
                return Informe::create([
                    'id_insp' => $validatedData['id_insp'],
                    'fecha_inf' => $validatedData['fecha_inf'],
                    'comunidad' => $validatedData['comunidad'],
                    // Los demás campos (antecedentes, planteamiento, etc.) se quedan en NULL
                ]);
            });

            // 6. Redirigir al siguiente paso
            if ($informe) {

                $redirectUrl = route('informes.edit.step2', ['id_inf' => $informe->id_inf]);

                return redirect($redirectUrl)
                    ->with('success', 'Paso 1: Datos generales guardados. Continúe con el paso 2.');
            }

        } catch (\Exception $e) {

            return back()->withInput()->with('error', 'Error de Transacción. Detalles: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición para el Paso 1 (Datos Generales)
     */
    public function editStep1($id_inf)
    {
        // 1. Buscar el informe existente y sus relaciones
        $informe = Informe::with('inspeccion.vivienda.propietario')->findOrFail($id_inf);

        // AUTORIZACIÓN: Se usa la política 'update'.
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia


        // 2. Cargamos el objeto de Inspección
        $inspeccion = $informe->inspeccion;

        // 3. Reutilizamos la vista create.blade.php, pasándole el informe existente
        return view('informes-tecnicos.create', compact('informe', 'inspeccion'));
    }

    /**
     * Actualiza los datos del Paso 1 (Datos Generales) para un informe existente.
     */
    public function updateStep1(Request $request, $id_inf)
    {
        // 1. Validación de datos
        $validatedData = $request->validate([
            'id_insp' => [
                'required',
                'integer',
                Rule::exists('inspecciones', 'id_insp') // Solo verificar que existe
            ],
            // Datos del Informe
            'fecha_inf' => 'required|date',
            'comunidad' => 'required|string|max:100',

            // Datos del Propietario (Responsable) - Se actualizarán
            'propietario_cedula' => 'required|string|max:8|regex:/^[0-9]+$/',
            'propietario_nombre' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_apellido' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_telefono' => 'required|string|max:11|regex:/^[0-9]+$/',

            // Datos de la Vivienda (Dirección) - Se actualizarán
            'direccion' => 'required|string|max:100',
        ]);

        try {
            // 2. Usar una transacción para actualizar múltiples tablas
            DB::transaction(function () use ($id_inf, $validatedData) {

                // A. Buscar el informe existente y sus relaciones
                $informe = Informe::with('inspeccion.vivienda.propietario')->findOrFail($id_inf);
                // AUTORIZACIÓN: Se usa la política 'update'.
                $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia
                $vivienda = $informe->inspeccion->vivienda;
                $propietario = $vivienda->propietario;

                // B. Actualizar datos del Propietario
                $propietario->update([
                    'cedula_propie' => $validatedData['propietario_cedula'],
                    'nombre_propie' => $validatedData['propietario_nombre'],
                    'apellido_propie' => $validatedData['propietario_apellido'],
                    'telefono' => $validatedData['propietario_telefono'],
                ]);

                // C. Actualizar datos de la Vivienda
                $vivienda->update([
                    'direccion' => $validatedData['direccion'],
                ]);

                // D. Actualizar el registro del Informe
                $informe->update([
                    'fecha_inf' => $validatedData['fecha_inf'],
                    'comunidad' => $validatedData['comunidad'],
                    // El id_insp ya no se actualiza, solo se modifica la fecha y comunidad
                ]);
            });

            // 3. Redirigir al siguiente paso (Paso 2)
            return redirect()->route('informes.edit.step2', $id_inf)
                ->with('success', 'Paso 1: Datos generales actualizados. Continúe con el paso 2.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar los datos generales: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para el Paso 2 de informe técnico (Diagnóstico y observaciones)
     */
    public function editStep2($id_inf)
    {
        // 1. Buscar el informe existente y cargar las relaciones necesarias:
        $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);

        // AUTORIZACIÓN: Se usa la política 'update'.
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        // La vista accede a $informe->inspeccion->vivienda->caracteristicas
        return view('informes-tecnicos.edit-step2', compact('informe'));
    }

    /**
     * Guardar y actualizar los datos del Paso 2 (columna 'antecedentes').
     */
    public function updateStep2(Request $request, $id_inf)
    {
        // 1. Validación de los cuatro campos
        $validatedData = $request->validate([
            'antecedentes' => 'required|string',
            'planteamiento' => 'required|string',
            'resultados' => 'required|string',
            'caracteristicas' => 'required|string',
        ]);

        try {
            // 2. Buscar el informe existente y sus relaciones
            $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);
            // AUTORIZACIÓN: Se usa la política 'update'.
            $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia
            $vivienda = $informe->inspeccion->vivienda;

            DB::transaction(function () use ($informe, $vivienda, $validatedData) {

                // A. Actualizar campos de la tabla informes
                $informe->update([
                    'antecedentes' => $validatedData['antecedentes'],
                    'planteamiento' => $validatedData['planteamiento'],
                    'resultados' => $validatedData['resultados'],
                ]);

                // B. Actualizar el campo 'caracteristicas' en la tabla viviendas
                $vivienda->update([
                    'caracteristicas' => $validatedData['caracteristicas'],
                ]);

            });

            // 3. Redirigir al siguiente paso
            return redirect()->route('informes.edit.step3', ['id_inf' => $informe->id_inf])
                ->with('success', 'Paso 2: Diagnóstico y observaciones guardados correctamente. Continúe con el paso 3.');

        } catch (\Exception $e) {
            // En caso de error (ej. Mass Assignment, fallo de DB, etc.)
            return back()->withInput()->with('error', 'Error al guardar los datos del Paso 2: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para el Paso 3 (Recomendaciones y mapa)
     */
    public function editStep3($id_inf)
    {
        // 1. Buscar el informe existente
        $informe = Informe::findOrFail($id_inf);

        // AUTORIZACIÓN: Se usa la política 'update'.
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        // 2. Cargar la vista
        return view('informes-tecnicos.edit-step3', compact('informe'));
    }

    /**
     * Guarda y actualiza los datos del Paso 3
     */
    public function updateStep3(Request $request, $id_inf)
    {
        // 1. Validación de los campos, incluyendo la imagen
        $validatedData = $request->validate([
            'recomendaciones' => 'required|string',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            // Validamos la subida del archivo: debe ser una imagen, máx 2MB, opcional si ya existe una (en una edición)
            'map_screenshot' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $fileName = null;

            // 2. Manejo de la subida del archivo (Screenshot del Mapa)
            if ($request->hasFile('map_screenshot')) {
                $file = $request->file('map_screenshot');
                // Generar un nombre único: id_informe + timestamp + extensión
                $fileName = $id_inf . '-' . time() . '.' . $file->getClientOriginalExtension();
                // Guardar el archivo en el storage (ej. storage/app/public/map_screenshots)
                $path = $file->storeAs('public/map_screenshots', $fileName);
                $fileName = basename($path); // Solo guardamos el nombre del archivo
            }

            // 3. Transacción de guardado
            DB::transaction(function () use ($id_inf, $validatedData, $fileName) {

                $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);
                // AUTORIZACIÓN: Se usa la política 'update'.
                $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia
                $vivienda = $informe->inspeccion->vivienda;

                // A. Actualizar campos de la tabla informes
                $informe->update([
                    'recomendacion' => $validatedData['recomendaciones'],
                ]);

                // B. Actualizar campos de la tabla viviendas
                $updateViviendaData = [
                    'latitud' => $validatedData['latitud'],
                    'longitud' => $validatedData['longitud'],
                ];

                // Si se subió un nuevo archivo, actualizamos el campo map_image_file
                if ($fileName) {
                    // Opcional: Eliminar el archivo viejo si existe uno
                    if ($vivienda->map_image_file) {
                        Storage::delete('public/map_screenshots/' . $vivienda->map_image_file);
                    }
                    $updateViviendaData['map_image_file'] = $fileName;
                }

                $vivienda->update($updateViviendaData);
            });

            // 4. Redirigir al siguiente paso (Paso 4)
            return redirect()->route('informes.edit.step4', $id_inf)
                ->with('success', 'Paso 3: Recomendaciones y ubicación guardados. Continúe con el paso 4.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar el Paso 3: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición para el Paso 4 (Materiales)
     */
    public function editStep4($id_inf)
    {
        // Cargamos la relación 'calculos' para saber qué códigos están asociados.
        $informe = Informe::with('calculos')->findOrFail($id_inf);

        // AUTORIZACIÓN: Se usa la política 'update'.
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        // Obtener todos los cálculos disponibles para el selector
        $calculos = Calculos::select('id_calculo', 'codigo_calculo', 'contenido')->get();

        // La vista se llama edit-step4
        return view('informes-tecnicos.edit-step4', compact('informe', 'calculos'));
    }

    /**
     * Almacena los datos del paso 4 (Relación Muchos a Muchos y Texto editable).
     */
    public function updateStep4(Request $request)
    {
        $request->validate([
            'id_inf' => 'required|exists:informes,id_inf',
            'calculos_codes' => 'nullable|array',
            'calculos_codes.*' => 'exists:calculos,id_calculo', // Validar que los IDs existan
            'materials_info' => 'nullable|string',
        ]);

        try {
            $informe = Informe::findOrFail($request->id_inf);
            // AUTORIZACIÓN: Se usa la política 'update'.
            $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

            // 1. Manejar la relación Muchos a Muchos: Sincronizar los IDs
            // Laravel usa 'calculos_informes' gracias a la definición en el modelo.
            $informe->calculos()->sync($request->calculos_codes ?? []);

            // 2. Guardar el texto editable de materiales
            $informe->materials_info = $request->materials_info;

            $informe->save();

            // Redirigir al siguiente paso (Paso 5)
            return redirect()->route('informes.edit.step5', $informe->id_inf)
                ->with('success', 'Paso 4: Materiales guardados. Continúe con el paso 5.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar el Paso 4: ' . $e->getMessage());
        }

    }


    /**
     * Muestra el formulario del paso 5 (Evidencia Fotográfica).
     */
    public function editStep5($id_inf)
    {
        // Cargamos la relación 'imagenes' (Asumiendo que así se llama la relación en el modelo Informe)
        $informe = Informe::with('imagenes')->findOrFail($id_inf);
        // AUTORIZACIÓN: Se usa la política 'update'.
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        return view('informes-tecnicos.edit-step5', compact('informe'));
    }

    /**
     * Procesa y almacena los datos del paso 5 (Subida de múltiples archivos).
     * Nomenclatura: updateStep5
     */
    public function updateStep5(Request $request)
    {
        $request->validate([
            'id_inf' => 'required|exists:informes,id_inf',
            // Validación para múltiples archivos de imagen
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120', // Máx 5MB por archivo
        ]);

        $informe = Informe::findOrFail($request->id_inf);
        // AUTORIZACIÓN: Se usa la política 'update'.
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        // Si hay archivos para subir
        if ($request->hasFile('photos')) {

            $imagenesData = [];

            foreach ($request->file('photos') as $photo) {
                // 1. Guardar el archivo en el disco
                // La ruta será algo como: 'public/informes/123/'
                $ruta = $photo->store('informes/' . $informe->id_inf, 'public');

                // 2. Preparar los datos para insertar en la BD
                $imagenesData[] = [
                    'id_inf' => $informe->id_inf,
                    'ruta_archivo' => $ruta,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 3. Insertar todos los registros en la tabla evidencia_fotografica
            evidencia_fotografica::insert($imagenesData);
        }

        // FIN DEL INFORME: Redirigir a la página del listado de informes con un mensaje

        return redirect()->route('informes.index')->with('success', 'Informe Técnico finalizado y guardado exitosamente.');

    }

    /**
     * Elimina el informe y sus relaciones.
     * @param int $id_inf
     */
    public function destroy($id_inf)
    {
        // 1. Buscar el informe y cargar la relación 'inspeccion'
        $informe = Informe::with('inspeccion')->findOrFail($id_inf);

        // AUTORIZACIÓN: Se usa la política 'delete'.
        // La Policy denegará el acceso a los usuarios con id_rol === 2.
        $this->authorize('delete', $informe);
        
        // 2. Eliminar el informe
        // ... Lógica para eliminar ...
        
        // 3. Redirigir
        return redirect()->route('informes.index')->with('success', 'El Informe Técnico ha sido eliminado correctamente.');
    }


    /**
     * Genera y descarga el PDF de un informe técnico específico.
     * @param int $id_inf El ID del informe.
     */
    public function downloadPdf($id_inf)
    {
        // 1. Obtener el informe con todas las relaciones necesarias
        $informe = Informe::with([
            'inspeccion.vivienda.propietario',
            'inspeccion.usuario',
            'imagenes', // Para la Memoria Fotográfica
            'inspeccion',
        ])->findOrFail($id_inf);

        // AUTORIZACIÓN: Usamos el método 'download' de la Policy.
        $this->authorize('download', $informe);
        

        // 2. Cargar la vista Blade que contiene la estructura del PDF
        $pdf = Pdf::loadView('informes-tecnicos.pdf.informe_tecnico', compact('informe'));

        // 3. Configurar y retornar el PDF para descarga
        return $pdf->setPaper('a4', 'portrait')->stream('Informe-Tecnico-' . $informe->id_inf . '.pdf');
    }
}
