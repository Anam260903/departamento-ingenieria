<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Inspeccion;
use App\Models\Propietario;
use App\Models\Vivienda;
use App\Models\Usuario;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InspeccionesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Lista de inspecciones registradas
     */
    public function index(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // 1. Inicializar la consulta con las relaciones necesarias
        $query = Inspeccion::with('usuario', 'vivienda.propietario', 'informe');

        // Lógica de autorización (Filtro en listado)

        // Si el usuario es de Rol ID 2 (Inspector), restringir a sus propios registros.
        if ($user && $user->id_rol === 2) {
            $query->where('id_user', $user->id_user);
        }

        // 2. Filtrar por palabra clave
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                // Buscar en la tabla principal (observación)
                $q->where('observacion', 'like', '%' . $keyword . '%');

                // Buscar en la relación vivienda (dirección)
                $q->orWhereHas('vivienda', function ($q_viv) use ($keyword) {
                    $q_viv->where('direccion', 'like', '%' . $keyword . '%');
                });

                // Buscar en la relación propietario (nombre o apellido)
                $q->orWhereHas('vivienda.propietario', function ($q_prop) use ($keyword) {
                    $q_prop->where('nombre_propie', 'like', '%' . $keyword . '%')
                        ->orWhere('apellido_propie', 'like', '%' . $keyword . '%');
                });

                // Buscar en la relación usuario (inspector)
                $q->orWhereHas('usuario', function ($q_user) use ($keyword) {
                    $q_user->where('nombre', 'like', '%' . $keyword . '%')
                        ->orWhere('apellido', 'like', '%' . $keyword . '%');
                });
            });
        }

        // 3. Filtrar por estado
        if ($request->filled('estado')) {
            // Aseguramos que el valor sea un entero para la columna INT
            $query->where('estado_insp', (int) $request->estado);
        }

        // 4. Filtrar por fecha de inspección
        if ($request->filled('fecha_inicio')) {
            // Busca inspecciones cuya fecha_insp sea mayor o igual a la fecha_inicio proporcionada
            $query->whereDate('fecha_insp', '>=', $request->fecha_inicio);
        }

        // 5. Ejecutar la consulta, ordenar y aplicar paginación
        $inspecciones = $query->orderBy('fecha_insp', 'desc')->paginate(10);

        // 6. Pasar los resultados a la vista
        return view('inspecciones.index', compact('inspecciones'));
    }

    /**
     * Muestra el formulario para crear una nueva inspección
     */
    public function create()
    {
        // Autorización: Verifica si el usuario puede crear esta inspección
        $this->authorize('create', Inspeccion::class);

        // Mostrar el formulario para crear una nueva inspección
        return view('inspecciones.formulario-inspeccion');
    }

    /**
     * Almacena una nueva inspección en la base de datos
     */
    public function store(Request $request)
    {
        // Calcula la fecha mínima permitida
        $minDate = Carbon::now()->subDays(30)->toDateString();

        // 1. Validar los datos del formulario
        $request->validate([
            'fecha' => 'required|date|after_or_equal:' . $minDate,
            'propietario_nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'propietario_apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'propietario_cedula' => [
                'required',
                'string',
                'min:7',
                'max:8',
                'regex:/^(?!0+$)(?!1{6,8}$)(?!2{6,8}$)(?!3{6,8}$)(?!4{6,8}$)(?!5{6,8}$)(?!6{6,8}$)(?!7{6,8}$)(?!8{6,8}$)(?!9{6,8}$)(?!123456$)(?!1234567$)(?!12345678$)(?!87654321$)(?!7654321$)(?!654321$)(?!(\d)\1+$)(\d{6,8})$/'
            ],
            'propietario_telefono' => 'required|string|min:11|max:11',
            'direccion' => 'required|string|max:100',
            'estado' => 'required|numeric|in:0,1',
            'observacion' => 'nullable|string|max:250',
        ], [
            // Mensaje personalizado para la Regex
            'fecha.after_or_equal' => 'La fecha de la inspección no puede ser anterior al ' . Carbon::parse($minDate)->format('d/m/Y') . '. Solo se permiten fechas de hace 30 días como máximo.',
            'propietario_cedula.regex' => 'La cédula ingresada no cumple con el formato válido. Por favor, ingrese un número de cédula real.',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Su sesión ha caducado. Por favor, inicie sesión de nuevo.');
        }

        try {
            // 2. Buscar o crear el propietario
            $propietario = Propietario::updateOrCreate(
                ['cedula_propie' => $request->propietario_cedula],
                [
                    'nombre_propie' => $request->propietario_nombre,
                    'apellido_propie' => $request->propietario_apellido,
                    'telefono' => $request->propietario_telefono,
                ]
            );

            // 3. Crear la vivienda
            $vivienda = Vivienda::firstOrCreate([
                'direccion' => $request->direccion,
                'id_propie' => $propietario->id_propie, // FK a Propietario
            ]);

            $userId = Auth::check() ? Auth::user()->getAuthIdentifier() : null;

            // 4. Crear la inspección
            Inspeccion::create([
                'fecha_insp' => $request->fecha,
                'estado_insp' => $request->estado,
                'observacion' => $request->observacion,
                'id_viv' => $vivienda->id_viv, // FK a Vivienda
            ]);

            // 5. Redirigir al usuario
            return redirect()->route('inspecciones.index')->with('success', '¡Inspección registrada con éxito!');

        } catch (QueryException $e) {
            // Manejar errores de la base de datos
            \Log::error("Error al guardar inspección: " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al guardar la inspección. Intente nuevamente.');
        }
    }

    /**
     * Edita una inspección existente
     */
    public function edit($id_insp)
    {
        // Cargar la inspección con sus relaciones (vivienda y propietario)
        $inspeccion = Inspeccion::with('vivienda.propietario')->findOrFail($id_insp);

        // Autorización: Verifica si el usuario puede editar la inspección
        $this->authorize('update', $inspeccion);

        return view('inspecciones.editar-inspeccion', compact('inspeccion'));
    }

    /**
     * Actualiza una inspección existente
     */
    public function update(Request $request, $id_insp)
    {
        // 1. Validar los datos del formulario
        $inspeccion = Inspeccion::with('vivienda.propietario')->findOrFail($id_insp);

        // Autorización: Verifica si el usuario puede actualizar la inspección
        $this->authorize('update', $inspeccion);

        // Lógica para la validación de la fecha (hace 30 días)
        $minDate = Carbon::now()->subDays(30)->toDateString();

        $vivienda = $inspeccion->vivienda;
        $propietario = $vivienda->propietario;

        $request->validate([
            'fecha' => 'required|date|after_or_equal:' . $minDate,
            'propietario_nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'propietario_apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            // Validar unique excluyendo el propio propietario
            'propietario_cedula' =>
                [
                    'required',
                    'string',
                    'min:7',
                    'max:8',
                    'regex:/^(?!0+$)(?!1{6,8}$)(?!2{6,8}$)(?!3{6,8}$)(?!4{6,8}$)(?!5{6,8}$)(?!6{6,8}$)(?!7{6,8}$)(?!8{6,8}$)(?!9{6,8}$)(?!123456$)(?!1234567$)(?!12345678$)(?!87654321$)(?!7654321$)(?!654321$)(?!(\d)\1+$)(\d{6,8})$/'
                ],
            'propietario_telefono' => 'required|string|min:11|max:11',
            'direccion' => 'required|string|max:100',
            'estado' => 'required|numeric|in:0,1',
            'observacion' => 'nullable|string|max:250',
        ], [
            // Mensaje personalizado para la Regex
            'fecha.after_or_equal' => 'La fecha de la inspección no puede ser anterior al ' . Carbon::parse($minDate)->format('d/m/Y') . '. Solo se permiten fechas de hace 30 días como máximo.',
            'propietario_cedula.regex' => 'La cédula ingresada no cumple con el formato válido. Por favor, ingrese un número de cédula real.',
        ]);


        try {
            // 2. Buscar o crear/actualizar el propietario
            $propietario = Propietario::updateOrCreate([
                'nombre_propie' => $request->propietario_nombre,
                'apellido_propie' => $request->propietario_apellido,
                'cedula_propie' => $request->propietario_cedula,
                'telefono' => $request->propietario_telefono,
            ]);

            // 3. Accedemos a la vivienda que está asociada actualmente a la inspección
            $vivienda = $inspeccion->vivienda;

            if ($vivienda) {
                // Actualizamos la dirección y el ID del propietario (si el propietario cambió)
                $vivienda->update([
                    'direccion' => $request->direccion,
                    'id_propie' => $propietario->id_propie, // Aseguramos que la FK apunte al propietario correcto
                ]);
            } else {
                // En un caso de error extremo donde no haya vivienda, la creamos (fallback)
                $vivienda = Vivienda::create([
                    'direccion' => $request->direccion,
                    'id_propie' => $propietario->id_propie,
                ]);
            }

            // 4. Actualizar la Inspección
            $inspeccion->update([
                'fecha_insp' => $request->fecha,
                'estado_insp' => $request->estado,
                'observacion' => $request->observacion,
                'id_viv' => $vivienda->id_viv,
            ]);

            // Llamada a la notificación
            $inspeccion->load('vivienda.propietario');
            $this->sendAdminNotification('updated', $inspeccion);

            // 5. Redirigir al usuario
            return redirect()->route('inspecciones.index')->with('success', '¡Inspección #' . $id_insp . ' actualizada con éxito!');

        } catch (\Exception $e) {
            // Manejar errores de la base de datos
            \Log::error("Error al actualizar inspección: " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al guardar los cambios. Intente nuevamente.');
        }
    }

    /**
     * Marca una inspección como completada
     */
    public function completeInspection($id_insp)
    {
        $inspeccion = Inspeccion::findOrFail($id_insp);

        // Autorización: Verifica si el usuario puede marcar como completada
        $this->authorize('update', $inspeccion);

        try {
            // Actualiza solo el campo de estado
            $inspeccion->update([
                'estado_insp' => 1,
            ]);

            return redirect()->route('inspecciones.index')->with('success', '¡Inspección #' . $id_insp . ' marcada como COMPLETADA con éxito! ✅');

        } catch (\Exception $e) {
            // Manejar errores de la base de datos
            \Log::error("Error al completar inspección: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al marcar la inspección como completada. Intente nuevamente.');
        }
    }

    /**
     * Cancela la asignación de una inspección, dejándola disponible para reasignación.
     * Solo disponible para administradores si no hay informe asociado.
     */
    public function cancelAssignment(Inspeccion $inspeccion)
    {
        // 1. Autorización:  Verifica si el usuario puede cancelar la inspección
        $this->authorize('reassign', $inspeccion);

        try {
            // 2. Verificación: Debe estar asignada y no tener informe
            if ($inspeccion->id_user === null) {
                return back()->with('warning', 'Esta inspección no estaba asignada a nadie.');
            }

            // Si existe un informe (la inspección ya fue trabajada), no debe cancelarse la asignación
            if ($inspeccion->informe()->exists()) {
                return back()->with('error', 'No se puede cancelar la asignación. El informe técnico ya fue iniciado/creado.');
            }

            $usuarioAnteriorId = $inspeccion->id_user;

            // 3. Lógica de cancelación
            $inspeccion->id_user = null; // Quitar el usuario asignado
            $inspeccion->save();

            // Llamada a la notificación
            $inspeccion->load('vivienda.propietario');
            $this->sendAdminNotification('canceled', $inspeccion);

            return redirect()->route('inspecciones.index')->with(
                'success',
                "La asignación de la inspección #{$inspeccion->id_insp} ha sido cancelada (Usuario ID: {$usuarioAnteriorId}). Está disponible para reasignación."
            );

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return back()->with('error', 'No tienes permiso para reasignar inspecciones.');
        } catch (\Exception $e) {
            \Log::error("Error al cancelar asignación de inspección #{$inspeccion->id_insp}: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al cancelar la asignación.');
        }
    }

    /**
     * Elimina una inspección (Soft Delete)
     */
    public function destroy($id_insp)
    {
        try {
            // 1. Cargar la inspección con sus relaciones antes de eliminarla
            $inspeccion = Inspeccion::with('vivienda.propietario', 'informe')->findOrFail($id_insp);

            // 2. Verificar si tiene informe
            if ($inspeccion->informe) {
                return redirect()->route('inspecciones.index')
                    ->with('warning', 'No se puede eliminar la inspección #' . $id_insp . ' porque ya tiene un informe realizado.');
            }

            // 3. Autorización: Verifica si el usuario puede eliminar la inspección
            $this->authorize('delete', $inspeccion);

            // 4. Eliminar (Soft Delete)
            $inspeccion->delete();

            // 5. Llamada a la notificación
            $this->sendAdminNotification('deleted', $inspeccion);

            return redirect()->route('inspecciones.index')
                ->with('success', '¡Inspección #' . $id_insp . ' eliminada correctamente!');

        } catch (\Exception $e) {
            \Log::error("Error al eliminar inspección: " . $e->getMessage());
            return redirect()->route('inspecciones.index')
                ->with('error', 'Ocurrió un error al eliminar la inspección o no tiene permisos.');
        }
    }

    /**
     * Exporta todas las inspecciones a un archivo PDF descargable
     */
    public function exportarPDF()
    {
        $user = Auth::user();

        //1. Obtener todas las inspecciones
        // Restricción para que el usuario con id_rol == 2 solo pueda descargar sus propias inspecciones
        if ($user?->id_rol === 2) {
            $inspecciones = Inspeccion::with(['vivienda.propietario', 'usuario'])
                ->where('id_user', Auth::id())
                ->get();
        } else {
            $inspecciones = Inspeccion::with(['vivienda.propietario', 'usuario'])->get();
        }

        // 2. Cargar la vista que contiene el PDF
        $pdf = PDF::loadView('inspecciones.pdf.reporte-inspecciones-pdf', compact('inspecciones'));

        // Ajuste para mejorar la paginación en tablas grandes
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        // 3. Devolver el archivo PDF para descargar
        $fecha = Carbon::now()->format('Ymd');
        $nombreArchivo = "Reporte_Inspecciones_{$fecha}.pdf";

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Exporta las inspecciones de un mes específico a un archivo PDF descargable
     */
    public function exportarPDFMes(Request $request)
    {
        $user = Auth::user();

        // 1. Obtener el mes y el año del formulario
        $mes = $request->input('mes');
        $ano = $request->input('ano');

        // Validación básica
        if (empty($mes) || empty($ano)) {
            return redirect()->back()->with('error', 'Debe seleccionar un Mes y un Año para exportar.');
        }

        // 2. Filtrar las inspecciones por Mes y Año
        // Si el usuario es de rol ID 2 (Inspector), restringir a sus propias inspecciones
        if ($user?->id_rol === 2) {
            $inspecciones = Inspeccion::with(['vivienda.propietario', 'usuario'])
                ->where('id_user', $user->id_user)
                ->whereYear('fecha_insp', $ano)
                ->whereMonth('fecha_insp', $mes)
                ->orderBy('fecha_insp', 'asc')
                ->get();
        } else {
            $inspecciones = Inspeccion::with(['vivienda.propietario', 'usuario'])
                ->whereYear('fecha_insp', $ano)
                ->whereMonth('fecha_insp', $mes)
                ->orderBy('fecha_insp', 'asc')
                ->get();
        }
        // 3. Preparar datos para el PDF
        Carbon::setLocale('es');
        $nombreMes = Carbon::createFromDate($ano, $mes)->monthName;
        $fechaReporte = "{$nombreMes} de {$ano}";

        // Si no hay inspecciones se envia un mensaje
        if ($inspecciones->isEmpty()) {
            return redirect()->back()->with('warning', "No se encontraron inspecciones para {$fechaReporte}.");
        }

        // 4. Cargar la vista Blade y generar el PDF
        $pdf = PDF::loadView('inspecciones.pdf.reporte-inspecciones-pdf', compact('inspecciones', 'fechaReporte'));
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'isPhpEnabled' => true]);

        // 5. Devolver el archivo PDF para descarga
        $nombreArchivo = "Reporte_Inspecciones_{$ano}_{$mes}.pdf";

        return $pdf->setPaper('a4', 'portrait')->stream($nombreArchivo);
    }

    /**
     * Crea y envía una notificación a todos los administradores (id_rol = 1)
     */
    private function sendAdminNotification(string $action, Inspeccion $inspeccion): void
    {
        try {
            $user = Auth::user();
            $userName = $user->nombre . ' ' . $user->apellido;
            $inspeccionId = $inspeccion->id_insp;

            // Obtener nombre del propietario
            $propietarioName = 'Propietario Desconocido';
            $propietario = optional($inspeccion->vivienda)->propietario;

            if ($propietario) {
                $propietarioName = $propietario->nombre_propie . ' ' . $propietario->apellido_propie;
            }

            // Definir el prefijo del mensaje con el propietario
            $prefijo = "(Prop: {$propietarioName})";

            $message = '';
            $type = '';

            // 2. Determinar el mensaje y tipo de la notificación
            if ($action === 'updated') {
                $message = "Inspección #{$inspeccionId} {$prefijo} ha sido ACTUALIZADA por {$userName}.";
                $type = 'inspeccion_actualizada';
            } elseif ($action === 'deleted') {
                $message = "Inspección #{$inspeccionId} {$prefijo} ha sido ELIMINADA por {$userName}.";
                $type = 'inspeccion_eliminada';
            } elseif ($action === 'canceled') {
                $message = "El Administrador {$userName} ha CANCELADO la asignación de {$prefijo}. Está disponible para reasignación.";
                $type = 'asignacion_cancelada';
            } else {
                \Log::warning("sendAdminNotification: Acción no reconocida: {$action} para Inspección ID: {$inspeccionId}");
                return;
            }

            // 3. Buscar todos los administradores (id_rol == 1).
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

            \Log::info("Notificación de inspección #{$inspeccionId} enviada exitosamente para la acción '{$action}'.");

        } catch (\Exception $e) {
            \Log::error("Error al crear notificación de inspección #{$inspeccionId}: " . $e->getMessage());
        }
    }
}