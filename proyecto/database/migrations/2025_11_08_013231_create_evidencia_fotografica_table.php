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
        Schema::create('evidencia_fotografica', function (Blueprint $table) {
            $table->id('id_evid');
            $table->string('ruta_archivo', 255)->nullable();
            $table->unsignedBigInteger('id_inf');
            $table->foreign('id_inf')->references('id_inf')->on('informes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencia_fotografica');
    }
};
