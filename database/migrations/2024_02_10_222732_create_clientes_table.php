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
            
            $table->string('nome_responsavel_legal')->nullable();;
            $table->string('cpf_responsavel_legal')->nullable();;
            $table->string('telefone_responsavel_legal')->nullable();;
            $table->string('email_responsavel_legal')->nullable();;

            $table->date('data_procuracao')->nullable();
            $table->date('data_vencimento_procuracao')->nullable();

            $table->date('marinha_mercante_inicial')->nullable();
            $table->date('credenciamento_radar_inicial')->nullable();
            $table->date('afrmm_bb_inicial')->nullable();
            $table->date('credenciamento_radar_final')->nullable();
            $table->date('marinha_mercante_final')->nullable();
            $table->date('afrmm_bb_final')->nullable();

            $table->boolean('itau_di')->default(false);
            $table->enum('modalidade_radar',['expresso','limitado','ilimitado'])->nullable();
            $table->string('beneficio_fiscal',255)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('clientes_responsaveis', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('departamento');

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->on('clientes')->references('id');
            $table->timestamps();

        });
        Schema::create('clientes_aduanas', function (Blueprint $table) {
            $table->id();
            $table->string('urf_despacho',255);
            $table->enum('modalidade',['aereo','maritima','rodoviaria','multimodal','courier'])->nullable();
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->on('clientes')->references('id');
            $table->timestamps();

        });
        Schema::create('clientes_bancos', function (Blueprint $table) {
            $table->id();
            $table->string('banco',255);
            $table->string('agencia',255);
            $table->string('conta_corrente',255);
            $table->string('numero_banco',255);
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
