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
     * Muestra la lista completa de notificaciones
     */
    public function index()
    {
        $notificaciones = Notificacion::where('id_user', Auth::id())
            ->orderBy('leida', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20); // Paginación para ver todas

        Notificacion::where('id_user', Auth::id())->update(['leida' => true]);

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