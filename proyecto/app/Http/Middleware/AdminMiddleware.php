<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar si el usuario está autenticado
        if (Auth::check()) {

            // 2. Verificar si el id_rol es igual a 1 (ADMINISTRADOR)
            if (Auth::user()->id_rol == 1) {
                // Si es administrador, permitir la solicitud
                return $next($request);
            }

            // 3. Si no es administrador, redirigir o abortar
            return redirect('/dashboard')->with('error', 'Acceso denegado. Se requiere rol de administrador.');
        }

        // Si no está autenticado, redirigir al login
        return redirect('/login');
    }
}