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
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->id('id_insp');
            $table->date('fecha_insp');
            $table->string('estado_insp', 1);
            $table->string('observacion', 50)->nullable();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_viv');
            $table->foreign('id_user')->references('id_user')->on('usuarios');
            $table->foreign('id_viv')->references('id_viv')->on('viviendas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
    }
};
