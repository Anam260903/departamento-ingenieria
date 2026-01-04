<?php

namespace Database\Factories;

use App\Models\recursos;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecursosFactory extends Factory
{
    protected $model = recursos::class;

    public function definition()
    {
        return [
            'codigo' => 'REC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nombre_rec' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'observacion' => $this->faker->sentence(),
        ];
    }
}