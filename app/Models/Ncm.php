<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ncm extends Model
{
    use HasFactory;
    public function atributos()
    {
        return $this->hasMany(Atributo::class);
    }

}
