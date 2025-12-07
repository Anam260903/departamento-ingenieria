<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('preguntas_seguridad', function (Blueprint $table) {
            $table->id('id_preg');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('usuarios')->onDelete('cascade');
            $table->string('pregunta');
            $table->string('respuesta');
            $table->timestamps();

            // Restricción para asegurar que un usuario solo pueda tener un número limitado de preguntas
            $table->unique(['id_user', 'pregunta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas_seguridad');
    }
};