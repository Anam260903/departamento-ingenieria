<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Informe;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Contar el número de inspecciones pendientes y completadas
        $inspeccionesPendientes = Inspeccion::where('estado_insp', '0')->count();
        $inspeccionesCompletadas = Inspeccion::where('estado_insp', '1')->count();

        // Contar el total de informes técnicos
        $totalInformes = Informe::count();

        // Obtener el recuento de informes por mes
        $informesPorMes = Informe::select(DB::raw('count(*) as total'), DB::raw('MONTH(created_at) as mes'))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        // Inicializar un array para los datos del gráfico
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $datosGrafico = array_fill(0, 12, 0); // Crea un array de 12 ceros

        // Rellenar el array de datos con los valores de la base de datos
        foreach ($informesPorMes as $informe) {
            $datosGrafico[$informe->mes - 1] = $informe->total;
        }

        // Pasar todos los datos a la vista
        return view('dashboard', compact('inspeccionesPendientes', 'inspeccionesCompletadas', 'totalInformes', 'datosGrafico', 'meses'));
    }
}