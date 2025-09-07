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
        Schema::create('materiales', function (Blueprint $table) {
            $table->id('id_mater');
            $table->string('nombre_mater', 15);
            $table->string('unidad_med', 10);
            $table->decimal('ancho', 10, 2)->nullable();
            $table->decimal('largo', 10, 2)->nullable();
            $table->decimal('alto', 10, 2)->nullable();
            $table->decimal('espesor', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
