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
        Schema::create('asignacion_recursos', function (Blueprint $table) {
            $table->id('id_asignacion');
            $table->date('fecha_asignacion');
            $table->date('fecha_devolucion')->nullable();
            $table->unsignedBigInteger('id_recurso');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_recurso')->references('id_recurso')->on('recursos')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('usuarios')->onDelete('cascade');            
            $table->timestamps();
        });
    }

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_recursos');
    }
};
