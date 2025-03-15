<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;

class Marca extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['nome'];

    public function scopeIndex($query)
    {
        return $query->orderBy('id')->withTrashed()
        ->when(request()->has('search'), function ($query) {
            $request = request()->all();
            return $query->where('nome', 'like', '%' . $request['search'] . '%');
        })->paginate(request()->paginacao ?? 10);
    }
}
