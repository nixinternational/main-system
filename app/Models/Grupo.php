<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'slug'];
    public function permissaoId()
    {
        return $this->hasMany(GrupoPermissao::class, 'grupo_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Permissao::class, 'grupo_permissaos', 'grupo_id', 'permissao_id');
    }

}
