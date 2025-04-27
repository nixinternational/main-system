<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo',
        'ncm_id',
        'modalidade',
        'obrigatorio',
        'multivalorado',
        'data_inicio_vigencia',
    ];
    public function ncm()
{
    return $this->belongsTo(Ncm::class);
}

    public function detalhes()
    {
        return $this->hasOne(AtributoDetalhes::class, 'atributo_ncm_id');
    }


}
