<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::create('processo_produtos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('processo_id')->nullable(); // se quiser vincular também ao processo

            $table->integer('adicao')->nullable();
            $table->decimal('quantidade', 15, 4)->nullable();
            $table->decimal('peso_liquido_unitario', 15, 6)->nullable();
            $table->decimal('peso_liquido_total', 15, 4)->nullable();
            $table->decimal('fator_peso', 15, 8)->nullable();
            $table->decimal('fob_unit_usd', 15, 2)->nullable();
            $table->decimal('fob_total_usd', 15, 2)->nullable();
            $table->decimal('fob_total_brl', 15, 2)->nullable();

            $table->decimal('frete_usd', 15, 2)->nullable();
            $table->decimal('frete_brl', 15, 2)->nullable();
            $table->decimal('seguro_usd', 15, 2)->nullable();
            $table->decimal('seguro_brl', 15, 2)->nullable();
            $table->decimal('acresc_frete_usd', 15, 2)->nullable();
            $table->decimal('acresc_frete_brl', 15, 2)->nullable();

            $table->decimal('thc_usd', 15, 2)->nullable();
            $table->decimal('thc_brl', 15, 2)->nullable();
            $table->decimal('valor_aduaneiro_usd', 15, 2)->nullable();
            $table->decimal('valor_aduaneiro_brl', 15, 2)->nullable();

            $table->decimal('ii_percent', 5, 2)->nullable();
            $table->decimal('ipi_percent', 5, 2)->nullable();
            $table->decimal('pis_percent', 5, 2)->nullable();
            $table->decimal('cofins_percent', 5, 2)->nullable();
            $table->decimal('icms_percent', 5, 2)->nullable();
            $table->decimal('icms_reduzido_percent', 5, 2)->nullable();
            $table->decimal('valor_ii', 15, 2)->nullable();
            $table->decimal('base_ipi', 15, 2)->nullable();
            $table->decimal('valor_ipi', 15, 2)->nullable();
            $table->decimal('base_pis_cofins', 15, 2)->nullable();
            $table->decimal('valor_pis', 15, 2)->nullable();
            $table->decimal('valor_cofins', 15, 2)->nullable();

            $table->decimal('despesa_aduaneira', 15, 2)->nullable();
            $table->decimal('base_icms_sem_reducao', 15, 2)->nullable();
            $table->decimal('valor_icms_sem_reducao', 15, 2)->nullable();
            $table->decimal('base_icms_reduzido', 15, 2)->nullable();
            $table->decimal('valor_icms_reduzido', 15, 2)->nullable();

            $table->decimal('valor_unit_nf', 15, 2)->nullable();
            $table->decimal('valor_total_nf', 15, 2)->nullable();
            $table->decimal('base_icms_st', 15, 2)->nullable();
            $table->decimal('mva', 15, 2)->nullable();
            $table->decimal('valor_icms_st', 15, 2)->nullable();
            $table->decimal('valor_total_nf_com_icms_st', 15, 2)->nullable();

            $table->decimal('fator_valor_fob', 15, 8)->nullable();
            $table->decimal('fator_tx_siscomex', 15, 8)->nullable();
            $table->decimal('multa', 15, 2)->nullable();
            $table->decimal('tx_def_li', 15, 2)->nullable();
            $table->decimal('taxa_siscomex', 15, 2)->nullable();
            $table->decimal('outras_taxas_agente', 15, 2)->nullable();
            $table->decimal('liberacao_bl', 15, 2)->nullable();
            $table->decimal('desconsolidacao', 15, 2)->nullable();
            $table->decimal('isps_code', 15, 2)->nullable();
            $table->decimal('handling', 15, 2)->nullable();
            $table->decimal('capatazia', 15, 2)->nullable();
            $table->decimal('afrmm', 15, 2)->nullable();
            $table->decimal('armazenagem_sts', 15, 2)->nullable();
            $table->decimal('frete_dta_sts_ana', 15, 2)->nullable();
            $table->decimal('sda', 15, 2)->nullable();
            $table->decimal('rep_sts', 15, 2)->nullable();
            $table->decimal('armaz_ana', 15, 2)->nullable();
            $table->decimal('lavagem_container', 15, 2)->nullable();
            $table->decimal('rep_anapolis', 15, 2)->nullable();
            $table->decimal('li_dta_honor_nix', 15, 2)->nullable();
            $table->decimal('honorarios_nix', 15, 2)->nullable();
            $table->decimal('desp_desenbaraco', 15, 2)->nullable();
            $table->decimal('diferenca_cambial_frete', 15, 2)->nullable();
            $table->decimal('diferenca_cambial_fob', 15, 2)->nullable();
            $table->decimal('custo_unitario_final', 15, 2)->nullable();
            $table->decimal('custo_total_final', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('processo_produtos');
    }
};
