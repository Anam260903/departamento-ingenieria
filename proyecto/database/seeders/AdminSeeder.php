<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos del usuario administrador
        Usuario::create([
            'cedula_user' => '00000000',
            'nombre' => 'Admin',
            'apellido' => 'Principal',
            'correo' => 'admin@gmail.com',
            'password' => Hash::make('Admin1234'),
            'profesion' => null,
            'estado_user' => 1 ,
            'id_rol' => 1,
        ]);

    }
}
