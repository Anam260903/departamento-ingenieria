<?php

namespace App\Http\Controllers;

use App\Models\Usuario; //
use App\Models\Inspeccion;
use App\Models\Vivienda;
use App\Models\Propietario;
use App\Models\roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PersonalController extends Controller
{
    /**
     * Muestra la lista de personal técnico (usuarios)
     */
    public function index()
    {
        // Obtener todo el personal ordenado por apellido
        $personal = Usuario::with('rol')->orderBy('nombre', 'asc')->get();
        return view('personal.index', compact('personal'));
    }

    /**
     * Muestra el formulario de edición para un usuario específico
     */
    public function edit(Usuario $personal)
    {
        // Cargar todos los roles disponibles para el selector
        $roles = roles::all();

        // Muestra la vista de edición, pasando el usuario y los roles
        return view('personal.edit', compact('personal', 'roles'));
    }

    /**
     * Actualiza la información de un usuario específico
     */
    public function update(Request $request, Usuario $personal)
    {
        // 1. Validar los datos de entrada
        $request->validate([
            // La cédula debe ser única, excepto para el usuario actual
            'cedula_user' => [
                'required',
                'string',
                'min:7',
                'max:8',
                Rule::unique('usuarios')->ignore($personal->id_user, 'id_user'),
                'regex:/^(?!0+$)(?!1{6,8}$)(?!2{6,8}$)(?!3{6,8}$)(?!4{6,8}$)(?!5{6,8}$)(?!6{6,8}$)(?!7{6,8}$)(?!8{6,8}$)(?!9{6,8}$)(?!123456$)(?!1234567$)(?!12345678$)(?!87654321$)(?!7654321$)(?!654321$)(?!(\d)\1+$)(\d{6,8})$/'
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
            'id_rol' => ['required', 'exists:roles,id_rol'],
            'password' => [
                'nulable',
                'string',
                'min:8',
                'max:15',
                'confirmed',
                'regex:/^.*((?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!$#%@]).*$/'
            ],
        ], [
            // Mensaje personalizado para la Regex
            'cedula_user.regex' => 'La cédula ingresada no cumple con el formato válido. Por favor, ingrese un número de cédula real.',
            'correo.regex' => 'Solo se permiten direcciones de correo electrónico con el dominio @gmail.com.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un símbolo (! $ # % @).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede exceder los 15 caracteres.',
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
     * Cambia el estado del usuario (activo o inactivo).
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
     * Muestra la lista de inspecciones disponibles, incluyendo el nombre del propietario
     */
    public function getAvailableInspections(Usuario $personal)
    {
        // Carga las inspecciones disponibles junto a sus relaciones anidadas
        $inspecciones = Inspeccion::whereNull('id_user')
            ->with(['vivienda.propietario'])
            ->get();

        // Mapear la colección para crear el texto que se mostrará en el select
        $inspecciones_disponibles = $inspecciones->map(function ($insp) {

            $nombre_propietario = 'Propietario Desconocido';
            $ci_propietario = 'N/A';

            // Verificamos si las relaciones anidadas existen para evitar errores
            if ($insp->vivienda && $insp->vivienda->propietario) {
                $prop = $insp->vivienda->propietario;
                $nombre_propietario = $prop->nombre_propie . ' ' . $prop->apellido_propie;
                $ci_propietario = $prop->cedula_propie ?? 'N/A';
            }

            return [
                'id_insp' => $insp->id_insp,
                'propietario_display' => "Inspección #{$insp->id_insp} | Propietario: {$nombre_propietario} (CI: {$ci_propietario})",
            ];
        });

        // Retornar datos JSON
        return response()->json([
            'user_id' => $personal->id_user,
            'user_name' => $personal->nombre . ' ' . $personal->apellido,
            'inspecciones' => $inspecciones_disponibles,
        ]);
    }

    /**
     * Procesa la solicitud y asigna una inspección seleccionada al usuario
     */
    public function assignInspection(Request $request, Usuario $personal)
    {
        // 1. Validar la entrada
        $request->validate([
            // Valida que el ID de inspección sea requerido y exista
            'id_insp' => [
                'required',
                'exists:inspecciones,id_insp',
            ],
        ]);

        try {
            // 2. Buscar la inspección
            $inspeccion = Inspeccion::find($request->id_insp);

            // Verificación final de disponibilidad antes de guardar
            if (!is_null($inspeccion->id_user)) {
                 return back()->with('error', 'Error: La inspección ya no está disponible.');
            }
            
            // 3. Asignar el ID del usuario al campo id_user de la inspección
            $inspeccion->id_user = $personal->id_user;
            
            $inspeccion->save();

            // Mensaje de éxito
            return back()->with('success', 'Inspección #'. $inspeccion->id_insp . ' asignada a ' . $personal->nombre . ' correctamente.');

        } catch (\Exception $e) {
            // En caso de error
            return back()->with('error', 'Hubo un error al asignar la inspección: ' . $e->getMessage());
        }
    }
}