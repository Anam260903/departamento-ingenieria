<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspeccion;
use App\Models\Propietario;
use App\Models\Vivienda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class InspeccionesController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inicializar la consulta con las relaciones necesarias
        $query = Inspeccion::with('vivienda.propietario');

        // 2. Filtrar por Palabra Clave (Keyword)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
        
            $query->where(function ($q) use ($keyword) {
                // Buscar en la tabla principal (observacion)
                $q->where('observacion', 'like', '%' . $keyword . '%');

                // Buscar en la relación vivienda (direccion)
                $q->orWhereHas('vivienda', function ($q_viv) use ($keyword) {
                    $q_viv->where('direccion', 'like', '%' . $keyword . '%');
                });

                // Buscar en la relación propietario (nombre o apellido)
                $q->orWhereHas('vivienda.propietario', function ($q_prop) use ($keyword) {
                    $q_prop->where('nombre_propie', 'like', '%' . $keyword . '%')
                        ->orWhere('apellido_propie', 'like', '%' . $keyword . '%');
                });
            });
        }

        // 3. Filtrar por Estado
        if ($request->filled('estado')) {
            // Aseguramos que el valor sea un entero para la columna INT
            $query->where('estado_insp', (int)$request->estado); 
        }

        // 4. Filtrar por Fecha de Inicio (Fecha de Inspección)
        if ($request->filled('fecha_inicio')) {
            // Busca inspecciones cuya fecha_insp sea MAYOR O IGUAL a la fecha_inicio proporcionada
            $query->whereDate('fecha_insp', '>=', $request->fecha_inicio);
        }
    
        // 5. Ejecutar la consulta y ordenar (las más recientes primero)
        $inspecciones = $query->orderBy('fecha_insp', 'desc')->get();

        // 6. Pasar los resultados a la vista
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
            'propietario_cedula' => 'required|string|max:8|unique:propietarios,cedula_propie',
            'propietario_telefono' => 'required|string|max:11',
            'direccion' => 'required|string|max:100',
            'estado' => 'required|numeric|in:0,1',
            'observacion' => 'nullable|string|max:250',
        ]);
        

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Su sesión ha caducado. Por favor, inicie sesión de nuevo.');
        }

        try {
            // 2. Buscar o crear el Propietario (usará la tabla 'propietarios')
            $propietario = Propietario::firstOrCreate(
                ['cedula_propie' => $request->propietario_cedula],
                [
                    'nombre_propie' => $request->propietario_nombre,
                    'apellido_propie' => $request->propietario_apellido,
                    'telefono' => $request->propietario_telefono,
                ]
            );

            // 3. Crear la Vivienda (usa la columna 'direccion' de la tabla 'vivienda')
            $vivienda = Vivienda::create([
                'direccion' => $request->direccion,
                'id_propie' => $propietario->id_propie, // FK a Propietario
            ]);

             $userId = Auth::check() ? Auth::user()->getAuthIdentifier() : null;

            // 4. Crear la Inspección (usa el ID de usuario autenticado)
            Inspeccion::create([
                'fecha_insp' => $request->fecha,
                'estado_insp' => $request->estado,
                'observacion' => $request->observacion,
                'id_user' => $userId, // ¡CORREGIDO: usa el ID entero del usuario!
                'id_viv' => $vivienda->id_viv, // FK a Vivienda
            ]);

            // 5. Redirigir al usuario
            return redirect()->route('inspecciones.index')->with('success', '¡Inspección registrada con éxito!');
        } catch (QueryException $e) {
            // Manejar errores de la base de datos
            return back()->withInput()->with('error', 'Ocurrió un error al guardar la inspección. Intente nuevamente.');
        }
    }
}