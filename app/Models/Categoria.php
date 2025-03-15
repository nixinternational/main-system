<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['nome','descricao'];

    public function produtos(){
        return $this->hasMany(Produto::class);
    }
    public function scopeIndex($query){
        return $query->orderBy('id')->withTrashed()
        ->when(request()->has('search'), function ($query) {
            $request = request()->all();
            return $query->where('nome', 'like', '%' . $request['search'] . '%')
                ->orWhere('descricao', 'like', '%' . $request['search'] . '%');
        })->paginate(request()->paginacao ?? 10);
    }
    public function scopeIndexHome($query){
        return $query->orderBy('id')
        ->when(request()->has('search'), function ($query) {
            $request = request()->all();
            return $query->where('nome', 'like', '%' . $request['search'] . '%')
                ->orWhere('descricao', 'like', '%' . $request['search'] . '%');
        })->paginate(request()->paginacao ?? 10);
    }
}
