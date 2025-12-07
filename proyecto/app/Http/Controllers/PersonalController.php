<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Inspeccion;
use App\Models\Vivienda;
use App\Models\Propietario;
use App\Models\roles;
use App\Models\asignacion_recursos;
use App\Models\Recursos;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PersonalController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra la lista de personal técnico (usuarios) y las profesiones únicas para filtros
     */
    public function index(Request $request)
    {
        // AUTORIZACIÓN: Verifica si puede ver la lista general
        $this->authorize('viewAny', Usuario::class);

        // 1. Inicializar la consulta
        $query = Usuario::with('rol');

        // 2. Filtrar por Palabra Clave
        // Buscar por Cédula, Nombre, Apellido, Correo, Profesión.
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {

                $q->where('cedula_user', 'like', '%' . $keyword . '%')
                    ->orWhere('nombre', 'like', '%' . $keyword . '%')
                    ->orWhere('apellido', 'like', '%' . $keyword . '%')
                    ->orWhere('correo', 'like', '%' . $keyword . '%')
                    ->orWhere('profesion', 'like', '%' . $keyword . '%');
                $q->orWhereHas('rol', function ($q_rol) use ($keyword) {
                    $q_rol->where('nombre_rol', 'like', '%' . $keyword . '%');
                });
            });
        }

        // 3. Filtrar por estado

        if ($request->filled('estado')) {
            $query->where('estado_user', (int) $request->estado);
        }

        // 4. Ejecutar la consulta y ordenar
        $personal = $query->orderBy('nombre', 'asc')->get();

        // 5. Obtener las profesiones únicas para el filtro PDF
        $profesionesUnicas = Usuario::select('profesion')
            ->whereNotNull('profesion')
            ->distinct()
            ->orderBy('profesion', 'asc')
            ->pluck('profesion'); // Obtiene solo los valores del campo 'profesion'

        // 6. Pasar los resultados a la vista
        return view('personal.index', compact('personal', 'profesionesUnicas'));
    }

    /**
     * Muestra el formulario de edición para un usuario específico
     */
    public function edit(Usuario $personal)
    {
        // AUTORIZACIÓN: Verifica si puede manipular al usuario
        $this->authorize('update', $personal);

        // Cargar todos los roles disponibles para el selector
        $roles = roles::all();

        // Muestra la vista de edición, pasando el usuario y los roles
        return view('personal.edit', compact('personal', 'roles'));
    }

    /**
     * Actualiza la información de un usuario específico
     */
    public function update(Request $request, Usuario $personal)
    {
        // AUTORIZACIÓN: Verifica si puede manipular al usuario
        $this->authorize('update', $personal);

        // 1. Validar los datos de entrada
        $request->validate([
            // La cédula debe ser única, excepto para el usuario actual
            'cedula_user' => [
                'required',
                'string',
                'min:7',
                'max:8',
                Rule::unique('usuarios')->ignore($personal->id_user, 'id_user'),
                'regex:/^(?!0+$)(?!1{6,8}$)(?!2{6,8}$)(?!3{6,8}$)(?!4{6,8}$)(?!5{6,8}$)(?!6{6,8}$)(?!7{6,8}$)(?!8{6,8}$)(?!9{6,8}$)(?!123456$)(?!1234567$)(?!12345678$)(?!87654321$)(?!7654321$)(?!654321$)(?!(\d)\1+$)(\d{6,8})$/'
            ],
            'nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            // El correo debe ser único, excepto para el usuario actual
            'correo' => [
                'required',
                'string',
                'email',
                'max:40',
                Rule::unique('usuarios')->ignore($personal->id_user, 'id_user'),
                'regex:/@gmail\.com$/i'
            ],
            'profesion' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'id_rol' => ['required', 'exists:roles,id_rol'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:15',
                'confirmed',
                'regex:/^.*(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!$#%@,.]).*$/'
            ],
        ], [
            // Mensaje personalizado para la Regex
            'cedula_user.regex' => 'La cédula ingresada no cumple con el formato válido. Por favor, ingrese un número de cédula real.',
            'correo.regex' => 'Solo se permiten direcciones de correo electrónico con el dominio @gmail.com.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un símbolo (! $ # % @ .).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede exceder los 15 caracteres.',
        ]);

        // 2. Preparar los datos para la actualización
        $data = $request->only([
            'cedula_user',
            'nombre',
            'apellido',
            'correo',
            'profesion',
            'id_rol'
        ]);

        // Si se proporciona una nueva contraseña, la hashea y la añade a los datos
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 3. Actualizar el usuario
        $personal->update($data);

        // 4. Redirigir y notificar
        return redirect()->route('personal.index')->with('success', 'Datos de ' . $personal->nombre . ' ' . $personal->apellido . ' actualizados exitosamente.');
    }


    /**
     * Cambia el estado del usuario (activo o inactivo).
     */
    public function toggleStatus(Usuario $personal)
    {
        // AUTORIZACIÓN: Verifica si puede manipular al usuario
        $this->authorize('update', $personal);

        // Determina el nuevo estado y su valor en la base de datos
        if ($personal->estado_user === '1') {
            $personal->estado_user = '0'; // Cambia a inactivo
            $nuevoEstadoTexto = 'inactivo';
        } else {
            $personal->estado_user = '1'; // Cambia a activo
            $nuevoEstadoTexto = 'activo';
        }

        $personal->save();

        return back()->with('success', 'Estado de ' . $personal->nombre . ' ' . $personal->apellido . ' actualizado a ' . $nuevoEstadoTexto . '.');
    }

    /**
     * Muestra la lista de inspecciones disponibles, incluyendo el nombre del propietario
     */
    public function getAvailableInspections(Usuario $personal)
    {

        // AUTORIZACIÓN: Verifica si puede manipular al usuario
        $this->authorize('update', $personal);

        // Carga las inspecciones disponibles junto a sus relaciones anidadas
        $inspecciones = Inspeccion::whereNull('id_user')
            ->with(['vivienda.propietario'])
            ->get();

        // Mapear la colección para crear el texto que se mostrará en el select
        $inspecciones_disponibles = $inspecciones->map(function ($insp) {

            $nombre_propietario = 'Propietario Desconocido';
            $ci_propietario = 'N/A';

            // Verificamos si las relaciones anidadas existen para evitar errores
            if ($insp->vivienda && $insp->vivienda->propietario) {
                $prop = $insp->vivienda->propietario;
                $nombre_propietario = $prop->nombre_propie . ' ' . $prop->apellido_propie;
                $ci_propietario = $prop->cedula_propie ?? 'N/A';
            }

            return [
                'id_insp' => $insp->id_insp,
                'propietario_display' => "Inspección #{$insp->id_insp} | Propietario: {$nombre_propietario} (CI: {$ci_propietario})",
            ];
        });

        // Retornar datos JSON
        return response()->json([
            'user_id' => $personal->id_user,
            'user_name' => $personal->nombre . ' ' . $personal->apellido,
            'inspecciones' => $inspecciones_disponibles,
        ]);
    }

    /**
     * Procesa la solicitud y asigna una inspección seleccionada al usuario
     */
    public function assignInspection(Request $request, Usuario $personal)
    {
        // AUTORIZACIÓN: Verifica si puede manipular al usuario
        $this->authorize('update', $personal);

        // 1. Validar la entrada
        $request->validate([
            // Valida que el ID de inspección sea requerido y exista
            'id_insp' => [
                'required',
                'exists:inspecciones,id_insp',
            ],
        ]);

        try {
            // 2. Buscar la inspección
            $inspeccion = Inspeccion::find($request->id_insp);

            // Verificación final de disponibilidad antes de guardar
            if (!is_null($inspeccion->id_user)) {
                return back()->with('error', 'Error: La inspección ya no está disponible.');
            }

            // 3. Asignar el ID del usuario al campo id_user de la inspección
            $inspeccion->id_user = $personal->id_user;

            $inspeccion->save();

            // Mensaje de éxito
            return back()->with('success', 'Inspección #' . $inspeccion->id_insp . ' asignada a ' . $personal->nombre . ' ' . $personal->apellido . ' correctamente.');

        } catch (\Exception $e) {
            // En caso de error
            return back()->with('error', 'Hubo un error al asignar la inspección: ' . $e->getMessage());
        }
    }

    /**
     * Devuelve una lista de recursos disponibles (no tienen asignación abierta).
     */
    public function getRecursosDisponibles()
    {
        // AUTORIZACIÓN: Solo el administrador puede ver los recursos disponibles.
        $this->authorize('viewAvailableRecursos', Usuario::class);

        // 1. Obtener los IDs de recursos que tienen una asignación PENDIENTE (fecha_devolucion = null)
        $recursosAsignadosIds = asignacion_recursos::whereNull('fecha_devolucion')
            ->pluck('id_recurso')
            ->toArray();

        // 2. Obtener los recursos que NO están en esa lista
        $recursosDisponibles = Recursos::whereNotIn('id_recurso', $recursosAsignadosIds)
            ->select('id_recurso', 'codigo', 'nombre_rec')
            ->get();

        // 3. Devolver como JSON (para el AJAX)
        return response()->json([
            'recursos' => $recursosDisponibles
        ]);
    }

    /**
     * Procesa la asignación de un recurso a un usuario.
     */
    public function assignRecurso(Request $request, $id_user)
    {
        // 1. Obtener el usuario al que se le va a asignar el recurso
        $personal = Usuario::findOrFail($id_user);

        // AUTORIZACIÓN: Solo el administrador puede asignar un recurso a un usuario.
        $this->authorize('assignRecurso', $personal);

        // 2. Validación
        $request->validate([
            'id_recurso' => 'required|exists:recursos,id_recurso',
        ]);

        $id_recurso = $request->input('id_recurso');

        // 3. Verificar que el recurso no esté ya asignado (Doble check de seguridad)
        $asignacionExistente = asignacion_recursos::where('id_recurso', $id_recurso)
            ->whereNull('fecha_devolucion')
            ->first();

        if ($asignacionExistente) {
            return back()->with('error', 'El recurso seleccionado ya se encuentra asignado a otra persona.');
        }

        try {
            // 4. Crear la nueva asignación
            asignacion_recursos::create([
                'id_user' => $id_user,
                'id_recurso' => $id_recurso,
                'fecha_asignacion' => Carbon::now(),
            ]);

            // 5. Obtener nombre del recurso para el mensaje
            $recurso = Recursos::find($id_recurso);
            $nombreRecurso = $recurso ? $recurso->nombre_rec : 'Recurso Desconocido';
            $usuario = Usuario::find($id_user);
            $nombreUsuario = $usuario ? $usuario->nombre : 'Usuario Desconocido';

            return redirect()->route('personal.index')->with(
                'success',
                "Recurso '{$nombreRecurso}' asignado exitosamente a {$nombreUsuario}."
            );
        } catch (\Exception $e) {
            \Log::error("Error al asignar recurso: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al intentar asignar el recurso. Intente nuevamente.');
        }
    }

    /**
     * Exporta el listado de personal a un archivo PDF descargable.
     */
    public function exportarPersonalPDF()
    {
        // 1. Obtener todos los usuarios del modelo Usuarios
        $usuarios = Usuario::all();

        // 2. Cargar la vista que contiene el PDF
        // Asegúrate que la ruta de la vista sea correcta (e.g., 'usuarios.pdf.reporte-personal-pdf')
        $pdf = PDF::loadView('personal.pdf.listado-personal-pdf', compact('usuarios'));

        // Ajuste para mejorar la paginación en tablas grandes
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        // 3. Devolver el archivo PDF para descargar
        $fecha = Carbon::now()->format('Ymd');
        $nombreArchivo = "Reporte_Personal_{$fecha}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exporta el listado de personal a un archivo PDF, filtrado por profesión.
     */
    public function exportarPersonalPorProfesionPDF(Request $request, $profesion)
    {
        // 1. Obtener los usuarios filtrados por la profesión
        $usuarios = Usuario::where('profesion', $profesion)->get();

        // 2. Variables para la vista
        $tituloReporte = "REPORTE LISTADO DE PERSONAL - PROFESIÓN: " . strtoupper($profesion);

        // 3. Cargar la vista
        $pdf = PDF::loadView('personal.pdf.listado-personal-pdf', compact('usuarios', 'tituloReporte'));

        // 4. Ajustes y descarga
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        $fecha = Carbon::now()->format('Ymd');
        $nombreArchivo = "Reporte_Personal_{$profesion}_{$fecha}.pdf";

        return $pdf->download($nombreArchivo);
    }

}