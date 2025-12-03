<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DecisionesController extends Controller
{
    /**
     * Muestra la vista de resumen de inspecciones.
     */
    public function resumenInspeccion(Request $request)
    {
        $inspecciones = Inspeccion::with(['vivienda.propietario', 'informe'])
            ->latest('fecha_insp') // Ordena por fecha más reciente
            ->paginate(12); // Paginación para no cargar demasiados elementos

        return view('toma-decisiones.resumen_inspeccion', compact('inspecciones'));
    }


    /**
     * Obtiene el resumen de una inspección (y su informe) para una solicitud AJAX.
     */
    public function obtenerResumenInspeccion(Inspeccion $inspeccion): JsonResponse
    {
        $inspeccion->refresh();

        // Obtener el estado
        $estado = (string) $inspeccion->estado_insp;

        if (empty($estado) && $estado !== '0') {
            return response()->json([
                'estado' => 'error_dato',
                'mensaje' => 'Error de dato: El estado de inspección no fue inicializado correctamente (valor vacío o nulo).',
            ], 400);
        }

        // 1. Si la inspección pendiente es '0'
        if ($estado === '0') {
            return response()->json([
                'estado' => 'pendiente',
                'mensaje' => 'No hay información de informe para mostrar. La inspección está en estado Pendiente.'
            ]);
        }

        // 2. Si la inspección es '1'
        elseif ($estado === '1') {

            $informe = $inspeccion->informe;

            if (!$informe) {
                // Completada pero sin informe asociado
                return response()->json([
                    'estado' => 'sin_informe',
                    'mensaje' => 'La inspección está marcada como completada, pero no se ha encontrado un informe técnico asociado.'
                ]);
            }

            // Completada y con informe listo
            $inspeccion->load('usuario', 'vivienda');

            $resumen = [
                'estado' => 'completado',
                'ingeniero' => $inspeccion->usuario->nombre . ' ' . $inspeccion->usuario->apellido,
                'comunidad' => $informe->comunidad ?? 'N/A',
                'antecedentes' => $informe->antecedentes,
                'resultados' => $informe->resultados,
                'recomendacion' => $informe->recomendacion,
                'fecha_inf' => \Carbon\Carbon::parse($informe->fecha_inf)->format('d-m-Y'),
                'id_informe' => $informe->id_inf,
            ];

            return response()->json($resumen);
        }

        // 3. Caso de un estado futuro
        else {
            return response()->json([
                'estado' => 'desconocido',
                'mensaje' => "El estado de inspección '$estado' es desconocido o no compatible."
            ], 400);
        }
    }
}
