<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BancoCliente extends Model
{
    use HasFactory;
    protected $table = "banco_clientes";
    protected $fillable = [
        'numero_banco',
        'cliente_id',
        'nome',
        'agencia',
        'conta_corrente',
        'banco_nix'
    ];
    protected $guarded = [];
}
