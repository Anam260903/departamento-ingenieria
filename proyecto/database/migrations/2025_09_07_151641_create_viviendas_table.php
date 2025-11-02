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
        Schema::create('viviendas', function (Blueprint $table) {
            $table->id('id_viv');
            $table->string('direccion', 100);
            $table->text('caracteristicas')->nullable();;
            $table->decimal('latitud', 10, 8)->nullable(); 
            $table->decimal('longitud', 11, 8)->nullable(); 
            $table->string('map_image_file', 255)->nullable();
            $table->unsignedBigInteger('id_propie');
            $table->foreign('id_propie')->references('id_propie')->on('propietarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viviendas');
    }
};
