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
        // TABELA: processo_rodoviarios
        // Alterar apenas campos com ≤7 casas decimais para numeric(15, 7) ou numeric(10, 7)
        // ============================================
        
        // Campos FOB e Valores Base (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_fob TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN seguro_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN acrescimo_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_cif TYPE numeric(15, 7);');
        
        // Campos Multas e Taxas (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN multa TYPE numeric(15, 7);');
        
        // Campos Impostos (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ii TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN pis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cofins TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN despesas_aduaneiras TYPE numeric(15, 7);');
        
        // Campos Diferença Cambial (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 7);');
        
        // Campos Despesas Gerais (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN outras_taxas_agente TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN liberacao_bl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN desconsolidacao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN isps_code TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN handling TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN dai TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN dape TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN correios TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN honorarios_nix TYPE numeric(15, 7);');
        
        // Campos Específicos Rodoviário (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN desp_fronteira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN das_fronteira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armazenagem TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_foz_gyn TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_fronteira TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armaz_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN mov_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_anapolis TYPE numeric(15, 7);');
        
        // Campos THC (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN thc_capatazia TYPE numeric(15, 7);');
        
        // Campos Opcionais (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_1_valor TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_2_valor TYPE numeric(15, 7);');
        
        // Campos de Peso (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_bruto TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_liquido TYPE numeric(15, 7);');
        
        // Campos de Cotação (de numeric(15, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_frete_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_seguro_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_acrescimo_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_service_charges TYPE numeric(15, 7);');
        
        // Campo Taxa Dólar (de numeric(10, 6) para numeric(10, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN taxa_dolar TYPE numeric(10, 7);');
        
        // Campos Service Charges (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN service_charges TYPE numeric(15, 7);');
        // service_charges_usd e service_charges_brl já são numeric(15, 7) - manter
        
        // Campo tx_def_li (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN tx_def_li TYPE numeric(15, 7);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_fob TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_exw_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN seguro_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN acrescimo_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN valor_cif TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN multa TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ii TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN pis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cofins TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN despesas_aduaneiras TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 6);');
        
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
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN desp_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN das_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armazenagem TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN frete_foz_gyn TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_fronteira TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN armaz_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN mov_anapolis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN rep_anapolis TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN thc_capatazia TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_1_valor TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN opcional_2_valor TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_bruto TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN peso_liquido TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_frete_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_seguro_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_acrescimo_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN cotacao_service_charges TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN taxa_dolar TYPE numeric(10, 6);');
        
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN service_charges TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_rodoviarios ALTER COLUMN tx_def_li TYPE numeric(15, 2);');
    }
};
