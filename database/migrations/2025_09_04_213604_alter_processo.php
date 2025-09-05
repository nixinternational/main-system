<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Colunas da tabela processo_produtos
        $produtos = [
            'quantidade' => 'NUMERIC(1000,5)',
            'peso_liquido_unitario' => 'NUMERIC(1000,5)',
            'peso_liquido_total' => 'NUMERIC(1000,5)',
            'fator_peso' => 'NUMERIC(1000,5)',
            'fob_unit_usd' => 'NUMERIC(1000,7)',
            'fob_total_usd' => 'NUMERIC(1000,7)',
            'fob_total_brl' => 'NUMERIC(1000,7)',
            'frete_usd' => 'NUMERIC(1000,7)',
            'frete_brl' => 'NUMERIC(1000,7)',
            'seguro_usd' => 'NUMERIC(1000,7)',
            'seguro_brl' => 'NUMERIC(1000,7)',
            'acresc_frete_usd' => 'NUMERIC(1000,7)',
            'acresc_frete_brl' => 'NUMERIC(1000,7)',
            'thc_usd' => 'NUMERIC(1000,7)',
            'thc_brl' => 'NUMERIC(1000,7)',
            'valor_aduaneiro_usd' => 'NUMERIC(1000,7)',
            'valor_aduaneiro_brl' => 'NUMERIC(1000,7)',
            'ii_percent' => 'NUMERIC(1000,7)',
            'ipi_percent' => 'NUMERIC(1000,7)',
            'pis_percent' => 'NUMERIC(1000,7)',
            'cofins_percent' => 'NUMERIC(1000,7)',
            'icms_percent' => 'NUMERIC(1000,7)',
            'icms_reduzido_percent' => 'NUMERIC(1000,7)',
            'valor_ii' => 'NUMERIC(1000,7)',
            'base_ipi' => 'NUMERIC(1000,7)',
            'valor_ipi' => 'NUMERIC(1000,7)',
            'base_pis_cofins' => 'NUMERIC(1000,7)',
            'valor_pis' => 'NUMERIC(1000,7)',
            'valor_cofins' => 'NUMERIC(1000,7)',
            'despesa_aduaneira' => 'NUMERIC(1000,7)',
            'base_icms_sem_reducao' => 'NUMERIC(1000,7)',
            'valor_icms_sem_reducao' => 'NUMERIC(1000,7)',
            'base_icms_reduzido' => 'NUMERIC(1000,7)',
            'valor_icms_reduzido' => 'NUMERIC(1000,7)',
            'valor_unit_nf' => 'NUMERIC(1000,7)',
            'valor_total_nf' => 'NUMERIC(1000,7)',
            'base_icms_st' => 'NUMERIC(1000,7)',
            'mva' => 'NUMERIC(1000,7)',
            'valor_icms_st' => 'NUMERIC(1000,7)',
            'valor_total_nf_com_icms_st' => 'NUMERIC(1000,7)',
            'fator_valor_fob' => 'NUMERIC(1000,5)',
            'fator_tx_siscomex' => 'NUMERIC(1000,5)',
            'multa' => 'NUMERIC(1000,7)',
            'tx_def_li' => 'NUMERIC(1000,7)',
            'taxa_siscomex' => 'NUMERIC(1000,7)',
            'outras_taxas_agente' => 'NUMERIC(1000,7)',
            'liberacao_bl' => 'NUMERIC(1000,7)',
            'desconsolidacao' => 'NUMERIC(1000,7)',
            'isps_code' => 'NUMERIC(1000,7)',
            'handling' => 'NUMERIC(1000,7)',
            'capatazia' => 'NUMERIC(1000,7)',
            'afrmm' => 'NUMERIC(1000,7)',
            'armazenagem_sts' => 'NUMERIC(1000,7)',
            'frete_dta_sts_ana' => 'NUMERIC(1000,7)',
            'sda' => 'NUMERIC(1000,7)',
            'rep_sts' => 'NUMERIC(1000,7)',
            'armaz_ana' => 'NUMERIC(1000,7)',
            'lavagem_container' => 'NUMERIC(1000,7)',
            'rep_anapolis' => 'NUMERIC(1000,7)',
            'li_dta_honor_nix' => 'NUMERIC(1000,7)',
            'honorarios_nix' => 'NUMERIC(1000,7)',
            'desp_desenbaraco' => 'NUMERIC(1000,7)',
            'diferenca_cambial_frete' => 'NUMERIC(1000,7)',
            'diferenca_cambial_fob' => 'NUMERIC(1000,7)',
            'custo_unitario_final' => 'NUMERIC(1000,7)',
            'custo_total_final' => 'NUMERIC(1000,7)',
        ];

        foreach ($produtos as $col => $type) {
            DB::statement("ALTER TABLE processo_produtos ALTER COLUMN $col TYPE $type");
        }

        // Colunas da tabela processos
        $processos = [
            'valor_fob' => 'NUMERIC(1000,2)',
            'frete_internacional' => 'NUMERIC(1000,2)',
            'seguro_internacional' => 'NUMERIC(1000,2)',
            'acrescimo_frete' => 'NUMERIC(1000,2)',
            'valor_cif' => 'NUMERIC(1000,2)',
            'multa' => 'NUMERIC(1000,2)',
            'taxa_dolar' => 'NUMERIC(1000,4)',
            'thc_capatazia' => 'NUMERIC(1000,2)',
            'peso_bruto' => 'NUMERIC(1000,4)',
            'peso_liquido' => 'NUMERIC(1000,4)',
            'ii' => 'NUMERIC(1000,2)',
            'ipi' => 'NUMERIC(1000,2)',
            'pis' => 'NUMERIC(1000,2)',
            'cofins' => 'NUMERIC(1000,2)',
            'despesas_aduaneiras' => 'NUMERIC(1000,2)',
            'quantidade' => 'NUMERIC(1000,4)',
            'outras_taxas_agente' => 'NUMERIC(1000,2)',
            'liberacao_bl' => 'NUMERIC(1000,2)',
            'desconsolidacao' => 'NUMERIC(1000,2)',
            'isps_code' => 'NUMERIC(1000,2)',
            'handling' => 'NUMERIC(1000,2)',
            'capatazia' => 'NUMERIC(1000,2)',
            'afrmm' => 'NUMERIC(1000,2)',
            'armazenagem_sts' => 'NUMERIC(1000,2)',
            'frete_dta_sts_ana' => 'NUMERIC(1000,2)',
            'sda' => 'NUMERIC(1000,2)',
            'rep_sts' => 'NUMERIC(1000,2)',
            'armaz_ana' => 'NUMERIC(1000,2)',
            'lavagem_container' => 'NUMERIC(1000,2)',
            'rep_anapolis' => 'NUMERIC(1000,2)',
            'li_dta_honor_nix' => 'NUMERIC(1000,2)',
            'honorarios_nix' => 'NUMERIC(1000,2)',
            'cotacao_frete_internacional' => 'NUMERIC(1000,4)',
            'cotacao_seguro_internacional' => 'NUMERIC(1000,4)',
            'cotacao_acrescimo_frete' => 'NUMERIC(1000,6)',
        ];

        foreach ($processos as $col => $type) {
            DB::statement("ALTER TABLE processos ALTER COLUMN $col TYPE $type");
        }
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE processos 
                ALTER COLUMN peso_bruto TYPE NUMERIC(8,2),
                ALTER COLUMN thc_capatazia TYPE NUMERIC(8,2),
                ALTER COLUMN frete_internacional TYPE NUMERIC(8,2),
                ALTER COLUMN seguro_internacional TYPE NUMERIC(8,2),
                ALTER COLUMN acrescimo_frete TYPE NUMERIC(8,2),
                ALTER COLUMN cotacao_frete_internacional TYPE NUMERIC(8,6),
                ALTER COLUMN cotacao_seguro_internacional TYPE NUMERIC(8,6),
                ALTER COLUMN cotacao_acrescimo_frete TYPE NUMERIC(8,6)
        ");
    }
};
