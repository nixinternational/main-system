<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $appends = ['endereco_completo'];
    protected $guarded = [];


    public function getEnderecoCompletoAttribute()
    {
        $endereco = '';
        $endereco .= $this->cidade != '' ? "{$this->cidade} - " : '';
        $endereco .= $this->bairro != '' ? "{$this->bairro} <br>" : '';
        $endereco .= $this->logradouro != '' ? "{$this->logradouro} - " : '';
        $endereco .= $this->numero != '' ? "N° {$this->numero} <br>" : '';
        $endereco .= $this->complemento != '' ? "Complemento: {$this->complemento}" : '';
        return $endereco;
    }
}
