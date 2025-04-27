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

        // Schema::create('produto_descricaos', function (Blueprint $table) {
        //     // $table->id();
        //     // $table->unsignedBigInteger('produto_id');
        //     // $table->foreign('produto_id')->references('id')->on('produtos');
        //     // $table->string('observacoes');
        //     // $table->unsignedBigInteger('ncm_id');
        //     // $table->string('codigo_interno')->nullable();
        //     // $table->foreign('ncm_id')->references('id')->on('ncms');
        //     // $table->enum('modalidade',['IMPORTACAO','EXPORTACAO'])->default('IMPORTACAO');
        //     // $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('produto_descricaos');
    }
};
