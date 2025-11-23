<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoRodoviario extends Model
{
    use HasFactory;
    
    protected $table = 'processo_rodoviarios';
    
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
        'thc_capatazia',
        // Campos específicos rodoviário
        'desp_fronteira',
        'das_fronteira',
        'armazenagem',
        'frete_foz_gyn',
        'rep_fronteira',
        'armaz_anapolis',
        'mov_anapolis',
        'rep_anapolis',
    ];

    protected $casts = [
        'cotacao_moeda_processo' => 'json'
    ];
    
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function processoRodoviarioProdutos()
    {
        return $this->hasMany(ProcessoRodoviarioProduto::class, 'processo_rodoviario_id')->orderBy('created_at', 'asc');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }
}

