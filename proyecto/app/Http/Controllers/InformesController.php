<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\Informe;
use App\Models\Inspeccion;
use App\Models\Propietario;
use App\Models\Vivienda;
use App\Models\calculos;
use App\Models\evidencia_fotografica;
use App\Models\Notificacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class InformesController extends BaseController
{
    use AuthorizesRequests;
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el listado de informes técnicos.
     */
    public function index()
    {
        // 1. Autorización: Verifica si el usuario puede acceder a la lista.
        $this->authorize('viewAny', Informe::class);

        // 2. Obtener los informes
        $query = Informe::with([
            'inspeccion.vivienda.propietario',
            'inspeccion.usuario'
        ]);

        // 3. Filtrado de informes para usuarios inspectores (id_rol === 2)
        $user = Auth::user();
        if ($user->id_rol === 2) {
            // Si es usuario inspector, filtra los informes por su id_user
            $query->whereHas('inspeccion', function ($q) use ($user) {
                $q->where('id_user', $user->id_user);
            });
        }

        // Ejecutar consulta y aplicar paginación
        $informes = $query
            ->orderBy('fecha_inf', 'desc')
            ->paginate(10);

        // 4. Pasar los datos a la vista
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

        // 2. Obtener el usuario autenticado
        $user = Auth::user();

        // 3. Inicializar la consulta base
        $inspecciones = Inspeccion::where('estado_insp', 1)
            ->whereNotIn('id_insp', $inspeccionesConInforme)
            ->where('id_user', $user->id_user)
            ->with('vivienda.propietario')
            ->get();

        return $inspecciones;
    }

    /**
     * Muestra el formulario de creación del informe técnico (Paso 1).
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

    /**
     * Almacena los datos del Paso 1 (Datos Generales) de un nuevo informe técnico.
     */
    public function storeStep1(Request $request)
    {
        // 1. Validación inicial para obtener la fecha de la inspección
        $request->validate([
            'id_insp' => [
                'required',
                'integer',
                Rule::exists('inspecciones', 'id_insp'),
                Rule::unique('informes', 'id_insp')
            ],
        ]);

        // Obtener la inspección para acceder a su fecha
        $inspeccion = Inspeccion::findOrFail($request->id_insp);
        $fechaInspeccion = $inspeccion->fecha_insp;

        // 2. Validación de datos
        $validatedData = $request->validate([

            // Datos del Informe
            'fecha_inf' => 'required|date|after_or_equal:' . $fechaInspeccion,
            'comunidad' => 'required|string|max:50',

            // Datos del Propietario
            'propietario_cedula' => [
                'required',
                'string',
                'min:7',
                'max:8',
                'regex:/^(?!0+$)(?!1{6,8}$)(?!2{6,8}$)(?!3{6,8}$)(?!4{6,8}$)(?!5{6,8}$)(?!6{6,8}$)(?!7{6,8}$)(?!8{6,8}$)(?!9{6,8}$)(?!123456$)(?!1234567$)(?!12345678$)(?!87654321$)(?!7654321$)(?!654321$)(?!(\d)\1+$)(\d{6,8})$/'
            ],
            'propietario_nombre' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_apellido' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_telefono' => 'required|string|max:11|regex:/^[0-9]+$/',

            // Datos de la Vivienda
            'direccion' => 'required|string|max:100',
        ], [
            // Mensaje personalizado para la regla after_or_equal
            'fecha_inf.after_or_equal' => 'La fecha del informe debe ser igual o posterior a la fecha de la inspección (' . $fechaInspeccion . ').',
            // Mensaje personalizado para la Regex
            'propietario_cedula.regex' => 'La cédula ingresada no cumple con el formato válido. Por favor, ingrese un número de cédula real.',
        ]);

        $validatedData['id_insp'] = $request->id_insp;


        // 3. Inicializar la variable informe
        $informe = null;

        // 4. Usar una transacción para crear el informe y actualizar las tablas relacionadas
        try {
            // Usamos una transacción para asegurar que todas las operaciones se completen
            $informe = DB::transaction(function () use ($validatedData) {

                // 1. Obtener la inspección y sus relaciones para obtener IDs
                $inspeccion = Inspeccion::with('vivienda.propietario')->findOrFail($validatedData['id_insp']);
                $vivienda = $inspeccion->vivienda;

                // 2. Buscar o crear/actualizar el propietario
                $propietario = Propietario::updateOrCreate(
                    ['cedula_propie' => $validatedData['propietario_cedula']],
                    [
                        'nombre_propie' => $validatedData['propietario_nombre'],
                        'apellido_propie' => $validatedData['propietario_apellido'],
                        'telefono' => $validatedData['propietario_telefono'],
                    ]
                );

                // 3. Actualizar la vivienda existente
                if ($vivienda) {
                    // Si la vivienda ya existe, la actualizamos
                    $vivienda->update([
                        'direccion' => $validatedData['direccion'],
                        'id_propie' => $propietario->id_propie, // Asegurar que apunte al propietario actualizado
                    ]);
                } else {
                    // Creación de vivienda si no existe
                    $vivienda = Vivienda::create([
                        'direccion' => $validatedData['direccion'],
                        'id_propie' => $propietario->id_propie,
                    ]);
                }

                $inspeccion->update(['id_viv' => $vivienda->id_viv]);

                // 4. Crear el registro inicial del informe
                return Informe::create([
                    'id_insp' => $validatedData['id_insp'],
                    'fecha_inf' => $validatedData['fecha_inf'],
                    'comunidad' => $validatedData['comunidad'],
                ]);
            });

            // 5. Redirigir al siguiente paso
            if ($informe) {
                $redirectUrl = route('informes.edit.step2', ['id_inf' => $informe->id_inf]);

                return redirect($redirectUrl)
                    ->with('success', 'Paso 1: Datos generales guardados. Continúe con el paso 2.');
            }

            // Si la transacción no retornó un informe
            return back()->withInput()->with('error', 'Error inesperado al crear el informe.');

        } catch (\Exception $e) {
            // Puedes agregar Log::error($e) aquí para un mejor debugging
            return back()->withInput()->with('error', 'Error de Transacción. No se pudo guardar el informe. Detalles: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición para el Paso 1 (Datos Generales)
     */
    public function editStep1($id_inf)
    {
        // 1. Buscar el informe existente y sus relaciones
        $informe = Informe::with('inspeccion.vivienda.propietario')->findOrFail($id_inf);

        // Autorización: Verifica si el usuario puede editar los datos
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia


        // 2. Cargamos el objeto de inspección
        $inspeccion = $informe->inspeccion;

        // 3. Pasamos los datos a la vista
        return view('informes-tecnicos.create', compact('informe', 'inspeccion'));
    }

    /**
     * Actualiza los datos del Paso 1 (Datos Generales) para un informe existente.
     */
    public function updateStep1(Request $request, $id_inf)
    {
        // A. Buscar el informe, inspección, vivienda y propietario
        $informe = Informe::with('inspeccion.vivienda.propietario')->findOrFail($id_inf);

        // Autorización: Verifica si el usuario puede actualizar los datos
        $this->authorize('update', $informe);

        // B. Obtener la fecha de la inspección asociada para la validación
        $fechaInspeccion = $informe->inspeccion->fecha_insp;

        // 1. Validación de datos
        $validatedData = $request->validate([
            'id_insp' => [
                'required',
                'integer',
                Rule::exists('inspecciones', 'id_insp') // Solo verificar que existe
            ],
            // Datos del Informe
            'fecha_inf' => 'required|date|after_or_equal:' . $fechaInspeccion,
            'comunidad' => 'required|string|max:100',

            // Datos del Propietario - Se actualizarán
            'propietario_cedula' => [
                'required',
                'string',
                'min:7',
                'max:8',
                'regex:/^(?!0+$)(?!1{6,8}$)(?!2{6,8}$)(?!3{6,8}$)(?!4{6,8}$)(?!5{6,8}$)(?!6{6,8}$)(?!7{6,8}$)(?!8{6,8}$)(?!9{6,8}$)(?!123456$)(?!1234567$)(?!12345678$)(?!87654321$)(?!7654321$)(?!654321$)(?!(\d)\1+$)(\d{6,8})$/'
            ],
            'propietario_nombre' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_apellido' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_telefono' => 'required|string|max:11|regex:/^[0-9]+$/',

            // Datos de la Vivienda - Se actualizarán
            'direccion' => 'required|string|max:100',
        ], [
            // Mensaje personalizado para la fecha
            'fecha_inf.after_or_equal' => 'La fecha del informe debe ser igual o posterior a la fecha de la inspección (' . $fechaInspeccion . ').',
            'propietario_cedula.regex' => 'La cédula ingresada no cumple con el formato válido. Por favor, ingrese un número de cédula real.',
        ]);

        try {
            // 2. Usar una transacción para actualizar múltiples tablas
            DB::transaction(function () use ($informe, $validatedData) {

                // C. Obtener referencias de relaciones
                $inspeccion = $informe->inspeccion;
                $vivienda = $inspeccion->vivienda;

                // D. Actualizar o crear el Propietario
                $propietario = Propietario::updateOrCreate(
                    ['cedula_propie' => $validatedData['propietario_cedula']],
                    [
                        'nombre_propie' => $validatedData['propietario_nombre'],
                        'apellido_propie' => $validatedData['propietario_apellido'],
                        'telefono' => $validatedData['propietario_telefono'],
                    ]
                );

                // E. Actualizar la vivienda existente
                if ($vivienda) {
                    $vivienda->update([
                        'direccion' => $validatedData['direccion'],
                        'id_propie' => $propietario->id_propie, // Aseguramos que apunte al propietario actualizado
                    ]);
                } else {
                    // Fallback: Si no hay vivienda asociada, la creamos
                    $vivienda = Vivienda::create([
                        'direccion' => $validatedData['direccion'],
                        'id_propie' => $propietario->id_propie,
                    ]);
                }

                // F. Asegurar que la inspección apunte a la vivienda correcta
                $inspeccion->update(['id_viv' => $vivienda->id_viv]);

                // G. Actualizar el registro del Informe
                $informe->update([
                    'fecha_inf' => $validatedData['fecha_inf'],
                    'comunidad' => $validatedData['comunidad'],
                ]);
            });

            // 3. Redirigir al siguiente paso (Paso 2)
            return redirect()->route('informes.edit.step2', $informe->id_inf)
                ->with('success', 'Paso 1: Datos generales actualizados. Continúe con el paso 2.');

            // En caso de error
        } catch (\Exception $e) {
            // Manejo de errores
            return back()->withInput()->with('error', 'Error al actualizar los datos generales. Intente de nuevo. Detalle: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para el Paso 2 del informe técnico (Diagnóstico y observaciones)
     */
    public function editStep2($id_inf)
    {
        // 1. Buscar el informe existente y cargar las relaciones necesarias:
        $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);

        // Autorización: Verifica si el usuario puede acceder
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        // Redirigir a la vista del Paso 2
        return view('informes-tecnicos.edit-step2', compact('informe'));
    }

    /**
     * Guardar y actualizar los datos del Paso 2
     */
    public function updateStep2(Request $request, $id_inf)
    {
        // 1. Validación de los campos
        $validatedData = $request->validate([
            'antecedentes' => 'required|string',
            'planteamiento' => 'required|string',
            'resultados' => 'required|string',
            'caracteristicas' => 'required|string',
        ]);

        try {
            // 2. Buscar el informe existente y sus relaciones
            $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);
            // Autorización: Verifica si el usuario puede actulizar los datos
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

        // En caso de error
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar los datos del Paso 2: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para el Paso 3 del informe técnico (Recomendaciones y mapa)
     */
    public function editStep3($id_inf)
    {
        // 1. Buscar el informe existente
        $informe = Informe::findOrFail($id_inf);

        // Autorización: Verifica si el usuario puede acceder a la vista
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
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            // Validamos la subida del archivo
            'map_screenshot' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $fileName = null;

            // 2. Manejo de la subida del archivo (Imagen del mapa)
            if ($request->hasFile('map_screenshot')) {
                $file = $request->file('map_screenshot');
                // Generar un nombre único para el archivo
                $fileBaseName = 'map_' . $id_inf . '-' . time() . '.' . $file->getClientOriginalExtension();
                // Guardar la imagen y obtener la ruta relativa en el disco 'public'
                $ruta = $file->storeAs('informes/' . $id_inf, $fileBaseName, 'public');
                // Guardamos la ruta para persistirla en la BD
                $fileName = $ruta;
            }

            // 3. Transacción de guardado
            DB::transaction(function () use ($id_inf, $validatedData, $fileName) {

                $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);
                // Autorización: Verifica si el usuario puede actualizar los datos
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

        // En caso de error
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar el Paso 3: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición para el Paso 4 del informe técnico (Materiales)
     */
    public function editStep4($id_inf)
    {
        $informe = Informe::findOrFail($id_inf);

        // Autorización: Verifica si el usuario puede acceder a la vista
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        // Obtener todos los cálculos necesarios.
        $calculos = calculos::select('id_calculo', 'nombre_calculo', 'contenido')->get();

        // Preparar la variable del ID seleccionado
        $selectedId = old('calculos_codes', $informe->id_calculo);

        // Transformar los datos de cálculos a un formato JSON listo para JavaScript
        $calculosJson = $calculos->keyBy('id_calculo')->toJson();

        // 5. Devolver la vista con los datos
        return view('informes-tecnicos.edit-step4', compact('informe', 'calculos', 'selectedId', 'calculosJson'));
    }

    /**
     * Almacena los datos del paso 4
     */
    public function updateStep4(Request $request)
    {
        $request->validate([
            'id_inf' => 'required|exists:informes,id_inf',
            'calculos_codes' => 'nullable|exists:calculos,id_calculo',
            'materials_info' => 'nullable|string',
        ]);

        try {
            $informe = Informe::findOrFail($request->id_inf);
            // Autorización: Verifica si el usuario pude actualizar los datos
            $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

            // 1. Manejar la relación Uno a Muchos
            $informe->id_calculo = $request->calculos_codes;

            // 2. Guardar el texto editable de materiales
            $informe->materials_info = $request->materials_info;

            $informe->save();

            // Redirigir al siguiente paso (Paso 5)
            return redirect()->route('informes.edit.step5', $informe->id_inf)
                ->with('success', 'Paso 4: Materiales guardados. Continúe con el paso 5.');

            // En caso de error
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar el Paso 4: ' . $e->getMessage());
        }

    }

    /**
     * Muestra el formulario del Paso 5 del informe técnico (Evidencia Fotográfica)
     */
    public function editStep5($id_inf)
    {
        // Cargamos la relación 'imagenes' para mostrar las imágenes ya subidas.
        $informe = Informe::with('imagenes')->findOrFail($id_inf);
        // Autorización: Verifica si el usuario puede acceder a la vista
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        return view('informes-tecnicos.edit-step5', compact('informe'));
    }

    /**
     * Procesa y almacena los datos del paso 5 (Subida de múltiples archivos)
     */
    public function updateStep5(Request $request)
    {
        // 1. Encontrar el informe y verificar si ya tiene imágenes
        $informe = Informe::findOrFail($request->id_inf);
        $tieneImagenesExistentes = $informe->imagenes()->exists();

        // 2. Definir las reglas de validación
        $rules = [
            'id_inf' => 'required|exists:informes,id_inf',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ];

        // Lógica condicional: Si el informe NO tiene imágenes existentes Y el usuario no ha subido archivos en este envío,
        // entonces hacemos que el campo 'photos' sea obligatorio.

        if (!$tieneImagenesExistentes && !$request->hasFile('photos')) {
            // En este caso, el usuario intentó finalizar sin subir la primera imagen.
            $rules['photos'] = 'required';
        }

        // 3. Ejecutar la validación
        $request->validate($rules);

        // Autorización: Verifica si el usuario puede actualizar los datos
        $this->authorize('update', $informe); // Pasa el modelo para la verificación de pertenencia

        // Si hay archivos para subir
        if ($request->hasFile('photos')) {

            $imagenesModelos = [];

            foreach ($request->file('photos') as $photo) {
                // 1. Guardar el archivo en el disco
                $ruta = $photo->store('informes/' . $informe->id_inf, 'public');

                // 2. Crear una instancia del modelo 
                $imagenesModelos[] = new evidencia_fotografica([
                    'ruta_archivo' => $ruta,
                ]);
            }

            // 3. Insertar todos los registros usando la relación
            $informe->imagenes()->saveMany($imagenesModelos);

        }

        // Llamada a la notificación
        $informe->load('inspeccion.vivienda.propietario');
        $this->sendAdminNotification('updated', $informe);

        // Fin del informe: Redirigir a la página del listado de informes con un mensaje

        return redirect()->route('informes.index')->with('success', 'Informe Técnico finalizado y guardado exitosamente.');
    }

    /**
     * Elimina una evidencia fotográfica por su ID.
     */
    public function destroyImage($id_evid)
    {
        $imagen = evidencia_fotografica::findOrFail($id_evid);

        // 1. Autorización: Verifica que el usuario pueda modificar el informe al que pertenece esta imagen
        $this->authorize('update', $imagen->informe);

        // 2. Eliminar el archivo físico
        if (Storage::disk('public')->exists($imagen->ruta_archivo)) {
            Storage::disk('public')->delete($imagen->ruta_archivo);
        }

        // 3. Eliminar el registro de la base de datos
        $imagen->delete();

        // 4. Redirigir de vuelta al formulario del paso 5
        return redirect()->back()->with('success', 'La imagen ha sido eliminada exitosamente.');
    }

    /**
     * Elimina el informe (Soft Delete)
     */

    public function destroy($id_inf)
    {
        try {
            $informe = Informe::with('inspeccion.vivienda.propietario')->findOrFail($id_inf);

            // AUTORIZACIÓN: Solo el administrador puede eliminar.
            $this->authorize('delete', $informe);

            // LLamada a la notificación
            $this->sendAdminNotification('deleted', $informe);

            $informe->delete();

            return redirect()->route('informes.index')->with('success', '¡Informe #' . $id_inf . ' eliminado correctamente!');

        } catch (\Exception $e) {
            \Log::error("Error al eliminar informe: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al eliminar el informe. Intente nuevamente.');
        }
    }


    /**
     * Genera y descarga el PDF de un informe técnico específico
     */
    public function downloadPdf($id_inf)
    {
        // 1. Obtener el informe con todas las relaciones necesarias
        $informe = Informe::with([
            'inspeccion.vivienda.propietario',
            'inspeccion.usuario',
            'imagenes',
            'inspeccion',
        ])->findOrFail($id_inf);

        // 2. Extraer el nombre y apellido del propietario

        $propietario = $informe->inspeccion->vivienda->propietario;

        // Construimos el nombre del archivo
        if ($propietario) {
            $nombre_completo = Str::slug($propietario->nombre_propie . ' ' . $propietario->apellido_propie, '-');
            $nombre_archivo = 'Informe-de-Inspeccion-' . $nombre_completo . '.pdf';
        } else {
            // Fallback en caso de que no se encuentre el propietario
            $nombre_archivo = 'Informe-de-Inspeccion-' . $informe->id_inf . '.pdf';
        }

        // 3. Cargar la vista que contiene la estructura del PDF
        $pdf = Pdf::loadView('informes-tecnicos.pdf.informe_tecnico', compact('informe'));

        // 4. Configurar y retornar el PDF para la descarga con el nuevo nombre
        return $pdf->setPaper('a4', 'portrait')->stream($nombre_archivo);
    }

    /**
     * Crea y envía una notificación a todos los administradores (id_rol = 1) del módulo de Informes.
     */
    private function sendAdminNotification(string $action, Informe $informe): void
    {
        try {
            // 1. Obtener datos del usuario que realizó la acción
            $user = Auth::user();
            if (!$user) {
                \Log::warning("Notificación de informe fallida: Usuario no autenticado.");
                return;
            }
            $userName = $user->nombre . ' ' . $user->apellido;

            // 2. Obtener datos del propietario
            $propietarioName = 'Propietario Desconocido';
            $propietario = optional(optional($informe->inspeccion)->vivienda)->propietario;

            if ($propietario) {
                $propietarioName = $propietario->nombre_propie . ' ' . $propietario->apellido_propie;
            }

            // 3. Determinar el mensaje y tipo
            $message = '';
            $type = '';

            if ($action === 'deleted') {
                $message = "El Informe #{$informe->id_inf} (Propietario: {$propietarioName}) ha sido ELIMINADO por {$userName}.";
                $type = 'informe_eliminado';
            } elseif ($action === 'updated') {
                $message = "El Informe #{$informe->id_inf} (Propietario: {$propietarioName}) ha sido ACTUALIZADO/FINALIZADO por {$userName}.";
                $type = 'informe_actualizado';
            } else {
                return;
            }

            // 4. Buscar todos los administradores (id_rol == 1)
            $administrators = Usuario::where('id_rol', 1)->get();

            // 5. Crear una notificación para cada administrador
            foreach ($administrators as $admin) {
                Notificacion::create([
                    'id_user' => $admin->id_user,
                    'mensaje' => $message,
                    'tipo' => $type,
                    'leida' => false,
                ]);
            }

        } catch (\Exception $e) {
            \Log::error("Error FATAL al crear notificación de informe #{$informe->id_inf}: " . $e->getMessage() . " en línea " . $e->getLine());
        }
    }
}