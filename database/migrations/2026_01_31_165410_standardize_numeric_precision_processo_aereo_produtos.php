<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ============================================
        // TABELA: processo_aereo_produtos
        // Alterar apenas campos com ≤7 casas decimais para numeric(15, 7)
        // MANTER campos com 8+ casas decimais (fator_peso, fator_valor_fob, fator_tx_siscomex)
        // ============================================
        
        // Campos de Quantidade e Peso
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN quantidade TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liquido_unitario TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liquido_total TYPE numeric(15, 7);');
        // fator_peso: MANTER numeric(15, 8) - NÃO ALTERAR
        
        // Campos FOB
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_unit_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_total_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_total_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_unit_moeda_estrangeira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_total_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Frete
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Seguro
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN seguro_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN seguro_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN seguro_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Acréscimo
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN acrescimo_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Delivery Fee e Collect Fee
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN delivery_fee TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN delivery_fee_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN collect_fee TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN collect_fee_brl TYPE numeric(15, 7);');
        
        // Campos CFR/CIF
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_crf_total TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_crf_unit TYPE numeric(15, 7);');
        
        // Campos Valor Aduaneiro
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(15, 7);');
        
        // Campos Percentuais (de numeric(5, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN ii_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN ipi_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN pis_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN cofins_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN icms_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN icms_reduzido_percent TYPE numeric(15, 7);');
        
        // Campos Impostos - Valores
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_ii TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_pis_cofins TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_pis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_cofins TYPE numeric(15, 7);');
        
        // Campos Despesas Aduaneiras
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(15, 7);');
        
        // Campos ICMS
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(15, 7);');
        
        // Campos Nota Fiscal
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_unit_nf TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_total_nf TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN mva TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(15, 7);');
        
        // Campos Multas e Taxas
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN multa TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN tx_def_li TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN taxa_siscomex TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 7);');
        
        // Campos Despesas Gerais
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN liberacao_bl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN desconsolidacao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN isps_code TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN handling TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN dai TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN dape TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN correios TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN honorarios_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN desp_desenbaraco TYPE numeric(15, 7);');
        
        // Campos THC
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN thc_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN thc_brl TYPE numeric(15, 7);');
        
        // Campos Diferença Cambial
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 7);');
        
        // Campos Custo Final
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN custo_unitario_final TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN custo_total_final TYPE numeric(15, 7);');
        
        // Campos Service Charges
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN service_charges TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN service_charges_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN service_charges_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Opcionais
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 7);');
        
        // Campos Específicos Aéreo
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN rep_itj TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_nvg_x_gyn TYPE numeric(15, 7);');
        
        // Campos de Peso Adicionais
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liq_lbs TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liq_total_kg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liq_kg TYPE numeric(15, 7);');
        
        // NOTA: fator_peso, fator_valor_fob, fator_tx_siscomex são numeric(15, 8) - NÃO ALTERAR
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverter para os valores anteriores (numeric(15, 6) para a maioria)
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN quantidade TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liquido_unitario TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liquido_total TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_unit_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_total_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_total_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_unit_moeda_estrangeira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN fob_total_moeda_estrangeira TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_moeda_estrangeira TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN seguro_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN seguro_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN seguro_moeda_estrangeira TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN acrescimo_moeda_estrangeira TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN delivery_fee TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN delivery_fee_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN collect_fee TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN collect_fee_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_crf_total TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN vlr_crf_unit TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN ii_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN ipi_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN pis_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN cofins_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN icms_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN icms_reduzido_percent TYPE numeric(5, 2);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_ii TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_pis_cofins TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_pis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_cofins TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_unit_nf TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_total_nf TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN base_icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN mva TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN multa TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN tx_def_li TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN taxa_siscomex TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN liberacao_bl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN desconsolidacao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN isps_code TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN handling TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN dai TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN dape TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN correios TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN honorarios_nix TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN desp_desenbaraco TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN thc_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN thc_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN custo_unitario_final TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN custo_total_final TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN service_charges TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN service_charges_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN service_charges_moeda_estrangeira TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN rep_itj TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN frete_nvg_x_gyn TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liq_lbs TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liq_total_kg TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereo_produtos ALTER COLUMN peso_liq_kg TYPE numeric(15, 6);');
    }
};
