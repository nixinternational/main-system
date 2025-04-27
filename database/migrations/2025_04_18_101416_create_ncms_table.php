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
        Schema::create('ncms', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->text('descricao');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->string('tipo_ato_ini');
            $table->string('numero_ato_ini');
            $table->string('ano_ato_ini');
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
        Schema::dropIfExists('ncms');
    }
};
