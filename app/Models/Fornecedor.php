<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fornecedor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['nome','cnpj','ativo'];


    public function scopeIndex($query){
        return $query->orderBy('id')->withTrashed()
        ->when(request()->has('search'), function ($query) {
            $request = request()->all();
            $cnpj = preg_replace('/[.\/-]/', '', $request['search']);
            return $query->where('nome', 'like', '%' . $request['search'] . '%')
                ->orWhere('cnpj', 'like', '%' .  $cnpj . '%');
        })->paginate(request()->paginacao ?? 10);
    }
}
