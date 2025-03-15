<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoProduto extends Model
{
    protected $guarded = [];
    use HasFactory;

    protected $appends = ['nome_produto'];
    public function produto(){
        return $this->hasOne(Produto::class,'id','produto_id');
    }
    public function getNomeProdutoAttribute(){
        return $this->produto->nome;
    }

    public function pedido(){
        return $this->hasOne(Pedido::class, 'id','pedido_id');
    }
}
