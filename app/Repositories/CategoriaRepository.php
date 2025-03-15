<?php

namespace App\Repositories;

use App\Http\Requests\CategoriaRequest;
use App\Interfaces\CategoriaRepositoryInterface;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;

class CategoriaRepository implements CategoriaRepositoryInterface
{
    private Categoria $categoria;

    public function __construct(Categoria $model)
    {
        $this->categoria = $model;
    }
    public function getIndex()
    {
        return $this->categoria->index();
    }
    public  function getIndexHome()
    {
        return $this->categoria->indexHome();
    }
    public function store(CategoriaRequest $request)
    {

        Categoria::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);
    }

    public function getProdutosCategoria(int $categoria_id)
    {

        return $this->categoria->findOrFail($categoria_id)->produtos->map->only(['id', 'nome']);
    }

    public function update(Categoria $categoria,CategoriaRequest $request)
    {
        $categoria->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
        ]);
    }

    public function destroy(int $categoria_id)
    {
        return $this->categoria->findOrFail($categoria_id)->delete();
    }
    public function ativar(int $categoria_id)
    {
        return $this->categoria->withTrashed()->where('id',$categoria_id)->update(['deleted_at' => null]);
    }
}
