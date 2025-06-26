<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('processo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->unsignedBigInteger('processo_id');
            $table->foreign('processo_id')->references('id')->on('processos');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('processo_documentos');
    }
};
