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
            $table->string('cedula_user', 15)->unique();
            $table->string('nombre', 30);
            $table->string('apellido', 30);
            $table->string('correo', 40)->unique();
            $table->string('password'); // Contraseña hasheada, VARCHAR(255)
            $table->string('profesion', 30)->nullable();
            $table->string('estado_user', 1);
            $table->unsignedBigInteger('id_rol');
            $table->foreign('id_rol')->references('id_rol')->on('roles');
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
