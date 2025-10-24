<?php

namespace App\Http\Controllers;
use App\Models\Informe; 
use Illuminate\Http\Request;
use Carbon\Carbon;  

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
     * Muestrar el formulario para crear un nuevo informe.
     */
    public function create()
    {
        // TO DO: Lógica para seleccionar qué inspección necesita un informe.
        // Por ahora, redirigimos al listado hasta que definamos esa lógica.
        return redirect()->route('informes.index')->with('info', 'Seleccione la inspección a reportar.');
    }

    // TO DO: Implementar store, show, edit, update, destroy
}
