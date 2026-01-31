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
        // TABELA: processo_aereos
        // Alterar apenas campos com ≤7 casas decimais para numeric(15, 7) ou numeric(10, 7)
        // ============================================
        
        // Campos FOB e Valores Base
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_fob TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_exw TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_exw_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN frete_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN seguro_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN acrescimo_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_cif TYPE numeric(15, 7);');
        
        // Campos Multas e Taxas
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN multa TYPE numeric(15, 7);');
        
        // Campos Impostos
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN ii TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN pis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cofins TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN despesas_aduaneiras TYPE numeric(15, 7);');
        
        // Campos Diferença Cambial
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 7);');
        
        // Campos Despesas Gerais
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN liberacao_bl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN desconsolidacao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN isps_code TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN handling TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN dai TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN dape TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN correios TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN honorarios_nix TYPE numeric(15, 7);');
        
        // Campos Específicos Aéreo
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN delivery_fee TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN collect_fee TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN delivery_fee_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN collect_fee_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN service_charges TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN service_charges_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN service_charges_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN thc_capatazia TYPE numeric(15, 7);');
        
        // Campos Opcionais
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN opcional_1_valor TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN opcional_2_valor TYPE numeric(15, 7);');
        
        // Campos de Peso
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN peso_bruto TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN peso_liquido TYPE numeric(15, 7);');
        
        // Campos de Cotação
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_frete_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_seguro_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_acrescimo_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_service_charges TYPE numeric(15, 7);');
        
        // Campo Taxa Dólar
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN taxa_dolar TYPE numeric(10, 7);');
        
        // Campos específicos (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN tx_def_li TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN taxa_siscomex TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN rep_itj TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN frete_nvg_x_gyn TYPE numeric(15, 7);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_fob TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_exw TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_exw_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN frete_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN seguro_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN acrescimo_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN valor_cif TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN multa TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN ii TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN ipi TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN pis TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cofins TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN despesas_aduaneiras TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN diferenca_cambial_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN diferenca_cambial_fob TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN liberacao_bl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN desconsolidacao TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN isps_code TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN handling TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN dai TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN dape TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN correios TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN honorarios_nix TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN delivery_fee TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN collect_fee TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN delivery_fee_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN collect_fee_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN service_charges TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN service_charges_usd TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN service_charges_brl TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN thc_capatazia TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN opcional_1_valor TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN opcional_2_valor TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN peso_bruto TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN peso_liquido TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_frete_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_seguro_internacional TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_acrescimo_frete TYPE numeric(15, 6);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN cotacao_service_charges TYPE numeric(15, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN taxa_dolar TYPE numeric(10, 6);');
        
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN tx_def_li TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN taxa_siscomex TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN rep_itj TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processo_aereos ALTER COLUMN frete_nvg_x_gyn TYPE numeric(15, 2);');
    }
};
