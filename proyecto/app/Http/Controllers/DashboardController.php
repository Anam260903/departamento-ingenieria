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

    public function index()
    {

        // 1. Obtener el usuario autenticado
        $user = Auth::user();

        // 2. Autorización: Usamos la política para verificar el acceso general
        $this->authorize('viewDashboard', Dashboard::class);


        // 3. Determinar si se debe filtrar por id_user
        $isUserRole = ($user->id_rol === 2);
        $userId = $user->id_user;


        // 4. Conteo de inspecciones
        $inspeccionQuery = Inspeccion::query();
        if ($isUserRole) {
            // id_rol=2 solo ve las inspecciones que él creó.
            $inspeccionQuery->where('id_user', $userId);
        }
        
        $inspeccionesPendientes = (clone $inspeccionQuery)->where('estado_insp', '0')->count();
        $inspeccionesCompletadas = (clone $inspeccionQuery)->where('estado_insp', '1')->count();


        // 5. Conteo de informes
        $informeQuery = Informe::query();
        if ($isUserRole) {
            // id_rol=2 solo ve los informes que él creó
            $informeQuery->whereHas('inspeccion', function ($q) use ($userId) {
                $q->where('id_user', $userId);
            });
        }
        $totalInformes = $informeQuery->count();


        // 6. Recuento de informes por mes (GRÁFICO)
        $informesPorMes = $informeQuery->select(
            DB::raw('count(*) as total'), 
            DB::raw('MONTH(created_at) as mes')
        )
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();


        // 7 Lógica del gráfico
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $datosGrafico = array_fill(0, 12, 0);

        foreach ($informesPorMes as $informe) {
            $datosGrafico[$informe->mes - 1] = $informe->total;
        }

        // 8. Pasar todos los datos a la vista
        return view('dashboard', compact('inspeccionesPendientes', 'inspeccionesCompletadas', 'totalInformes', 'datosGrafico', 'meses'));
    }
}