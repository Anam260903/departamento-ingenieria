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
        Schema::create('materiales_informe', function (Blueprint $table) {
            $table->id('id_mater_inf');
            $table->decimal('cantidad', 10, 2);
            $table->unsignedBigInteger('id_inf');
            $table->unsignedBigInteger('id_mater');
            $table->foreign('id_inf')->references('id_inf')->on('informes');
            $table->foreign('id_mater')->references('id_mater')->on('materiales');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales_informes');
    }
};
