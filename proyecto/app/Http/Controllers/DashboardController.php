<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Informe;

class DashboardController extends Controller
{
    public function index()
    {
        // Contar el número de inspecciones pendientes
        $inspeccionesPendientes = Inspeccion::where('estado_insp', '0')->count();

        // Contar el número de inspecciones completadas
        $inspeccionesCompletadas = Inspeccion::where('estado_insp', '1')->count();

        // Contar el total de informes técnicos
        $totalInformes = Informe::count();

        // Pasar los datos a la vista
        return view('dashboard', compact('inspeccionesPendientes', 'inspeccionesCompletadas', 'totalInformes'));
    }
}