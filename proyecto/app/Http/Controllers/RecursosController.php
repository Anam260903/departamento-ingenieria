<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recursos;
use App\Models\Usuario;
use App\Models\asignacion_recursos;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecursosController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {

        // AUTORIZACIÓN: Solo el Administrador (id_rol=1) tiene acceso al index.
        $this->authorize('viewAny', Recursos::class);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // 1. Inicializar la consulta con las relaciones necesarias
        $query = Recursos::with('asignaciones.usuario');

        // 2. Filtrar por Palabra Clave
        // Buscar por codigo, nombre
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

        // 4. Ejecutar la consulta y ordenar
        $recursos = $query->orderBy('nombre_rec', 'asc')->get();

        // 5. Pasar los resultados a la vista
        return view('recursos.index', compact('recursos'));
    }

    /**
     * Muestra el historial completo de asignaciones
     */
    public function assignmentsHistory(Request $request)
    {
        // AUTORIZACIÓN: Ambos roles tienen acceso a este método.
        $this->authorize('viewHistory', Recursos::class);

        $user = Auth::user();

        // 1. Inicializar la consulta con las relaciones necesarias
        $query = asignacion_recursos::with(['recurso', 'usuario'])->orderBy('fecha_asignacion', 'desc');

        // LÓGICA DE FILTRO POR ROL:
        // Si el usuario es de Rol ID 2, restringir a sus propias asignaciones.
        if ($user->id_rol === 2) {
            $query->where('id_user', $user->id_user);
        }

        // 2. Filtrar por Palabra Clave (Nombre del Recurso, Nombre o Apellido del Usuario)
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

        // 3. Filtrar por Fecha de Asignación (Rango o exacta)
        if ($request->filled('fecha_asignacion_start')) {
            $query->whereDate('fecha_asignacion', '>=', $request->fecha_asignacion_start);
        }

        // 4. Filtrar por Fecha de Devolución (Rango o exacta)
        if ($request->filled('fecha_devolucion_end')) {
            $query->whereDate('fecha_devolucion', '<=', $request->fecha_devolucion_end);
        }

        // 5. Filtrar por Estado (Devuelto o Pendiente)
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

    // Muestra la vista de creación de recursos
    public function create()
    {
        // AUTORIZACIÓN: Solo el administrador puede crear.
        $this->authorize('manage', Recursos::class);
        return view('recursos.formulario-recurso');
    }

    /**
     * Almacena un recurso recién creado en la base de datos
     */
    public function store(Request $request)
    {
        // AUTORIZACIÓN: Solo el administrador puede almacenar.
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

        // AUTORIZACIÓN: Solo el administrador puede acceder a la edición.
        $this->authorize('manage', $recurso);
        return view('recursos.editar-recurso', compact('recurso'));
    }

    /**
     * Actualiza el recurso en la base de datos.
     */
    public function update(Request $request, Recursos $recurso)
    {
        // AUTORIZACIÓN: Solo el administrador puede actualizar.
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

            // 3. Redireccionar con mensaje de éxito
            return redirect()->route('recursos.index')->with('success', 'Recurso "' . $recurso->nombre_rec . '" actualizado exitosamente.');
        } catch (\Exception $e) {
            // 4. Manejo de errores
            \Log::error("Error al actualizar recurso: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error al actualizar el recurso. Intente nuevamente.');
        }
    }

    /**
     * Elimina un recurso (Soft Delete)
     */
    public function destroy($id_recurso)
    {
        try {
            $recurso = Recursos::findOrFail($id_recurso);

            // AUTORIZACIÓN: Solo el administrador puede eliminar.
            $this->authorize('manage', $recurso);

            $recurso->delete();

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
        // AUTORIZACIÓN 1: Verifica que el rol sea el correcto.
        $this->authorize('canReturn', Recursos::class);

        try {
            // 1. Encuentra la asignación. Usamos with('recurso') para asegurar el nombre después.
            $asignacion = asignacion_recursos::with('recurso')->findOrFail($id_asignacion);
            $user = Auth::user();

            // 2. AUTORIZACIÓN 2 (Verificación de propiedad para Rol 2):
            if ($user->id_rol === 2 && $asignacion->id_user !== $user->id_user) {
                abort(403, 'No está autorizado para devolver un recurso que no le ha sido asignado.');
            }

            // 3. Verificar si ya está devuelto
            if ($asignacion->fecha_devolucion !== null) {
                return back()->with('warning', 'Esta asignación ya fue marcada como devuelta anteriormente.');
            }

            // 4. Establecer la fecha de devolución y guardar (ESTE ES EL ÚNICO CAMBIO REQUERIDO)
            $asignacion->fecha_devolucion = \Carbon\Carbon::now();
            $asignacion->save();

            // 5. Obtener el nombre del recurso para el mensaje de éxito
            $recursoNombre = $asignacion->recurso->nombre_rec ?? 'Recurso Desconocido';

            // 6. Redirección exitosa
            return redirect()->route('recursos.assignments.history')->with('success', '¡Recurso "' . $recursoNombre . '" marcado como devuelto con éxito!');

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Manejar específicamente la excepción de autorización de la Política
            return back()->with('error', 'No tiene permiso para realizar esta acción.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar caso donde no se encuentra la asignación (ID incorrecto)
            return back()->with('error', 'El registro de asignación no fue encontrado. Intente nuevamente.');
        } catch (\Exception $e) {
            // Manejar cualquier otro error de servidor o base de datos
            // Registrar el error para su diagnóstico
            \Log::error("Error al marcar como devuelto (ID: {$id_asignacion}): " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al procesar la devolución. Intente nuevamente.');
        }
    }

    /**
     * Exporta todos los recursos a un archivo PDF descargable (Listado General).
     */
    public function exportarRecursosGeneralPDF()
    {
        // 1. Obtener todos los recursos
        $recursos = Recursos::all();

        // 2. Cargar la vista que contiene el PDF
        $pdf = PDF::loadView('recursos.pdf.listado-recursos-pdf', compact('recursos'));

        // 3. Ajustes de DomPDF
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        // 4. Devolver el archivo PDF para descargar
        $fecha = Carbon::now()->format('Ymd');
        $nombreArchivo = "Reporte_Recursos_General_{$fecha}.pdf";

        return $pdf->download($nombreArchivo);
    }
}
