<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteDocumento extends Model
{
    use HasFactory;
    protected $fillable = [
        'cliente_id',
        'tipo_documento',
        'path_file',
        'url',
    ];
}
