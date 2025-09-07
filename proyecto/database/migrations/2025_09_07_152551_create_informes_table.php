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
        Schema::create('informes', function (Blueprint $table) {
            $table->id('id_inf');
            $table->date('fecha_inf');
            $table->text('comunidad');
            $table->text('antecedentes');
            $table->text('planteamiento');
            $table->text('resultados');
            $table->text('recomendacion');
            $table->unsignedBigInteger('id_insp');
            $table->foreign('id_insp')->references('id_insp')->on('inspecciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informes');
    }
};
