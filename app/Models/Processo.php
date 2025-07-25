<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo_interno',
        'di',
        'numero_processo',
        'valor_fob',
        'frete_internacional',
        'seguro_internacional',
        'acrescimo_frete',
        'valor_cif',
        'taxa_dolar',
        'thc_capatazia',
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
        'frete_internacional_moeda',
        'seguro_internacional_moeda',
        'acrescimo_frete_moeda',
        'descricao',
        'status',
        'canal',
        'data_desembaraco_inicio',
        'data_desembaraco_fim',
    ];
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function processoProdutos()
    {
        return $this->hasMany(ProcessoProduto::class)->orderBy('created_at', 'asc');;
    }
}
