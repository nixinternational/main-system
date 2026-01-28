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
use App\Services\Auditoria\CatalogoAuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
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

        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        
        $allowedColumns = ['id', 'nome', 'unidade', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        $produtos = Produto::withTrashed()
            ->when(request()->search != '', function ($query) {
                $query->where(DB::raw('lower(nome)'), 'like', '%' . request()->search . '%');
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(request()->paginacao ?? 10)
            ->appends(request()->except('page'));
        return view('produto.index', compact('produtos'));
    }

    public function searchByCatalogo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'catalogo_id' => ['required', 'integer', 'exists:catalogos,id'],
            'q' => ['nullable', 'string'],
        ]);

        $catalogo = Catalogo::select(['id', 'cliente_id'])->findOrFail($validated['catalogo_id']);
        $user = $request->user();
        if ($user && !$user->canAccessCliente($catalogo->cliente_id)) {
            return response()->json(['message' => 'Cliente não autorizado.'], 403);
        }

        $term = strtolower(trim((string) ($validated['q'] ?? '')));
        $perPage = 20;

        $query = Produto::query()
            ->where('catalogo_id', $catalogo->id)
            ->select(['id', 'modelo', 'codigo', 'ncm', 'descricao']);

        if ($term !== '') {
            $like = '%' . $term . '%';
            $query->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(modelo) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(codigo) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(ncm) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(descricao) LIKE ?', [$like]);
            });
        }

        $results = $query->orderBy('modelo')->paginate($perPage);

        $items = collect($results->items())->map(function ($produto) {
            $text = trim(($produto->modelo ?? '') . ' - ' . ($produto->codigo ?? ''));
            return [
                'id' => $produto->id,
                'text' => $text !== '-' ? $text : (string) $produto->id,
                'modelo' => $produto->modelo,
                'codigo' => $produto->codigo,
                'ncm' => $produto->ncm,
                'descricao' => $produto->descricao,
            ];
        })->values();

        return response()->json([
            'results' => $items,
            'pagination' => [
                'more' => $results->hasMorePages(),
            ],
        ]);
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
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('open_modal', 'exampleModal');
            }

            $produtoDuplicado = Produto::where('modelo', $request->modelo)
                ->where('codigo', $request->codigo)
                ->where('catalogo_id', $request->catalogo_id)
                ->exists();

            if ($produtoDuplicado) {
                return back()
                    ->withErrors(['codigo' => 'Já existe um produto com esse modelo e código nesse catálogo.'])
                    ->withInput()
                    ->with('open_modal', 'exampleModal');
            }

            $data = [
                'modelo'      => $request->modelo,
                'codigo'      => $request->codigo,
                'ncm'         => $request->ncm,
                'descricao'   => $request->descricao,
                'catalogo_id' => $request->catalogo_id,
                'fornecedor_id' => $request->fornecedor_id,
            ];

            $catalogo = Catalogo::findOrFail($request->catalogo_id);

            $produto = Produto::create($data);
            $auditService = app(CatalogoAuditService::class);
            $auditService->logCreate([
                'auditable_type' => Produto::class,
                'auditable_id' => $produto->id,
                'process_type' => 'catalogo',
                'process_id' => $produto->catalogo_id,
                'client_id' => $catalogo->cliente_id,
                'context' => 'catalogo.produto.create',
            ], $produto->getAttributes());

            if ($request->has('add_more') && $request->add_more == 1) {
                return redirect(route('catalogo.edit', $catalogo->id) . '?page=' . $request->page)
                    ->with('messages', ['success' => ['Produto criado com sucesso!']])
                    ->with('open_modal', 'exampleModal');
            }

            // Caso contrário, só volta para edição normal
            return redirect(route('catalogo.edit', $catalogo->id))
                ->with('messages', ['success' => ['Produto criado com sucesso!']]);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['produto' => 'Não foi possível cadastrar o produto!'])
                ->withInput()
                ->with('open_modal', 'exampleModal');
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
                'modelo_edit' => 'required',
                'descricao_edit' => 'required',
            ], [
                'modelo_edit.required' => 'O campo modelo do produto é obrigatório!',
                'descricao_edit.required' => 'Necessário informar a descrição do produto'
            ]);


            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('open_modal', 'editProductModal')
                    ->with('edit_product_id', $id);
            }
            $produto = Produto::findOrFail($id);
            $auditService = app(CatalogoAuditService::class);
            $produtoOriginal = $produto->getAttributes();

            $produto->update([
                'modelo' => $request->modelo_edit,
                'ncm' => $request->ncm_edit,
                'codigo' => $request->codigo_edit,
                'descricao' => $request->descricao_edit,
                'fornecedor_id' => $request->fornecedor_id_edit,

            ]);
            $produto->refresh();
            $catalogo = Catalogo::find($produto->catalogo_id);
            $auditService->logUpdate([
                'auditable_type' => Produto::class,
                'auditable_id' => $produto->id,
                'process_type' => 'catalogo',
                'process_id' => $produto->catalogo_id,
                'client_id' => $catalogo?->cliente_id,
                'context' => 'catalogo.produto.update',
            ], $produtoOriginal, $produto->getAttributes());


            if ($request->has('add_more_edit') && $request->add_more_edit == 1) {
                return redirect(route('catalogo.edit', $produto->catalogo_id) . '?page=' . $request->page)
                    ->with('messages', ['success' => ['Produto atualizado com sucesso!']])
                    ->with('open_modal', 'exampleModal');
            }


            return redirect(route('catalogo.edit', $produto->catalogo_id) . '?page=' . $request->page)->with('messages', ['success' => ['Produto atualizado com sucesso!']]);
        } catch (Exception $e) {
            return back()
                ->withErrors(['produto_edit' => 'Não foi possível atualizar o produto!'])
                ->withInput()
                ->with('open_modal', 'editProductModal')
                ->with('edit_product_id', $id);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $produto = Produto::findOrFail($id);
            $auditService = app(CatalogoAuditService::class);
            $produtoSnapshot = $produto->getAttributes();
            $catalogo = Catalogo::find($produto->catalogo_id);
            $produto->delete();
            $auditService->logDelete([
                'auditable_type' => Produto::class,
                'auditable_id' => $produto->id,
                'process_type' => 'catalogo',
                'process_id' => $produto->catalogo_id,
                'client_id' => $catalogo?->cliente_id,
                'context' => 'catalogo.produto.delete',
            ], $produtoSnapshot);
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
                    $produtos = Produto::whereIn('categoria_id', $request->produto)->pluck('id')->toArray();
                    $query->whereIn('produto_id', $produtos);
                })
                ->selectRaw('categorias.nome as categoria ,produtos.nome as nome, sum(producaos.quantidade) as quantidade, producaos.turno  as turno')
                ->join('produtos', 'producaos.produto_id', '=', 'produtos.id')
                ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id') // Realiza o INNER JOIN

                ->groupBy('producaos.produto_id', 'produtos.nome', 'producaos.turno', 'categorias.nome')
                ->get();
            $producaoCategorias = [];
            foreach ($producaos as $producao) {
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
