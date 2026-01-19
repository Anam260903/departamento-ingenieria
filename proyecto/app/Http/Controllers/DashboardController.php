<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Informe;
use App\Models\Dashboard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends BaseController
{

    use AuthorizesRequests;
    // Aseguramos que solo usuarios autenticados puedan acceder
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorize('viewDashboard', Dashboard::class);

        // 1. Parámetros para el gráfico
        $anioSeleccionado = $request->input('anio', date('Y'));
        $aniosDisponibles = range(date('Y'), date('Y') - 5);

        $isUserRole = ($user->id_rol === 2);
        $userId = $user->id_user;

        // 2. Crear la base de la consulta para informes
        $informeBaseQuery = Informe::query();
        if ($isUserRole) {
            $informeBaseQuery->whereHas('inspeccion', function ($q) use ($userId) {
                $q->where('id_user', $userId);
            });
        }

        // 3. Total global
        $totalInformes = (clone $informeBaseQuery)->count();

        // 4. Cosulta para el gráfico
        $informesPorMes = $informeBaseQuery->whereYear('fecha_inf', $anioSeleccionado)
            ->select(
                DB::raw('count(*) as total'),
                DB::raw('MONTH(fecha_inf) as mes')
            )
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        // 5. Lógica del gráfico (Llenado de array)
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $datosGrafico = array_fill(0, 12, 0);
        foreach ($informesPorMes as $informe) {
            $indice = (int) $informe->mes - 1;
            $datosGrafico[$indice] = $informe->total;
        }

        // 6. Inspecciones pendientes y completadas
        $inspeccionQuery = Inspeccion::query();
        if ($isUserRole) {
            $inspeccionQuery->where('id_user', $userId);
        }
        $inspeccionesPendientes = (clone $inspeccionQuery)->where('estado_insp', '0')->count();
        $inspeccionesCompletadas = (clone $inspeccionQuery)->where('estado_insp', '1')->count();

        // 7. Retornar vista con datos
        return view('dashboard', compact(
            'inspeccionesPendientes',
            'inspeccionesCompletadas',
            'totalInformes',
            'datosGrafico',
            'meses',
            'anioSeleccionado',
            'aniosDisponibles'
        ));
    }
}