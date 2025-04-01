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
        Schema::create('banco_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('agencia');
            $table->string('conta_corrente');
            $table->string('numero_banco');
            $table->boolean('banco_nix')->default(false);
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->on('clientes')->references('id');
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
        Schema::dropIfExists('banco_clientes');
    }
};
