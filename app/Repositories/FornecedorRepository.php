<?php

namespace App\Repositories;

use App\Http\Requests\FornecedorRequest;
use App\Interfaces\FornecedorRepositoryInterface;
use App\Models\Fornecedor;

class FornecedorRepository implements FornecedorRepositoryInterface
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

    public function store(FornecedorRequest $request)
    {

        $this->fornecedor->create($request->except(['_token']));
    }

    public function update(FornecedorRequest $request, int $id)
    {
        $this->fornecedor->findOrFail($id)->update($request->except(['_token','_method']));
    }

    public function destroy(int $id)
    {

        $this->fornecedor->findOrFail($id)->delete();
    }
    public function ativar(int $fornecedor_id)
    {
        return $this->fornecedor->withTrashed()->where('id',$fornecedor_id)->update(['deleted_at' => null]);
    }
}
