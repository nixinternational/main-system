<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atributo_detalhes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('atributo_ncm_id');
            $table->foreign('atributo_ncm_id')->references('id')->on('atributos');
            $table->string('codigo')->unique();
            $table->jsonb('dados'); // Armazena o JSON completo
            $table->timestamps();
        });
 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atributo_detalhes');
    }
};
