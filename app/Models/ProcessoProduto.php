<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoProduto extends Model
{
    use HasFactory;
    protected $fillable = [
        'adicao',
        'produto_id',
        'processo_id',
        'quantidade',
        'peso_liquido_unitario',
        'peso_liquido_total',
        'fator_peso',
        'fob_unit_usd',
        'fob_total_usd',
        'fob_total_brl',
        'frete_usd',
        'frete_brl',
        'seguro_usd',
        'seguro_brl',
        'acresc_frete_usd',
        'acresc_frete_brl',
        'thc_usd',
        'thc_brl',
        'valor_aduaneiro_usd',
        'valor_aduaneiro_brl',
        'ii_percent',
        'ipi_percent',
        'pis_percent',
        'cofins_percent',
        'icms_percent',
        'icms_reduzido_percent',
        'valor_ii',
        'base_ipi',
        'valor_ipi',
        'base_pis_cofins',
        'valor_pis',
        'valor_cofins',
        'despesa_aduaneira',
        'base_icms_sem_reducao',
        'valor_icms_sem_reducao',
        'base_icms_reduzido',
        'valor_icms_reduzido',
        'valor_unit_nf',
        'valor_total_nf',
        'base_icms_st',
        'mva',
        'valor_icms_st',
        'valor_total_nf_com_icms_st',
        'fator_valor_fob',
        'fator_tx_siscomex',
        'multa',
        'tx_def_li',
        'taxa_siscomex',
        'outras_taxas_agente',
        'liberacao_bl',
        'desconsolidacao',
        'isps_code',
        'handling',
        'capatazia',
        'afrmm',
        'armazenagem_sts',
        'frete_dta_sts_ana',
        'sda',
        'rep_sts',
        'armaz_ana',
        'lavagem_container',
        'rep_anapolis',
        'li_dta_honor_nix',
        'honorarios_nix',
        'desp_desenbaraco',
        'diferenca_cambial_frete',
        'diferenca_cambial_fob',
        'custo_unitario_final',
        'custo_total_final',
    ];

    public function produto(){
        return $this->hasOne(Produto::class,'id','produto_id');
    }
}
