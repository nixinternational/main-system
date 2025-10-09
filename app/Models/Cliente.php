<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];



    public function responsaveisProcesso(){
        return $this->hasMany(ClienteResponsavelProcesso::class);
    }

    public function aduanas(){
        return $this->hasMany(ClienteAduana::class);
    }

    public function documentos(){
        return $this->hasMany(ClienteDocumento::class);
    }

    public function fornecedores(){
        return $this->hasMany(Fornecedor::class);
    }
}
