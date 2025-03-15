<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoRequest;
use App\Http\Requests\RelatorioProduto;
use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Marca;
use App\Models\Pedido;
use App\Models\PedidoProduto;
use App\Models\Producao;
use App\Models\Produto;
use App\Repositories\ProdutoRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProdutoController extends Controller
{
    private ProdutoRepository $produtoRepository;

    public function __construct(ProdutoRepository $produtoRepository)
    {
        $this->produtoRepository = $produtoRepository;
    }
    public function index(): View|RedirectResponse
    {

        $produtos = Produto::withTrashed()->when(request()->search != '', function ($query) {
            $query->where(DB::raw('lower(nome)'), 'like', '%' . request()->search . '%');
        })
        ->orderBy('id','asc')
        ->paginate(request()->paginacao ?? 10);
        return view('produto.index', compact('produtos'));
    }

    public function create(): View|RedirectResponse
    {

        $categorias = Categoria::all();
        return view('produto.form',compact('categorias'));
    }

    public function store(ProdutoRequest $request): RedirectResponse
    {
        try {


            Produto::create([
                'nome' => $request->nome,
                'unidade' => $request->unidade,
                'categoria_id' => $request->categoria_id,
                'precos' => [
                    'a' => $request->precoA,
                    'b' => $request->precoB,
                    'c' => $request->precoC,
                    'd' => $request->precoD,
                    'e' => $request->precoE,
                    'f' => $request->precoF,
                    'g' => $request->precoG,
                ]
            ]);
            return redirect(route('produto.index'))->with('messages', ['success' => ['Produto criado com sucesso!']]);
        } catch (\Exception $e) {

            return back()->with('messages', ['error' => ['Não foi possível salvar o produto. Tente novamente mais tarde!']])->withInput($request->all());
        }
    }

    public function show(int $produto_id): JsonResponse
    {
        try {
            $produto = $this->produtoRepository->getProduto($produto_id);
            return response()->json(['success' => true, 'data' => $produto], 200);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json(['success' => false, 'data' => null, 'message' => 'Produto não encontrado'], 400);
            }
            return response()->json(['success' => false, 'data' => null, 'message' => 'Erro ao processar requisição. Tente novamente mais tarde.'], 500);
        }
    }

    public function edit($id): View|RedirectResponse
    {

        try {

            $produto = Produto::findOrFail($id);
            $categorias = Categoria::all();

            return view('produto.form', compact('produto','categorias'));
        } catch (\Exception $e) {
		Log::info(json_encode($e,true));
		return back()->with('messages', ['error' => ['Não foi possível encontrar o produto!']]);
        }
    }

    public function update(ProdutoRequest $request, int $id): RedirectResponse
    {
        try {
            Produto::findOrFail($id)->update([
                'nome' => $request->nome,
                'unidade' => $request->unidade,
                'categoria_id' => $request->categoria_id,

                'precos' => [
                    'a' => str_replace(',', '.', $request->precoA),
                    'b' => str_replace(',', '.', $request->precoB),
                    'c' => str_replace(',', '.', $request->precoC),
                    'd' => str_replace(',', '.', $request->precoD),
                    'e' => str_replace(',', '.', $request->precoE),
                    'f' => str_replace(',', '.', $request->precoF),
                    'g' => str_replace(',', '.', $request->precoG),

                ]
            ]);
            return redirect(route('produto.index'))->with('messages', ['success' => ['Produto atualizado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível atualizar o produto!']])->withInput($request->all());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            Produto::findOrFail($id)->delete();
            return back()->with('messages', ['success' => ['Produto excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o produto!']]);
        }
    }

    public function ativar(int $produto_id)
    {
        try {
            Produto::withTrashed()->where('id', $produto_id)->update(['deleted_at' => null]);
            return back()->with('messages', ['success' => ['Produto ativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível ativar a categoria!' . $e->getMessage()]]);
        }
    }

    public function relatorioProduto(RelatorioProduto $request)
    {
        try {

            $datas = explode(' - ', $request->intervalo);

            $inicio = Carbon::createFromFormat('d/m/Y H:i', $datas[0]);
            $fim = Carbon::createFromFormat('d/m/Y H:i', $datas[1]);

            $pedidos = Pedido::whereBetween('dt_previsao', [$inicio, $fim])
                ->whereNotIn('status', ['CANCELADO'])
                ->pluck('id')->toArray();
            $produtos = DB::table('pedido_produtos')
                ->selectRaw('pedido_produtos.produto_id as produto,sum(quantidade) as total, produtos.nome as nome_produto')
                ->join('produtos', 'produtos.id', '=', 'pedido_produtos.produto_id')
                ->whereIn('pedido_produtos.pedido_id', $pedidos)
                ->when($request->produto != '', function ($query) {
                    $query->where('pedido_produtos.produto_id', '=', request()->produto);
                })
                ->groupBy('pedido_produtos.produto_id', 'produtos.nome')->get();
            $pdf =  Pdf::loadView('relatorios.pdf.produtos', [
                'total' => $produtos,
                'inicio' => $inicio,
                'fim' => $fim
            ]);
            return $pdf->download("Relatório produtos.pdf");
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'data' => null, 'message' => 'Erro ao processar requisição. Tente novamente mais tarde.' . $e->getMessage()], 400);
        }
    }
    public function relatorioProdutoIndex()
    {
        try {
            $produtos = Produto::get();
            return view('relatorios.produtosHoje', compact('produtos'));
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível abrir os relatórios!' . $e->getMessage()]]);
        }
    }

    public function relatorioProducaoIndex()
    {
        $produtos = Categoria::all();
        return view('relatorios.producao', compact('produtos'));
    }

    public function processRelatorioProducao(Request $request)
    {
        try {
            $datas = explode(' - ', $request->data);
            $inicio = Carbon::createFromFormat('d/m/Y H:i', $datas[0])->startOfDay();
            $fim = Carbon::createFromFormat('d/m/Y H:i', $datas[1])->endOfDay();
            
            $producaos = Producao::whereBetween('dt_inicio', [$inicio, $fim])
            ->when($request->produto != null && count($request->produto), function ($query) use ($request) {
                    $produtos = Produto::whereIn('categoria_id',$request->produto)->pluck('id')->toArray();
                    $query->whereIn('produto_id', $produtos);

                })
                ->selectRaw('categorias.nome as categoria ,produtos.nome as nome, sum(producaos.quantidade) as quantidade, producaos.turno  as turno')
                ->join('produtos', 'producaos.produto_id', '=', 'produtos.id')
                ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id') // Realiza o INNER JOIN

                ->groupBy('producaos.produto_id','produtos.nome','producaos.turno','categorias.nome')
                ->get();
            $producaoCategorias = [];
            foreach($producaos as $producao) {
                $producaoCategorias[$producao->categoria][] = $producao;
            }
            $pdf =  Pdf::loadView('relatorios.pdf.producao', [
                'producao' => $producaoCategorias,
                'inicio' => $inicio,
                'fim' => $fim,
            ]);
            $today = Carbon::now()->format('d-m-y H:i');
            return $pdf->download("Relatório producao $today.pdf");
        } catch (Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi gerar o relatório! ']]);
        }
    }
}
