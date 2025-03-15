<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Pedido;

class Motorista extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function pedidos(){
        return $this->hasMany(Pedido::class,'motorista_id','id');
    }
}