<?php

namespace App\Repositories;

use App\Http\Requests\ProdutoRequest;
use App\Interfaces\ProdutoRepositoryInterface;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;

class ProdutoRepository implements ProdutoRepositoryInterface
{
    private Produto $produto;

    public function __construct(Produto $produto)
    {
        $this->produto = $produto;
    }

    public function getIndex()
    {
        return $this->produto->index();
    }

    public function getIndexHome(int $categoria_id)
    {
        return $this->produto->indexHome($categoria_id);
    }
    public function store(ProdutoRequest $request)
    {
        Produto::create([
            "nome" => $request->nome,
            "unidade_medida" => $request->unidade_medida,
            "categoria_id" => (int) $request->categoria,
            "marca_id" => (int) $request->marca,
            "fornecedor_id" => (int) $request->fornecedor,
            "descricao" => $request->descricao,
            "created_by" => Auth::id(),
            "informacao_nutricional" => [
                "porcao" => $request->porcao,
                "proteina" => $request->proteina,
                "carboidrato" => $request->carboidrato,
                "gordura_total" => $request->gordura_total,
            ]
        ]);
    }

    public function getProduto(int $produto_id)
    {

        return $this->produto->with(['fornecedor', 'marca', 'responsavel'])->withTrashed()->findOrFail($produto_id);
    }

    public function update(ProdutoRequest $request, int $id)
    {
        Produto::findOrFail($id)->update([
            "nome" => $request->nome,
            "unidade_medida" => $request->unidade_medida,
            "categoria_id" => (int) $request->categoria,
            "marca_id" => (int) $request->marca,
            "fornecedor_id" => (int) $request->fornecedor,
            "descricao" => $request->descricao,
            "created_by" => Auth::id(),
            "informacao_nutricional" => [
                "porcao" => $request->porcao,
                "proteina" => $request->proteina,
                "carboidrato" => $request->carboidrato,
                "gordura_total" => $request->gordura_total,
            ]
        ]);
    }

    public function destroy(int $id)
    {
        $this->produto->findOrFail($id)->delete();
    }
    public function ativar(int $produto_id)
    {
        return $this->produto->withTrashed()->where('id',$produto_id)->update(['deleted_at' => null]);
    }
}
