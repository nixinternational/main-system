<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'slug'
    ];

    public function gruposId(){
        return $this->hasMany(GrupoPermissao::class,'permissao_id','id');
    }
}
