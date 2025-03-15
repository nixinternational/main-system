<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarcaRequest;
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    private MarcaRepository $marcaRepository;

    public function __construct(MarcaRepository $marcaRepository){
        $this->marcaRepository = $marcaRepository;
    }
    public function index(): View|RedirectResponse
    {
        
            $marcas = $this->marcaRepository->getIndex();
            return view('marca.index', compact('marcas'));
     

    }

    public function create(): View|RedirectResponse
    {
       
            return view('marca.form');
       
    }

    public function store(MarcaRequest $request): RedirectResponse
    {
        try {
            $this->marcaRepository->store($request);
            return redirect(route('marca.index'))->with('messages', ['success' => ['Marca criada com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível salvar a marca!']])->withInput($request->all());;
        }
    }

    public function edit(int $id) : View|RedirectResponse
    {
        try {
            $marca = Marca::findOrFail($id);
            return view('marca.form', compact('marca'));
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível encontrar a marca!']]);
        }
    }

    public function update(MarcaRequest $request, int $id) : RedirectResponse
    {
        try {
            $this->marcaRepository->update($request,$id);
            return redirect(route('marca.index'))->with('messages', ['success' => ['Marca atualizada com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível atualizar a marca!']])->withInput($request->all());;
        }
    }

    public function destroy(int $id) : RedirectResponse
    {
        try {
            $this->marcaRepository->destroy($id);
            return back()->with('messages', ['success' => ['Marca excluída com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluir a marca!']]);
        }
    }
    public function ativar(int $categoria_id){
        try {
            $this->marcaRepository->ativar($categoria_id);
            return back()->with('messages', ['success' => ['Categoria ativada com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar a categoria!'.$e->getMessage()]]);
        }
    }
}
