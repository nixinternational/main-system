<?php

namespace App\Interfaces;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;

interface CategoriaRepositoryInterface
{
    public function getIndex();
    public function update(Categoria $request, CategoriaRequest $categoria);
    public function destroy(int $id);
    public function store(CategoriaRequest $request);
    public function getProdutosCategoria(int $categoria_id);
}
