<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recursos;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RecursosController extends Controller
{
    public function index(Request $request)
    {

        // Obtener el usuario autenticado
        $user = Auth::user();

        // 1. Inicializar la consulta con las relaciones necesarias
        $query = Recursos::with('asignaciones.usuario');

        // Lógica de autorización (Filtro en listado)

        // Si el usuario es de Rol ID 2 (Usuario), restringir a sus propios registros.
        if ($user && $user->id_rol === 2) {
            
            $query->whereHas('asignaciones', function ($q) use ($user) {
                $q->where('id_user', $user->id_user);
            });
        }

        // 2. Filtrar por Palabra Clave
        // Buscar por codigo, nombre
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('codigo', 'like', '%' . $keyword . '%')
                    ->orWhere('nombre_rec', 'like', '%' . $keyword . '%');
            });
        }


        // 3. Filtrar por estado (ASIGNADO / NO ASIGNADO)
        if ($request->filled('estado')) {
            $estado = $request->estado; // '1' para ASIGNADO, '0' para NO ASIGNADO

            if ($estado === '1') {
                $query->whereHas('asignaciones', function ($q) {
                    $q->whereNull('fecha_devolucion');
                });

            } elseif ($estado === '0') {
                $query->whereDoesntHave('asignaciones', function ($q) {
                    $q->whereNull('fecha_devolucion');
                });
            }
        }


        // 4. Ejecutar la consulta y ordenar
        $recursos = $query->orderBy('nombre_rec', 'asc')->get();

        // 5. Pasar los resultados a la vista
        return view('recursos.index', compact('recursos'));
    }

    // Muestra la vista de creación de recursos
    public function create()
    {
        return view('recursos.formulario-recurso');
    }

    /**
     * Almacena un recurso recién creado en la base de datos (Guardar).
     * Corresponde a la ruta POST /resources
     */
    public function store(Request $request)
    {
        // 1. Validar los datos
        $request->validate([
            'codigo' => ['required', 'string', 'max:255', Rule::unique('recursos', 'codigo')],
            'nombre_rec' => ['required', 'string', 'max:255', Rule::unique('recursos', 'nombre_rec')],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'observacion' => ['nullable', 'string'],
        ], [
            // Mensajes de error personalizados
            'codigo.required' => 'El codigo del recurso es obligatorio.',
            'codigo.unique' => 'Ya existe un recurso con este codigo.',
            'nombre_rec.required' => 'El nombre del recurso es obligatorio.',
            'nombre_rec.unique' => 'Ya existe un recurso con este nombre.',
        ]);

        try {
            // 2. Crear y guardar el recurso
            Recursos::create([
                'codigo' => $request->codigo,
                'nombre_rec' => $request->nombre_rec,
                'descripcion' => $request->descripcion,
                'observacion' => $request->observacion,
                // Si la tabla tiene otros campos obligatorios, inclúyelos aquí.
            ]);

            // 3. Redireccionar con mensaje de éxito
            return redirect()->route('recursos.index')->with('success', 'Recurso registrado exitosamente.');
        } catch (\Exception $e) {
            // 4. Manejo de errores
            return back()->withInput()->with('error', 'Error al registrar el recurso. Intente nuevamente.');
        }
    }

    /**
     * Muestra el formulario para editar el recurso (Mostrar).
     * Corresponde a la ruta GET /resources/{resource}/edit
     */
    public function edit(Recursos $resource)
    {
        // Pasar el objeto $resource a la vista de edición
        return view('recursos.edit', compact('resource'));
    }
}
