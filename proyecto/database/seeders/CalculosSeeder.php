<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Calculos;

class CalculosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Primer cálculo predeterminado
        Calculos::create([
            'nombre_calculo' => '1 Casa',
            'contenido' => 'Este es el primer cálculo de prueba',
        ]);

        // Segundo cálculo predeterminado
        Calculos::create([
            'nombre_calculo' => '1/2 Casa',
            'contenido' => 'Este es el segundo cálculo de prueba',
        ]);

        Calculos::create([
            'nombre_calculo' => '1/4 Casa',
            'contenido' => 'Este es el tercer cálculo de prueba',
        ]);

    }
}