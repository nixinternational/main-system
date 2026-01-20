<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_produto_multa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('processo_id')->nullable();

            $table->integer('adicao')->nullable();
            $table->integer('item')->nullable();
            $table->decimal('quantidade', 20, 5)->nullable();
            $table->decimal('peso_liquido_unitario', 20, 5)->nullable();
            $table->decimal('peso_liquido_total', 20, 5)->nullable();
            $table->decimal('fator_peso', 20, 5)->nullable();

            $table->decimal('fob_unit_usd', 20, 7)->nullable();
            $table->decimal('fob_total_usd', 20, 7)->nullable();
            $table->decimal('fob_total_brl', 20, 7)->nullable();

            $table->decimal('service_charges', 20, 7)->nullable();
            $table->decimal('service_charges_brl', 20, 7)->nullable();

            $table->decimal('frete_usd', 20, 7)->nullable();
            $table->decimal('frete_brl', 20, 7)->nullable();
            $table->decimal('acresc_frete_usd', 20, 7)->nullable();
            $table->decimal('acresc_frete_brl', 20, 7)->nullable();

            $table->decimal('vlr_crf_unit', 20, 7)->nullable();
            $table->decimal('vlr_crf_total', 20, 7)->nullable();

            $table->decimal('seguro_usd', 20, 7)->nullable();
            $table->decimal('seguro_brl', 20, 7)->nullable();

            $table->decimal('thc_usd', 20, 7)->nullable();
            $table->decimal('thc_brl', 20, 7)->nullable();

            $table->decimal('valor_aduaneiro_usd', 20, 7)->nullable();
            $table->decimal('valor_aduaneiro_brl', 20, 7)->nullable();

            $table->decimal('ii_percent', 20, 7)->nullable();
            $table->decimal('ipi_percent', 20, 7)->nullable();
            $table->decimal('pis_percent', 20, 7)->nullable();
            $table->decimal('cofins_percent', 20, 7)->nullable();
            $table->decimal('icms_percent', 20, 7)->nullable();
            $table->decimal('icms_reduzido_percent', 20, 7)->nullable();
            $table->decimal('reducao', 20, 7)->nullable();

            $table->decimal('valor_ii', 20, 7)->nullable();
            $table->decimal('base_ipi', 20, 7)->nullable();
            $table->decimal('valor_ipi', 20, 7)->nullable();
            $table->decimal('base_pis_cofins', 20, 7)->nullable();
            $table->decimal('valor_pis', 20, 7)->nullable();
            $table->decimal('valor_cofins', 20, 7)->nullable();
            $table->decimal('despesa_aduaneira', 20, 7)->nullable();

            $table->decimal('vlr_ii_pos_despesa', 20, 7)->nullable();
            $table->decimal('vlr_ipi_pos_despesa', 20, 7)->nullable();
            $table->decimal('vlr_pis_pos_despesa', 20, 7)->nullable();
            $table->decimal('vlr_cofins_pos_despesa', 20, 7)->nullable();

            $table->string('nova_ncm')->nullable();
            $table->decimal('ii_nova_ncm_percent', 20, 7)->nullable();
            $table->decimal('ipi_nova_ncm_percent', 20, 7)->nullable();
            $table->decimal('pis_nova_ncm_percent', 20, 7)->nullable();
            $table->decimal('cofins_nova_ncm_percent', 20, 7)->nullable();
            $table->decimal('vlr_ii_nova_ncm', 20, 7)->nullable();
            $table->decimal('vlr_ipi_nova_ncm', 20, 7)->nullable();
            $table->decimal('vlr_pis_nova_ncm', 20, 7)->nullable();
            $table->decimal('vlr_cofins_nova_ncm', 20, 7)->nullable();

            $table->decimal('vlr_ii_recalc', 20, 7)->nullable();
            $table->decimal('vlr_ipi_recalc', 20, 7)->nullable();
            $table->decimal('vlr_pis_recalc', 20, 7)->nullable();
            $table->decimal('vlr_cofins_recalc', 20, 7)->nullable();

            $table->decimal('valor_aduaneiro_multa', 20, 7)->nullable();
            $table->decimal('ii_percent_aduaneiro', 20, 7)->nullable();
            $table->decimal('ipi_percent_aduaneiro', 20, 7)->nullable();
            $table->decimal('pis_percent_aduaneiro', 20, 7)->nullable();
            $table->decimal('cofins_percent_aduaneiro', 20, 7)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_produto_multa');
    }
};
