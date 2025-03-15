<?php

namespace App\Repositories;

use App\Enums\TipoLancamento;
use App\Http\Requests\LancamentoRequest;
use App\Models\Lancamento;
use App\Models\Lote;
use Illuminate\Support\Facades\Auth;

class LancamentoRepository
{
    private Lancamento $lancamento;

    public function __construct(Lancamento $lancamento)
    {
        $this->lancamento = $lancamento;
    }

    public function getIndex()
    {

        return $this->lancamento->index();
    }

    public function saida(int $lote_id, int $quantidade)
    {
        $this->lancamento->create([
            'tipo' => TipoLancamento::Saida,
            'lote_id' => $lote_id,
            'quantidade' => $quantidade,
            'created_by' => Auth::id()
        ]);
    }

    public static function entrada(int $lote_id, int $quantidade)
    {

        Lancamento::create([
            'tipo' => TipoLancamento::Entrada,
            'quantidade' => $quantidade,
            'lote_id' => $lote_id,
            'created_by' => Auth::id()
        ]);
    }
}
