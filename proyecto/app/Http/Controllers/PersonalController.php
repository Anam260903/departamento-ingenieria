<?php

namespace App\Http\Controllers;

use App\Models\Usuario; // Asumiendo que tu modelo de personal se llama 'User'
use App\Models\roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PersonalController extends Controller
{
    /**
     * Muestra la lista de personal técnico (usuarios).
     */
    public function index()
    {
        // Obtener todo el personal ordenado por apellido
        // Asegúrate de que tu modelo User tiene las columnas: cedula, nombre, apellido, email, profesion, estado/activo.
        $personal = Usuario::with('rol')->orderBy('nombre', 'asc')->get();
        return view('personal.index', compact('personal'));
    }

    /**
     * Redirige al formulario de edición (debes crear el método 'update' y la vista 'edit').
     */
    public function edit(Usuario $personal)
    {
        // Cargar todos los roles disponibles para el selector
        $roles = roles::all();

        // Muestra la vista de edición, pasando el usuario ($personal) y los roles
        return view('personal.edit', compact('personal', 'roles'));
    }


    public function update(Request $request, Usuario $personal)
    {
        // 1. Validar los datos de entrada
        $request->validate([
            // La cédula debe ser única, excepto para el usuario actual
            'cedula_user' => [
                'required',
                'string',
                'max:8',
                Rule::unique('usuarios')->ignore($personal->id_user, 'id_user')
            ],
            'nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            // El correo debe ser único, excepto para el usuario actual
            'correo' => [
                'required',
                'string',
                'email',
                'max:40',
                Rule::unique('usuarios')->ignore($personal->id_user, 'id_user')
            ],
            'profesion' => ['nullable', 'string', 'max:50'],
            'id_rol' => ['required', 'exists:roles,id_rol'], // Asegura que el rol exista
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Opcional, solo si se cambia
        ]);

        // 2. Preparar los datos para la actualización
        $data = $request->only([
            'cedula_user',
            'nombre',
            'apellido',
            'correo',
            'profesion',
            'id_rol'
        ]);

        // Si se proporciona una nueva contraseña, la hashea y la añade a los datos
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 3. Actualizar el usuario
        $personal->update($data);

        // 4. Redirigir y notificar
        return redirect()->route('personal.index')->with('success', 'Personal ' . $personal->nombre . ' ' . $personal->apellido . ' actualizado exitosamente.');
    }


    /**
     * Cambia el estado del usuario ('0' a '1' o '1' a '0').
     */
    public function toggleStatus(Usuario $personal)
    {
        // Determina el nuevo estado y su valor en la base de datos
        if ($personal->estado_user === '1') {
            $personal->estado_user = '0'; // Cambia a inactivo
            $nuevoEstadoTexto = 'inactivo';
        } else {
            $personal->estado_user = '1'; // Cambia a activo
            $nuevoEstadoTexto = 'activo';
        }

        $personal->save();

        return back()->with('success', 'Estado de ' . $personal->nombre . ' actualizado a ' . $nuevoEstadoTexto . '.');
    }

    /**
     * Redirige a la vista para asignar inspecciones.
     */
    public function assignInspections(Usuario $personal)
    {
        // Aquí se mostraría una vista con las inspecciones pendientes para asignar a este usuario
        return view('personal.assign_inspections', compact('personal'));
    }

    // Debes añadir los métodos store, update y destroy según tu flujo
}