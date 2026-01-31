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
        // TABELA: processos
        // Alterar apenas campos com ≤7 casas decimais para numeric(15, 7)
        // MANTER diferenca_cambial_frete e diferenca_cambial_fob como numeric(1000, 10) - NÃO ALTERAR
        // ============================================
        
        // Campos FOB e Valores Base (de numeric(1000, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_fob TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN seguro_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN acrescimo_frete TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_cif TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN multa TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN thc_capatazia TYPE numeric(15, 7);');
        
        // Campos de Peso (de numeric(1000, 4) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN peso_bruto TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN peso_liquido TYPE numeric(15, 7);');
        
        // Campos Impostos (de numeric(1000, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN ii TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN ipi TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN pis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN cofins TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN despesas_aduaneiras TYPE numeric(15, 7);');
        
        // Campo Quantidade (de numeric(1000, 4) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN quantidade TYPE numeric(15, 7);');
        
        // Campos Despesas Gerais (de numeric(1000, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN outras_taxas_agente TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN liberacao_bl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN desconsolidacao TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN isps_code TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN handling TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN capatazia TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN afrmm TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN armazenagem_sts TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_dta_sts_ana TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN sda TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN rep_sts TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN armaz_ana TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN lavagem_container TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN rep_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN li_dta_honor_nix TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN honorarios_nix TYPE numeric(15, 7);');
        
        // Campos de Cotação (de numeric(1000, 4) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN cotacao_frete_internacional TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN cotacao_seguro_internacional TYPE numeric(15, 7);');
        
        // Campo Cotação Acréscimo (de numeric(1000, 6) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN cotacao_acrescimo_frete TYPE numeric(15, 7);');
        
        // Campo Moeda Processo Dólar (de numeric(15, 4) para numeric(15, 7))
        // NOTA: taxa_dolar não existe na tabela processos, apenas em processo_aereos e processo_rodoviarios
        DB::statement('ALTER TABLE processos ALTER COLUMN moeda_processo_dolar TYPE numeric(15, 7);');
        
        // Campos Frete/Seguro/Acréscimo USD/BRL (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_internacional_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_internacional_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN seguro_internacional_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN seguro_internacional_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN acrescimo_frete_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN acrescimo_frete_brl TYPE numeric(15, 7);');
        
        // Campos Service Charges (de numeric(15, 7) - já está correto, mas garantir)
        DB::statement('ALTER TABLE processos ALTER COLUMN service_charges TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN service_charges_usd TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN service_charges_brl TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN cotacao_service_charges TYPE numeric(15, 7);');
        
        // Campos Adicionais (de numeric(15, 7) - já está correto)
        DB::statement('ALTER TABLE processos ALTER COLUMN desp_anapolis TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN correios TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN tx_correcao_lacre TYPE numeric(15, 7);');
        
        // Campos Opcionais (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN opcional_1_valor TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN opcional_2_valor TYPE numeric(15, 7);');
        
        // Campos Valor EXW (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_exw TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_exw_brl TYPE numeric(15, 7);');
        
        // Campos Delivery/Collect Fee (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN delivery_fee TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN collect_fee TYPE numeric(15, 7);');
        
        // Campos DAI/DAPE (de numeric(15, 2) para numeric(15, 7))
        DB::statement('ALTER TABLE processos ALTER COLUMN dai TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN dape TYPE numeric(15, 7);');
        
        // Campos Mato Grosso e outros (de numeric(15, 7) - já está correto)
        DB::statement('ALTER TABLE processos ALTER COLUMN multa_complem TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN dif_impostos TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_rodoviario TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN dif_frete_rodoviario TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN armazenagem_porto TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN rep_porto TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_sts_cgb TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN diarias TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN armaz_cgb TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN rep_cgb TYPE numeric(15, 7);');
        DB::statement('ALTER TABLE processos ALTER COLUMN demurrage TYPE numeric(15, 7);');
        
        // NOTA: diferenca_cambial_frete e diferenca_cambial_fob são numeric(1000, 10) - NÃO ALTERAR
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_fob TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_internacional TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN seguro_internacional TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN acrescimo_frete TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_cif TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN multa TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN thc_capatazia TYPE numeric(1000, 2);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN peso_bruto TYPE numeric(1000, 4);');
        DB::statement('ALTER TABLE processos ALTER COLUMN peso_liquido TYPE numeric(1000, 4);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN ii TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN ipi TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN pis TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN cofins TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN despesas_aduaneiras TYPE numeric(1000, 2);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN quantidade TYPE numeric(1000, 4);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN outras_taxas_agente TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN liberacao_bl TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN desconsolidacao TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN isps_code TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN handling TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN capatazia TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN afrmm TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN armazenagem_sts TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_dta_sts_ana TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN sda TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN rep_sts TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN armaz_ana TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN lavagem_container TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN rep_anapolis TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN li_dta_honor_nix TYPE numeric(1000, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN honorarios_nix TYPE numeric(1000, 2);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN cotacao_frete_internacional TYPE numeric(1000, 4);');
        DB::statement('ALTER TABLE processos ALTER COLUMN cotacao_seguro_internacional TYPE numeric(1000, 4);');
        DB::statement('ALTER TABLE processos ALTER COLUMN cotacao_acrescimo_frete TYPE numeric(1000, 6);');
        
        // NOTA: taxa_dolar não existe na tabela processos
        DB::statement('ALTER TABLE processos ALTER COLUMN moeda_processo_dolar TYPE numeric(15, 4);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_internacional_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN frete_internacional_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN seguro_internacional_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN seguro_internacional_brl TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN acrescimo_frete_usd TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN acrescimo_frete_brl TYPE numeric(15, 2);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN opcional_1_valor TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN opcional_2_valor TYPE numeric(15, 2);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_exw TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN valor_exw_brl TYPE numeric(15, 2);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN delivery_fee TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN collect_fee TYPE numeric(15, 2);');
        
        DB::statement('ALTER TABLE processos ALTER COLUMN dai TYPE numeric(15, 2);');
        DB::statement('ALTER TABLE processos ALTER COLUMN dape TYPE numeric(15, 2);');
    }
};
