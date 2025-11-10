<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Propietario;
use App\Models\Vivienda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InspeccionesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Aplica la lógica de autorización para restringir la vista de registros
     * basada en el rol del usuario autenticado.
     */
    public function index(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // 1. Inicializar la consulta con las relaciones necesarias
        $query = Inspeccion::with('vivienda.propietario');

        // =================================================================
        // LÓGICA DE AUTORIZACIÓN (FILTRO EN LISTADO)
        // =================================================================

        // Si el usuario es de Rol ID 2 (Usuario), restringir a sus propios registros.
        // Asumiendo que el modelo Usuario tiene la propiedad 'id_rol' y 'id_user'.
        if ($user && $user->id_rol === 2) {
            $query->where('id_user', $user->id_user);
        }

        // =================================================================
        // FIN LÓGICA DE AUTORIZACIÓN
        // =================================================================

        // 2. Filtrar por Palabra Clave (Keyword)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                // Buscar en la tabla principal (observacion)
                $q->where('observacion', 'like', '%' . $keyword . '%');

                // Buscar en la relación vivienda (direccion)
                $q->orWhereHas('vivienda', function ($q_viv) use ($keyword) {
                    $q_viv->where('direccion', 'like', '%' . $keyword . '%');
                });

                // Buscar en la relación propietario (nombre o apellido)
                $q->orWhereHas('vivienda.propietario', function ($q_prop) use ($keyword) {
                    $q_prop->where('nombre_propie', 'like', '%' . $keyword . '%')
                        ->orWhere('apellido_propie', 'like', '%' . $keyword . '%');
                });
            });
        }

        // 3. Filtrar por Estado
        if ($request->filled('estado')) {
            // Aseguramos que el valor sea un entero para la columna INT
            $query->where('estado_insp', (int) $request->estado);
        }

        // 4. Filtrar por Fecha de Inspección
        if ($request->filled('fecha_inicio')) {
            // Busca inspecciones cuya fecha_insp sea MAYOR O IGUAL a la fecha_inicio proporcionada
            $query->whereDate('fecha_insp', '>=', $request->fecha_inicio);
        }

        // 5. Ejecutar la consulta y ordenar (las más recientes primero)
        $inspecciones = $query->orderBy('fecha_insp', 'desc')->get();

        // 6. Pasar los resultados a la vista
        return view('gestion-inspecciones', compact('inspecciones'));
    }

    public function create()
    {
        // =================================================================
        // AUTORIZACIÓN: Verifica si el usuario puede crear esta inspección
        // =================================================================

        $this->authorize('create', Inspeccion::class);

        // Mostrar el formulario para crear una nueva inspección
        // No necesita autorización específica, ya que todos los roles la crean.
        return view('formulario-inspeccion');
    }

    public function store(Request $request)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'fecha' => 'required|date',
            'propietario_nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'propietario_apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'propietario_cedula' => 'required|string|max:8|unique:propietarios,cedula_propie',
            'propietario_telefono' => 'required|string|max:11',
            'direccion' => 'required|string|max:100',
            'estado' => 'required|numeric|in:0,1',
            'observacion' => 'nullable|string|max:250',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Su sesión ha caducado. Por favor, inicie sesión de nuevo.');
        }

        try {
            // 2. Buscar o crear el Propietario
            $propietario = Propietario::firstOrCreate(
                ['cedula_propie' => $request->propietario_cedula],
                [
                    'nombre_propie' => $request->propietario_nombre,
                    'apellido_propie' => $request->propietario_apellido,
                    'telefono' => $request->propietario_telefono,
                ]
            );

            // 3. Crear la Vivienda
            $vivienda = Vivienda::create([
                'direccion' => $request->direccion,
                'id_propie' => $propietario->id_propie, // FK a Propietario
            ]);

            $userId = Auth::check() ? Auth::user()->getAuthIdentifier() : null;

            // 4. Crear la Inspección
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

    public function edit($id_insp)
    {
        // Cargar la inspección con sus relaciones anidadas (vivienda y propietario)
        $inspeccion = Inspeccion::with('vivienda.propietario')->findOrFail($id_insp);

        // =================================================================
        // AUTORIZACIÓN: Verifica si el usuario puede editar esta inspección
        // =================================================================
        $this->authorize('update', $inspeccion);

        return view('editar-inspeccion', compact('inspeccion'));
    }

    public function update(Request $request, $id_insp)
    {
        // 1. Validar los datos del formulario
        $inspeccion = Inspeccion::with('vivienda.propietario')->findOrFail($id_insp);

        // =================================================================
        // AUTORIZACIÓN: Verifica si el usuario puede actualizar esta inspección
        // =================================================================
        $this->authorize('update', $inspeccion);

        $vivienda = $inspeccion->vivienda;
        $propietario = $vivienda->propietario;

        $request->validate([
            'fecha' => 'required|date',
            'propietario_nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'propietario_apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            // Validar unique excluyendo el propio propietario
            'propietario_cedula' => 'required|string|max:8|unique:propietarios,cedula_propie,' . $propietario->id_propie . ',id_propie',
            'propietario_telefono' => 'required|string|max:11',
            'direccion' => 'required|string|max:100',
            'estado' => 'required|numeric|in:0,1',
            'observacion' => 'nullable|string|max:250',
        ]);


        try {
            // 2. Actualizar el Propietario
            $propietario->update([
                'nombre_propie' => $request->propietario_nombre,
                'apellido_propie' => $request->propietario_apellido,
                'cedula_propie' => $request->propietario_cedula,
                'telefono' => $request->propietario_telefono,
            ]);

            // 3. Actualizar la Vivienda
            $vivienda->update([
                'direccion' => $request->direccion,
                // id_propie no cambia, ya que solo estamos modificando los datos del propietario
            ]);

            // 4. Actualizar la Inspección
            $inspeccion->update([
                'fecha_insp' => $request->fecha,
                'estado_insp' => $request->estado,
                'observacion' => $request->observacion,
                // id_user y id_viv no cambian
            ]);

            // 5. Redirigir al usuario
            return redirect()->route('inspecciones.index')->with('success', '¡Inspección #' . $id_insp . ' actualizada con éxito!');

        } catch (\Exception $e) {
            \Log::error("Error al actualizar inspección: " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al guardar los cambios. Intente nuevamente.');
        }
    }

    public function completeInspection($id_insp)
    {
        $inspeccion = Inspeccion::findOrFail($id_insp);

        // =================================================================
        // AUTORIZACIÓN: Verifica si el usuario puede marcar como completada
        // =================================================================
        $this->authorize('update', $inspeccion); // Usamos 'update' para esta acción

        try {
            // Actualiza solo el campo de estado
            $inspeccion->update([
                'estado_insp' => 1,
            ]);

            return redirect()->route('inspecciones.index')->with('success', '¡Inspección #' . $id_insp . ' marcada como COMPLETADA con éxito! ✅');

        } catch (\Exception $e) {
            // En caso de error en la base de datos
            \Log::error("Error al completar inspección: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al marcar la inspección como completada. Intente nuevamente.');
        }
    }

    public function destroy($id_insp)
    {
        try {
            $inspeccion = Inspeccion::findOrFail($id_insp);

            // =================================================================
            // AUTORIZACIÓN: Verifica si el usuario puede eliminar esta inspección
            // =================================================================
            $this->authorize('delete', $inspeccion);

            // Al usar el Trait SoftDeletes, el método delete() establece deleted_at.
            $inspeccion->delete();

            return redirect()->route('inspecciones.index')->with('success', '¡Inspección #' . $id_insp . ' eliminada correctamente!');

        } catch (\Exception $e) {
            \Log::error("Error al eliminar inspección: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al eliminar la inspección. Intente nuevamente.');
        }
    }

    // Nota: Los métodos exportarPDF y exportarPDFMes NO están restringidos por ID de usuario
    // ya que generalmente los reportes (PDFs) son generados por administradores.
    // Si quisieras restringir, aplicarías un filtro en la consulta de $inspecciones
    // similar a como se hizo en el método index().

    public function exportarPDF()
    {
        $user = Auth::user();

        //1. Obtener todas las inspecciones
        // Restricción para que el usuario con id_rol == 2 solo pueda descargar sus propias inspecciones
        if ($user?->id_rol === 2) {
            // Usar Auth::id() para obtener el id del usuario autenticado y evitar accesos a propiedades mágicas
            $inspecciones = Inspeccion::with(['vivienda.propietario', 'usuario'])
                ->where('id_user', Auth::id())
                ->get();
        } else {
            $inspecciones = Inspeccion::with(['vivienda.propietario', 'usuario'])->get();
        }


        // 2. Cargar la vista Blade en la librería PDF
        $pdf = PDF::loadView('reporte-inspecciones-pdf', compact('inspecciones'));

        // Ajuste para mejorar la paginación en tablas grandes (Dompdf)
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        // 3. Devolver el archivo PDF para descarga
        $fecha = \Carbon\Carbon::now()->format('Ymd');
        $nombreArchivo = "Reporte_Inspecciones_{$fecha}.pdf";

        return $pdf->download($nombreArchivo);
    }

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
        // Si el usuario es de Rol ID 2 (Usuario), restringir a sus propias inspecciones
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
        \Carbon\Carbon::setLocale('es');
        $nombreMes = \Carbon\Carbon::createFromDate($ano, $mes)->monthName;
        $fechaReporte = "{$nombreMes} de {$ano}";

        // Si no hay inspecciones se envia un mensaje
        if ($inspecciones->isEmpty()) {
            return redirect()->back()->with('warning', "No se encontraron inspecciones para {$fechaReporte}.");
        }

        // 4. Cargar la vista Blade y generar el PDF
        // Pasamos la fecha de reporte para actualizar el título del PDF
        $pdf = PDF::loadView('reporte-inspecciones-pdf', compact('inspecciones', 'fechaReporte'));
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        // 5. Devolver el archivo PDF para descarga
        $nombreArchivo = "Reporte_Inspecciones_{$ano}_{$mes}.pdf";

        return $pdf->download($nombreArchivo);
    }
}