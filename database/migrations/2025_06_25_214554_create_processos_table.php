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
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno');
            $table->string('di')->nullable();
            $table->string('numero_processo')->nullable();
            $table->decimal('valor_fob', 15, 2)->nullable();
            $table->decimal('frete_internacional', 15, 2)->nullable();
            $table->decimal('seguro_internacional', 15, 2)->nullable();
            $table->decimal('acrescimo_frete', 15, 2)->nullable();
            $table->decimal('valor_cif', 15, 2)->nullable();

            $table->decimal('taxa_dolar', 10, 4)->nullable();
            $table->decimal('thc_capatazia', 15, 2)->nullable();

            $table->decimal('peso_bruto', 15, 4)->nullable();
            $table->decimal('peso_liquido', 15, 4)->nullable();

            $table->decimal('ii', 15, 2)->nullable();
            $table->decimal('ipi', 15, 2)->nullable();
            $table->decimal('pis', 15, 2)->nullable();
            $table->decimal('cofins', 15, 2)->nullable();
            $table->decimal('despesas_aduaneiras', 15, 2)->nullable();

            $table->integer('quantidade')->nullable();
            $table->string('especie')->nullable();
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');
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
        Schema::dropIfExists('processos');
    }
};
