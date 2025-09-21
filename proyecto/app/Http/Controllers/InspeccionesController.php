<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Propietario;
use App\Models\Vivienda;
use Illuminate\Support\Facades\Auth;

class InspeccionesController extends Controller
{
    public function index()
    {
        // Obtener todas las inspecciones de la base de datos
        $inspecciones = Inspeccion::all();

        // Pasar los datos a la vista
        return view('gestion-inspecciones', compact('inspecciones'));
    }

    public function create()
    {
        // Mostrar el formulario para crear una nueva inspección
        return view('formulario-inspeccion');
    }

     public function store(Request $request)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'fecha' => 'required|date',
            'propietario_nombre' => 'required|string|max:30',
            'propietario_apellido' => 'required|string|max:30',
            'propietario_cedula' => 'required|string|max:8|unique:propietario,cedula_propie',
            'propietario_telefono' => 'required|string|max:11',
            'direccion' => 'required|string|max:50',
            'estado' => 'required|numeric|in:0,1',
            'observacion' => 'nullable|string|max:50',
        ]);

        // 2. Buscar o crear el Propietario
        // Se busca por cédula para evitar duplicados de propietarios
        $propietario = Propietario::firstOrCreate(
            ['cedula_propietario' => $request->propietario_cedula],
            [
                'nombre_propie' => $request->propietario_nombre,
                'apellido_propie' => $request->propietario_apellido,
                'telefono' => $request->propietario_telefono,
            ]
        );

        // 3. Crear la Vivienda
        // La tabla 'vivienda' tiene id_viv (PK) y id_propie (FK)
        $vivienda = Vivienda::create([
            'direccion' => $request->direccion,
            'id_propie' => $propietario->id_propie, // Asociar al propietario encontrado o creado
        ]);

        // 4. Crear la Inspección
        // La tabla 'inspeccion' tiene id_insp (PK), id_user (FK), id_viv (FK)
        Inspeccion::create([
            'fecha_insp' => $request->fecha,
            'estado_insp' => $request->estado,
            'observacion' => $request->observacion,
            'id_user' => Auth::id(), // ID del usuario autenticado
            'id_viv' => $vivienda->id_viv, // ID de la vivienda creada
        ]);

        // 5. Redirigir al usuario
        return redirect()->route('inspecciones.index')->with('success', '¡Inspección registrada con éxito!');
    }
}