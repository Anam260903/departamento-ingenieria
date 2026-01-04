<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        return [
            'cedula_user' => $this->faker->unique()->numerify('########'),
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'correo' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('secret'),
            'profesion' => 'Ingeniero',
            'estado_user' => 1,
            'id_rol' => 1, // 1 para Administrador por defecto
        ];
    }
}