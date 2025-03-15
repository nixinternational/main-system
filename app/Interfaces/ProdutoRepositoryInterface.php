<?php

namespace App\Interfaces;

use App\Http\Requests\ProdutoRequest;

interface ProdutoRepositoryInterface
{
    public function getIndex();
    public function update(ProdutoRequest $request, int $id);
    public function destroy(int $id);
    public function store(ProdutoRequest $request);
}
