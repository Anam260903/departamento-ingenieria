<?php

namespace Database\Factories;

use App\Models\propietario;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropietarioFactory extends Factory
{
    protected $model = propietario::class;

    public function definition()
    {
        return [
            // Genera una cédula de 8 dígitos aleatorios no repetitivos para pasar la regex
            'cedula_propie' => (string) $this->faker->unique()->numberBetween(10000000, 29999999),
            'nombre_propie' => $this->faker->firstName(),
            'apellido_propie' => $this->faker->lastName(),
            'telefono' => '04121274597',
        ];
    }
}