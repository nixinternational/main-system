<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use App\Repositories\CategoriaRepository;
use App\Services\CategoriaService;
use Illuminate\Contracts\View\View;

class CategoriaController extends Controller
{
    private CategoriaRepository $categoriaRepository;

    public function __construct(CategoriaRepository $categoriaRepository)
    {
        $this->categoriaRepository = $categoriaRepository;
    }

    public function index()
    {
        $categorias = $this->categoriaRepository->getIndex();
        return view('categoria.index', compact('categorias'));
    }

    public function create(): View
    {

        return view('categoria.form');
    }

    public function store(CategoriaRequest $request)
    {
        try {
            CategoriaService::store($request, $this->categoriaRepository);
            return redirect(route('categoria.index'))->with('messages', ['success' => ['Categoria criada com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível criar a categoria!']])->withInput($request->all());;
        }
    }

    public function show(int $categoria_id): array
    {

        try {
            $produtosCategoria = $this->categoriaRepository->getProdutosCategoria($categoria_id);
            return ['success' => true, 'data' => $produtosCategoria];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => '', 'message' => 'Não foi possível encontrar a categoria', 'error' => $e->getMessage()];
        }
    }

    public function edit(int $categoria_id)
    {
        try {
            $categoria = Categoria::findOrFail($categoria_id);
            return view('categoria.form', compact('categoria'));
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possícel editar a categoria!']]);
        }
    }

    public function update(CategoriaRequest $request, int $categoria_id)
    {
        try {
            CategoriaService::update($request, $categoria_id, $this->categoriaRepository);
            return redirect(route('categoria.index'))->with('messages', ['success' => ['Categoria atualizada com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);

            return back()->with('messages', ['error' => ['Não foi possível atualizar a categoria!']])->withInput($request->all());;
        }
    }

    public function destroy(int $categoria_id)
    {
        try {
            $this->categoriaRepository->destroy($categoria_id);
            return back()->with('messages', ['success' => ['Categoria excluída com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluir a categoria!']]);
        }
    }

    public function ativar(int $categoria_id){
        try {
            $this->categoriaRepository->ativar($categoria_id);
            return back()->with('messages', ['success' => ['Categoria ativada com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar a categoria!'.$e->getMessage()]]);
        }
    }
}
