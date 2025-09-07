<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table)  {
            $table->id('id_user');
            $table->string('cedula_user', 8)->unique();
            $table->string('nombre', 15);
            $table->string('apellido', 15);
            $table->string('correo', 40)->unique();
            $table->string('password'); // Contraseña hasheada, VARCHAR(255)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
