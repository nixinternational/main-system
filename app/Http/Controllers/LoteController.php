<?php

namespace App\Http\Controllers;

use App\Enums\TipoLancamento;
use App\Http\Requests\LoteRequest;
use App\Models\Categoria;
use App\Models\Lancamento;
use App\Models\Lote;
use App\Models\Produto;
use App\Repositories\LoteRepository;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    private LoteRepository $loteRepository;

    public function __construct(LoteRepository $loteRepository)
    {
        $this->loteRepository = $loteRepository;
    }
    public function index(): View|RedirectResponse
    {
        $lotes = $this->loteRepository->getIndex();
        return view('lote.index', compact('lotes'));
    }

    public function create(): View|RedirectResponse
    {

        $categorias = Categoria::orderBy('nome')->get();
        return view('lote.form', compact('categorias'));
    }

    public function store(LoteRequest $request): RedirectResponse
    {
        try {
            $this->loteRepository->store($request);
            return redirect(route('lote.index'))->with('messages', ['success' => ['Produtos cadastrados no estoque com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o lote!']])->withInput($request->all());
        }
    }

    public function show(int $lote_id): JsonResponse
    {
        try {
            $lote = $this->loteRepository->getLote($lote_id);
            return response()->json(['success' => true, 'data' => $lote], 200);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json(['success' => true, 'data' => null, 'message' => 'Lote não encontrado'], 400);
            }
            return response()->json(['success' => true, 'data' => null, 'message' => 'Erro ao processar requisição. Tente novamente mais tarde.'.$e->getMessage()], 400);
        }
    }
}
