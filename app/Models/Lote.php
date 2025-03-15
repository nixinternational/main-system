<?php

namespace App\Models;

use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['quantidadeAtual','diasAteVencimento'];

    public function getQuantidadeAtualAttribute()
    {
        $entradas = $this->lancamentosEntradas()->sum('quantidade');
        $saidas = $this->lancamentosSaidas()->sum('quantidade');

        return $entradas - $saidas;
    }

    public function getDiasAteVencimentoAttribute()
    {
        return Carbon::now()->startOfDay()->diffInDays(Carbon::parse($this->data_validade)->startOfDay(),false);
    }

    public function responsavel()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function lancamentosEntradas(){
        return $this->hasMany(Lancamento::class)->where('tipo','Entrada');
    }
    public function lancamentosSaidas(){
        return $this->hasMany(Lancamento::class)->where('tipo','Saida');
    }

    public function produto(){
        return $this->hasOne(Produto::class,'id','produto_id')->withTrashed();
    }

    public function scopeIndex($query){
        return $this->orderBy('created_at','DESC')->when(request()->has('search'),function($q){
            return $q->whereHas('produto',function($q2){
                $q2->where('nome','like','%'.request()->search.'%');
            })->orWhere('id','like','%'.request()->search.'%');
        })->paginate(request()->paginacao ?? 10);
    }
}
