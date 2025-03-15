<?php

namespace App\Services;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use Illuminate\Support\Facades\Storage;

class CategoriaService
{
    public static function store(CategoriaRequest $request, CategoriaRepository $categoriaRepository)
    {
        $categoriaRepository->store($request);
    }

    public static function update(CategoriaRequest $request, int $categoria_id, CategoriaRepository $categoriaRepository)
    {
        $categoria = Categoria::findOrFail($categoria_id);
        $categoriaRepository->update($categoria, $request);
    }
}
