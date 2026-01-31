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
        // TABELA: processo_produtos
        // Alterar apenas campos com ≤7 casas decimais para numeric(15, 7)
        // NOTA: diferenca_cambial_frete e diferenca_cambial_fob são numeric(1000, 7) - devem ser alterados
        // ============================================
        
        // Campos de Quantidade e Peso (de numeric(1000, 5) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN quantidade TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN peso_liquido_unitario TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN peso_liquido_total TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fator_peso TYPE numeric(15, 7);');
        
        // Campos FOB (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_unit_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_total_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_total_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_unit_moeda_estrangeira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_total_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Frete (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Seguro (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN seguro_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN seguro_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN seguro_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Acréscimo (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN acrescimo_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos THC (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN thc_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN thc_brl TYPE numeric(15, 7);');
        
        // Campos Valor Aduaneiro (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(15, 7);');
        
        // Campos Percentuais (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN ii_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN ipi_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN pis_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN cofins_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN icms_percent TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN icms_reduzido_percent TYPE numeric(15, 7);');
        
        // Campos Impostos - Valores (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_ii TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_pis_cofins TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_pis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_cofins TYPE numeric(15, 7);');
        
        // Campos Despesas Aduaneiras (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(15, 7);');
        
        // Campos ICMS (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(15, 7);');
        
        // Campos Nota Fiscal (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_unit_nf TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_total_nf TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN mva TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(15, 7);');
        
        // Campos Fatores (de numeric(1000, 5) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fator_valor_fob TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fator_tx_siscomex TYPE numeric(15, 7);');
        
        // Campos Multas e Taxas (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN multa TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN tx_def_li TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN taxa_siscomex TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 7);');
        
        // Campos Despesas Gerais (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN liberacao_bl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN desconsolidacao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN isps_code TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN handling TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN capatazia TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN afrmm TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN armazenagem_sts TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_dta_sts_ana TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN sda TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN rep_sts TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN armaz_ana TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN lavagem_container TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN rep_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN honorarios_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN desp_desenbaraco TYPE numeric(15, 7);');
        
        // Campos Custo Final (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_unitario_final TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_total_final TYPE numeric(15, 7);');
        
        // Campos Service Charges (de numeric(15, 7) - já está correto, mas garantir)
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN service_charges TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN service_charges_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN service_charges_moeda_estrangeira TYPE numeric(15, 7);');
        
        // Campos Opcionais (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 7);');
        
        // Campos Adicionais (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN delivery_fee TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN delivery_fee_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN collect_fee TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN collect_fee_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dai TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dai_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dape TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dape_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 7);');
        
        // Campos CFR/CRF (de numeric(15, 7) - já está correto)
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_crf_total TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_crf_unit TYPE numeric(15, 7);');
        
        // Campos Adicionais Marítimo (de numeric(15, 7) - já está correto)
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN desp_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN correios TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN tx_correcao_lacre TYPE numeric(15, 7);');
        
        // Campos Mato Grosso e outros (de numeric(15, 7) - já está correto)
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN multa_complem TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dif_impostos TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_rodoviario TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dif_frete_rodoviario TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN armazenagem_porto TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN rep_porto TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_sts_cgb TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN diarias TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN armaz_cgb TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN rep_cgb TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN demurrage TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dez_porcento TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_com_margem TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_ipi_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_icms_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN pis_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN cofins_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_total_final_credito TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_unit_credito TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN bc_icms_st_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_icms_st_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_total_c_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_unit_c_icms_st TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN mva_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN icms_st_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN exportador_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN tributos_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN despesas_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN total_pago_mg TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN percentual_s_fob_mg TYPE numeric(15, 7);');
        
        // Campos Diferença Cambial (de numeric(1000, 7) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 7);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN quantidade TYPE numeric(1000, 5);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN peso_liquido_unitario TYPE numeric(1000, 5);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN peso_liquido_total TYPE numeric(1000, 5);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fator_peso TYPE numeric(1000, 5);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_unit_usd TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_total_usd TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_total_brl TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_unit_moeda_estrangeira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fob_total_moeda_estrangeira TYPE numeric(15, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_usd TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_brl TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_moeda_estrangeira TYPE numeric(15, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN seguro_usd TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN seguro_brl TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN seguro_moeda_estrangeira TYPE numeric(15, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN acrescimo_moeda_estrangeira TYPE numeric(15, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN thc_usd TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN thc_brl TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN ii_percent TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN ipi_percent TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN pis_percent TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN cofins_percent TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN icms_percent TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN icms_reduzido_percent TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_ii TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_ipi TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_ipi TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_pis_cofins TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_pis TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_cofins TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_unit_nf TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_total_nf TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN base_icms_st TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN mva TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN icms_st TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_icms_st TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fator_valor_fob TYPE numeric(1000, 5);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN fator_tx_siscomex TYPE numeric(1000, 5);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN multa TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN tx_def_li TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN taxa_siscomex TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN liberacao_bl TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN desconsolidacao TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN isps_code TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN handling TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN capatazia TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN afrmm TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN armazenagem_sts TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN frete_dta_sts_ana TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN sda TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN rep_sts TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN armaz_ana TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN lavagem_container TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN rep_anapolis TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN li_dta_honor_nix TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN honorarios_nix TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN desp_desenbaraco TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_unitario_final TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN custo_total_final TYPE numeric(1000, 7);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 2);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN delivery_fee TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN delivery_fee_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN collect_fee TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN collect_fee_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dai TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dai_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dape TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN dape_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 2);');
        
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(1000, 7);');
        DB::statement('ALTER TABLE processo_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(1000, 7);');
    }
};
