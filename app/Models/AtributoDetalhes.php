<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoDetalhes extends Model
{
    use HasFactory;
    protected $fillable = ['codigo', 'dados','atributo_ncm_id'];
    public function atributo()
    {
        return $this->belongsTo(Atributo::class, 'atributo_ncm_id');
    }
    
}
