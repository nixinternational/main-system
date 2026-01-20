<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoProdutoMulta extends Model
{
    use HasFactory;

    protected $table = 'processo_produto_multa';

    protected $fillable = [
        'processo_id',
        'produto_id',
        'adicao',
        'item',
        'quantidade',
        'peso_liquido_unitario',
        'peso_liquido_total',
        'fator_peso',
        'fob_unit_usd',
        'fob_total_usd',
        'fob_total_brl',
        'service_charges',
        'service_charges_brl',
        'frete_usd',
        'frete_brl',
        'acresc_frete_usd',
        'acresc_frete_brl',
        'vlr_crf_unit',
        'vlr_crf_total',
        'seguro_usd',
        'seguro_brl',
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
        'reducao',
        'valor_ii',
        'base_ipi',
        'valor_ipi',
        'base_pis_cofins',
        'valor_pis',
        'valor_cofins',
        'despesa_aduaneira',
        'vlr_ii_pos_despesa',
        'vlr_ipi_pos_despesa',
        'vlr_pis_pos_despesa',
        'vlr_cofins_pos_despesa',
        'nova_ncm',
        'ii_nova_ncm_percent',
        'ipi_nova_ncm_percent',
        'pis_nova_ncm_percent',
        'cofins_nova_ncm_percent',
        'vlr_ii_nova_ncm',
        'vlr_ipi_nova_ncm',
        'vlr_pis_nova_ncm',
        'vlr_cofins_nova_ncm',
        'vlr_ii_recalc',
        'vlr_ipi_recalc',
        'vlr_pis_recalc',
        'vlr_cofins_recalc',
        'valor_aduaneiro_multa',
        'ii_percent_aduaneiro',
        'ipi_percent_aduaneiro',
        'pis_percent_aduaneiro',
        'cofins_percent_aduaneiro',
    ];

    public function produto()
    {
        return $this->hasOne(Produto::class, 'id', 'produto_id');
    }

    public function processo()
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }
}
