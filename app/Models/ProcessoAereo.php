<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoAereo extends Model
{
    use HasFactory;
    
    protected $table = 'processo_aereos';
    
    protected $fillable = [
        'codigo_interno',
        'di',
        'numero_processo',
        'valor_fob',
        'valor_exw',
        'valor_exw_brl',
        'frete_internacional',
        'seguro_internacional',
        'acrescimo_frete',
        'valor_cif',
        'taxa_dolar',
        'delivery_fee',
        'delivery_fee_brl',
        'collect_fee',
        'collect_fee_brl',
        'service_charges',
        'service_charges_moeda',
        'service_charges_usd',
        'service_charges_brl',
        'cotacao_service_charges',
        'peso_bruto',
        'peso_liquido',
        'ii',
        'ipi',
        'pis',
        'cofins',
        'despesas_aduaneiras',
        'quantidade',
        'especie',
        'cliente_id',
        'fornecedor_id',
        'frete_internacional_moeda',
        'seguro_internacional_moeda',
        'acrescimo_frete_moeda',
        'descricao',
        'status',
        'canal',
        'data_desembaraco_inicio',
        'data_desembaraco_fim',
        'cotacao_frete_internacional',
        'cotacao_seguro_internacional',
        'cotacao_acrescimo_frete',
        'data_moeda_frete_internacional',
        'data_moeda_seguro_internacional',
        'data_moeda_acrescimo_frete',
        'cotacao_moeda_processo',
        'data_cotacao_processo',
        'moeda_processo',
        'diferenca_cambial_frete',
        'diferenca_cambial_fob',
        'outras_taxas_agente',
        'liberacao_bl',
        'desconsolidacao',
        'isps_code',
        'handling',
        'dai',
        'dape',
        'correios',
        'li_dta_honor_nix',
        'honorarios_nix',
        'nacionalizacao',
        'transportadora_nome',
        'transportadora_endereco',
        'transportadora_municipio',
        'transportadora_cnpj',
        'info_complementar_nf',
        'multa',
        'tx_def_li',
        'taxa_siscomex',
        'rep_itj',
        'frete_nvg_x_gyn',
        'thc_capatazia',
        'tipo_peso',
        'opcional_1_valor',
        'opcional_1_descricao',
        'opcional_1_compoe_despesas',
        'opcional_2_valor',
        'opcional_2_descricao',
        'opcional_2_compoe_despesas',
    ];

    protected $casts = [
        'cotacao_moeda_processo' => 'json',
        'diferenca_cambial_frete' => 'decimal:4',
    ];
    
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function processoAereoProdutos()
    {
        return $this->hasMany(ProcessoAereoProduto::class, 'processo_aereo_id')->orderBy('created_at', 'asc');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }
}
