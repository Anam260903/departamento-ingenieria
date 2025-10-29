<?php

namespace App\Http\Controllers;
use App\Models\Informe;
use App\Models\Inspeccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InformesController extends Controller
{
    /**
     * Mostrar el listado de informes técnicos.
     */
    public function index()
    {
        // 1. Obtener los informes
        $informes = Informe::with([
            'inspeccion.vivienda.propietario',
            'inspeccion.usuario'
        ])
            ->orderBy('fecha_inf', 'desc')
            ->paginate(10); // Paginación

        return view('informes-tecnicos.index', compact('informes'));
    }

    /**
     * Devuelve una lista de inspecciones completadas que aún no tienen un informe.
     * Esto se usa para poblar el modal de selección.
     */
    public function obtenerInspeccionesDisponibles()
    {
        // 1. Obtener los IDs de las inspecciones que ya tienen un informe
        $inspeccionesConInforme = Informe::pluck('id_insp');

        // 2. Obtener las inspecciones que están 'Completadas' y no tienen un informe
        $inspecciones = Inspeccion::where('estado_insp', 1)
            ->whereNotIn('id_insp', $inspeccionesConInforme)
            ->with('vivienda.propietario')
            ->get();

        return $inspecciones;
    }

    /**
     * Muestra el formulario de creación del Informe Técnico.
     * @param int $id_insp
     */
    public function create($id_insp)
    {
        // 1. Cargar la inspección y sus relaciones para prellenar el formulario
        $inspeccion = Inspeccion::with([
            'vivienda.propietario',
            'usuario'
        ])->findOrFail($id_insp);

        // 2. Prepara la fecha del informe con la fecha de la inspección
        $fecha_inf = Carbon::parse($inspeccion->fecha_insp)->format('Y-m-d');

        // El paso actual es 1 (Datos Generales)
        $pasoActual = 1;

        // 3. Pasar los datos a la vista
        return view('informes-tecnicos.create', compact('inspeccion', 'fecha_inf', 'pasoActual'));
    }

}
