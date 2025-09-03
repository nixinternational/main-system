<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $quantidadeCols = ['quantidade'];
        $pesoCols = ['peso_liquido_unitario', 'peso_liquido_total', 'fator_peso', 'fator_valor_fob', 'fator_tx_siscomex'];
        $valorCols = [
            'fob_unit_usd','fob_total_usd','fob_total_brl','frete_usd','frete_brl',
            'seguro_usd','seguro_brl','acresc_frete_usd','acresc_frete_brl',
            'thc_usd','thc_brl','valor_aduaneiro_usd','valor_aduaneiro_brl',
            'ii_percent','ipi_percent','pis_percent','cofins_percent','icms_percent',
            'icms_reduzido_percent','valor_ii','base_ipi','valor_ipi','base_pis_cofins',
            'valor_pis','valor_cofins','despesa_aduaneira','base_icms_sem_reducao',
            'valor_icms_sem_reducao','base_icms_reduzido','valor_icms_reduzido',
            'valor_unit_nf','valor_total_nf','base_icms_st','mva','valor_icms_st',
            'valor_total_nf_com_icms_st','multa','tx_def_li','taxa_siscomex',
            'outras_taxas_agente','liberacao_bl','desconsolidacao','isps_code','handling',
            'capatazia','afrmm','armazenagem_sts','frete_dta_sts_ana','sda','rep_sts',
            'armaz_ana','lavagem_container','rep_anapolis','li_dta_honor_nix',
            'honorarios_nix','desp_desenbaraco','diferenca_cambial_frete',
            'diferenca_cambial_fob','custo_unitario_final','custo_total_final'
        ];

        foreach ($quantidadeCols as $col) {
            DB::statement("ALTER TABLE processo_produtos ALTER COLUMN {$col} TYPE NUMERIC(20,5) USING {$col}::numeric;");
        }

        foreach ($pesoCols as $col) {
            DB::statement("ALTER TABLE processo_produtos ALTER COLUMN {$col} TYPE NUMERIC(20,5) USING {$col}::numeric;");
        }

        foreach ($valorCols as $col) {
            DB::statement("ALTER TABLE processo_produtos ALTER COLUMN {$col} TYPE NUMERIC(20,7) USING {$col}::numeric;");
        }
    }

    public function down(): void
    {
        $allCols = array_merge(
            ['quantidade'],
            ['peso_liquido_unitario', 'peso_liquido_total', 'fator_peso', 'fator_valor_fob', 'fator_tx_siscomex'],
            [
                'fob_unit_usd','fob_total_usd','fob_total_brl','frete_usd','frete_brl',
                'seguro_usd','seguro_brl','acresc_frete_usd','acresc_frete_brl',
                'thc_usd','thc_brl','valor_aduaneiro_usd','valor_aduaneiro_brl',
                'ii_percent','ipi_percent','pis_percent','cofins_percent','icms_percent',
                'icms_reduzido_percent','valor_ii','base_ipi','valor_ipi','base_pis_cofins',
                'valor_pis','valor_cofins','despesa_aduaneira','base_icms_sem_reducao',
                'valor_icms_sem_reducao','base_icms_reduzido','valor_icms_reduzido',
                'valor_unit_nf','valor_total_nf','base_icms_st','mva','valor_icms_st',
                'valor_total_nf_com_icms_st','multa','tx_def_li','taxa_siscomex',
                'outras_taxas_agente','liberacao_bl','desconsolidacao','isps_code','handling',
                'capatazia','afrmm','armazenagem_sts','frete_dta_sts_ana','sda','rep_sts',
                'armaz_ana','lavagem_container','rep_anapolis','li_dta_honor_nix',
                'honorarios_nix','desp_desenbaraco','diferenca_cambial_frete',
                'diferenca_cambial_fob','custo_unitario_final','custo_total_final'
            ]
        );

        foreach ($allCols as $col) {
            DB::statement("ALTER TABLE processo_produtos ALTER COLUMN {$col} TYPE NUMERIC(15,2) USING {$col}::numeric;");
        }
    }
};
