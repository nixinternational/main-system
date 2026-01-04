<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoAereoProduto extends Model
{
    use HasFactory;
    
    protected $table = 'processo_aereo_produtos';
    
    protected $fillable = [
        'adicao',
        'item',
        'origem',
        'produto_id',
        'processo_aereo_id',
        'quantidade',
        'peso_liq_lbs',
        'peso_liq_kg',
        'peso_liquido_unitario',
        'peso_liquido_total',
        'peso_liq_total_kg',
        'fator_peso',
        'fob_unit_usd',
        'fob_total_usd',
        'fob_total_brl',
        'fob_unit_moeda_estrangeira',
        'fob_total_moeda_estrangeira',
        'frete_usd',
        'frete_brl',
        'frete_moeda_estrangeira',
        'frete_moeda',
        'seguro_usd',
        'seguro_brl',
        'seguro_moeda_estrangeira',
        'seguro_moeda',
        'acresc_frete_usd',
        'acresc_frete_brl',
        'acrescimo_moeda_estrangeira',
        'acrescimo_moeda',
        // Campos específicos do transporte aéreo
        'delivery_fee',
        'delivery_fee_brl',
        'collect_fee',
        'collect_fee_brl',
        'vlr_cfr_unit',
        'vlr_cfr_total',
        'vlr_crf_total',
        'vlr_crf_unit',
        'valor_aduaneiro_usd',
        'valor_aduaneiro_brl',
        'item',
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
        'icms_st',
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
        'dai',
        'dai_brl',
        'dape',
        'dape_brl',
        'correios',
        'li_dta_honor_nix',
        'honorarios_nix',
        'desp_desenbaraco',
        'diferenca_cambial_frete',
        'diferenca_cambial_fob',
        'custo_unitario_final',
        'custo_total_final',
        'descricao',
        'service_charges',
        'service_charges_brl',
        'service_charges_moeda_estrangeira',
        'thc_usd',
        'thc_brl',
        'codigo_giiro',
        'opcional_1_valor',
        'opcional_2_valor',
    ];
    
    protected $casts = [
        'frete_moeda_estrangeira' => 'decimal:7',
        'seguro_moeda_estrangeira' => 'decimal:7',
        'acrescimo_moeda_estrangeira' => 'decimal:7',
        'diferenca_cambial_frete' => 'decimal:4',
        'fob_unit_usd' => 'decimal:7',
        'fob_unit_moeda_estrangeira' => 'decimal:7',
    ];
    
    public function produto()
    {
        return $this->hasOne(Produto::class, 'id', 'produto_id');
    }

    public function processoAereo()
    {
        return $this->belongsTo(ProcessoAereo::class, 'processo_aereo_id');
    }
}
