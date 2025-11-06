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
        Schema::create('calculos', function (Blueprint $table) {
            $table->id( 'id_calculo');
            $table->string('codigo_calculo', 10)->unique();;
            $table->text('contenido');
            $table->date('fecha_creacion');
            $table->unsignedBigInteger('id_cate');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_cate')->references('id_cate')->on('categorias')->onDelete('restrict'); // No se puede borrar una categoría si tiene cálculos asociados
            $table->foreign('id_user')->references('id_user')->on('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculos');
    }
};
