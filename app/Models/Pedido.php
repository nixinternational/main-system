<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['qtd_produtos','dt_previsao_formatted'];

    public function cliente(){  
        return $this->hasOne(Cliente::class,'id','cliente_id')->withTrashed();
    }

    public function motorista(){
        return $this->hasOne(Motorista::class,'id','motorista_id')->withTrashed();
    }

    public function produtos(){
        return $this->hasMany(PedidoProduto::class,'pedido_id','id');
    }

    public function getQtdProdutosAttribute()
    {
        // Lógica para obter o va/lor do atributo
        return $this->produtos()->count();
    }
    public function getDtPrevisaoFormattedAttribute()
    {
        // Lógica para obter o va/lor do atributo
        return Carbon::parse($this->dt_previsao)->format('d/m/Y H:i');
    }
}
