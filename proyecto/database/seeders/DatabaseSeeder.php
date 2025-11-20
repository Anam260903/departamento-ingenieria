<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Deshabilitar la verificación de claves foráneas
        Schema::disableForeignKeyConstraints();

        // LLamar a los seeder
        $this->call([
            RolesSeeder::class,
            AdminSeeder::class,
            CalculosSeeder::class,
        ]);

        // Habilitar nuevamente las restricciones de claves foráneas
        Schema::enableForeignKeyConstraints();

    }
}
