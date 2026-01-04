<?php

namespace Database\Factories;

use App\Models\asignacion_recursos;
use App\Models\recursos;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsignacionRecursosFactory extends Factory
{
    protected $model = asignacion_recursos::class;

    public function definition()
    {
        return [
            'id_user' => Usuario::factory(),
            'id_recurso' => recursos::factory(),
            'fecha_asignacion' => now(),
            'fecha_devolucion' => null, // Activo por defecto
        ];
    }
}