<?php

namespace App\Interfaces;

use App\Http\Requests\FornecedorRequest;

interface FornecedorRepositoryInterface
{
    public function getIndex();
    public function update(FornecedorRequest $request, int $id);
    public function destroy(int $id);
    public function store(FornecedorRequest $request);
}
