<?php

namespace Database\Factories;

use App\Models\Inspeccion;
use App\Models\vivienda;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspeccionFactory extends Factory
{
    protected $model = Inspeccion::class;

    public function definition()
    {
        return [
            'fecha_insp' => now()->format('Y-m-d'),
            'estado_insp' => 1,
            'observacion' => $this->faker->sentence(),
            'id_user' => Usuario::factory(), // Crea el usuario relacionado
            'id_viv' => vivienda::factory(), // Crea la vivienda relacionada
        ];
    }
}