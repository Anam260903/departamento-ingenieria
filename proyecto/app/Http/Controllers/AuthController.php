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

        // Estandarizar el correo a minúsculas antes de la autenticación
        $credentials['correo'] = strtolower($credentials['correo']);

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
                    'correo' => 'Tu usuario está inactivo. Contacta a la administración.',
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
            'cedula_user' => [
                'required',
                'string',
                'min:7',
                'max:8',
                'unique:usuarios',
                'regex:/^(?!0+$)(?!1{6,8}$)(?!2{6,8}$)(?!3{6,8}$)(?!4{6,8}$)(?!5{6,8}$)(?!6{6,8}$)(?!7{6,8}$)(?!8{6,8}$)(?!9{6,8}$)(?!123456$)(?!1234567$)(?!12345678$)(?!87654321$)(?!7654321$)(?!654321$)(?!(\d)\1+$)(\d{6,8})$/'
            ],

            'correo' => [
                'required',
                'string',
                'email',
                'max:40',
                'unique:usuarios',
                'regex:/@gmail\.com$/i'
            ],
            'password' => [
                'required',
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