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
        Schema::create('calculo_informes', function (Blueprint $table) {
            
            $table->unsignedBigInteger('id_calculo');
            $table->unsignedBigInteger('id_inf');
            $table->foreign('id_calculo')->references('id_calculo')->on('calculos')->onDelete('restrict');;
            $table->foreign('id_inf')->references('id_inf')->on('informes')->onDelete('cascade');;
            $table->primary(['id_inf', 'id_calculo']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculo_informes');
    }
};
