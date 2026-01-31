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
        // TABELA: processo_rodoviario_produtos
        // Alterar apenas campos com ≤7 casas decimais para numeric(15, 7)
        // MANTER fator_peso, fator_valor_fob, fator_tx_siscomex como numeric(15, 8) - NÃO ALTERAR
        // ============================================
        
        // Campos de Quantidade e Peso (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN quantidade TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liq_lbs TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liquido_unitario TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liquido_total TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liq_total_kg TYPE numeric(15, 7);');
        // fator_peso: MANTER numeric(15, 8) - NÃO ALTERAR
        
        // Campos FOB (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_unit_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_brl TYPE numeric(15, 7);');
        
        // Campos Frete (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_brl TYPE numeric(15, 7);');
        
        // Campos Seguro (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_brl TYPE numeric(15, 7);');
        
        // Campos Acréscimo (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(15, 7);');
        
        // Campos THC (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_brl TYPE numeric(15, 7);');
        
        // Campos CFR/CIF (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_total TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_unit TYPE numeric(15, 7);');
        
        // Campos Valor Aduaneiro (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(15, 7);');
        
        // Campos Percentuais (de numeric(5, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN ii_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN ipi_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN pis_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN cofins_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_reduzido_percent TYPE numeric(15, 7);');
        
        // Campos Impostos - Valores (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ii TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_pis_cofins TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_pis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_cofins TYPE numeric(15, 7);');
        
        // Campos Despesas Aduaneiras (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(15, 7);');
        
        // Campos ICMS (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(15, 7);');
        
        // Campos Nota Fiscal (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_unit_nf TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mva TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(15, 7);');
        
        // Campos Multas e Taxas (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN multa TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN tx_def_li TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN taxa_siscomex TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 7);');
        
        // Campos Despesas Gerais (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN liberacao_bl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desconsolidacao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN isps_code TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN handling TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dai TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dai_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dape TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dape_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN correios TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN honorarios_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desp_desenbaraco TYPE numeric(15, 7);');
        
        // Campos Específicos Rodoviário (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desp_fronteira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN das_fronteira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armazenagem TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_foz_gyn TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_fronteira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armaz_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mov_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_anapolis TYPE numeric(15, 7);');
        
        // Campos Diferença Cambial (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 7);');
        
        // Campos Custo Final (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_unitario_final TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_total_final TYPE numeric(15, 7);');
        
        // Campos Service Charges (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges_brl TYPE numeric(15, 7);');
        
        // Campos Opcionais (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 7);');
        
        // Campos Moeda Estrangeira (já são numeric(15, 7) - manter)
        // frete_moeda_estrangeira, seguro_moeda_estrangeira, acrescimo_moeda_estrangeira, 
        // fob_unit_moeda_estrangeira, fob_total_moeda_estrangeira, service_charges_moeda_estrangeira
        
        // NOTA: fator_peso, fator_valor_fob, fator_tx_siscomex são numeric(15, 8) - NÃO ALTERAR
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN quantidade TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liq_lbs TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liquido_unitario TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liquido_total TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liq_total_kg TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_unit_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_total TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_unit TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN ii_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN ipi_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN pis_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN cofins_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_percent TYPE numeric(5, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_reduzido_percent TYPE numeric(5, 2);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ii TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_pis_cofins TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_pis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_cofins TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_unit_nf TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mva TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN multa TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN tx_def_li TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN taxa_siscomex TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN liberacao_bl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desconsolidacao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN isps_code TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN handling TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dai TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dai_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dape TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dape_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN correios TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN honorarios_nix TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desp_desenbaraco TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desp_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN das_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armazenagem TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_foz_gyn TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armaz_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mov_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_anapolis TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_unitario_final TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_total_final TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges_brl TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 6);');
    }
};
