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
        Schema::create('atributos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); 
            $table->foreign('ncm_id')->references(columns: 'id')->on('ncms');
            $table->unsignedBigInteger('ncm_id');
            $table->string('modalidade');
            $table->boolean('obrigatorio');
            $table->boolean('multivalorado');
            $table->date('data_inicio_vigencia');
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
        Schema::dropIfExists('atributos');
    }
};
