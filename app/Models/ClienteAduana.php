<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteAduana extends Model
{

    use HasFactory;

    protected $guarded = [];
    protected $table = "clientes_aduanas";


}
