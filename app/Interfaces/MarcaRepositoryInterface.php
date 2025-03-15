<?php

namespace App\Interfaces;

use App\Http\Requests\MarcaRequest;

interface MarcaRepositoryInterface
{
    public function getIndex();
    public function update(MarcaRequest $request, int $id);
    public function destroy(int $id);
    public function store(MarcaRequest $request);
}
