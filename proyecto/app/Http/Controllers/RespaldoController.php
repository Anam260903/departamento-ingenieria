<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class RespaldoController extends Controller
{
    public function backup()
    {
        // 1. Obtener las credenciales de la base de datos desde el archivo .env
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        // 2. Establecer el nombre del archivo de respaldo
        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';

        // 3. Establecer la ruta de guardado dentro del sistema de archivos de Laravel
        $storagePath = 'backups/' . $filename;

        // 4. Construir el comando mysqldump
        // Nota: En XAMPP, mysqldump se encuentra típicamente en 'C:\xampp\mysql\bin\mysqldump.exe'
        // Asegúrate de que esta ruta sea correcta para tu instalación de XAMPP.
        $dumpPath = env('MYSQLDUMP_PATH', 'C:\xampp\mysql\bin\mysqldump.exe');

        // Comando mysqldump: 
        // -u [usuario] -p[contraseña] [nombre_db] > [ruta_archivo]
        $command = $dumpPath . ' -u' . $username .
            ' ' . (empty($password) ? '' : '-p' . $password) .
            ' ' . $database;

        // 5. Ejecutar el comando y capturar la salida
        try {
            // Ejecuta el comando y pasa la salida directamente al contenido del archivo
            $result = Process::run($command);

            if ($result->successful()) {
                // Guarda el contenido (el SQL dump) en el disco de Laravel
                Storage::put($storagePath, $result->output());

                // Devuelve el archivo para su descarga inmediata
                return Storage::download($storagePath, $filename);
            } else {
                // Si el comando falló (ej. mysqldump no encontrado o credenciales incorrectas)
                return back()->with('error', 'El respaldo de la base de datos falló. Mensaje: ' . $result->errorOutput());
            }

        } catch (\Exception $e) {
            // Manejo de otras excepciones (ej. si Process no existe)
            return back()->with('error', 'Ocurrió un error en el proceso de respaldo: ' . $e->getMessage());
        }
    }
}