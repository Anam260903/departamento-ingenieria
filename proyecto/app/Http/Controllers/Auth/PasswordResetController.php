<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    // Muestra el formulario para solicitar el enlace de restablecimiento
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // Envía el enlace de restablecimiento al correo del usuario
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['correo' => 'required|email']);

        $response = Password::broker()->sendResetLink(
            $request->only('correo')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return back()->with('status', __($response));
        }

        throw ValidationException::withMessages([
            'correo' => [trans($response)],
        ]);
    }

    // Muestra el formulario para restablecer la contraseña con el token
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password')->with(
            ['token' => $request->route('token'), 'correo' => $request->correo]
        );
    }

    // Procesa la solicitud para restablecer la contraseña
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'correo' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = Password::broker()->reset(
            $request->only('correo', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password) // Hashear la nueva contraseña
                ])->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($response));
        }

        return back()->withInput($request->only('correo'))
            ->withErrors(['correo' => trans($response)]);
    }
}