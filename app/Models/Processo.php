<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function cliente(){
        return $this->hasOne(Cliente::class,'id','cliente_id');
    }
}
