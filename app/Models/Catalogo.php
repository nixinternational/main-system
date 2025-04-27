<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf_cnpj',
        'cliente_id',
    ];

    public function produtos(){
        return $this->hasMany(Produto::class);
    }

    public function cliente(){
        return $this->hasOne(Cliente::class,'id','cliente_id');
    }
}
