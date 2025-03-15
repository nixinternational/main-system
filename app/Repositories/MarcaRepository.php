<?php

namespace App\Repositories;

use App\Http\Requests\MarcaRequest;
use App\Interfaces\MarcaRepositoryInterface;
use App\Models\Marca;

class MarcaRepository implements MarcaRepositoryInterface
{
    private Marca $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    public function getIndex()
    {
        return $this->marca->index();
    }

    public function store(MarcaRequest $request)
    {
        $this->marca->create($request->except('_token'));
    }

    public function update(MarcaRequest $request, int $id)
    {
        $this->marca->findOrFail($id)->update($request->except(['_token', '_method']));
    }

    public function destroy(int $id)
    {
        $this->marca->findOrFail($id)->delete();
    }
    public function ativar(int $marca_id)
    {
        return $this->marca->withTrashed()->where('id',$marca_id)->update(['deleted_at' => null]);
    }
}
