<?php

namespace App\Repositories;

use App\Enums\TipoLancamento;
use App\Http\Requests\LoteRequest;
use App\Interfaces\LoteRepositoryInterface;
use App\Models\Lancamento;
use App\Models\Lote;
use App\Models\Produto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class  LoteRepository implements LoteRepositoryInterface
{
    private Lote $lote;

    public function __construct(Lote $lote)
    {
        $this->lote = $lote;
    }

    public function getIndex()
    {
        return $this->lote->index();
    }
    public function store(LoteRequest $request)
    {
        try {
            $produto = Produto::findOrFail($request->produto);
            DB::beginTransaction();

            $lote = Lote::create([
                'data_fabricacao' => Carbon::parse($request->dataFabricacao)->startOfDay(),
                'data_validade' => Carbon::parse($request->dataValidade)->startOfDay(),
                'preco_custo' => (float) $request->preco_custo,
                'preco_venda' => $request->preco_venda,
                'produto_id' => $produto->id,
                'created_by' => Auth::id()
            ]);

            LancamentoRepository::entrada($lote->id, $request->quantidade);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getLote(int $lote_id)
    {

        return $this->lote
            ->with([
                'produto',
                'produto.marca',
                'produto.fornecedor',
                'produto.categoria',
                'responsavel' 
            ])
            ->findOrFail($lote_id);
    }
}
