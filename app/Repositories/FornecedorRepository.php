<?php

namespace App\Repositories;

use App\Http\Requests\FornecedorRequest;
use App\Interfaces\FornecedorRepositoryInterface;
use App\Models\Fornecedor;
use App\Models\ProcessoProduto;
use Illuminate\Http\Request;

class FornecedorRepository 
{
    private Fornecedor $fornecedor;
    public function __construct(Fornecedor $fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }

    public function getIndex()
    {
        return $this->fornecedor->index();
    }

    public function store(Request $request)
    {

        $dados = $request->except(['_token']);

        if (!empty($dados['cnpj'])) {
            $dados['cnpj'] = preg_replace('/[.\/-]/', '', $dados['cnpj']);
        }
        $this->fornecedor->create($dados);
    }

    public function update(Request  $request, int $id)
    {
        $dados = $request->except(['_token','_method']);

        if (!empty($dados['cnpj'])) {
            $dados['cnpj'] = preg_replace('/[.\/-]/', '', $dados['cnpj']);
        }
        $this->fornecedor->findOrFail($id)->update($dados);
    }

    public function destroy(int $id)
    {
        $fornecedor = $this->fornecedor->findOrFail($id);
        foreach ($fornecedor->produtos as $produto) {
            ProcessoProduto::where('produto_id', $produto->id)->delete();
            $produto->delete();
        }
        return $fornecedor->delete();
    }
    public function ativar(int $fornecedor_id)
    {
        return $this->fornecedor->withTrashed()->where('id', $fornecedor_id)->update(['deleted_at' => null]);
    }
}
