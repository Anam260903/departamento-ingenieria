<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        // Obtener la información del usuario autenticado
        $usuario = Auth::user();

        // Pasar el objeto de usuario a la vista
        return view('perfil', compact('usuario'));
    }
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
    public function changePassword(Request $request)
    {
        // Validar las contraseñas
        $request->validate([
            'password_actual' => 'required',
            'password_nueva' => 'required|min:8|confirmed',
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

