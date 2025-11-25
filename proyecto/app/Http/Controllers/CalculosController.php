<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calculos;

class CalculosController extends Controller
{
    public function index()
    {
        // Obtener todos los cálculos con el ID, nombre y contenido.
        $calculos = Calculos::select('id_calculo', 'nombre_calculo', 'contenido')->get();

        // Transformar los datos de cálculos a un formato JSON
        $calculosJson = $calculos->keyBy('id_calculo')->toJson();

        // Devolver la vista con los datos
        return view('estimacion-materiales', compact('calculos', 'calculosJson'));
    }
}