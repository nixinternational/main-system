<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function lote(){
        return $this->hasOne(Lote::class,'id','lote_id');
    }

    public function scopeIndex($query){
        return $query->with(['lote'])->when(request()->search != null, function ($query) {
            return $query->where('lote_id', (int)request()->search);
        })->orderBy('created_at','desc')->paginate(request()->paginacao ?? 10);
    }
}
