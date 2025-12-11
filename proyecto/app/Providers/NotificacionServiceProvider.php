<?php

namespace App\Providers;

use App\Models\Notificacion;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class NotificacionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('components._navbar', function ($view) {

            // Solo si el usuario está autenticado
            if (Auth::check()) {
                $user = Auth::user();

                // Obtener las 7 notificaciones más recientes (no leídas primero)
                $notificaciones = Notificacion::where('id_user', $user->id_user)
                    ->orderBy('leida', 'asc') // Las no leídas primero
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $unreadCount = $notificaciones->where('leida', false)->count();

                $view->with('notificaciones', $notificaciones);
                $view->with('unreadCount', $unreadCount);
            }
        });
    }

    public function register()
    {
        //
    }
}