<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoRequest;
use App\Http\Requests\RelatorioProduto;
use App\Models\Catalogo;
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
use Illuminate\Support\Facades\Validator;


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

    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'modelo' => 'required',
                'descricao' => 'required',
            ], [
                'modelo.required' => 'O campo modelo do produto é obrigatório!',
                'descricao.required' => 'Necessário informar a descrição do produto'
            ]);


            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $data = [
                'modelo' => $request->modelo,
                'codigo' => $request->codigo,
                'ncm' => $request->ncm,
                'descricao' => $request->descricao,
                'catalogo_id' => $request->catalogo_id,
            ];
            $catalogo = Catalogo::findOrFail($request->catalogo_id);

            Produto::create($data);
            return redirect(route('catalogo.edit', $catalogo->id))->with('messages', ['success' => ['Produto criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o produto!']])->withInput($request->all());
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


    public function update(Request $request, int $id): RedirectResponse
    {
        try {
          $validator = Validator::make($request->all(), [
                'modelo' => 'required',
                'descricao' => 'required',
            ], [
                'modelo.required' => 'O campo modelo do produto é obrigatório!',
                'descricao.required' => 'Necessário informar a descrição do produto'
            ]);


            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $produto = Produto::findOrFail($id);
            
            $produto->update([
                'modelo' => $request->modelo,
                'codigo' => $request->codigo,
                'descricao' => $request->descricao_edit,
            ]);
            return redirect(route('catalogo.edit', $produto->catalogo_id))->with('messages', ['success' => ['Produto atualizado com sucesso!']]);
        } catch (Exception $e) {
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
