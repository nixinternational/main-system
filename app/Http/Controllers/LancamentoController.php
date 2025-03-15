<?php

namespace App\Http\Controllers;

use App\Enums\TipoLancamento;
use App\Http\Requests\LancamentoRequest;
use App\Models\Lancamento;
use App\Models\Lote;
use App\Repositories\LancamentoRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LancamentoController extends Controller
{

    private LancamentoRepository $lancamentoRepository;

    public function __construct(LancamentoRepository $lancamentoRepository)
    {
        $this->lancamentoRepository = $lancamentoRepository;
    }

    public function index() : View|RedirectResponse
    {

        $lancamentos = $this->lancamentoRepository->getIndex();
        return view('lancamento.index', compact('lancamentos'));
    }

    public function create(): View|RedirectResponse
    {

        return view('lancamento.form');
    }


    public function store(LancamentoRequest $request): RedirectResponse
    {
        try {

            $lote = Lote::findOrFail($request->lote_id);

            if ($lote->quantidadeAtual == 0) {
                return back()->with('messages', ['error' => ['Não é possível realizar lançamento de saída para lotes sem produtos!']])->withInput($request->all());;
            }

            $this->lancamentoRepository->saida($request->lote_id, $request->quantidade);

            return redirect(route('lancamento.index'))->with('messages', ['success' => ['Saídas lançadas com sucesso!']]);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return back()->with('messages', ['error' => ['Lote não encontrado!']])->withInput($request->all());;
            }
            return back()->with('messages', ['error' => ['Não foi possível salvar o lançamento!']])->withInput($request->all());;
        }
    }
}
