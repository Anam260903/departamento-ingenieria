<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DecisionesController extends Controller
{

    // Muestra el Dashboard principal de Toma de Decisiones
    public function index()
    {
        // --- Lógica para el Gráfico 1: Carga de trabajo por inspector ---

        // Obtener todos los usuarios, contando sus inspecciones pendientes (estado_insp = 0)
        $inspectores = Usuario::withCount('inspeccionesPendientes')
            ->get()
            ->sortByDesc('inspecciones_pendientes_count'); // Ordenar por carga de trabajo

        $labelsCarga = [];
        $dataCarga = [];

        foreach ($inspectores as $inspector) {
            $labelsCarga[] = $inspector->nombre . ' ' . $inspector->apellido;
            // Obtener el conteo de la relación 'inspeccionesPendientes'
            $dataCarga[] = $inspector->inspecciones_pendientes_count;
        }

        $cargaTrabajoData = [
            'labels' => $labelsCarga,
            'data' => $dataCarga,
        ];

        // --- Lógica para el Gráfico 2: Comparación de 3 meses ---

        // 1. Definir los rangos de fechas
        $fecha_actual_inicio = now()->startOfMonth();
        $fecha_actual_fin = now()->endOfMonth();

        // Mes Anterior
        $fecha_mes1_anterior_inicio = now()->subMonth()->startOfMonth();
        $fecha_mes1_anterior_fin = now()->subMonth()->endOfMonth();

        // Dos Meses Anteriores
        $fecha_mes2_anterior_inicio = now()->copy()->subMonths(2)->startOfMonth();
        $fecha_mes2_anterior_fin = now()->copy()->subMonths(2)->endOfMonth();

        // 2. Ejecutar las consultas de conteo
        // Conteo del Mes Actual
        $actual_count = Inspeccion::where('estado_insp', 1)
            ->whereBetween('fecha_insp', [$fecha_actual_inicio, $fecha_actual_fin])
            ->count();

        // Conteo del primer mes anterior
        $mes1_anterior_count = Inspeccion::where('estado_insp', 1)
            ->whereBetween('fecha_insp', [$fecha_mes1_anterior_inicio, $fecha_mes1_anterior_fin])
            ->count();

        // Conteo del segundo mes anterior
        $mes2_anterior_count = Inspeccion::where('estado_insp', 1)
            ->whereBetween('fecha_insp', [$fecha_mes2_anterior_inicio, $fecha_mes2_anterior_fin])
            ->count();

        // 3. Preparar los datos para la vista

        $historicoData = [
            // Las etiquetas del gráfico (3 puntos: M-2, M-1, M-Actual)
            'labels' => [
                $fecha_mes2_anterior_inicio->isoFormat('MMM YYYY'),
                $fecha_mes1_anterior_inicio->isoFormat('MMM YYYY'),
                $fecha_actual_inicio->isoFormat('MMM YYYY')
            ],
            // Datos de las inspecciones completadas
            'data' => [
                $mes2_anterior_count,
                $mes1_anterior_count,
                $actual_count,
            ]
        ];

        // --- Lógica para el Gráfico 3: Disponibilidad de Personal ---

        $disponibleCount = 0;
        $noDisponibleCount = 0;
        $sobrecargadoCount = 0;

        foreach ($inspectores as $inspector) {
            $pendientes = $inspector->inspecciones_pendientes_count;

            // Regla 1: Disponible (menos de 5)
            if ($pendientes < 5) {
                $disponibleCount++;
                // Regla 2: No Disponible (de 5 a 10)
            } elseif ($pendientes >= 5 && $pendientes <= 10) {
                $noDisponibleCount++;
                // Regla 3: Sobrecargado (más de 10)
            } else { // $pendientes > 10
                $sobrecargadoCount++;
            }
        }

        // 3. Preparar los datos para la vista
        $disponibilidadData = [
            'labels' => [
                'Disponible (' . $disponibleCount . ')',
                'No Disponible (' . $noDisponibleCount . ')',
                'Sobrecargado (' . $sobrecargadoCount . ')'
            ],
            // Datos calculados
            'data' => [
                $disponibleCount,
                $noDisponibleCount,
                $sobrecargadoCount
            ],
            // Colores para Chart.js: Verde, Amarillo, Rojo
            'backgroundColor' => ['#28a745', '#ffc107', '#dc3545'],
        ];


        return view('toma-decisiones.index', [
            'historicoData' => $historicoData,
            'cargaTrabajoData' => $cargaTrabajoData,
            'disponibilidadData' => $disponibilidadData,
        ]);
    }

    /**
     * Muestra la vista de resumen de inspecciones.
     */
    public function resumenInspeccion(Request $request)
    {
        $inspecciones = Inspeccion::with(['vivienda.propietario', 'informe'])
            ->latest('fecha_insp') // Ordena por fecha más reciente
            ->paginate(12); // Paginación para no cargar demasiados elementos

        return view('toma-decisiones.resumen-inspeccion', compact('inspecciones'));
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
