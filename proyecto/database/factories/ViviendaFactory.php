<?php

namespace Database\Factories;

use App\Models\vivienda;
use App\Models\propietario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ViviendaFactory extends Factory
{
    protected $model = vivienda::class;

    public function definition()
    {
        return [
            'direccion' => $this->faker->address(),
            'id_propie' => propietario::factory(), // Crea un propietario al crear la vivienda
            'latitud' => 10.4880,
            'longitud' => -66.8792,
        ];
    }
}