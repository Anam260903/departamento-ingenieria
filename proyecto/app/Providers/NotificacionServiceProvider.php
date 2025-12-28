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

                // 1. Obtener solo las 4 que se mostrarán en el dropdown
                $notificaciones = Notificacion::where('id_user', $user->id_user)
                    ->orderBy('leida', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();

                // 2. Contar el total de no leídas en la DB
                $unreadCount = Notificacion::where('id_user', $user->id_user)
                    ->where('leida', false)
                    ->count();

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