<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    /**
     * Muestra la vista del perfil del usuario autenticado
     */
    public function index()
    {
        // Obtener la información del usuario autenticado
        $usuario = Auth::user();

        // Pasar el objeto de usuario a la vista
        return view('perfil', compact('usuario'));
    }

    /**
     * Actualiza la información del perfil del usuario autenticado
     */
    public function update(Request $request)
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Validar los datos del formulario, excluyendo al usuario actual
        $request->validate([
            'nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'correo' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios', 'correo')->ignore($usuario),
            ],
          ], [
            // Mensaje personalizado para la Regex
            'correo.regex' => 'Solo se permiten direcciones de correo electrónico con el dominio @gmail.com.',
        ]);

        // Actualizar los datos del usuario
        $usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
        ]);

        // Redirigir al usuario de vuelta a la página de perfil con un mensaje de éxito
        return redirect()->route('perfil')->with('success', '¡Tu perfil ha sido actualizado con éxito!');
    }

    /**
     * Actualiza la contraseña del usuario autenticado
     */
    public function changePassword(Request $request)
    {
        // Validar las contraseñas
        $request->validate([
            'password_actual' => 'required',
            'password_nueva' => [
                'required',
                'string',
                'min:8',
                'max:15',
                'confirmed',
                'regex:/^.*((?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!$#%@]).*$/'
            ],
        ], [
            // Mensaje personalizado para la Regex
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un símbolo (! $ # % @).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede exceder los 15 caracteres.',
        ]);

        $usuario = Auth::user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->password_actual, $usuario->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }

        // Actualizar la contraseña en la base de datos
        $usuario->update([
            'password' => Hash::make($request->password_nueva),
        ]);

        return redirect()->route('perfil')->with('success', '¡Tu contraseña ha sido actualizada con éxito!');
    }
}