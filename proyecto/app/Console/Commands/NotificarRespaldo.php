<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Notificacion;

class NotificarRespaldo extends Command
{
    protected $signature = 'respaldo:notificar';
    protected $description = 'Envíar una notificación al administrador para realizar el respaldo semanal';

    public function handle()
    {
        // 1. Buscar al administrador específico por su correo
        $admin = Usuario::where('correo', 'admin@gmail.com')->first();

        if ($admin) {
            // 2. Crear la notificación usando tu estructura actual
            Notificacion::create([
                'id_user' => $admin->id_user,
                'mensaje' => 'Recordatorio semanal: Es momento de realizar un respaldo de seguridad de la base de datos.',
                'tipo' => 'recordatorio_respaldo',
                'leida' => false,
            ]);

            $this->info('Notificación de respaldo enviada al administrador.');
        } else {
            $this->error('No se encontró al usuario con el correo admin@gmail.com.');
        }
    }
}