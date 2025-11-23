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
        Schema::create('processo_rodoviarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno');
            $table->string('di')->nullable();
            $table->string('numero_processo')->nullable();
            $table->decimal('valor_fob', 15, 2)->nullable();
            $table->decimal('valor_exw', 15, 2)->nullable();
            $table->decimal('valor_exw_brl', 15, 2)->nullable();
            $table->decimal('frete_internacional', 15, 2)->nullable();
            $table->decimal('seguro_internacional', 15, 2)->nullable();
            $table->decimal('acrescimo_frete', 15, 2)->nullable();
            $table->decimal('valor_cif', 15, 2)->nullable();
            $table->decimal('multa', 15, 2)->nullable();

            $table->decimal('taxa_dolar', 10, 4)->nullable();
            $table->decimal('service_charges', 15, 2)->nullable();
            $table->string('service_charges_moeda', 3)->nullable();
            $table->decimal('service_charges_usd', 15, 7)->nullable();
            $table->decimal('service_charges_brl', 15, 7)->nullable();
            $table->decimal('cotacao_service_charges', 15, 4)->nullable();

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
            $table->foreignId('fornecedor_id')
                ->nullable()
                ->constrained('fornecedors')
                ->nullOnDelete();

            $table->string('frete_internacional_moeda')->nullable();
            $table->string('seguro_internacional_moeda')->nullable();
            $table->string('acrescimo_frete_moeda')->nullable();
            $table->string('descricao', 120)->nullable();
            $table->string('status', 120)->nullable();
            $table->string('canal', 120)->nullable();
            $table->string('data_desembaraco_inicio')->nullable();
            $table->string('data_desembaraco_fim')->nullable();

            $table->decimal('cotacao_frete_internacional', 15, 4)->nullable();
            $table->decimal('cotacao_seguro_internacional', 15, 4)->nullable();
            $table->decimal('cotacao_acrescimo_frete', 15, 6)->nullable();
            $table->date('data_moeda_frete_internacional')->nullable();
            $table->date('data_moeda_seguro_internacional')->nullable();
            $table->date('data_moeda_acrescimo_frete')->nullable();
            $table->json('cotacao_moeda_processo')->nullable();
            $table->date('data_cotacao_processo')->nullable();
            $table->string('moeda_processo', 3)->nullable();

            $table->decimal('diferenca_cambial_frete', 15, 2)->nullable();
            $table->decimal('diferenca_cambial_fob', 15, 2)->nullable();

            // Campos de despesas
            $table->decimal('outras_taxas_agente', 15, 2)->nullable();
            $table->decimal('liberacao_bl', 15, 2)->nullable();
            $table->decimal('desconsolidacao', 15, 2)->nullable();
            $table->decimal('isps_code', 15, 2)->nullable();
            $table->decimal('handling', 15, 2)->nullable();
            $table->decimal('dai', 15, 2)->nullable();
            $table->decimal('dape', 15, 2)->nullable();
            $table->decimal('correios', 15, 2)->nullable();
            $table->decimal('li_dta_honor_nix', 15, 2)->nullable();
            $table->decimal('honorarios_nix', 15, 2)->nullable();

            // Campos específicos rodoviário
            $table->decimal('desp_fronteira', 15, 2)->nullable();
            $table->decimal('das_fronteira', 15, 2)->nullable();
            $table->decimal('armazenagem', 15, 2)->nullable();
            $table->decimal('frete_foz_gyn', 15, 2)->nullable();
            $table->decimal('rep_fronteira', 15, 2)->nullable();
            $table->decimal('armaz_anapolis', 15, 2)->nullable();
            $table->decimal('mov_anapolis', 15, 2)->nullable();
            $table->decimal('rep_anapolis', 15, 2)->nullable();

            $table->string('nacionalizacao')->default('outros');
            $table->string('transportadora_nome')->nullable();
            $table->string('transportadora_endereco')->nullable();
            $table->string('transportadora_municipio')->nullable();
            $table->string('transportadora_cnpj', 32)->nullable();
            $table->text('info_complementar_nf')->nullable();
            $table->decimal('thc_capatazia', 15, 2)->nullable();

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
        Schema::dropIfExists('processo_rodoviarios');
    }
};
