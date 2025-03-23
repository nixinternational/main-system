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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cnpj');
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('cep')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('nome_responsavel_legal');
            $table->string('cpf_responsavel_legal');
            $table->date('data_vencimento_procuracao')->nullable();
            $table->boolean('despachante_siscomex')->default(false);
            $table->boolean('marinha_mercante')->default(false);
            $table->boolean('afrmm')->default(false);
            $table->boolean('itau_di')->default(false);
            $table->enum('modalidade_radar',['expresso','limitado','ilimitado'])->nullable();
            $table->string('beneficio_fiscal',255)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('clientes_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->on('clientes')->references('id');
            $table->timestamps();

        });
        Schema::create('clientes_responsaveis', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->on('clientes')->references('id');
            $table->timestamps();

        });
        Schema::create('clientes_aduanas', function (Blueprint $table) {
            $table->id();
            $table->string('nome',255);
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
        Schema::dropIfExists('clientes_emails');
        Schema::dropIfExists('clientes_responsaveis');
        Schema::dropIfExists('clientes_aduanas');
        Schema::dropIfExists('clientes');
    }
};
