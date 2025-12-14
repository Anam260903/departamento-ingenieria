<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Marca una notificación como leída
     */
    public function markAsRead(Notificacion $notificacion)
    {
        // Asegura que el usuario autenticado es dueño de la notificación
        if ($notificacion->id_user !== Auth::id()) {
            abort(403);
        }

        $notificacion->leida = true;
        $notificacion->save();

        return back();
    }

    /**
     * Muestra la lista completa de notificaciones, limitada a los últimos 50 registros.
     */
    public function index()
    {
        $notificaciones = Notificacion::where('id_user', Auth::id())
            // 1. Ordena los registros: no leídas primero, luego por fecha descendente
            ->orderBy('leida', 'asc')
            ->orderBy('created_at', 'desc')
            // 2. Limita la consulta a los primeros 50 registros de ese resultado
            ->take(50)
            // 3. Aplicar paginación
            ->paginate(10);

        return view('notificaciones', compact('notificaciones'));
    }

    public function markAllAsRead()
    {
        Notificacion::where('id_user', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return redirect()->route('notifications.index')->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }
}