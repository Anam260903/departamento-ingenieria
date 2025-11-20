<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiamos la tabla para evitar duplicados si se ejecuta varias veces
        DB::table('roles')->truncate(); 

        // Insertamos los roles
        DB::table('roles')->insert([
            [
                'id_rol' => 1,
                'nombre_rol' => 'Administrador',
            ],
            [
                'id_rol' => 2,
                'nombre_rol' => 'Usuario Básico',
            ],
        ]);

    }
}
