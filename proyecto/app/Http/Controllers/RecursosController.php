<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recursos;
use App\Models\Usuario;
use App\Models\asignacion_recursos;
use App\Models\Notificacion;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecursosController extends Controller
{
    use AuthorizesRequests;

     /**
     * Muestra la lista de recursos.
     */
    public function index(Request $request)
    {

        // Autorización: Solo el Administrador tiene acceso al index.
        $this->authorize('viewAny', Recursos::class);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // 1. Inicializar la consulta con las relaciones necesarias
        $query = Recursos::with('asignaciones.usuario');

        // 2. Filtrar por palabra clave
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('codigo', 'like', '%' . $keyword . '%')
                    ->orWhere('nombre_rec', 'like', '%' . $keyword . '%');
            });
        }

        // 3. Filtrar por estado (ASIGNADO / NO ASIGNADO)
        if ($request->filled('estado')) {
            $estado = $request->estado; // '1' para ASIGNADO, '0' para NO ASIGNADO

            if ($estado === '1') {
                $query->whereHas('asignaciones', function ($q) {
                    $q->whereNull('fecha_devolucion');
                });

            } elseif ($estado === '0') {
                $query->whereDoesntHave('asignaciones', function ($q) {
                    $q->whereNull('fecha_devolucion');
                });
            }
        }

        // 4. Ejecutar la consulta, ordenar y aplicar paginación
        $recursos = $query->orderBy('nombre_rec', 'asc')->paginate(10);

        // 5. Pasar los resultados a la vista
        return view('recursos.index', compact('recursos'));
    }

    /**
     * Muestra el historial completo de asignaciones.
     */
    public function assignmentsHistory(Request $request)
    {
        // Autorización: Ambos roles tienen acceso a este método
        $this->authorize('viewHistory', Recursos::class);

        // Validación de filtros
        $request->validate([
            'fecha_asignacion_start' => 'nullable|date',
            'fecha_devolucion_end' => 'nullable|date|after_or_equal:fecha_asignacion_start',
        ], [
            // Mensaje de error
            'fecha_devolucion_end.after_or_equal' => 'La Fecha de Devolución no puede ser anterior a la Fecha de Asignación.',
        ]);

        $user = Auth::user();

        // 1. Inicializar la consulta con las relaciones
        $query = asignacion_recursos::with(['recurso', 'usuario'])->orderBy('fecha_asignacion', 'desc');

        // Lógica de filtro por rol:
        // Si el usuario es de Rol ID 2, restringir a sus propias asignaciones
        if ($user->id_rol === 2) {
            $query->where('id_user', $user->id_user);
        }

        // 2. Filtrar por palabra clave (Nombre del recurso, nombre o apellido del usuario)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                // Filtro por nombre de recurso
                $q->whereHas('recurso', function ($qRec) use ($keyword) {
                    $qRec->where('nombre_rec', 'like', '%' . $keyword . '%');
                })
                    // Filtro por nombre/apellido del usuario
                    ->orWhereHas('usuario', function ($qUser) use ($keyword) {
                        $qUser->where('nombre', 'like', '%' . $keyword . '%')
                            ->orWhere('apellido', 'like', '%' . $keyword . '%');
                    });
            });
        }

        // 3. Filtrar por fecha de asignación
        if ($request->filled('fecha_asignacion_start')) {
            $query->whereDate('fecha_asignacion', '>=', $request->fecha_asignacion_start);
        }

        // 4. Filtrar por fecha de devolución
        if ($request->filled('fecha_devolucion_end')) {
            $query->whereDate('fecha_devolucion', '<=', $request->fecha_devolucion_end);
        }

        // 5. Filtrar por estado (Devuelto o Pendiente)
        if ($request->filled('estado')) {
            if ($request->estado === '1') {
                $query->whereNull('fecha_devolucion'); // Pendiente de devolución
            } elseif ($request->estado === '0') {
                $query->whereNotNull('fecha_devolucion'); // Ya devuelto
            }
        }

        // 6. Ejecutar la consulta y obtener resultados
        $asignaciones = $query->paginate(10);

        // 7. Pasar los resultados a la vista
        return view('recursos.historial-asignaciones', compact('asignaciones'));
    }

    // Muestra la vista de registro de recursos
    public function create()
    {
        // Autorización: Solo el administrador puede registrar
        $this->authorize('manage', Recursos::class);
        return view('recursos.formulario-recurso');
    }

    /**
     * Almacena un recurso en la base de datos.
     */
    public function store(Request $request)
    {
        // Autorización: Solo el administrador puede almacenar
        $this->authorize('manage', Recursos::class);

        // 1. Validar los datos
        $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('recursos', 'codigo')],
            'nombre_rec' => ['required', 'string', 'max:30', Rule::unique('recursos', 'nombre_rec')],
            'descripcion' => ['required', 'string', 'max:255'],
            'observacion' => ['nullable', 'string'],
        ], [
            // Mensajes de error personalizados
            'codigo.required' => 'El codigo del recurso es obligatorio.',
            'codigo.unique' => 'Ya existe un recurso con este codigo.',
            'nombre_rec.required' => 'El nombre del recurso es obligatorio.',
            'nombre_rec.unique' => 'Ya existe un recurso con este nombre.',
            'descripcion.required' => 'La descripción del recurso es obligatorio.',
        ]);

        try {
            // 2. Crear y guardar el recurso
            Recursos::create([
                'codigo' => $request->codigo,
                'nombre_rec' => $request->nombre_rec,
                'descripcion' => $request->descripcion,
                'observacion' => $request->observacion,
            ]);

            // 3. Redireccionar con mensaje de éxito
            return redirect()->route('recursos.index')->with('success', 'Recurso registrado exitosamente.');
        } catch (\Exception $e) {
            // 4. Manejo de errores
            return back()->withInput()->with('error', 'Error al registrar el recurso. Intente nuevamente.');
        }
    }

    /**
     * Muestra el formulario para editar el recurso.
     */
    public function edit(Recursos $recurso)
    {

        // Autorización: Solo el administrador puede acceder a la edición
        $this->authorize('manage', $recurso);
        return view('recursos.editar-recurso', compact('recurso'));
    }

    /**
     * Actualiza el recurso en la base de datos.
     */
    public function update(Request $request, Recursos $recurso)
    {
        // Autorización: Solo el administrador puede actualizar
        $this->authorize('manage', $recurso);

        // 1. Validar los datos
        $request->validate([
            // Valida el código: obligatorio, único en la tabla 'recursos', ignorando el ID actual
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('recursos', 'codigo')->ignore($recurso->id_recurso, 'id_recurso')
            ],
            // Valida el nombre: obligatorio, único, ignorando el ID actual
            'nombre_rec' => [
                'required',
                'string',
                'max:30',
                Rule::unique('recursos', 'nombre_rec')->ignore($recurso->id_recurso, 'id_recurso')
            ],
            'descripcion' => ['required', 'string', 'max:255'],
            'observacion' => ['nullable', 'string'],
        ], [
            // Mensajes de error personalizados
            'codigo.required' => 'El código del recurso es obligatorio.',
            'codigo.unique' => 'Ya existe un recurso con este código.',
            'nombre_rec.required' => 'El nombre del recurso es obligatorio.',
            'nombre_rec.unique' => 'Ya existe un recurso con este nombre.',
            'descripcion.required' => 'La descripción del recurso es obligatorio.',
        ]);

        try {
            // 2. Actualizar el recurso
            $recurso->update([
                'codigo' => $request->codigo,
                'nombre_rec' => $request->nombre_rec,
                'descripcion' => $request->descripcion,
                'observacion' => $request->observacion,
            ]);

            // Llamada a la notificación
            $this->sendAdminNotification('updated', $recurso->id_recurso, $recurso->nombre_rec);

            // 3. Redireccionar con mensaje de éxito
            return redirect()->route('recursos.index')->with('success', 'Recurso "' . $recurso->nombre_rec . '" actualizado exitosamente.');
        } catch (\Exception $e) {
            // 4. Manejo de errores
            \Log::error("Error al actualizar recurso: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error al actualizar el recurso. Intente nuevamente.');
        }
    }

    /**
     * Elimina un recurso (Soft Delete).
     */
    public function destroy($id_recurso)
    {
        try {
            $recurso = Recursos::findOrFail($id_recurso);

            // Autorización: Solo el administrador puede eliminar
            $this->authorize('manage', $recurso);

            $recurso->delete();

            // Llamada a la notificación
            $this->sendAdminNotification('deleted', $recurso->id_recurso, $recurso->nombre_rec);

            return redirect()->route('recursos.index')->with('success', '¡Recurso #' . $id_recurso . ' eliminado correctamente!');

        } catch (\Exception $e) {
            \Log::error("Error al eliminar recurso: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al eliminar el recurso. Intente nuevamente.');
        }
    }

    /**
     * Marca un recurso como devuelto (establece la fecha de devolución actual).
     * El estado del recurso se determina automáticamente por la existencia de fecha_devolucion.
     */
    public function markAsReturned(Request $request, $id_asignacion)
    {
        // Autorización 1: Verifica que el rol sea el correcto.
        $this->authorize('canReturn', Recursos::class);

        try {
            // 1. Encuentra la asignación
            $asignacion = asignacion_recursos::with(['recurso', 'usuario'])->findOrFail($id_asignacion);
            $user = Auth::user();

            // 2. Autorización 2 (Verificación de propiedad para Rol 2):
            if ($user->id_rol === 2 && $asignacion->id_user !== $user->id_user) {
                abort(403, 'No está autorizado para devolver un recurso que no le ha sido asignado.');
            }

            // 3. Verificar si ya está devuelto
            if ($asignacion->fecha_devolucion !== null) {
                return back()->with('warning', 'Esta asignación ya fue marcada como devuelta anteriormente.');
            }

            // 4. Establecer la fecha de devolución y guardar
            $asignacion->fecha_devolucion = Carbon::now();
            $asignacion->save();

            // 5. Obtener el nombre del recurso para el mensaje de éxito
            $recursoNombre = $asignacion->recurso->nombre_rec ?? 'Recurso Desconocido';
            $usuarioAsignado = optional($asignacion->usuario)->nombre . ' ' . optional($asignacion->usuario)->apellido ?? 'Usuario Desconocido';

            // Llamada a la notificación
            $this->sendAdminNotification('returned', $id_asignacion, $recursoNombre, $usuarioAsignado);

            // 6. Redirección exitosa
            return redirect()->route('recursos.assignments.history')->with('success', '¡Recurso "' . $recursoNombre . '" marcado como devuelto con éxito!');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Manejar específicamente la excepción de autorización de la política
            return back()->with('error', 'No tiene permiso para realizar esta acción.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar caso donde no se encuentra la asignación (ID incorrecto)
            return back()->with('error', 'El registro de asignación no fue encontrado. Intente nuevamente.');
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            \Log::error("Error al marcar como devuelto (ID: {$id_asignacion}): " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al procesar la devolución. Intente nuevamente.');
        }
    }

    /**
     * Exporta todos los recursos a un archivo PDF descargable (Listado General).
     */
    public function exportarRecursosGeneralPDF()
    {
        // Autorización: Solo Rol de Administrador puede exportar el listado general
        $this->authorize('exportGeneralPDF', Recursos::class);

        // 1. Obtener todos los recursos
        $recursos = Recursos::all();

        // 2. Cargar la vista que contiene el PDF
        $pdf = PDF::loadView('recursos.pdf.listado-recursos-pdf', compact('recursos'));
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        // 3. Devolver el archivo PDF para descargar
        $fecha = Carbon::now()->format('Ymd');
        $nombreArchivo = "Reporte_Recursos_{$fecha}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exporta el historial completo de asignaciones de recursos a un archivo PDF.
     */
    public function exportarHistorialAsignacionesPDF()
    {
        // Autorización: Ambos roles pueden descargar el historial.
        $this->authorize('exportHistoryPDF', Recursos::class);

        $user = Auth::user();

        // 1. Obtener todas las asignaciones
        $query = asignacion_recursos::with(['usuario', 'recurso'])
            ->orderBy('fecha_asignacion', 'desc');

        // Si el usuario es Rol 2, filtrar por su ID
        if ($user->id_rol === 2) {
            $query->where('id_user', $user->id_user);
        }

        $asignaciones = $query->get();

        // 2. Cargar la vista que contiene el PDF
        $pdf = PDF::loadView('recursos.pdf.historial-asignaciones-pdf', compact('asignaciones'));
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        // 3. Devolver el archivo PDF para descargar
        $fecha = Carbon::now()->format('Ymd');
        $nombreArchivo = "Historial_Asignaciones_{$fecha}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Exporta el historial de asignaciones filtrado por un rango de fechas.
     */
    public function exportarHistorialAsignacionesPorFechaPDF(Request $request)
    {
        // Autorización: Ambos roles pueden descargar el historial.
        $this->authorize('exportHistoryPDF', Recursos::class);

        $user = Auth::user();

        // 1. Validar las fechas de entrada
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
        ]);

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        // 2. Obtener las asignaciones filtradas con filtro condicional
        $query = asignacion_recursos::with(['usuario', 'recurso'])
            ->whereDate('fecha_asignacion', '>=', $fechaDesde)
            ->whereDate('fecha_asignacion', '<=', $fechaHasta)
            ->orderBy('fecha_asignacion', 'desc');

        // Si el usuario es Rol 2, filtrar por su ID
        if ($user->id_rol === 2) {
            $query->where('id_user', $user->id_user);
        }

        $asignaciones = $query->get();

        // 3. Preparar datos adicionales para la vista
        $rangoFechas = [
            'desde' => Carbon::parse($fechaDesde)->format('d-m-Y'),
            'hasta' => Carbon::parse($fechaHasta)->format('d-m-Y'),
        ];

        // 4. Generar PDF
        $pdf = PDF::loadView('recursos.pdf.historial-asignaciones-pdf', compact('asignaciones', 'rangoFechas'));

        // 5. Ajustes y Descarga
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);
        $nombreArchivo = "Historial_Asignaciones_Filtrado_{$rangoFechas['desde']}_a_{$rangoFechas['hasta']}.pdf";

        return $pdf->download($nombreArchivo);
    }

    /**
     * Crea y envía una notificación a todos los administradores.
     */
    private function sendAdminNotification(string $action, int $id, string $recursoNombre, ?string $usuarioAsignado = null): void
    {
        // 1. Obtener el nombre del usuario que realizó la acción
        $user = Auth::user();
        $userName = $user->nombre . ' ' . $user->apellido;

        // 2. Determinar el mensaje y tipo de la notificación
        $message = '';
        $type = '';

        if ($action === 'updated') {
            $message = "El recurso '{$recursoNombre}' (#{$id}) ha sido ACTUALIZADO por {$userName}.";
            $type = 'recurso_actualizado';
        } elseif ($action === 'deleted') {
            $message = "El recurso '{$recursoNombre}' (#{$id}) ha sido ELIMINADO por {$userName}.";
            $type = 'recurso_eliminado';
        } elseif ($action === 'returned' && $usuarioAsignado) {
            $message = "El recurso '{$recursoNombre}' (Asignación #{$id}) ha sido DEVUELTO por {$usuarioAsignado}.";
            $type = 'recurso_devuelto';
        } else {
            return;
        }

        // 3. Buscar todos los administradores (id_rol == 1)
        $administrators = Usuario::where('id_rol', 1)->get();

        // 4. Crear una notificación para cada administrador
        foreach ($administrators as $admin) {
            Notificacion::create([
                'id_user' => $admin->id_user,
                'mensaje' => $message,
                'tipo' => $type,
                'leida' => false,
            ]);
        }
    }

}
