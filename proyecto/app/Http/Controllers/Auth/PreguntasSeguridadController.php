<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\PreguntaSeguridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PreguntasSeguridadController extends Controller
{
    // Número requerido de preguntas
    private const NUM_QUESTIONS_REQUIRED = 3;

    // Etapa 1: Registro de preguntas (Después del primer login)

    /**
     * Muestra el formulario para registrar las preguntas de seguridad.
     */
    public function showRegistrationForm(Request $request)
    {
        if (auth()->user()->preguntasSeguridad()->count() >= self::NUM_QUESTIONS_REQUIRED) {
            return redirect('/dashboard')->with('info', 'Ya has registrado tus preguntas de seguridad.');
        }

        return view('auth.register-security-questions', [
            'num_questions' => self::NUM_QUESTIONS_REQUIRED
        ]);
    }

    /**
     * Guarda las preguntas y respuestas de seguridad.
     */
    public function registerQuestions(Request $request)
    {
        $rules = [];
        $messages = [];

        // Reglas dinámicas para las 3 preguntas
        for ($i = 1; $i <= self::NUM_QUESTIONS_REQUIRED; $i++) {
            $rules["pregunta_$i"] = ['required', 'string', 'max:255', 'distinct'];
            $rules["respuesta_$i"] = [
                'required',
                'string',
                'min:4',
                'max:30',
                'regex:/^[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/'
            ];

            // Mensaje personalizado para la respuesta
            $messages["respuesta_$i.regex"] = "La respuesta #$i solo debe contener letras, números y espacios.";
            $messages["respuesta_$i.min"] = "La respuesta #$i debe tener al menos 4 caracteres.";
        }

        $request->validate($rules, $messages);

        $usuario = auth()->user();

        // Eliminar las preguntas antiguas
        $usuario->preguntasSeguridad()->delete();

        for ($i = 1; $i <= self::NUM_QUESTIONS_REQUIRED; $i++) {

            $respuestaRaw = $request->input("respuesta_$i");

            // 1. Estandarizar la respuesta a minúsculas
            $respuestaEstandarizada = strtolower($respuestaRaw);

            $usuario->preguntasSeguridad()->create([
                'pregunta' => $request->input("pregunta_$i"),

                // 2. Hashear la versión en minúsculas
                'respuesta' => Hash::make($respuestaEstandarizada),
            ]);
        }

        return redirect('/dashboard')->with('status', '¡Preguntas de seguridad registradas exitosamente!');
    }


    // Etapa 2: Recuperación de contraseña

    /**
     * Paso 1: Muestra el formulario para pedir el correo.
     */
    public function showIdentifierForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Paso 1 (POST): Identifica al usuario y selecciona una pregunta al azar.
     */
    public function identifyUser(Request $request)
    {
        // Estandarizar el correo a minúsculas antes de la validación
        $request->merge(['correo' => strtolower($request->input('correo'))]);

        // Definir las reglas de validación y los mensajes personalizados
        $rules = [
            'correo' => 'required|email|exists:usuarios,correo',
        ];

        $messages = [
            'correo.exists' => 'El correo ingresado no se encuentra registrado.',
        ];

        // Aplicar la validación con los mensajes personalizados
        $request->validate($rules, $messages);

        // Buscar al usuario
        $usuario = Usuario::where('correo', $request->correo)->first();

        // Verificar si el usuario tiene preguntas de seguridad registradas
        if ($usuario->preguntasSeguridad()->count() < self::NUM_QUESTIONS_REQUIRED) {
            return back()->withErrors(['correo' => 'El usuario no tiene las preguntas de seguridad registradas. Contacte a soporte.']);
        }

        // Obtener una pregunta de seguridad al azar
        $preguntaAleatoria = $usuario->preguntasSeguridad()->inRandomOrder()->first();

        // Almacenar datos en la sesión para el siguiente paso
        session([
            'password_reset_user_id' => $usuario->id_user,
            'password_reset_pregunta_id' => $preguntaAleatoria->id_preg,
            'password_reset_challenge_token' => Str::random(60), // Token de seguridad temporal
        ]);

        return redirect()->route('form.preguntas');
    }

    /**
     * Paso 2: Muestra el formulario con la pregunta de seguridad.
     */
    public function showChallengeForm(Request $request)
    {
        $preguntaId = session('password_reset_pregunta_id');
        $challengeToken = session('password_reset_challenge_token');

        // Si falta alguno de los elementos clave de la sesión, reiniciar el flujo
        if (!$challengeToken || !$preguntaId) {
            return redirect()->route('form.olvideContraseña')->withErrors(['global' => 'Flujo de recuperación expirado o no iniciado. Comience de nuevo.']);
        }

        $preguntaSeguridad = PreguntaSeguridad::find($preguntaId);

        if (!$preguntaSeguridad) {
            $this->clearResetSession();
            return redirect()->route('form.olvideContraseña')->withErrors(['global' => 'Error al cargar la pregunta de seguridad. Comience el proceso de nuevo.']);
        }

        return view('auth.security-question-challenge', [
            'pregunta' => $preguntaSeguridad->pregunta,
            'challengeToken' => $challengeToken,
        ]);
    }

    /**
     * Paso 2 (POST): Valida la respuesta de seguridad.
     */
    public function validateAnswer(Request $request)
    {

        $request->validate([
            'respuesta' => 'required|string',
            'challenge_token' => 'required|string',
        ]);

        $sessionToken = session('password_reset_challenge_token');
        $preguntaSeguridad = PreguntaSeguridad::find(session('password_reset_pregunta_id'));
        $usuarioId = session('password_reset_user_id');

        // Verificación del token de desafío
        if (!$sessionToken || $request->challenge_token !== $sessionToken) {
            return back()->withErrors(['global' => 'Token de desafío inválido o expirado. Por favor, reintente la respuesta.']);
        }

        // Verificación de la respuesta de seguridad
        if (!$preguntaSeguridad || $preguntaSeguridad->id_user !== $usuarioId) {
            $this->clearResetSession(); // Borrar sesión solo si faltan datos críticos
            return redirect()->route('form.olvideContraseña')->withErrors(['global' => 'Error de integridad en el flujo. Comience de nuevo.']);
        }

        // Obtener la respuesta del usuario y convertirla a minúsculas
        $respuestaIngresada = strtolower($request->respuesta);


        // Verificar la respuesta hasheada
        if (Hash::check($respuestaIngresada, $preguntaSeguridad->respuesta)) {

            // Respuesta correcta
            session()->forget('password_reset_challenge_token');

            // Generar un token de restablecimiento de contraseña (para el siguiente paso)
            $resetToken = Str::random(60);
            session(['password_reset_token' => $resetToken]);

            // Redirigir al formulario de restablecimiento
            return redirect()->route('form.reestablecerContraseña');
        }

        // Si la respuesta es incorrecta, regresamos al formulario actual
        return back()->withErrors(['respuesta' => 'La respuesta de seguridad es incorrecta.']);
    }

    /**
     * Paso 3: Muestra el formulario para restablecer la contraseña.
     */
    public function showResetForm(Request $request)
    {
        $resetToken = session('password_reset_token');
        $usuarioId = session('password_reset_user_id');

        if (!$resetToken || !$usuarioId) {
            return redirect()->route('form.olvideContraseña')->withErrors(['global' => 'Token de restablecimiento expirado o no válido. Comience el proceso de nuevo.']);
        }

        return view('auth.reset-password', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Paso 3 (POST): Procesa el restablecimiento de la contraseña.
     */
    public function resetPassword(Request $request)
    {
        // Validación de la nueva contraseña
        $request->validate([
            'reset_token' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:15',
                'confirmed',
                'regex:/^.*(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!$#%@,.]).*$/'
            ],
        ], [
            // Mensaje personalizado para la Regex
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un símbolo (! $ # % @ .).',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede exceder los 15 caracteres.',
        ]);

        // Verificación del token de sesión
        if ($request->reset_token !== session('password_reset_token')) {
            $this->clearResetSession();
            return back()->withErrors(['reset_token' => 'Error de seguridad. Por favor, intente de nuevo.']);
        }

        $usuarioId = session('password_reset_user_id');
        $usuario = Usuario::find($usuarioId);

        if (!$usuario) {
            $this->clearResetSession();
            return back()->withErrors(['global' => 'Usuario no encontrado.']);
        }

        // Restablecer la contraseña
        $usuario->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        // Limpiar la sesión de restablecimiento después de un restablecimiento exitoso
        $this->clearResetSession();

        return redirect()->route('login')->with('status', '¡Contraseña restablecida exitosamente!');
    }

    /**
     * Limpia las variables de sesión usadas en el proceso de restablecimiento.
     */
    private function clearResetSession()
    {
        session()->forget([
            'password_reset_user_id',
            'password_reset_pregunta_id',
            'password_reset_challenge_token',
            'password_reset_token'
        ]);
    }

    // Etapa 3: Actualización de preguntas de seguridad (desde el perfil)

    /**
     * Verifica la contraseña actual del usuario (POST desde el modal).
     */
    public function verifyCurrentPassword(Request $request)
    {
        $request->validate(['password' => 'required'], [], ['password' => 'Contraseña Actual']);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            // Falla si la contraseña es incorrecta
            return redirect()->route('perfil')
                ->with('error', 'Contraseña actual incorrecta. No se pudo verificar tu identidad para cambiar las preguntas de seguridad.');
        }

        // Si la contraseña es correcta
        $updateToken = Str::random(32);
        session(['security_questions_update_token' => $updateToken]);

        // Redirigir a la vista de actualización
        return redirect()->route('seguridad.form.actualizarPreguntas');
    }

    /**
     * Muestra la vista para registrar las nuevas preguntas (requiere token).
     */
    public function showUpdateForm()
    {
        // Verifica que el usuario haya pasado la confirmación de contraseña.
        if (!session('security_questions_update_token')) {
            // Si no hay token, lo enviamos de vuelta al perfil con un mensaje de error.
            return redirect()->route('perfil')
                ->with('error', 'Debes confirmar tu contraseña para actualizar las preguntas.');
        }

        // Devolvemos la nueva vista para la actualización.
        return view('auth.security-questions-update-page', [
            'num_questions' => self::NUM_QUESTIONS_REQUIRED,
            'is_update_mode' => true
        ]);
    }

    /**
     * Procesa la actualización de las preguntas
     */
    public function updateQuestions(Request $request)
    {
        // 1. Verificar el token de autorización
        if (!session('security_questions_update_token')) {
            return redirect()->route('perfil')
                ->with('error', 'Acceso no autorizado. Vuelve a intentarlo desde tu perfil.');
        }

        // Validación de las nuevas preguntas y respuestas
        $rules = [];
        $messages = [];
        for ($i = 1; $i <= self::NUM_QUESTIONS_REQUIRED; $i++) {
            $rules["pregunta_$i"] = ['required', 'string', 'max:255', 'distinct'];
            $rules["respuesta_$i"] = ['required', 'string', 'min:4', 'max:30', 'regex:/^[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/'];
            $messages["respuesta_$i.regex"] = "La respuesta #$i solo debe contener letras, números y espacios.";
            $messages["respuesta_$i.min"] = "La respuesta #$i debe tener al menos 4 caracteres.";
        }
        $request->validate($rules, $messages);

        $usuario = auth()->user();

        // 2. Eliminar las preguntas antiguas antes de crear las nuevas
        $usuario->preguntasSeguridad()->delete();

        // 3. Guardar las nuevas preguntas
        for ($i = 1; $i <= self::NUM_QUESTIONS_REQUIRED; $i++) {
            $respuestaRaw = $request->input("respuesta_$i");
            $respuestaEstandarizada = strtolower($respuestaRaw);

            $usuario->preguntasSeguridad()->create([
                'pregunta' => $request->input("pregunta_$i"),
                'respuesta' => Hash::make($respuestaEstandarizada),
            ]);
        }

        // 4. Limpiar el token de sesión después de la operación exitosa
        session()->forget('security_questions_update_token');

        return redirect()->route('perfil')->with('success', '¡Preguntas de seguridad actualizadas exitosamente!');
    }
}