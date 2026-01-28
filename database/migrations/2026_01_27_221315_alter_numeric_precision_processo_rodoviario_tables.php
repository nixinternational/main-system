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
        // ============================================
        
        // Campos FOB
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_unit_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_brl TYPE numeric(15, 6);');
        
        // Campos Frete
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_brl TYPE numeric(15, 6);');
        
        // Campos Seguro
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_brl TYPE numeric(15, 6);');
        
        // Campos Acréscimo
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(15, 6);');
        
        // Campos THC
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_brl TYPE numeric(15, 6);');
        
        // Campos CFR/CIF
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_total TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_unit TYPE numeric(15, 6);');
        
        // Campos Valor Aduaneiro
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(15, 6);');
        
        // Campos Impostos - Valores
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ii TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_pis_cofins TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_pis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_cofins TYPE numeric(15, 6);');
        
        // Campos Despesas Aduaneiras
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(15, 6);');
        
        // Campos ICMS
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(15, 6);');
        
        // Campos Nota Fiscal
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_unit_nf TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mva TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_st TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(15, 6);');
        
        // Campos Multas e Taxas
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN multa TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN tx_def_li TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN taxa_siscomex TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 6);');
        
        // Campos Despesas Gerais
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
        
        // Campos Específicos Rodoviário
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desp_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN das_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armazenagem TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_foz_gyn TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armaz_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mov_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_anapolis TYPE numeric(15, 6);');
        
        // Campos Diferença Cambial
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 6);');
        
        // Campos Custo Final
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_unitario_final TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_total_final TYPE numeric(15, 6);');
        
        // Campos Service Charges
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges_brl TYPE numeric(15, 6);');
        
        // Campos Opcionais
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 6);');
        
        // Campos de Quantidade e Peso (de numeric(15, 4) para numeric(15, 6))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN quantidade TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liquido_total TYPE numeric(15, 6);');

        // ============================================
        // TABELA: processo_rodoviarios
        // ============================================
        
        // Campos FOB e Valores Base
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_fob TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN seguro_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN acrescimo_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_cif TYPE numeric(15, 6);');
        
        // Campos Multas e Taxas
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN multa TYPE numeric(15, 6);');
        
        // Campos Impostos
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ii TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN pis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cofins TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN despesas_aduaneiras TYPE numeric(15, 6);');
        
        // Campos Diferença Cambial
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 6);');
        
        // Campos Despesas Gerais
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN outras_taxas_agente TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN liberacao_bl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN desconsolidacao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN isps_code TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN handling TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN dai TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN dape TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN correios TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN honorarios_nix TYPE numeric(15, 6);');
        
        // Campos Específicos Rodoviário
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN desp_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN das_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armazenagem TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_foz_gyn TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armaz_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN mov_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_anapolis TYPE numeric(15, 6);');
        
        // Campos THC
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN thc_capatazia TYPE numeric(15, 6);');
        
        // Campos Opcionais
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_1_valor TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_2_valor TYPE numeric(15, 6);');
        
        // Campos de Peso (de numeric(15, 4) para numeric(15, 6))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_bruto TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_liquido TYPE numeric(15, 6);');
        
        // Campos de Cotação (de numeric(15, 4) para numeric(15, 6))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_frete_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_seguro_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_service_charges TYPE numeric(15, 6);');
        
        // Campo Taxa Dólar (de numeric(10, 4) para numeric(10, 6))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN taxa_dolar TYPE numeric(10, 6);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ============================================
        // TABELA: processo_rodoviario_produtos
        // ============================================
        
        // Campos FOB
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_unit_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN fob_total_brl TYPE numeric(15, 2);');
        
        // Campos Frete
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_brl TYPE numeric(15, 2);');
        
        // Campos Seguro
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN seguro_brl TYPE numeric(15, 2);');
        
        // Campos Acréscimo
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN acresc_frete_brl TYPE numeric(15, 2);');
        
        // Campos THC
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN thc_brl TYPE numeric(15, 2);');
        
        // Campos CFR/CIF
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_unit TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_cfr_total TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_total TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN vlr_crf_unit TYPE numeric(15, 2);');
        
        // Campos Valor Aduaneiro
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_aduaneiro_brl TYPE numeric(15, 2);');
        
        // Campos Impostos - Valores
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ii TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_ipi TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_ipi TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_pis_cofins TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_pis TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_cofins TYPE numeric(15, 2);');
        
        // Campos Despesas Aduaneiras
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN despesa_aduaneira TYPE numeric(15, 2);');
        
        // Campos ICMS
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_sem_reducao TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_sem_reducao TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_reduzido TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_reduzido TYPE numeric(15, 2);');
        
        // Campos Nota Fiscal
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_unit_nf TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN base_icms_st TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mva TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN icms_st TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_icms_st TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN valor_total_nf_com_icms_st TYPE numeric(15, 2);');
        
        // Campos Multas e Taxas
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN multa TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN tx_def_li TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN taxa_siscomex TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 2);');
        
        // Campos Despesas Gerais
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN liberacao_bl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desconsolidacao TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN isps_code TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN handling TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dai TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dai_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dape TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN dape_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN correios TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN honorarios_nix TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desp_desenbaraco TYPE numeric(15, 2);');
        
        // Campos Específicos Rodoviário
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN desp_fronteira TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN das_fronteira TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armazenagem TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN frete_foz_gyn TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_fronteira TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN armaz_anapolis TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN mov_anapolis TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN rep_anapolis TYPE numeric(15, 2);');
        
        // Campos Diferença Cambial
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 2);');
        
        // Campos Custo Final
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_unitario_final TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN custo_total_final TYPE numeric(15, 2);');
        
        // Campos Service Charges
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN service_charges_brl TYPE numeric(15, 2);');
        
        // Campos Opcionais
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_1_valor TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN opcional_2_valor TYPE numeric(15, 2);');
        
        // Campos de Quantidade e Peso (voltar para numeric(15, 4))
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN quantidade TYPE numeric(15, 4);');
        DB::statement('ALTER TABLE processo_rodoviario_produtos ALTER COLUMN peso_liquido_total TYPE numeric(15, 4);');

        // ============================================
        // TABELA: processo_rodoviarios
        // ============================================
        
        // Campos FOB e Valores Base
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_fob TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_internacional TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN seguro_internacional TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN acrescimo_frete TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_cif TYPE numeric(15, 2);');
        
        // Campos Multas e Taxas
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN multa TYPE numeric(15, 2);');
        
        // Campos Impostos
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ii TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ipi TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN pis TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cofins TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN despesas_aduaneiras TYPE numeric(15, 2);');
        
        // Campos Diferença Cambial
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 2);');
        
        // Campos Despesas Gerais
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN outras_taxas_agente TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN liberacao_bl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN desconsolidacao TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN isps_code TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN handling TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN dai TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN dape TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN correios TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN honorarios_nix TYPE numeric(15, 2);');
        
        // Campos Específicos Rodoviário
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN desp_fronteira TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN das_fronteira TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armazenagem TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_foz_gyn TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_fronteira TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armaz_anapolis TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN mov_anapolis TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_anapolis TYPE numeric(15, 2);');
        
        // Campos THC
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN thc_capatazia TYPE numeric(15, 2);');
        
        // Campos Opcionais
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_1_valor TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_2_valor TYPE numeric(15, 2);');
        
        // Campos de Peso (voltar para numeric(15, 4))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_bruto TYPE numeric(15, 4);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_liquido TYPE numeric(15, 4);');
        
        // Campos de Cotação (voltar para numeric(15, 4))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_frete_internacional TYPE numeric(15, 4);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_seguro_internacional TYPE numeric(15, 4);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_service_charges TYPE numeric(15, 4);');
        
        // Campo Taxa Dólar (voltar para numeric(10, 4))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN taxa_dolar TYPE numeric(10, 4);');
    }
};
