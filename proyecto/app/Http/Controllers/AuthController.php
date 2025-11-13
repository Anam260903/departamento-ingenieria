<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Muestra el formulario de inicio de sesión
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesa el inicio de sesión
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Intentar autenticar al usuario por credenciales
        if (Auth::attempt(['correo' => $credentials['correo'], 'password' => $credentials['password']])) {

            $user = Auth::user(); // Obtener el usuario autenticado

            // 2. Verificar el estado del usuario ('0' = inactivo, '1' = activo)
            if ($user->estado_user === '0') {
                // Si está inactivo, cerrar la sesión y bloquear el acceso
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    // Mostrar mensaje de bloqueo
                    'correo' => 'Tu cuenta está inactiva y pendiente de aprobación por el administrador. Espera a ser activado.',
                ])->onlyInput('correo');
            }

            // Si está activo ('1'), continuar con el inicio de sesión normal
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        // Si las credenciales son incorrectas
        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    // Cierra la sesión del usuario
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Muestra el formulario de registro
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Procesa el registro de un nuevo usuario
    public function register(Request $request)
    {
        // 1. Validar los datos del formulario
        $request->validate([
            'nombre' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'apellido' => ['required', 'string', 'max:30', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'cedula_user' => 'required|string|max:8|unique:usuarios',
            'correo' => 'required|string|email|max:40|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Crear el nuevo usuario
        Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula_user' => $request->cedula_user,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'estado_user' => '0', // Inactivo por defecto
            'id_rol' => 2,      // Rol 'Usuario' por defecto
        ]);

        // 3. Redirigir al usuario con mensaje
        return redirect()->route('login')->with('success', '¡Registro exitoso! Tu cuenta ha sido creada y está pendiente de activación por el administrador.');
    
    }
}