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
            $table->text('antecedentes')->nullable();
            $table->text('planteamiento')->nullable();
            $table->text('resultados')->nullable();
            $table->text('recomendacion')->nullable();
            $table->text('materials_info')->nullable();
            $table->unsignedBigInteger('id_insp');
            $table->foreign('id_insp')->references('id_insp')->on('inspecciones');
            $table->unsignedBigInteger('id_calculo')->nullable()->default(null);
            $table->foreign('id_calculo')->references('id_calculo')->on('calculos');
            $table->timestamp('deleted_at')->nullable()->default(null);
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
