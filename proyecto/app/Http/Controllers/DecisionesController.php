<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use App\Models\Informe;
use App\Models\Usuario;
use App\Models\asignacion_recursos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DecisionesController extends Controller
{

    // Muestra el Dashboard principal de Toma de Decisiones
    public function index()
    {
        // --- Lógica para el Gráfico 1: Carga de trabajo por inspector ---

        // Obtener todos los usuarios, contando sus inspecciones pendientes (estado_insp = 0)
        $inspectores = Usuario::withCount('inspeccionesPendientes')
            ->where('correo', '!=', 'admin@gmail.com')
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

        // Mes actual
        $fecha_actual_inicio = now()->startOfMonth();
        $fecha_actual_fin = now()->endOfMonth();

        // Mes anterior
        $fecha_mes1_anterior_inicio = now()->copy()->subMonth()->startOfMonth();
        $fecha_mes1_anterior_fin = now()->copy()->subMonth()->endOfMonth();

        // Dos meses anteriores
        $fecha_mes2_anterior_inicio = now()->copy()->subMonths(2)->startOfMonth();
        $fecha_mes2_anterior_fin = now()->copy()->subMonths(2)->endOfMonth();

        // 2. Ejecutar las consultas de conteo
        // Conteo del aes actual
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
                // Regla 2: No disponible (de 5 a 10)
            } elseif ($pendientes >= 5 && $pendientes <= 10) {
                $noDisponibleCount++;
                // Regla 3: Sobrecargado (más de 10)
            } else {
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
            // Colores para Chart.js
            'backgroundColor' => ['#28a745', '#ffc107', '#dc3545'],
        ];


        return view('toma-decisiones.index', [
            'historicoData' => $historicoData,
            'cargaTrabajoData' => $cargaTrabajoData,
            'disponibilidadData' => $disponibilidadData,
        ]);
    }

    /**
     * Muestra la vista enfocada en logística y recursos.
     */
    public function showRecursos(Request $request)
    {
        $hoy = Carbon::today();

        // 1. Lógica para gráfico uso de recursos por inspector
        $usoRecursosPorInspector = $this->obtenerUsoRecursosPorInspector();

        // 2. Lógica para gráfico de recursos más solicitados (Top N)
        // Por defecto, calculamos el Top 10 para el mes actual
        $topRecursosData = $this->obtenerTopRecursosPorMes();

        // 3. Generar la recomendación inicial basada en el Top 3
        $recomendacion = $this->generarRecomendacionRecursos($topRecursosData);

        // 4. Obtenemos una lista de meses para el selector del Top N
        $mesesDisponibles = asignacion_recursos::select(
            DB::raw('YEAR(fecha_asignacion) as ano'),
            DB::raw('MONTH(fecha_asignacion) as mes')
        )
            ->distinct()
            ->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc')
            ->get()
            ->map(function ($item) use ($hoy) {
                $fecha = Carbon::create($item->ano, $item->mes, 1);
                return [
                    'value' => "{$item->ano}-{$item->mes}",
                    'label' => $fecha->isoFormat('MMMM YYYY'),
                    'selected' => ($item->ano == $hoy->year && $item->mes == $hoy->month)
                ];
            });

        // Cálculo del mes actual seleccionado para mostrar en la vista
        $mesSeleccionado = $request->input('mes', null);

        if ($mesSeleccionado) {
            list($ano, $mes) = explode('-', $mesSeleccionado);
            $fechaSeleccionada = Carbon::create($ano, $mes, 1);
            $mesActualTopN = $fechaSeleccionada->isoFormat('MMMM YYYY');
        } else {
            // Si no hay mes seleccionado, usa el valor por defecto (mes actual)
            $mesActualTopN = $hoy->isoFormat('MMMM YYYY');
        }

        return view('toma-decisiones.decision-recurso', [
            'usoRecursosPorInspector' => $usoRecursosPorInspector,
            'topRecursosData' => $topRecursosData,
            'mesesDisponibles' => $mesesDisponibles,
            'recomendacion' => $recomendacion,
            'mesActualTopN' => $hoy->isoFormat('MMMM YYYY')
        ]);
    }

    /**
     * Genera un texto de recomendación basado en el Top 3 de recursos más usados.
     */
    protected function generarRecomendacionRecursos(array $topRecursosData): string
    {
        $labels = $topRecursosData['labels'];
        $data = $topRecursosData['data'];

        if (empty($labels)) {
            return "Actualmente, no se registraron asignaciones de recursos en este período. No se puede generar una recomendación.";
        }

        // Obtener el Top 3
        $top3 = array_slice($labels, 0, 3);
        $conteoTop1 = $data[0] ?? 0;
        $recursoTop1 = $top3[0] ?? 'Recurso Desconocido';

        $recomendacion = "El análisis del Top 10 de Recursos muestra que $recursoTop1 es el más solicitado, con $conteoTop1 asignaciones en el período. ";

        if (count($top3) >= 2) {
            $recomendacion .= "Otros recursos clave son {$top3[1]}";
        }

        if (count($top3) >= 3) {
            $recomendacion .= " y {$top3[2]}. ";
        } else {
            $recomendacion .= ". ";
        }

        $recomendacion .= "Considere las siguientes recomendaciones: 
        1. Inventario: Evalúe el nivel de stock de estos recursos de alta demanda (especialmente $recursoTop1) para evitar interrupciones operacionales.
        2. Alternativas: Considere si existe una alternativa más eficiente o si el uso elevado de $recursoTop1 indica una necesidad de capacitación para el personal en el uso de otros equipos menos demandados.";

        return $recomendacion;
    }

    // Método auxiliares para gráficos de recursos

    /**
     * Prepara los datos para el gráfico de uso de recursos por inspector.
     */
    protected function obtenerUsoRecursosPorInspector(): array
    {
        // 1. Obtener todas las asignaciones que ya han sido devueltas o que están activas.
        // Contar cuántas veces se ha asignado un recurso por inspector.
        $asignaciones = asignacion_recursos::whereHas('recurso', function ($query) {
            $query->whereNull('deleted_at');
        })
            ->get()
            ->groupBy('id_user');

        $inspectores = [];
        $recursosDataSet = []; // Estructura para Chart.js

        // 2. Iterar sobre las asignaciones agrupadas por usuario (inspector)
        foreach ($asignaciones as $id_user => $coleccionAsignaciones) {
            $usuario = $coleccionAsignaciones->first()->usuario;
            $nombreInspector = $usuario->nombre . ' ' . $usuario->apellido;

            $inspectores[$id_user] = $nombreInspector;

            // Contar el uso de cada recurso para este inspector
            $conteoRecursos = $coleccionAsignaciones->groupBy('id_recurso')->map(function ($items) {
                return $items->count();
            });

            // Almacenar el conteo por recurso
            foreach ($conteoRecursos as $id_recurso => $conteo) {
                // Obtener el nombre del recurso
                $recurso = $coleccionAsignaciones->where('id_recurso', $id_recurso)->first()->recurso;
                $nombreRecurso = $recurso ? $recurso->nombre_rec : 'Recurso Desconocido';

                // Si el recurso no existe en el dataSet global, inicializarlo
                if (!isset($recursosDataSet[$id_recurso])) {
                    $recursosDataSet[$id_recurso] = [
                        'label' => $nombreRecurso,
                        'data' => array_fill_keys(array_keys($inspectores), 0), // Inicializar con ceros para todos los inspectores
                        'id_recurso' => $id_recurso,
                        'backgroundColor' => $this->generarColorAleatorio(count($recursosDataSet)),
                    ];
                }

                // Asignar el conteo al inspector correspondiente
                $recursosDataSet[$id_recurso]['data'][$id_user] = $conteo;
            }
        }

        // 3. Reordenar los datos para Chart.js
        $datasets = array_values($recursosDataSet);

        // 4. Asegurar que los arrays de datos dentro de cada dataset estén alineados por índice
        $inspectorLabels = array_values($inspectores);
        $inspectorIds = array_keys($inspectores);

        foreach ($datasets as $key => $dataset) {
            $datasets[$key]['data'] = array_values(
                array_replace(array_fill_keys($inspectorIds, 0), $dataset['data'])
            );
        }

        return [
            'labels' => $inspectorLabels,
            'datasets' => $datasets,
        ];
    }

    /**
     * Prepara los datos para el gráfico de Top N de recursos.
     */
    public function obtenerTopRecursosPorMes(?Request $request = null, int $limit = 10): array
    {
        // 1. Determinar el rango de fechas
        $mesAno = $request ? $request->input('mes', null) : null;

        if ($mesAno) {
            list($ano, $mes) = explode('-', $mesAno);
            $start = Carbon::create($ano, $mes, 1)->startOfMonth();
            $end = Carbon::create($ano, $mes, 1)->endOfMonth();
        } else {
            // Por defecto, usar el mes actual
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // 2. Consulta a la base de datos
        $topRecursos = asignacion_recursos::select(
            'recursos.nombre_rec',
            DB::raw('COUNT(asignacion_recursos.id_asignacion) as conteo')
        )
            ->join('recursos', function ($join) {
                $join->on('asignacion_recursos.id_recurso', '=', 'recursos.id_recurso')
                    ->whereNull('recursos.deleted_at'); // Excluir recursos eliminados
            })
            ->whereBetween('fecha_asignacion', [$start, $end])
            ->groupBy('recursos.nombre_rec')
            ->orderBy('conteo', 'desc')
            ->limit($limit)
            ->get();

        // 3. Formatear para Chart.js
        return [
            'labels' => $topRecursos->pluck('nombre_rec')->toArray(),
            'data' => $topRecursos->pluck('conteo')->toArray(),
        ];
    }

    /**
     * Endpoint para AJAX (usado para actualizar el Top N de Recursos al cambiar el mes)
     */
    public function obtenerTopRecursosJson(Request $request): JsonResponse
    {
        $data = $this->obtenerTopRecursosPorMes($request);

        // Generar la nueva recomendación basada en los nuevos datos
        $recomendacion = $this->generarRecomendacionRecursos($data);

        return response()->json([
            'labels' => $data['labels'],
            'data' => $data['data'],
            'recomendacion' => $recomendacion,
        ]);
    }

    /**
     * Genera un color hexadecimal aleatorio basado en un índice para consistencia.
     */
    protected function generarColorAleatorio(int $index): string
    {
        // Colores predefinidos y distintivos
        $colors = [
            '#0d6efd',
            '#dc3545',
            '#198754',
            '#ffc107',
            '#6f42c1',
            '#fd7e14',
            '#20c997',
            '#6c757d',
            '#f7a249',
            '#1abc9c',
            '#9b59b6',
        ];

        return $colors[$index % count($colors)];
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
                'fecha_inf' => Carbon::parse($informe->fecha_inf)->format('d-m-Y'),
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


    /**
     * Muestra la vista para la comparación de inspecciones
     */

    public function comparacion()
    {
        // Obtener las inspecciones que tienen un informe asociado
        $inspecciones = Inspeccion::whereHas('informe')
            ->with(['vivienda.propietario'])
            ->orderBy('fecha_insp', 'desc')
            ->get();

        // Mapear los datos para enviarlos a la vista
        $dataInspecciones = $inspecciones->map(function ($insp) {
            return [
                'id' => $insp->id_insp,
                'propietario' => $insp->vivienda->propietario->nombre_propie . ' ' . $insp->vivienda->propietario->apellido_propie,
                'fecha' => Carbon::parse($insp->fecha_insp)->format('d/m/Y'),
                'comunidad' => $insp->informe ? $insp->informe->comunidad : 'N/A',
            ];
        });

        return view('toma-decisiones.comparacion-inspecciones', [
            'inspecciones' => $dataInspecciones,
        ]);
    }

}
