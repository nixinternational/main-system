<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\Processo;
use App\Models\ProcessoAereo;
use App\Models\ProcessoAereoProduto;
use App\Models\ProcessoRodoviario;
use App\Models\ProcessoRodoviarioProduto;
use App\Models\ProcessoProduto;
use App\Models\ProcessoProdutoMulta;
use App\Models\Fornecedor;
use App\Models\Produto;
use App\Services\Auditoria\ProcessoAuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ProcessoController extends Controller
{



    public function updatecurrencies()
    {
        Artisan::call('atualizar:moedas'); // dispara a command
        return back()->with('messages', ['success' => ['Moedas atualizadas com sucesso!']]);;
    }
    public static function getBid()
    {
        $cacheKey = 'cotacoes_bids_' . now()->format('Y-m-d');
        return Cache::get($cacheKey, []);
    }

    private static function buscarMoedasSuportadas(): array
    {
        $cacheKey = 'moedas_suportadas';
        $cacheTtl = now()->addWeek();

        return Cache::remember($cacheKey, $cacheTtl, function () {
            try {
                $resposta = Http::timeout(10)->get(
                    'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?$format=json'
                );
                $dados = $resposta->json()['value'] ?? [];
                return collect($dados)
                    ->pluck('nomeFormatado', 'simbolo')
                    ->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });
    }




    private static function cotacaoVazia(string $codigo, string $nome, ?string $erro = null): array
    {
        return [
            'nome' => $nome,
            'moeda' => $codigo,
            'compra' => null,
            'venda' => null,
            'erro' => $erro,
        ];
    }

    public function index()
    {
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        
        $allowedColumns = ['id', 'nome'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        $user = auth()->user();
        $allowedClienteIds = $user?->accessibleClienteIds();

        $processos = Cliente::when(request()->search != '', function ($query) {
            // $query->where('name','like','%'.request()->search.'%');
        })
        ->when($allowedClienteIds !== null, function ($query) use ($allowedClienteIds) {
            $query->whereIn('id', $allowedClienteIds);
        })
        ->orderBy($sortColumn, $sortDirection)
        ->paginate(request()->paginacao ?? 10)
        ->appends(request()->except('page'));
        
        return view('processo.index', compact('processos'));
    }

    public function processoCliente($cliente_id)
    {
        $this->ensureClienteAccess((int) $cliente_id);
        $sortColumn = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        
        $allowedColumns = ['id', 'codigo_interno', 'descricao', 'status', 'canal', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        // Buscar processos marítimos e adicionar tipo_processo
        $processosMaritimos = Processo::when(request()->search != '', function ($query) {})
            ->where('cliente_id', $cliente_id)
            ->get()
            ->map(function ($processo) {
                $processo->tipo_processo = $processo->tipo_processo ?? 'maritimo';
                return $processo;
            });
        
        // Buscar processos aéreos e adicionar tipo_processo
        $processosAereos = ProcessoAereo::when(request()->search != '', function ($query) {})
            ->where('cliente_id', $cliente_id)
            ->get()
            ->map(function ($processo) {
                $processo->tipo_processo = 'aereo';
                return $processo;
            });
        
        // Buscar processos rodoviários e adicionar tipo_processo
        $processosRodoviarios = ProcessoRodoviario::when(request()->search != '', function ($query) {})
            ->where('cliente_id', $cliente_id)
            ->get()
            ->map(function ($processo) {
                $processo->tipo_processo = 'rodoviario';
                return $processo;
            });
        
        // Unir as collections
        $processosUnidos = $processosMaritimos->concat($processosAereos)->concat($processosRodoviarios);
        
        // Ordenar a collection
        $processosOrdenados = $processosUnidos->sortBy(function ($processo) use ($sortColumn, $sortDirection) {
            $value = $processo->$sortColumn ?? '';
            return is_numeric($value) ? (float)$value : strtolower($value);
        }, SORT_REGULAR, $sortDirection === 'desc');
        
        // Paginar manualmente
        $perPage = request()->paginacao ?? 10;
        $currentPage = request()->get('page', 1);
        $items = $processosOrdenados->values();
        $total = $items->count();
        $items = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        // Criar paginator manual
        $processos = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
            
        $cliente = Cliente::findOrFail($cliente_id);
        return view('processo.processos', compact('processos', 'cliente'));
    }


    public function create(Request $request, $cliente_id)
    {
        try {
            if ($cliente_id == null) {
                return back()->with('messages', ['error' => ['Não foi possível cadastrar o processo!']]);
            }

            $this->ensureClienteAccess((int) $cliente_id);
            
            $tipo_processo = $request->input('tipo_processo', 'maritimo');
            
            // Validar tipo_processo
            if (!in_array($tipo_processo, ['maritimo', 'aereo', 'rodoviario'])) {
                return back()->with('messages', ['error' => ['Tipo de processo inválido!']]);
            }
            
            // Criar processo na tabela correta
            if ($tipo_processo === 'aereo') {
                $dadosCriacao = [
                    'codigo_interno' => '-',
                    'numero_processo' => '-',
                    'cliente_id' => $cliente_id,
                ];
                $processo = ProcessoAereo::create($dadosCriacao);
                DB::afterCommit(function () use ($processo, $cliente_id, $dadosCriacao) {
                    $auditService = app(ProcessoAuditService::class);
                    $auditService->logCreate([
                        'auditable_type' => ProcessoAereo::class,
                        'auditable_id' => $processo->id,
                        'process_type' => 'aereo',
                        'process_id' => $processo->id,
                        'client_id' => $cliente_id,
                        'context' => 'processo.create',
                    ], $dadosCriacao);
                });
                return redirect(route('processo.edit', ['processo' => $processo->id, 'tipo_processo' => 'aereo']))->with('messages', ['success' => ['Processo aéreo criado com sucesso!']]);
            } elseif ($tipo_processo === 'rodoviario') {
                $dadosCriacao = [
                    'codigo_interno' => '-',
                    'numero_processo' => '-',
                    'cliente_id' => $cliente_id,
                ];
                $processo = ProcessoRodoviario::create($dadosCriacao);
                DB::afterCommit(function () use ($processo, $cliente_id, $dadosCriacao) {
                    $auditService = app(ProcessoAuditService::class);
                    $auditService->logCreate([
                        'auditable_type' => ProcessoRodoviario::class,
                        'auditable_id' => $processo->id,
                        'process_type' => 'rodoviario',
                        'process_id' => $processo->id,
                        'client_id' => $cliente_id,
                        'context' => 'processo.create',
                    ], $dadosCriacao);
                });
                return redirect(route('processo.edit', ['processo' => $processo->id, 'tipo_processo' => 'rodoviario']))->with('messages', ['success' => ['Processo rodoviário criado com sucesso!']]);
            } else {
                // Apenas marítimo cai aqui
                $dadosCriacao = [
                    'codigo_interno' => '-',
                    'numero_processo' => '-',
                    'cliente_id' => $cliente_id,
                    'tipo_processo' => $tipo_processo
                ];
                $processo = Processo::create($dadosCriacao);
                DB::afterCommit(function () use ($processo, $cliente_id, $dadosCriacao, $tipo_processo) {
                    $auditService = app(ProcessoAuditService::class);
                    $auditService->logCreate([
                        'auditable_type' => Processo::class,
                        'auditable_id' => $processo->id,
                        'process_type' => $tipo_processo,
                        'process_id' => $processo->id,
                        'client_id' => $cliente_id,
                        'context' => 'processo.create',
                    ], $dadosCriacao);
                });
                return redirect(route('processo.edit', ['processo' => $processo->id, 'tipo_processo' => $tipo_processo]))->with('messages', ['success' => ['Processo criado com sucesso!']]);
            }
        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o processo!']])->withInput($request->all());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cliente_id' => 'required',
            ], [
                'cliente_id.required' => 'O campo cliente do tipo de documento é obrigatório!',

            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            $cliente_id = (int) $request->cliente_id;
            $this->ensureClienteAccess($cliente_id);
            $tipo_processo = $request->input('tipo_processo', 'maritimo');
            
            // Validar tipo_processo
            if (!in_array($tipo_processo, ['maritimo', 'aereo', 'rodoviario'])) {
                $tipo_processo = 'maritimo';
            }
            
            // Criar processo na tabela correta
            if ($tipo_processo === 'aereo') {
                $dadosCriacao = [
                    'codigo_interno' => '-',
                    'numero_processo' => '-',
                    'cliente_id' => $cliente_id,
                ];
                $processo = ProcessoAereo::create($dadosCriacao);
                DB::afterCommit(function () use ($processo, $cliente_id, $dadosCriacao) {
                    $auditService = app(ProcessoAuditService::class);
                    $auditService->logCreate([
                        'auditable_type' => ProcessoAereo::class,
                        'auditable_id' => $processo->id,
                        'process_type' => 'aereo',
                        'process_id' => $processo->id,
                        'client_id' => $cliente_id,
                        'context' => 'processo.create',
                    ], $dadosCriacao);
                });
                return redirect(route('processo.edit', ['processo' => $processo->id, 'tipo_processo' => 'aereo']))->with('messages', ['success' => ['Processo aéreo criado com sucesso!']]);
            } elseif ($tipo_processo === 'rodoviario') {
                $dadosCriacao = [
                    'codigo_interno' => '-',
                    'numero_processo' => '-',
                    'cliente_id' => $cliente_id,
                ];
                $processo = ProcessoRodoviario::create($dadosCriacao);
                DB::afterCommit(function () use ($processo, $cliente_id, $dadosCriacao) {
                    $auditService = app(ProcessoAuditService::class);
                    $auditService->logCreate([
                        'auditable_type' => ProcessoRodoviario::class,
                        'auditable_id' => $processo->id,
                        'process_type' => 'rodoviario',
                        'process_id' => $processo->id,
                        'client_id' => $cliente_id,
                        'context' => 'processo.create',
                    ], $dadosCriacao);
                });
                return redirect(route('processo.edit', ['processo' => $processo->id, 'tipo_processo' => 'rodoviario']))->with('messages', ['success' => ['Processo rodoviário criado com sucesso!']]);
            } else {
                $dadosCriacao = [
                    'codigo_interno' => '-',
                    'numero_processo' => '-',
                    'cliente_id' => $cliente_id,
                    'tipo_processo' => $tipo_processo
                ];
                $processo = Processo::create($dadosCriacao);
                DB::afterCommit(function () use ($processo, $cliente_id, $dadosCriacao, $tipo_processo) {
                    $auditService = app(ProcessoAuditService::class);
                    $auditService->logCreate([
                        'auditable_type' => Processo::class,
                        'auditable_id' => $processo->id,
                        'process_type' => $tipo_processo,
                        'process_id' => $processo->id,
                        'client_id' => $cliente_id,
                        'context' => 'processo.create',
                    ], $dadosCriacao);
                });
                return redirect(route('processo.edit', ['processo' => $processo->id, 'tipo_processo' => $tipo_processo]))->with('messages', ['success' => ['Processo criado com sucesso!']]);
            }
        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
        }
    }


    public function show($id)
    {
        //
    }
    public function esbocoPdf($id, Request $request)
    {
        // Verificar se é processo aéreo, rodoviário ou marítimo
        $tipoProcesso = $request->query('tipo_processo', 'maritimo');
        
        if ($tipoProcesso === 'aereo') {
            $processo = ProcessoAereo::with([
                'cliente',
                'processoAereoProdutos.produto.fornecedor',
                'fornecedor'
            ])->findOrFail($id);
        } elseif ($tipoProcesso === 'rodoviario') {
            $processo = ProcessoRodoviario::with([
                'cliente',
                'processoRodoviarioProdutos.produto.fornecedor',
                'fornecedor'
            ])->findOrFail($id);
        } else {
            $processo = Processo::with([
                'cliente',
                'processoProdutos.produto.fornecedor',
                'fornecedor'
            ])->findOrFail($id);
        }
        
        $this->ensureClienteAccess($processo->cliente_id);
        
        // Parse dos campos monetários do processo
        $processo = $this->parseModelFieldsFromModel($processo);
        
        // Parse dos campos monetários dos produtos e manter a relação produto
        $processoProdutos = [];
        
        if ($tipoProcesso === 'aereo') {
            foreach ($processo->processoAereoProdutos as $produto) {
                $produtoParsed = $this->parseModelFieldsFromModel($produto);
                // Manter a relação produto após o parse
                $produtoParsed->setRelation('produto', $produto->produto);
                $processoProdutos[] = $produtoParsed;
            }
        } elseif ($tipoProcesso === 'rodoviario') {
            foreach ($processo->processoRodoviarioProdutos as $produto) {
                $produtoParsed = $this->parseModelFieldsFromModel($produto);
                // Manter a relação produto após o parse
                $produtoParsed->setRelation('produto', $produto->produto);
                $processoProdutos[] = $produtoParsed;
            }
        } else {
            foreach ($processo->processoProdutos as $produto) {
                $produtoParsed = $this->parseModelFieldsFromModel($produto);
                // Manter a relação produto após o parse
                $produtoParsed->setRelation('produto', $produto->produto);
                $processoProdutos[] = $produtoParsed;
            }
        }
        
        // Calcular totais
        $totalProdutos = 0;
        $totalICMS = 0;
        $totalIPI = 0;
        $totalPIS = 0;
        $totalCOFINS = 0;
        $totalICMSST = 0;
        $totalNota = 0;
        
        foreach ($processoProdutos as $produto) {
            $totalProdutos += $produto->valor_total_nf ?? 0;
            $totalICMS += $produto->valor_icms_reduzido ?? 0;
            $totalIPI += $produto->valor_ipi ?? 0;
            $totalPIS += $produto->valor_pis ?? 0;
            $totalCOFINS += $produto->valor_cofins ?? 0;
            $totalICMSST += $produto->valor_icms_st ?? 0;
            $totalNota += $produto->valor_total_nf_com_icms_st ?? 0;
        }
        
        $totalBaseCalculoReducao = collect($processoProdutos)->sum(function ($produto) {
            return $produto->base_icms_reduzido ?? 0;
        });

        $totalDespesasAduaneirasItens = collect($processoProdutos)->sum(function ($produto) {
            return $produto->despesa_aduaneira ?? 0;
        });
        
        $dados = [
            'processo' => $processo,
            'processoProdutos' => $processoProdutos,
            'cliente' => $processo->cliente,
            'totalProdutos' => $totalProdutos,
            'totalICMS' => $totalICMS,
            'totalIPI' => $totalIPI,
            'totalPIS' => $totalPIS,
            'totalCOFINS' => $totalCOFINS,
            'totalICMSST' => $totalICMSST,
            'totalNota' => $totalNota,
            'totalBaseCalculoReducao' => $totalBaseCalculoReducao,
            'totalDespesasAduaneirasItens' => $totalDespesasAduaneirasItens,
        ];

        $pdf = Pdf::loadView('processo.esboco', $dados);
        return $pdf->stream('esboco.pdf');
    }
    private function parseModelFieldsFromModel($model)
    {
        foreach ($model->getAttributes() as $field => $value) {

            if (!is_null($value) && is_numeric($value) && !in_array($field, [
                'cotacao_frete_internacional',
                'cotacao_seguro_internacional',
                'cotacao_acrescimo_frete',
                'transportadora_cnpj'
            ])) {

                // apenas transforma string numérica em float, sem truncar casas decimais
                $model->$field = (float) $value;

                // se quiser, pode controlar arredondamento só para exibição:
                // $model->$field = round((float)$value, 7); // por exemplo, até 7 casas
            } elseif (is_null($value) && !in_array($field, [
                'cotacao_frete_internacional',
                'cotacao_seguro_internacional',
                'cotacao_acrescimo_frete',
                'transportadora_cnpj',
                'fornecedor_id',
                'cliente_id',
                'catalogo_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ]) && in_array($field, [
                'outras_taxas_agente',
                'delivery_fee',
                'delivery_fee_brl',
                'collect_fee',
                'collect_fee_brl',
                'desconsolidacao',
                'handling',
                'dai',
                'dape',
                'correios',
                'li_dta_honor_nix',
                'honorarios_nix',
                'diferenca_cambial_frete',
                'diferenca_cambial_fob'
            ])) {
                // Converter valores null para 0 para campos monetários específicos do processo aéreo
                $model->$field = 0;
            }
        }

        return $model;
    }

    public function edit($id)
    {
        try {
            // Obter o tipo de processo do query param ou tentar detectar
            $tipoProcessoRequest = request()->get('tipo_processo', null);
            $processoModel = null;
            $tipoProcesso = 'maritimo';
            $processoProdutosCollection = null;
            
            // Se o tipo foi especificado no query param, buscar diretamente na tabela específica
            if ($tipoProcessoRequest === 'aereo') {
                $processoModel = ProcessoAereo::find($id);
                if ($processoModel) {
                $tipoProcesso = 'aereo';
                $this->ensureClienteAccess($processoModel->cliente_id);
                $processoModel->loadMissing([
                        'cliente:id,nome',
                        'cliente.fornecedores:id,cliente_id,nome',
                        'processoAereoProdutos' => function ($query) {
                            $query->with([
                                'produto:id,modelo,codigo,ncm,descricao,fornecedor_id',
                                'produto.fornecedor:id,cliente_id,nome'
                            ]);
                        },
                    'fornecedor'
                ]);
                $processoProdutosCollection = $processoModel->processoAereoProdutos;
                }
            } elseif ($tipoProcessoRequest === 'rodoviario') {
                $processoModel = ProcessoRodoviario::find($id);
                if ($processoModel) {
                $tipoProcesso = 'rodoviario';
                $this->ensureClienteAccess($processoModel->cliente_id);
                $processoModel->loadMissing([
                        'cliente:id,nome',
                        'cliente.fornecedores:id,cliente_id,nome',
                        'processoRodoviarioProdutos' => function ($query) {
                            $query->with([
                                'produto:id,modelo,codigo,ncm,descricao,fornecedor_id',
                                'produto.fornecedor:id,cliente_id,nome'
                            ]);
                        },
                    'fornecedor'
                ]);
                $processoProdutosCollection = $processoModel->processoRodoviarioProdutos;
                }
            } else {
                // Se tipo não foi especificado ou é marítimo, buscar na tabela principal Processo
                $processoPrincipal = Processo::find($id);
                
                if ($processoPrincipal) {
                    // Se encontrou na tabela principal, usar o tipo_processo dela
                    $tipoProcesso = $processoPrincipal->tipo_processo ?? 'maritimo';
                    $processoModel = $processoPrincipal;
                    $this->ensureClienteAccess($processoModel->cliente_id);
                    $processoModel->loadMissing([
                        'cliente:id,nome',
                        'cliente.fornecedores:id,cliente_id,nome',
                        'processoProdutos' => function ($query) {
                            $query->with([
                                'produto:id,modelo,codigo,ncm,descricao,fornecedor_id',
                                'produto.fornecedor:id,cliente_id,nome'
                            ]);
                        },
                        'processoProdutosMulta' => function ($query) {
                            $query->with([
                                'produto:id,modelo,codigo,ncm,descricao,fornecedor_id',
                                'produto.fornecedor:id,cliente_id,nome'
                            ]);
                        },
                        'fornecedor'
                    ]);
                    $processoProdutosCollection = $processoModel->processoProdutos;
                } else {
                    // Se não encontrou na tabela principal e tipo não foi especificado, tentar em todas as tabelas
                    $processoModel = ProcessoAereo::find($id);
                    if ($processoModel) {
                        $tipoProcesso = 'aereo';
                        $this->ensureClienteAccess($processoModel->cliente_id);
                        $processoModel->loadMissing([
                            'cliente:id,nome',
                            'cliente.fornecedores:id,cliente_id,nome',
                            'processoAereoProdutos' => function ($query) {
                                $query->with([
                                    'produto:id,modelo,codigo,ncm,descricao,fornecedor_id',
                                    'produto.fornecedor:id,cliente_id,nome'
                                ]);
                            },
                            'fornecedor'
                        ]);
                        $processoProdutosCollection = $processoModel->processoAereoProdutos;
                    } else {
                        $processoModel = ProcessoRodoviario::find($id);
                        if ($processoModel) {
                        $tipoProcesso = 'rodoviario';
                        $this->ensureClienteAccess($processoModel->cliente_id);
                        $processoModel->loadMissing([
                            'cliente:id,nome',
                            'cliente.fornecedores:id,cliente_id,nome',
                        'processoRodoviarioProdutos' => function ($query) {
                            $query->with([
                                'produto:id,modelo,codigo,ncm,descricao,fornecedor_id',
                                'produto.fornecedor:id,cliente_id,nome'
                            ]);
                            },
                            'fornecedor'
                        ]);
                        $processoProdutosCollection = $processoModel->processoRodoviarioProdutos;
                        }
                    }
                }
            }
            
            // Se não encontrou em nenhuma tabela, lançar erro
            if (!$processoModel) {
                return redirect(route('processo.index'))->with('messages', ['error' => ['Processo não encontrado!']]);
            }
            
            $processo = $this->parseModelFieldsFromModel($processoModel);
            $catalogo = Catalogo::where('cliente_id', $processo->cliente_id)->first();
            if (!$catalogo) {
                return redirect(route('processo.index'))->with('messages', ['error' => ['Não é possível acessar um processo com catálogo desde cliente vazio!']]);
            }
            $catalogoId = $catalogo->id;
            $catalogoProductsThreshold = config('app.catalogo_produtos_ajax_threshold', 2000);
            $catalogoProductsCount = $catalogo->produtos()->count();
            $processoProdutosCount = $processoProdutosCollection ? $processoProdutosCollection->count() : 0;
            $produtosMultaCount = ($processoModel instanceof Processo && $processoModel->relationLoaded('processoProdutosMulta'))
                ? $processoModel->processoProdutosMulta->count()
                : 0;
            $optionsBudget = config('app.catalogo_produtos_ajax_options_budget', 30000);
            $estimatedOptions = max(1, $processoProdutosCount + $produtosMultaCount) * $catalogoProductsCount;
            $useProductsAjax = $catalogoProductsCount > $catalogoProductsThreshold || $estimatedOptions > $optionsBudget;

            $produtoIds = $processoProdutosCollection
                ? $processoProdutosCollection->pluck('produto_id')->filter()->unique()
                : collect();

            if ($processoModel instanceof Processo && $processoModel->relationLoaded('processoProdutosMulta')) {
                $produtoIds = $produtoIds->merge(
                    $processoModel->processoProdutosMulta->pluck('produto_id')->filter()
                )->unique();
            }

            $baseProductsQuery = Produto::query()
                ->where('catalogo_id', $catalogoId)
                ->select(['id', 'modelo', 'codigo', 'ncm', 'descricao']);

            $productsClient = $useProductsAjax
                ? ( $produtoIds->isEmpty()
                    ? collect()
                    : $baseProductsQuery->whereIn('id', $produtoIds)->get()
                )
                : $baseProductsQuery->get();
            $dolar = self::getBid();

            $moedasSuportadas = self::buscarMoedasSuportadas();

            $fornecedoresPorProduto = $processoProdutosCollection
                ? $processoProdutosCollection
                    ->map(function ($processoProduto) {
                        return $processoProduto->produto?->fornecedor;
                    })
                    ->filter()
                    ->unique('id')
                    ->values()
                : collect();

            $podeSelecionarFornecedor = $processoProdutosCollection && $processoProdutosCollection->count() > 0;

            if ($podeSelecionarFornecedor && $fornecedoresPorProduto->isEmpty()) {
                $fornecedoresPorProduto = optional($processo->cliente)->fornecedores ?? collect();
            }

            $fornecedoresEsboco = $fornecedoresPorProduto ?? collect();

            $produtos = [];
            if ($processoProdutosCollection && $processoProdutosCollection->count() > 0) {
                foreach ($processoProdutosCollection as $produto) {
                    $produtos[] = $this->parseModelFieldsFromModel($produto);
                }
            }
            $processoProdutos = $produtos;

            $produtosMulta = [];
            if ($processoModel instanceof Processo) {
                $processoProdutosMultaCollection = $processoModel->processoProdutosMulta;
                if ($processoProdutosMultaCollection && $processoProdutosMultaCollection->count() > 0) {
                    foreach ($processoProdutosMultaCollection as $produtoMulta) {
                        $produtosMulta[] = $this->parseModelFieldsFromModel($produtoMulta);
                    }
                }
            }
            $processoProdutosMulta = $produtosMulta;
            
            // Determinar a view baseada no tipo_processo
            $viewName = 'processo.form-' . $tipoProcesso;
            
            // Se a view específica não existir, usar a view marítimo como fallback
            if (!view()->exists($viewName)) {
                $viewName = 'processo.form-maritimo';
            }
            
            return view($viewName, compact(
                'processo',
                'productsClient',
                'dolar',
                'processoProdutos',
                'processoProdutosMulta',
                'moedasSuportadas',
                'fornecedoresEsboco',
                'podeSelecionarFornecedor',
                'tipoProcesso',
                'useProductsAjax',
                'catalogoId'
            ));
        } catch (ModelNotFoundException $e) {
            return redirect(route('processo.index'))->with('messages', ['error' => ['Processo não encontrado!']]);
        } catch (Exception $e) {
            Log::error('Erro ao editar processo: ' . $e->getMessage(), [
                'id' => $id,
                'tipo_processo' => request()->get('tipo_processo'),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect(route('processo.index'))->with('messages', ['error' => ['Erro ao carregar o processo. Por favor, tente novamente.']]);
        }
    }

    private function parseMoneyToFloat($value, int $decimals = 2)
    {
        if (is_null($value) || $value === '') return null;
        //1.231,23123
        //1231.23123
        if (str_contains($value, ',')) {
            $value = str_replace(['.', ','], ['', '.'], $value);
        }
        // dump($value);
        return (float) $value;
    }
    /**
     * Processar produtos aéreos
     */
    private function processarProdutosAereos($request, $id, $processo, $produtosExistentes, $processType, &$pesoLiquidoTotal, &$produtosProcessados, &$auditEntries)
    {
        foreach ($request->produtos as $key => $produto) {
            $pesoLiquidoTotal += isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : 0;
            
            // Verificar tipo de peso do processo
            $tipoPeso = $processo->tipo_peso ?? 'lbs';
            
            $pesoLiqLbsValue = null;
            $pesoLiqKgValue = null;
            $pesoLiqTotalKgValue = null;
            
            if ($tipoPeso === 'kg') {
                // Modo KG: usar peso_liq_kg
                if (isset($produto['peso_liq_kg']) && $produto['peso_liq_kg'] !== '') {
                    $pesoLiqKgValue = $this->parseMoneyToFloat($produto['peso_liq_kg']);
                    // peso_liq_total_kg = peso_liq_kg (sem conversão)
                    $pesoLiqTotalKgValue = $pesoLiqKgValue;
                }
            } else {
                // Modo LBS: usar peso_liq_lbs
                if (isset($produto['peso_liq_lbs']) && $produto['peso_liq_lbs'] !== '') {
                    $pesoLiqLbsValue = $this->parseMoneyToFloat($produto['peso_liq_lbs']);
                }
                // peso_liq_total_kg será calculado a partir de peso_liq_lbs * fator
                if (isset($produto['peso_liq_total_kg']) && $produto['peso_liq_total_kg'] !== '') {
                    $pesoLiqTotalKgValue = $this->parseMoneyToFloat($produto['peso_liq_total_kg']);
                }
            }
            
            $dadosProduto = [
                'item' => $produto['item'],
                'produto_id' => $produto['produto_id'],
                'adicao' => isset($produto['adicao']) ? (int)$produto['adicao'] : null,
                'origem' => isset($produto['origem']) ? $produto['origem'] : null,
                'codigo_giiro' => isset($produto['codigo_giiro']) ? $produto['codigo_giiro'] : null,
                'quantidade' => isset($produto['quantidade']) ? $this->parseMoneyToFloat($produto['quantidade']) : null,
                'peso_liquido_unitario' => isset($produto['peso_liquido_unitario']) && $produto['peso_liquido_unitario'] !== '' ? $this->parseMoneyToFloat($produto['peso_liquido_unitario']) : null,
                'peso_liquido_total' => isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : null,
                'fator_peso' => isset($produto['fator_peso']) ? $this->parseMoneyToFloat($produto['fator_peso']) : null,
                'fob_unit_usd' => (isset($produto['fob_unit_usd']) && trim($produto['fob_unit_usd']) !== '') ? $this->parseMoneyToFloat($produto['fob_unit_usd']) : (array_key_exists('fob_unit_usd', $produto) ? null : null),
                'fob_total_usd' => isset($produto['fob_total_usd']) ? $this->parseMoneyToFloat($produto['fob_total_usd']) : null,
                'fob_total_brl' => isset($produto['fob_total_brl']) ? $this->parseMoneyToFloat($produto['fob_total_brl']) : null,
                'frete_usd' => isset($produto['frete_usd']) ? $this->parseMoneyToFloat($produto['frete_usd']) : null,
                'frete_brl' => isset($produto['frete_brl']) ? $this->parseMoneyToFloat($produto['frete_brl']) : null,
                'seguro_usd' => isset($produto['seguro_usd']) ? $this->parseMoneyToFloat($produto['seguro_usd']) : null,
                'seguro_brl' => isset($produto['seguro_brl']) ? $this->parseMoneyToFloat($produto['seguro_brl']) : null,
                'acresc_frete_usd' => isset($produto['acresc_frete_usd']) ? $this->parseMoneyToFloat($produto['acresc_frete_usd']) : null,
                'acresc_frete_brl' => isset($produto['acresc_frete_brl']) ? $this->parseMoneyToFloat($produto['acresc_frete_brl']) : null,
                'thc_usd' => isset($produto['thc_usd']) ? $this->parseMoneyToFloat($produto['thc_usd']) : null,
                'thc_brl' => isset($produto['thc_brl']) ? $this->parseMoneyToFloat($produto['thc_brl']) : null,
                // Campos específicos do transporte aéreo
                'delivery_fee' => isset($produto['delivery_fee']) ? $this->parseMoneyToFloat($produto['delivery_fee']) : null,
                'delivery_fee_brl' => isset($produto['delivery_fee_brl']) ? $this->parseMoneyToFloat($produto['delivery_fee_brl']) : null,
                'collect_fee' => isset($produto['collect_fee']) ? $this->parseMoneyToFloat($produto['collect_fee']) : null,
                'collect_fee_brl' => isset($produto['collect_fee_brl']) ? $this->parseMoneyToFloat($produto['collect_fee_brl']) : null,
                'dai' => isset($produto['dai']) ? $this->parseMoneyToFloat($produto['dai']) : null,
                'dape' => isset($produto['dape']) ? $this->parseMoneyToFloat($produto['dape']) : null,
                'vlr_cfr_unit' => isset($produto['vlr_cfr_unit']) ? $this->parseMoneyToFloat($produto['vlr_cfr_unit']) : null,
                'vlr_cfr_total' => isset($produto['vlr_cfr_total']) ? $this->parseMoneyToFloat($produto['vlr_cfr_total']) : null,
                'valor_aduaneiro_usd' => isset($produto['valor_aduaneiro_usd']) ? $this->parseMoneyToFloat($produto['valor_aduaneiro_usd']) : null,
                'valor_aduaneiro_brl' => isset($produto['valor_aduaneiro_brl']) ? $this->parseMoneyToFloat($produto['valor_aduaneiro_brl']) : null,
                'ii_percent' => isset($produto['ii_percent']) && trim($produto['ii_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['ii_percent'])) !== '' ? $this->safePercentage($produto['ii_percent']) : null,
                'ipi_percent' => isset($produto['ipi_percent']) && trim($produto['ipi_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['ipi_percent'])) !== '' ? $this->safePercentage($produto['ipi_percent']) : null,
                'pis_percent' => isset($produto['pis_percent']) && trim($produto['pis_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['pis_percent'])) !== '' ? $this->safePercentage($produto['pis_percent']) : null,
                'cofins_percent' => isset($produto['cofins_percent']) && trim($produto['cofins_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['cofins_percent'])) !== '' ? $this->safePercentage($produto['cofins_percent']) : null,
                'icms_percent' => isset($produto['icms_percent']) && trim($produto['icms_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['icms_percent'])) !== '' ? $this->safePercentage($produto['icms_percent']) : null,
                'icms_reduzido_percent' => isset($produto['icms_reduzido_percent']) && trim($produto['icms_reduzido_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['icms_reduzido_percent'])) !== '' ? $this->safePercentage($produto['icms_reduzido_percent']) : null,
                'frete_moeda_estrangeira' => isset($produto['frete_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['frete_moeda_estrangeira']) : null,
                'seguro_moeda_estrangeira' => isset($produto['seguro_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['seguro_moeda_estrangeira']) : null,
                'acrescimo_moeda_estrangeira' => isset($produto['acrescimo_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['acrescimo_moeda_estrangeira']) : null,
                'frete_moeda' => $request->frete_internacional_moeda,
                'seguro_moeda' => $request->seguro_internacional_moeda,
                'acrescimo_moeda' => $request->acrescimo_frete_moeda,
                'valor_ii' => isset($produto['valor_ii']) ? $this->parseMoneyToFloat($produto['valor_ii']) : null,
                'base_ipi' => isset($produto['base_ipi']) ? $this->parseMoneyToFloat($produto['base_ipi']) : null,
                'valor_ipi' => isset($produto['valor_ipi']) ? $this->parseMoneyToFloat($produto['valor_ipi']) : null,
                'base_pis_cofins' => isset($produto['base_pis_cofins']) ? $this->parseMoneyToFloat($produto['base_pis_cofins']) : null,
                'valor_pis' => isset($produto['valor_pis']) ? $this->parseMoneyToFloat($produto['valor_pis']) : null,
                'valor_cofins' => isset($produto['valor_cofins']) ? $this->parseMoneyToFloat($produto['valor_cofins']) : null,
                'despesa_aduaneira' => isset($produto['despesa_aduaneira']) ? $this->parseMoneyToFloat($produto['despesa_aduaneira']) : null,
                'base_icms_sem_reducao' => isset($produto['base_icms_sem_reducao']) ? $this->parseMoneyToFloat($produto['base_icms_sem_reducao']) : null,
                'valor_icms_sem_reducao' => isset($produto['valor_icms_sem_reducao']) ? $this->parseMoneyToFloat($produto['valor_icms_sem_reducao']) : null,
                'base_icms_reduzido' => isset($produto['base_icms_reduzido']) ? $this->parseMoneyToFloat($produto['base_icms_reduzido']) : null,
                'valor_icms_reduzido' => isset($produto['valor_icms_reduzido']) ? $this->parseMoneyToFloat($produto['valor_icms_reduzido']) : null,
                'valor_unit_nf' => isset($produto['valor_unit_nf']) ? $this->parseMoneyToFloat($produto['valor_unit_nf']) : null,
                'valor_total_nf' => isset($produto['valor_total_nf']) ? $this->parseMoneyToFloat($produto['valor_total_nf']) : null,
                'base_icms_st' => isset($produto['base_icms_st']) ? $this->parseMoneyToFloat($produto['base_icms_st']) : null,
                'mva' => isset($produto['mva']) ? $this->parseMoneyToFloat($produto['mva']) : null,
                'valor_icms_st' => isset($produto['valor_icms_st']) ? $this->parseMoneyToFloat($produto['valor_icms_st']) : null,
                'icms_st' => isset($produto['icms_st']) ? $this->parseMoneyToFloat($produto['icms_st']) : null,
                'valor_total_nf_com_icms_st' => isset($produto['valor_total_nf_com_icms_st']) ? $this->parseMoneyToFloat($produto['valor_total_nf_com_icms_st']) : null,
                'fator_valor_fob' => isset($produto['fator_valor_fob']) ? $this->parseMoneyToFloat($produto['fator_valor_fob']) : null,
                'fator_tx_siscomex' => isset($produto['fator_tx_siscomex']) ? $this->parseMoneyToFloat($produto['fator_tx_siscomex']) : null,
                'multa' => isset($produto['multa']) ? $this->parseMoneyToFloat($produto['multa']) : null,
                'tx_def_li' => isset($produto['tx_def_li']) && trim($produto['tx_def_li']) !== '' && trim(str_replace(['%', ' '], '', $produto['tx_def_li'])) !== '' ? $this->safePercentage($produto['tx_def_li']) : null,
                'taxa_siscomex' => isset($produto['taxa_siscomex']) ? $this->parseMoneyToFloat($produto['taxa_siscomex']) : null,
                'outras_taxas_agente' => isset($produto['outras_taxas_agente']) ? $this->parseMoneyToFloat($produto['outras_taxas_agente']) : null,
                'liberacao_bl' => isset($produto['liberacao_bl']) ? $this->parseMoneyToFloat($produto['liberacao_bl']) : null,
                'desconsolidacao' => isset($produto['desconsolidacao']) ? $this->parseMoneyToFloat($produto['desconsolidacao']) : null,
                'isps_code' => isset($produto['isps_code']) ? $this->parseMoneyToFloat($produto['isps_code']) : null,
                'handling' => isset($produto['handling']) ? $this->parseMoneyToFloat($produto['handling']) : null,
                'capatazia' => isset($produto['capatazia']) ? $this->parseMoneyToFloat($produto['capatazia']) : null,
                'tx_correcao_lacre' => isset($produto['tx_correcao_lacre']) ? $this->parseMoneyToFloat($produto['tx_correcao_lacre']) : null,
                'afrmm' => isset($produto['afrmm']) ? $this->parseMoneyToFloat($produto['afrmm']) : null,
                'armazenagem_sts' => isset($produto['armazenagem_sts']) ? $this->parseMoneyToFloat($produto['armazenagem_sts']) : null,
                'frete_dta_sts_ana' => isset($produto['frete_dta_sts_ana']) ? $this->parseMoneyToFloat($produto['frete_dta_sts_ana']) : null,
                'frete_sts_cgb' => isset($produto['frete_sts_cgb']) ? $this->parseMoneyToFloat($produto['frete_sts_cgb']) : null,
                'diarias' => isset($produto['diarias']) ? $this->parseMoneyToFloat($produto['diarias']) : null,
                'sda' => isset($produto['sda']) ? $this->parseMoneyToFloat($produto['sda']) : null,
                'rep_sts' => isset($produto['rep_sts']) ? $this->parseMoneyToFloat($produto['rep_sts']) : null,
                'armaz_cgb' => isset($produto['armaz_cgb']) ? $this->parseMoneyToFloat($produto['armaz_cgb']) : null,
                'rep_cgb' => isset($produto['rep_cgb']) ? $this->parseMoneyToFloat($produto['rep_cgb']) : null,
                'demurrage' => isset($produto['demurrage']) ? $this->parseMoneyToFloat($produto['demurrage']) : null,
                'armaz_ana' => isset($produto['armaz_ana']) ? $this->parseMoneyToFloat($produto['armaz_ana']) : null,
                'lavagem_container' => isset($produto['lavagem_container']) ? $this->parseMoneyToFloat($produto['lavagem_container']) : null,
                'rep_anapolis' => isset($produto['rep_anapolis']) ? $this->parseMoneyToFloat($produto['rep_anapolis']) : null,
                // Campos aéreos já adicionados acima (dai, dape)
                'desp_anapolis' => isset($produto['desp_anapolis']) ? $this->parseMoneyToFloat($produto['desp_anapolis']) : null,
                'correios' => isset($produto['correios']) ? $this->parseMoneyToFloat($produto['correios']) : null,
                'li_dta_honor_nix' => isset($produto['li_dta_honor_nix']) ? $this->parseMoneyToFloat($produto['li_dta_honor_nix']) : null,
                'honorarios_nix' => isset($produto['honorarios_nix']) ? $this->parseMoneyToFloat($produto['honorarios_nix']) : null,
                // Campos específicos de Santa Catarina
                'rep_itj' => isset($produto['rep_itj']) ? $this->parseMoneyToFloat($produto['rep_itj']) : null,
                'frete_nvg_x_gyn' => isset($produto['frete_nvg_x_gyn']) ? $this->parseMoneyToFloat($produto['frete_nvg_x_gyn']) : null,
                'desp_desenbaraco' => isset($produto['desp_desenbaraco']) ? $this->parseMoneyToFloat($produto['desp_desenbaraco']) : null,
                'diferenca_cambial_frete' => isset($produto['diferenca_cambial_frete']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_frete']) : null,
                'diferenca_cambial_fob' => isset($produto['diferenca_cambial_fob']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_fob']) : null,
                'opcional_1_valor' => isset($produto['opcional_1_valor']) ? $this->parseMoneyToFloat($produto['opcional_1_valor']) : null,
                'opcional_2_valor' => isset($produto['opcional_2_valor']) ? $this->parseMoneyToFloat($produto['opcional_2_valor']) : null,
                'custo_unitario_final' => isset($produto['custo_unitario_final']) ? $this->parseMoneyToFloat($produto['custo_unitario_final']) : null,
                'custo_total_final' => isset($produto['custo_total_final']) ? $this->parseMoneyToFloat($produto['custo_total_final']) : null,
                // Campos específicos de Mato Grosso
                'dez_porcento' => isset($produto['dez_porcento']) ? $this->parseMoneyToFloat($produto['dez_porcento']) : null,
                'custo_com_margem' => isset($produto['custo_com_margem']) ? $this->parseMoneyToFloat($produto['custo_com_margem']) : null,
                'vlr_ipi_mg' => isset($produto['vlr_ipi_mg']) ? $this->parseMoneyToFloat($produto['vlr_ipi_mg']) : null,
                'vlr_icms_mg' => isset($produto['vlr_icms_mg']) ? $this->parseMoneyToFloat($produto['vlr_icms_mg']) : null,
                'pis_mg' => isset($produto['pis_mg']) ? $this->parseMoneyToFloat($produto['pis_mg']) : null,
                'cofins_mg' => isset($produto['cofins_mg']) ? $this->parseMoneyToFloat($produto['cofins_mg']) : null,
                'custo_total_final_credito' => isset($produto['custo_total_final_credito']) ? $this->parseMoneyToFloat($produto['custo_total_final_credito']) : null,
                'custo_unit_credito' => isset($produto['custo_unit_credito']) ? $this->parseMoneyToFloat($produto['custo_unit_credito']) : null,
                'mva_mg' => isset($produto['mva_mg']) ? $this->safePercentage($produto['mva_mg']) : null,
                'icms_st_mg' => isset($produto['icms_st_mg']) ? $this->safePercentage($produto['icms_st_mg']) : null,
                'bc_icms_st_mg' => isset($produto['bc_icms_st_mg']) ? $this->parseMoneyToFloat($produto['bc_icms_st_mg']) : null,
                'vlr_icms_st_mg' => isset($produto['vlr_icms_st_mg']) ? $this->parseMoneyToFloat($produto['vlr_icms_st_mg']) : null,
                'custo_total_c_icms_st' => isset($produto['custo_total_c_icms_st']) ? $this->parseMoneyToFloat($produto['custo_total_c_icms_st']) : null,
                'custo_unit_c_icms_st' => isset($produto['custo_unit_c_icms_st']) ? $this->parseMoneyToFloat($produto['custo_unit_c_icms_st']) : null,
                'exportador_mg' => isset($produto['exportador_mg']) ? $this->parseMoneyToFloat($produto['exportador_mg']) : null,
                'tributos_mg' => isset($produto['tributos_mg']) ? $this->parseMoneyToFloat($produto['tributos_mg']) : null,
                'despesas_mg' => isset($produto['despesas_mg']) ? $this->parseMoneyToFloat($produto['despesas_mg']) : null,
                'total_pago_mg' => isset($produto['total_pago_mg']) ? $this->parseMoneyToFloat($produto['total_pago_mg']) : null,
                'percentual_s_fob_mg' => isset($produto['percentual_s_fob_mg']) ? $this->parseMoneyToFloat($produto['percentual_s_fob_mg']) : null,
                "descricao" => $produto['descricao'],
                'fob_unit_moeda_estrangeira' => isset($produto['fob_unit_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_unit_moeda_estrangeira']) : null,
                'fob_total_moeda_estrangeira' => isset($produto['fob_total_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_total_moeda_estrangeira']) : null,
                'vlr_crf_total' => isset($produto['vlr_crf_total']) ? $this->parseMoneyToFloat($produto['vlr_crf_total']) : null,
                'vlr_crf_unit' => isset($produto['vlr_crf_unit']) ? $this->parseMoneyToFloat($produto['vlr_crf_unit']) : null,
                // Preservar valores existentes de service_charges se não foram enviados ou estão vazios
                'service_charges' => isset($produto['service_charges']) && $produto['service_charges'] !== '' ? $this->parseMoneyToFloat($produto['service_charges']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges'] ?? null : null),
                // Campos de peso específicos do aéreo
                'peso_liq_lbs' => $pesoLiqLbsValue,
                'peso_liq_kg' => $pesoLiqKgValue ?? null,
                'peso_liq_total_kg' => $pesoLiqTotalKgValue,
            ];
            
            $processoProduto = ProcessoAereoProduto::updateOrCreate(
                [
                    'id' => $produto['processo_produto_id'] ?? null,
                    'processo_aereo_id' => $id ?? 0,
                ],
                $dadosProduto
            );

            $produtoAntesId = $produto['processo_produto_id'] ?? null;
            $produtoAntes = $produtoAntesId && isset($produtosExistentes[$produtoAntesId])
                ? $produtosExistentes[$produtoAntesId]
                : null;

            $auditEntries[] = [
                'action' => $produtoAntes ? 'update' : 'create',
                'meta' => [
                    'auditable_type' => ProcessoAereoProduto::class,
                    'auditable_id' => $processoProduto->id,
                    'process_type' => $processType,
                    'process_id' => $id,
                    'client_id' => $processo->cliente_id,
                    'context' => 'processo.produto',
                ],
                'before' => $produtoAntes,
                'after' => $dadosProduto,
            ];
            
            $produtosProcessados++;
        }
    }

    /**
     * Processar produtos rodoviários
     */
    private function processarProdutosRodoviarios($request, $id, $processo, $produtosExistentes, $processType, &$pesoLiquidoTotal, &$produtosProcessados, &$auditEntries)
    {
        foreach ($request->produtos as $key => $produto) {
            $pesoLiquidoTotal += isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : 0;
            
            $dadosProduto = [
                'item' => $produto['item'],
                'produto_id' => $produto['produto_id'],
                'adicao' => isset($produto['adicao']) ? (int)$produto['adicao'] : null,
                'origem' => isset($produto['origem']) ? $produto['origem'] : null,
                'codigo_giiro' => isset($produto['codigo_giiro']) ? $produto['codigo_giiro'] : null,
                'quantidade' => isset($produto['quantidade']) ? $this->parseMoneyToFloat($produto['quantidade']) : null,
                'peso_liq_lbs' => isset($produto['peso_liq_lbs']) && $produto['peso_liq_lbs'] !== '' ? $this->parseMoneyToFloat($produto['peso_liq_lbs']) : null,
                'peso_liquido_unitario' => isset($produto['peso_liquido_unitario']) && $produto['peso_liquido_unitario'] !== '' ? $this->parseMoneyToFloat($produto['peso_liquido_unitario']) : null,
                'peso_liquido_total' => isset($produto['peso_liquido_total']) && $produto['peso_liquido_total'] !== '' ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : null,
                'peso_liq_total_kg' => isset($produto['peso_liq_total_kg']) && $produto['peso_liq_total_kg'] !== '' ? $this->parseMoneyToFloat($produto['peso_liq_total_kg']) : null,
                'fator_peso' => isset($produto['fator_peso']) ? $this->parseMoneyToFloat($produto['fator_peso']) : null,
                'fob_unit_usd' => (isset($produto['fob_unit_usd']) && trim($produto['fob_unit_usd']) !== '') ? $this->parseMoneyToFloat($produto['fob_unit_usd']) : (array_key_exists('fob_unit_usd', $produto) ? null : null),
                'fob_total_usd' => isset($produto['fob_total_usd']) ? $this->parseMoneyToFloat($produto['fob_total_usd']) : null,
                'fob_total_brl' => isset($produto['fob_total_brl']) ? $this->parseMoneyToFloat($produto['fob_total_brl']) : null,
                'frete_usd' => isset($produto['frete_usd']) ? $this->parseMoneyToFloat($produto['frete_usd']) : null,
                'frete_brl' => isset($produto['frete_brl']) ? $this->parseMoneyToFloat($produto['frete_brl']) : null,
                'seguro_usd' => isset($produto['seguro_usd']) ? $this->parseMoneyToFloat($produto['seguro_usd']) : null,
                'seguro_brl' => isset($produto['seguro_brl']) ? $this->parseMoneyToFloat($produto['seguro_brl']) : null,
                'acresc_frete_usd' => isset($produto['acresc_frete_usd']) ? $this->parseMoneyToFloat($produto['acresc_frete_usd']) : null,
                'acresc_frete_brl' => isset($produto['acresc_frete_brl']) ? $this->parseMoneyToFloat($produto['acresc_frete_brl']) : null,
                'thc_usd' => isset($produto['thc_usd']) ? $this->parseMoneyToFloat($produto['thc_usd']) : null,
                'thc_brl' => isset($produto['thc_brl']) ? $this->parseMoneyToFloat($produto['thc_brl']) : null,
                // Campos específicos rodoviário (sem delivery_fee e collect_fee)
                'dai' => isset($produto['dai']) ? $this->parseMoneyToFloat($produto['dai']) : null,
                'dape' => isset($produto['dape']) ? $this->parseMoneyToFloat($produto['dape']) : null,
                'vlr_cfr_unit' => isset($produto['vlr_cfr_unit']) ? $this->parseMoneyToFloat($produto['vlr_cfr_unit']) : null,
                'vlr_cfr_total' => isset($produto['vlr_cfr_total']) ? $this->parseMoneyToFloat($produto['vlr_cfr_total']) : null,
                'valor_aduaneiro_usd' => isset($produto['valor_aduaneiro_usd']) ? $this->parseMoneyToFloat($produto['valor_aduaneiro_usd']) : null,
                'valor_aduaneiro_brl' => isset($produto['valor_aduaneiro_brl']) ? $this->parseMoneyToFloat($produto['valor_aduaneiro_brl']) : null,
                'ii_percent' => isset($produto['ii_percent']) && trim($produto['ii_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['ii_percent'])) !== '' ? $this->safePercentage($produto['ii_percent']) : null,
                'ipi_percent' => isset($produto['ipi_percent']) && trim($produto['ipi_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['ipi_percent'])) !== '' ? $this->safePercentage($produto['ipi_percent']) : null,
                'pis_percent' => isset($produto['pis_percent']) && trim($produto['pis_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['pis_percent'])) !== '' ? $this->safePercentage($produto['pis_percent']) : null,
                'cofins_percent' => isset($produto['cofins_percent']) && trim($produto['cofins_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['cofins_percent'])) !== '' ? $this->safePercentage($produto['cofins_percent']) : null,
                'icms_percent' => isset($produto['icms_percent']) && trim($produto['icms_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['icms_percent'])) !== '' ? $this->safePercentage($produto['icms_percent']) : null,
                'icms_reduzido_percent' => isset($produto['icms_reduzido_percent']) && trim($produto['icms_reduzido_percent']) !== '' && trim(str_replace(['%', ' '], '', $produto['icms_reduzido_percent'])) !== '' ? $this->safePercentage($produto['icms_reduzido_percent']) : null,
                'frete_moeda_estrangeira' => isset($produto['frete_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['frete_moeda_estrangeira']) : null,
                'seguro_moeda_estrangeira' => isset($produto['seguro_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['seguro_moeda_estrangeira']) : null,
                'acrescimo_moeda_estrangeira' => isset($produto['acrescimo_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['acrescimo_moeda_estrangeira']) : null,
                'frete_moeda' => $request->frete_internacional_moeda,
                'seguro_moeda' => $request->seguro_internacional_moeda,
                'acrescimo_moeda' => $request->acrescimo_frete_moeda,
                'valor_ii' => isset($produto['valor_ii']) ? $this->parseMoneyToFloat($produto['valor_ii']) : null,
                'base_ipi' => isset($produto['base_ipi']) ? $this->parseMoneyToFloat($produto['base_ipi']) : null,
                'valor_ipi' => isset($produto['valor_ipi']) ? $this->parseMoneyToFloat($produto['valor_ipi']) : null,
                'base_pis_cofins' => isset($produto['base_pis_cofins']) ? $this->parseMoneyToFloat($produto['base_pis_cofins']) : null,
                'valor_pis' => isset($produto['valor_pis']) ? $this->parseMoneyToFloat($produto['valor_pis']) : null,
                'valor_cofins' => isset($produto['valor_cofins']) ? $this->parseMoneyToFloat($produto['valor_cofins']) : null,
                'despesa_aduaneira' => isset($produto['despesa_aduaneira']) ? $this->parseMoneyToFloat($produto['despesa_aduaneira']) : null,
                'base_icms_sem_reducao' => isset($produto['base_icms_sem_reducao']) ? $this->parseMoneyToFloat($produto['base_icms_sem_reducao']) : null,
                'valor_icms_sem_reducao' => isset($produto['valor_icms_sem_reducao']) ? $this->parseMoneyToFloat($produto['valor_icms_sem_reducao']) : null,
                'base_icms_reduzido' => isset($produto['base_icms_reduzido']) ? $this->parseMoneyToFloat($produto['base_icms_reduzido']) : null,
                'valor_icms_reduzido' => isset($produto['valor_icms_reduzido']) ? $this->parseMoneyToFloat($produto['valor_icms_reduzido']) : null,
                'valor_unit_nf' => isset($produto['valor_unit_nf']) ? $this->parseMoneyToFloat($produto['valor_unit_nf']) : null,
                'valor_total_nf' => isset($produto['valor_total_nf']) ? $this->parseMoneyToFloat($produto['valor_total_nf']) : null,
                'base_icms_st' => isset($produto['base_icms_st']) ? $this->parseMoneyToFloat($produto['base_icms_st']) : null,
                'mva' => isset($produto['mva']) ? $this->parseMoneyToFloat($produto['mva']) : null,
                'valor_icms_st' => isset($produto['valor_icms_st']) ? $this->parseMoneyToFloat($produto['valor_icms_st']) : null,
                'icms_st' => isset($produto['icms_st']) ? $this->parseMoneyToFloat($produto['icms_st']) : null,
                'valor_total_nf_com_icms_st' => isset($produto['valor_total_nf_com_icms_st']) ? $this->parseMoneyToFloat($produto['valor_total_nf_com_icms_st']) : null,
                'fator_valor_fob' => isset($produto['fator_valor_fob']) ? $this->parseMoneyToFloat($produto['fator_valor_fob']) : null,
                'fator_tx_siscomex' => isset($produto['fator_tx_siscomex']) ? $this->parseMoneyToFloat($produto['fator_tx_siscomex']) : null,
                'multa' => isset($produto['multa']) ? $this->parseMoneyToFloat($produto['multa']) : null,
                'tx_def_li' => isset($produto['tx_def_li']) && trim($produto['tx_def_li']) !== '' && trim(str_replace(['%', ' '], '', $produto['tx_def_li'])) !== '' ? $this->safePercentage($produto['tx_def_li']) : null,
                'taxa_siscomex' => isset($produto['taxa_siscomex']) ? $this->parseMoneyToFloat($produto['taxa_siscomex']) : null,
                'outras_taxas_agente' => isset($produto['outras_taxas_agente']) ? $this->parseMoneyToFloat($produto['outras_taxas_agente']) : null,
                'liberacao_bl' => isset($produto['liberacao_bl']) ? $this->parseMoneyToFloat($produto['liberacao_bl']) : null,
                'desconsolidacao' => isset($produto['desconsolidacao']) ? $this->parseMoneyToFloat($produto['desconsolidacao']) : null,
                'isps_code' => isset($produto['isps_code']) ? $this->parseMoneyToFloat($produto['isps_code']) : null,
                'handling' => isset($produto['handling']) ? $this->parseMoneyToFloat($produto['handling']) : null,
                'correios' => isset($produto['correios']) ? $this->parseMoneyToFloat($produto['correios']) : null,
                'li_dta_honor_nix' => isset($produto['li_dta_honor_nix']) ? $this->parseMoneyToFloat($produto['li_dta_honor_nix']) : null,
                'honorarios_nix' => isset($produto['honorarios_nix']) ? $this->parseMoneyToFloat($produto['honorarios_nix']) : null,
                'desp_desenbaraco' => isset($produto['desp_desenbaraco']) ? $this->parseMoneyToFloat($produto['desp_desenbaraco']) : null,
                // Campos específicos rodoviário
                'desp_fronteira' => isset($produto['desp_fronteira']) ? $this->parseMoneyToFloat($produto['desp_fronteira']) : null,
                'das_fronteira' => isset($produto['das_fronteira']) ? $this->parseMoneyToFloat($produto['das_fronteira']) : null,
                'armazenagem' => isset($produto['armazenagem']) ? $this->parseMoneyToFloat($produto['armazenagem']) : null,
                'frete_foz_gyn' => isset($produto['frete_foz_gyn']) ? $this->parseMoneyToFloat($produto['frete_foz_gyn']) : null,
                'rep_fronteira' => isset($produto['rep_fronteira']) ? $this->parseMoneyToFloat($produto['rep_fronteira']) : null,
                'armaz_anapolis' => isset($produto['armaz_anapolis']) ? $this->parseMoneyToFloat($produto['armaz_anapolis']) : null,
                'mov_anapolis' => isset($produto['mov_anapolis']) ? $this->parseMoneyToFloat($produto['mov_anapolis']) : null,
                'rep_anapolis' => isset($produto['rep_anapolis']) ? $this->parseMoneyToFloat($produto['rep_anapolis']) : null,
                'diferenca_cambial_frete' => isset($produto['diferenca_cambial_frete']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_frete']) : null,
                'diferenca_cambial_fob' => isset($produto['diferenca_cambial_fob']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_fob']) : null,
                'opcional_1_valor' => isset($produto['opcional_1_valor']) ? $this->parseMoneyToFloat($produto['opcional_1_valor']) : null,
                'opcional_2_valor' => isset($produto['opcional_2_valor']) ? $this->parseMoneyToFloat($produto['opcional_2_valor']) : null,
                'custo_unitario_final' => isset($produto['custo_unitario_final']) ? $this->parseMoneyToFloat($produto['custo_unitario_final']) : null,
                'custo_total_final' => isset($produto['custo_total_final']) ? $this->parseMoneyToFloat($produto['custo_total_final']) : null,
                "descricao" => $produto['descricao'],
                'fob_unit_moeda_estrangeira' => isset($produto['fob_unit_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_unit_moeda_estrangeira']) : null,
                'fob_total_moeda_estrangeira' => isset($produto['fob_total_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_total_moeda_estrangeira']) : null,
                'vlr_crf_total' => isset($produto['vlr_crf_total']) ? $this->parseMoneyToFloat($produto['vlr_crf_total']) : null,
                'vlr_crf_unit' => isset($produto['vlr_crf_unit']) ? $this->parseMoneyToFloat($produto['vlr_crf_unit']) : null,
                'service_charges' => isset($produto['service_charges']) && $produto['service_charges'] !== '' ? $this->parseMoneyToFloat($produto['service_charges']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges'] ?? null : null),
                'service_charges_brl' => isset($produto['service_charges_brl']) && $produto['service_charges_brl'] !== '' ? $this->parseMoneyToFloat($produto['service_charges_brl']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges_brl'] ?? null : null),
                'service_charges_moeda_estrangeira' => isset($produto['service_charges_moeda_estrangeira']) && $produto['service_charges_moeda_estrangeira'] !== '' ? $this->parseMoneyToFloat($produto['service_charges_moeda_estrangeira']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges_moeda_estrangeira'] ?? null : null),
            ];

            $processoProduto = ProcessoRodoviarioProduto::updateOrCreate(
                [
                    'id' => $produto['processo_produto_id'] ?? null,
                    'processo_rodoviario_id' => $id ?? 0,
                ],
                $dadosProduto
            );

            $produtoAntesId = $produto['processo_produto_id'] ?? null;
            $produtoAntes = $produtoAntesId && isset($produtosExistentes[$produtoAntesId])
                ? $produtosExistentes[$produtoAntesId]
                : null;

            $auditEntries[] = [
                'action' => $produtoAntes ? 'update' : 'create',
                'meta' => [
                    'auditable_type' => ProcessoRodoviarioProduto::class,
                    'auditable_id' => $processoProduto->id,
                    'process_type' => $processType,
                    'process_id' => $id,
                    'client_id' => $processo->cliente_id,
                    'context' => 'processo.produto',
                ],
                'before' => $produtoAntes,
                'after' => $dadosProduto,
            ];
            
            $produtosProcessados++;
        }
    }

    /**
     * Processar produtos marítimos
     */
    private function processarProdutosMaritimos($request, $id, $processo, $produtosExistentes, $processType, &$pesoLiquidoTotal, &$produtosProcessados, &$auditEntries)
    {
        foreach ($request->produtos as $key => $produto) {
            $pesoLiquidoTotal += isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : 0;
            
            $dadosProduto = [
                'item' => $produto['item'],
                'produto_id' => $produto['produto_id'],
                'adicao' => isset($produto['adicao']) ? (int)$produto['adicao'] : null,
                'quantidade' => isset($produto['quantidade']) ? $this->parseMoneyToFloat($produto['quantidade']) : null,
                'peso_liquido_unitario' => isset($produto['peso_liquido_unitario']) ? $this->parseMoneyToFloat($produto['peso_liquido_unitario']) : null,
                'peso_liquido_total' => isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : null,
                'fator_peso' => isset($produto['fator_peso']) ? $this->parseMoneyToFloat($produto['fator_peso']) : null,
                'fob_unit_usd' => (isset($produto['fob_unit_usd']) && trim($produto['fob_unit_usd']) !== '') ? $this->parseMoneyToFloat($produto['fob_unit_usd']) : (array_key_exists('fob_unit_usd', $produto) ? null : null),
                'fob_total_usd' => isset($produto['fob_total_usd']) ? $this->parseMoneyToFloat($produto['fob_total_usd']) : null,
                'fob_total_brl' => isset($produto['fob_total_brl']) ? $this->parseMoneyToFloat($produto['fob_total_brl']) : null,
                'frete_usd' => isset($produto['frete_usd']) ? $this->parseMoneyToFloat($produto['frete_usd']) : null,
                'frete_brl' => isset($produto['frete_brl']) ? $this->parseMoneyToFloat($produto['frete_brl']) : null,
                'seguro_usd' => isset($produto['seguro_usd']) ? $this->parseMoneyToFloat($produto['seguro_usd']) : null,
                'seguro_brl' => isset($produto['seguro_brl']) ? $this->parseMoneyToFloat($produto['seguro_brl']) : null,
                'acresc_frete_usd' => isset($produto['acresc_frete_usd']) ? $this->parseMoneyToFloat($produto['acresc_frete_usd']) : null,
                'acresc_frete_brl' => isset($produto['acresc_frete_brl']) ? $this->parseMoneyToFloat($produto['acresc_frete_brl']) : null,
                'thc_usd' => isset($produto['thc_usd']) ? $this->parseMoneyToFloat($produto['thc_usd']) : null,
                'thc_brl' => isset($produto['thc_brl']) ? $this->parseMoneyToFloat($produto['thc_brl']) : null,
                'valor_aduaneiro_usd' => isset($produto['valor_aduaneiro_usd']) ? $this->parseMoneyToFloat($produto['valor_aduaneiro_usd']) : null,
                'valor_aduaneiro_brl' => isset($produto['valor_aduaneiro_brl']) ? $this->parseMoneyToFloat($produto['valor_aduaneiro_brl']) : null,
                'ii_percent' => isset($produto['ii_percent']) ? $this->safePercentage($produto['ii_percent']) : null,
                'ipi_percent' => isset($produto['ipi_percent']) ? $this->safePercentage($produto['ipi_percent']) : null,
                'pis_percent' => isset($produto['pis_percent']) ? $this->safePercentage($produto['pis_percent']) : null,
                'cofins_percent' => isset($produto['cofins_percent']) ? $this->safePercentage($produto['cofins_percent']) : null,
                'icms_percent' => isset($produto['icms_percent']) ? $this->safePercentage($produto['icms_percent']) : null,
                'icms_reduzido_percent' => isset($produto['icms_reduzido_percent']) ? $this->safePercentage($produto['icms_reduzido_percent']) : null,
                'frete_moeda_estrangeira' => isset($produto['frete_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['frete_moeda_estrangeira']) : null,
                'seguro_moeda_estrangeira' => isset($produto['seguro_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['seguro_moeda_estrangeira']) : null,
                'acrescimo_moeda_estrangeira' => isset($produto['acrescimo_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['acrescimo_moeda_estrangeira']) : null,
                'frete_moeda' => $request->frete_internacional_moeda,
                'seguro_moeda' => $request->seguro_internacional_moeda,
                'acrescimo_moeda' => $request->acrescimo_frete_moeda,
                'valor_ii' => isset($produto['valor_ii']) ? $this->parseMoneyToFloat($produto['valor_ii']) : null,
                'base_ipi' => isset($produto['base_ipi']) ? $this->parseMoneyToFloat($produto['base_ipi']) : null,
                'valor_ipi' => isset($produto['valor_ipi']) ? $this->parseMoneyToFloat($produto['valor_ipi']) : null,
                'base_pis_cofins' => isset($produto['base_pis_cofins']) ? $this->parseMoneyToFloat($produto['base_pis_cofins']) : null,
                'valor_pis' => isset($produto['valor_pis']) ? $this->parseMoneyToFloat($produto['valor_pis']) : null,
                'valor_cofins' => isset($produto['valor_cofins']) ? $this->parseMoneyToFloat($produto['valor_cofins']) : null,
                'despesa_aduaneira' => isset($produto['despesa_aduaneira']) ? $this->parseMoneyToFloat($produto['despesa_aduaneira']) : null,
                'base_icms_sem_reducao' => isset($produto['base_icms_sem_reducao']) ? $this->parseMoneyToFloat($produto['base_icms_sem_reducao']) : null,
                'valor_icms_sem_reducao' => isset($produto['valor_icms_sem_reducao']) ? $this->parseMoneyToFloat($produto['valor_icms_sem_reducao']) : null,
                'base_icms_reduzido' => isset($produto['base_icms_reduzido']) ? $this->parseMoneyToFloat($produto['base_icms_reduzido']) : null,
                'valor_icms_reduzido' => isset($produto['valor_icms_reduzido']) ? $this->parseMoneyToFloat($produto['valor_icms_reduzido']) : null,
                'valor_unit_nf' => isset($produto['valor_unit_nf']) ? $this->parseMoneyToFloat($produto['valor_unit_nf']) : null,
                'valor_total_nf' => isset($produto['valor_total_nf']) ? $this->parseMoneyToFloat($produto['valor_total_nf']) : null,
                'base_icms_st' => isset($produto['base_icms_st']) ? $this->parseMoneyToFloat($produto['base_icms_st']) : null,
                'mva' => isset($produto['mva']) ? $this->parseMoneyToFloat($produto['mva']) : null,
                'valor_icms_st' => isset($produto['valor_icms_st']) ? $this->parseMoneyToFloat($produto['valor_icms_st']) : null,
                'icms_st' => isset($produto['icms_st']) ? $this->parseMoneyToFloat($produto['icms_st']) : null,
                'valor_total_nf_com_icms_st' => isset($produto['valor_total_nf_com_icms_st']) ? $this->parseMoneyToFloat($produto['valor_total_nf_com_icms_st']) : null,
                'fator_valor_fob' => isset($produto['fator_valor_fob']) ? $this->parseMoneyToFloat($produto['fator_valor_fob']) : null,
                'fator_tx_siscomex' => isset($produto['fator_tx_siscomex']) ? $this->parseMoneyToFloat($produto['fator_tx_siscomex']) : null,
                'multa' => isset($produto['multa']) ? $this->parseMoneyToFloat($produto['multa']) : null,
                'tx_def_li' => isset($produto['tx_def_li']) && trim($produto['tx_def_li']) !== '' && trim(str_replace(['%', ' '], '', $produto['tx_def_li'])) !== '' ? $this->safePercentage($produto['tx_def_li']) : null,
                'taxa_siscomex' => isset($produto['taxa_siscomex']) ? $this->parseMoneyToFloat($produto['taxa_siscomex']) : null,
                'outras_taxas_agente' => isset($produto['outras_taxas_agente']) ? $this->parseMoneyToFloat($produto['outras_taxas_agente']) : null,
                'liberacao_bl' => isset($produto['liberacao_bl']) ? $this->parseMoneyToFloat($produto['liberacao_bl']) : null,
                'desconsolidacao' => isset($produto['desconsolidacao']) ? $this->parseMoneyToFloat($produto['desconsolidacao']) : null,
                'isps_code' => isset($produto['isps_code']) ? $this->parseMoneyToFloat($produto['isps_code']) : null,
                'handling' => isset($produto['handling']) ? $this->parseMoneyToFloat($produto['handling']) : null,
                'capatazia' => isset($produto['capatazia']) ? $this->parseMoneyToFloat($produto['capatazia']) : null,
                'tx_correcao_lacre' => isset($produto['tx_correcao_lacre']) ? $this->parseMoneyToFloat($produto['tx_correcao_lacre']) : null,
                'afrmm' => isset($produto['afrmm']) ? $this->parseMoneyToFloat($produto['afrmm']) : null,
                'armazenagem_sts' => isset($produto['armazenagem_sts']) ? $this->parseMoneyToFloat($produto['armazenagem_sts']) : null,
                'frete_dta_sts_ana' => isset($produto['frete_dta_sts_ana']) ? $this->parseMoneyToFloat($produto['frete_dta_sts_ana']) : null,
                'frete_sts_cgb' => isset($produto['frete_sts_cgb']) ? $this->parseMoneyToFloat($produto['frete_sts_cgb']) : null,
                'diarias' => isset($produto['diarias']) ? $this->parseMoneyToFloat($produto['diarias']) : null,
                'sda' => isset($produto['sda']) ? $this->parseMoneyToFloat($produto['sda']) : null,
                'rep_sts' => isset($produto['rep_sts']) ? $this->parseMoneyToFloat($produto['rep_sts']) : null,
                'armaz_cgb' => isset($produto['armaz_cgb']) ? $this->parseMoneyToFloat($produto['armaz_cgb']) : null,
                'rep_cgb' => isset($produto['rep_cgb']) ? $this->parseMoneyToFloat($produto['rep_cgb']) : null,
                'demurrage' => isset($produto['demurrage']) ? $this->parseMoneyToFloat($produto['demurrage']) : null,
                'armaz_ana' => isset($produto['armaz_ana']) ? $this->parseMoneyToFloat($produto['armaz_ana']) : null,
                'lavagem_container' => isset($produto['lavagem_container']) ? $this->parseMoneyToFloat($produto['lavagem_container']) : null,
                'rep_anapolis' => isset($produto['rep_anapolis']) ? $this->parseMoneyToFloat($produto['rep_anapolis']) : null,
                'desp_anapolis' => isset($produto['desp_anapolis']) ? $this->parseMoneyToFloat($produto['desp_anapolis']) : null,
                'correios' => isset($produto['correios']) ? $this->parseMoneyToFloat($produto['correios']) : null,
                'li_dta_honor_nix' => isset($produto['li_dta_honor_nix']) ? $this->parseMoneyToFloat($produto['li_dta_honor_nix']) : null,
                'honorarios_nix' => isset($produto['honorarios_nix']) ? $this->parseMoneyToFloat($produto['honorarios_nix']) : null,
                'desp_desenbaraco' => isset($produto['desp_desenbaraco']) ? $this->parseMoneyToFloat($produto['desp_desenbaraco']) : null,
                'diferenca_cambial_frete' => isset($produto['diferenca_cambial_frete']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_frete']) : null,
                'diferenca_cambial_fob' => isset($produto['diferenca_cambial_fob']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_fob']) : null,
                'opcional_1_valor' => isset($produto['opcional_1_valor']) ? $this->parseMoneyToFloat($produto['opcional_1_valor']) : null,
                'opcional_2_valor' => isset($produto['opcional_2_valor']) ? $this->parseMoneyToFloat($produto['opcional_2_valor']) : null,
                'custo_unitario_final' => isset($produto['custo_unitario_final']) ? $this->parseMoneyToFloat($produto['custo_unitario_final']) : null,
                'custo_total_final' => isset($produto['custo_total_final']) ? $this->parseMoneyToFloat($produto['custo_total_final']) : null,
                // Campos específicos de Mato Grosso
                'dez_porcento' => isset($produto['dez_porcento']) ? $this->parseMoneyToFloat($produto['dez_porcento']) : null,
                'custo_com_margem' => isset($produto['custo_com_margem']) ? $this->parseMoneyToFloat($produto['custo_com_margem']) : null,
                'vlr_ipi_mg' => isset($produto['vlr_ipi_mg']) ? $this->parseMoneyToFloat($produto['vlr_ipi_mg']) : null,
                'vlr_icms_mg' => isset($produto['vlr_icms_mg']) ? $this->parseMoneyToFloat($produto['vlr_icms_mg']) : null,
                'pis_mg' => isset($produto['pis_mg']) ? $this->parseMoneyToFloat($produto['pis_mg']) : null,
                'cofins_mg' => isset($produto['cofins_mg']) ? $this->parseMoneyToFloat($produto['cofins_mg']) : null,
                'custo_total_final_credito' => isset($produto['custo_total_final_credito']) ? $this->parseMoneyToFloat($produto['custo_total_final_credito']) : null,
                'custo_unit_credito' => isset($produto['custo_unit_credito']) ? $this->parseMoneyToFloat($produto['custo_unit_credito']) : null,
                'mva_mg' => isset($produto['mva_mg']) ? $this->safePercentage($produto['mva_mg']) : null,
                'icms_st_mg' => isset($produto['icms_st_mg']) ? $this->safePercentage($produto['icms_st_mg']) : null,
                'bc_icms_st_mg' => isset($produto['bc_icms_st_mg']) ? $this->parseMoneyToFloat($produto['bc_icms_st_mg']) : null,
                'vlr_icms_st_mg' => isset($produto['vlr_icms_st_mg']) ? $this->parseMoneyToFloat($produto['vlr_icms_st_mg']) : null,
                'custo_total_c_icms_st' => isset($produto['custo_total_c_icms_st']) ? $this->parseMoneyToFloat($produto['custo_total_c_icms_st']) : null,
                'custo_unit_c_icms_st' => isset($produto['custo_unit_c_icms_st']) ? $this->parseMoneyToFloat($produto['custo_unit_c_icms_st']) : null,
                'exportador_mg' => isset($produto['exportador_mg']) ? $this->parseMoneyToFloat($produto['exportador_mg']) : null,
                'tributos_mg' => isset($produto['tributos_mg']) ? $this->parseMoneyToFloat($produto['tributos_mg']) : null,
                'despesas_mg' => isset($produto['despesas_mg']) ? $this->parseMoneyToFloat($produto['despesas_mg']) : null,
                'total_pago_mg' => isset($produto['total_pago_mg']) ? $this->parseMoneyToFloat($produto['total_pago_mg']) : null,
                'percentual_s_fob_mg' => isset($produto['percentual_s_fob_mg']) ? $this->parseMoneyToFloat($produto['percentual_s_fob_mg']) : null,
                "descricao" => $produto['descricao'],
                'fob_unit_moeda_estrangeira' => isset($produto['fob_unit_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_unit_moeda_estrangeira']) : null,
                'fob_total_moeda_estrangeira' => isset($produto['fob_total_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_total_moeda_estrangeira']) : null,
                'vlr_crf_total' => isset($produto['vlr_crf_total']) ? $this->parseMoneyToFloat($produto['vlr_crf_total']) : null,
                'vlr_crf_unit' => isset($produto['vlr_crf_unit']) ? $this->parseMoneyToFloat($produto['vlr_crf_unit']) : null,
                'service_charges' => isset($produto['service_charges']) && $produto['service_charges'] !== '' ? $this->parseMoneyToFloat($produto['service_charges']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges'] ?? null : null),
                'service_charges_brl' => isset($produto['service_charges_brl']) && $produto['service_charges_brl'] !== '' ? $this->parseMoneyToFloat($produto['service_charges_brl']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges_brl'] ?? null : null),
                'service_charges_moeda_estrangeira' => isset($produto['service_charges_moeda_estrangeira']) && $produto['service_charges_moeda_estrangeira'] !== '' ? $this->parseMoneyToFloat($produto['service_charges_moeda_estrangeira']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges_moeda_estrangeira'] ?? null : null),
            ];

            $processoProduto = ProcessoProduto::updateOrCreate(
                [
                    'id' => $produto['processo_produto_id'] ?? null,
                    'processo_id' => $id ?? 0,
                ],
                $dadosProduto
            );

            $produtoAntesId = $produto['processo_produto_id'] ?? null;
            $produtoAntes = $produtoAntesId && isset($produtosExistentes[$produtoAntesId])
                ? $produtosExistentes[$produtoAntesId]
                : null;

            $auditEntries[] = [
                'action' => $produtoAntes ? 'update' : 'create',
                'meta' => [
                    'auditable_type' => ProcessoProduto::class,
                    'auditable_id' => $processoProduto->id,
                    'process_type' => $processType,
                    'process_id' => $id,
                    'client_id' => $processo->cliente_id,
                    'context' => 'processo.produto',
                ],
                'before' => $produtoAntes,
                'after' => $dadosProduto,
            ];
            
            $produtosProcessados++;
        }
    }

    /**
     * Detectar e carregar o processo baseado no tipo
     * Retorna array com processo e flags, ou array com 'error' se não encontrado
     */
    private function detectarECarregarProcesso($request, $id)
    {
        $tipoProcessoRequest = $request->input('tipo_processo') ?? $request->query('tipo_processo') ?? $request->get('tipo_processo');
        $processo = null;
        $isAereo = false;
        $isRodoviario = false;

        if ($tipoProcessoRequest === 'aereo') {
            $processo = ProcessoAereo::find($id);
            if (!$processo) {
                return ['error' => 'Processo não encontrado'];
            }
            $isAereo = true;
        } elseif ($tipoProcessoRequest === 'rodoviario') {
            $processo = ProcessoRodoviario::find($id);
            if (!$processo) {
                return ['error' => 'Processo não encontrado'];
            }
            $isRodoviario = true;
        } else {
            // Tentar buscar como processo marítimo primeiro
            $processo = Processo::find($id);
            if ($processo) {
                // Processo marítimo encontrado
            } else {
                // Se não encontrar, tentar como processo aéreo
                $processo = ProcessoAereo::find($id);
                if ($processo) {
                    $isAereo = true;
                } else {
                    // Se não encontrar, tentar como processo rodoviário
                    $processo = ProcessoRodoviario::find($id);
                    if ($processo) {
                        $isRodoviario = true;
                    } else {
                        // Se não encontrou em nenhuma tabela, retornar erro
                        return ['error' => 'Processo não encontrado'];
                    }
                }
            }
        }
        
        return [
            'processo' => $processo,
            'isAereo' => $isAereo,
            'isRodoviario' => $isRodoviario
        ];
    }

    public function update(Request $request, $id)
    {
        try {
            // Detectar e carregar processo
            $processoInfo = $this->detectarECarregarProcesso($request, $id);
            if (isset($processoInfo['error'])) {
                return response()->json([
                    'success' => false,
                    'error' => $processoInfo['error']
                ], 404);
            }
            $processo = $processoInfo['processo'];
            $isAereo = $processoInfo['isAereo'];
            $isRodoviario = $processoInfo['isRodoviario'];
            
            $this->ensureClienteAccess($processo->cliente_id);
            $auditService = app(ProcessoAuditService::class);
            $auditEntries = [];
            $processoOriginal = $processo->getAttributes();
            $processType = $isAereo ? 'aereo' : ($isRodoviario ? 'rodoviario' : ($processo->tipo_processo ?? 'maritimo'));
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [], []);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return response()->json(['error' => [implode('<br> ', $message)]]);
            }

            $pesoLiquidoTotal = 0;
            $produtosProcessados = 0;

            // Carregar produtos existentes para preservar valores de service_charges
            $produtosExistentes = [];
            if ($request->produtos && count($request->produtos) > 0) {
                $idsProdutos = array_filter(array_column($request->produtos, 'processo_produto_id'));
                if (!empty($idsProdutos)) {
                    if ($isAereo) {
                        $produtosExistentes = ProcessoAereoProduto::whereIn('id', $idsProdutos)
                            ->get()
                            ->keyBy('id')
                            ->toArray();
                    } elseif ($isRodoviario) {
                        $produtosExistentes = ProcessoRodoviarioProduto::whereIn('id', $idsProdutos)
                            ->get()
                            ->keyBy('id')
                            ->toArray();
                    } else {
                        $produtosExistentes = ProcessoProduto::whereIn('id', $idsProdutos)
                            ->get()
                            ->keyBy('id')
                            ->toArray();
                    }
                }
            }

            $produtosMultaExistentes = [];
            if ($request->produtos_multa && count($request->produtos_multa) > 0) {
                $idsProdutosMulta = array_filter(array_column($request->produtos_multa, 'processo_produto_multa_id'));
                if (!empty($idsProdutosMulta)) {
                    $produtosMultaExistentes = ProcessoProdutoMulta::whereIn('id', $idsProdutosMulta)
                        ->get()
                        ->keyBy('id')
                        ->toArray();
                }
            }

            $possuiProdutosExistentes = $isAereo 
                ? $processo->processoAereoProdutos()->exists() 
                : ($isRodoviario 
                    ? $processo->processoRodoviarioProdutos()->exists() 
                    : $processo->processoProdutos()->exists());
            $fornecedorValidado = null;

            if ($request->has('fornecedor_id')) {
                $fornecedorId = $request->input('fornecedor_id');
                if (!empty($fornecedorId)) {
                    $possuiProdutos = $possuiProdutosExistentes;
                    if (!$possuiProdutos && $request->produtos && count($request->produtos) > 0) {
                        $possuiProdutos = true;
                    }

                    if (!$possuiProdutos) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'error' => 'Adicione ao menos um produto antes de vincular um fornecedor.'
                        ], 422);
                    }

                    $fornecedorValidado = Fornecedor::where('id', $fornecedorId)
                        ->where('cliente_id', $processo->cliente_id)
                        ->first();

                    if (!$fornecedorValidado) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'error' => 'Fornecedor inválido para este processo.'
                        ], 422);
                    }
                }
            }

            if ($request->produtos && count($request->produtos) > 0) {
                foreach ($request->produtos as $key => $produto) {
                    if (!isset($produto['produto_id']) || empty($produto['produto_id'])) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'error' => 'Todos as linhas devem ter um produto selecionado!'
                        ]);
                    }
                }

                // Processar produtos baseado no tipo de processo
                if ($isAereo) {
                    $this->processarProdutosAereos($request, $id, $processo, $produtosExistentes, $processType, $pesoLiquidoTotal, $produtosProcessados, $auditEntries);
                } elseif ($isRodoviario) {
                    $this->processarProdutosRodoviarios($request, $id, $processo, $produtosExistentes, $processType, $pesoLiquidoTotal, $produtosProcessados, $auditEntries);
                } else {
                    $this->processarProdutosMaritimos($request, $id, $processo, $produtosExistentes, $processType, $pesoLiquidoTotal, $produtosProcessados, $auditEntries);
                }
            }

            // Processamento de produtos agora é feito pelos métodos privados acima

            if ($request->produtos_multa && count($request->produtos_multa) > 0) {
                foreach ($request->produtos_multa as $produtoMulta) {
                    if (!isset($produtoMulta['produto_id']) || empty($produtoMulta['produto_id'])) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'error' => 'Todos as linhas da multa devem ter um produto selecionado!'
                        ]);
                    }
                }

                $camposPercentuaisMulta = [
                    'ii_percent',
                    'ipi_percent',
                    'pis_percent',
                    'cofins_percent',
                    'icms_percent',
                    'icms_reduzido_percent',
                    'ii_nova_ncm_percent',
                    'ipi_nova_ncm_percent',
                    'pis_nova_ncm_percent',
                    'cofins_nova_ncm_percent',
                ];

                $camposNumericosMulta = [
                    'quantidade',
                    'peso_liquido_unitario',
                    'peso_liquido_total',
                    'fator_peso',
                    'fob_unit_usd',
                    'fob_total_usd',
                    'fob_total_brl',
                    'service_charges',
                    'service_charges_brl',
                    'frete_usd',
                    'frete_brl',
                    'acresc_frete_usd',
                    'acresc_frete_brl',
                    'vlr_crf_unit',
                    'vlr_crf_total',
                    'seguro_usd',
                    'seguro_brl',
                    'thc_usd',
                    'thc_brl',
                    'valor_aduaneiro_usd',
                    'valor_aduaneiro_brl',
                    'valor_ii',
                    'base_ipi',
                    'valor_ipi',
                    'base_pis_cofins',
                    'valor_pis',
                    'valor_cofins',
                    'despesa_aduaneira',
                    'base_icms_sem_reducao',
                    'valor_icms_sem_reducao',
                    'base_icms_reduzido',
                    'valor_icms_reduzido',
                    'valor_unit_nf',
                    'valor_total_nf',
                    'base_icms_st',
                    'mva',
                    'valor_icms_st',
                    'icms_st',
                    'valor_total_nf_com_icms_st',
                    'fator_valor_fob',
                    'fator_tx_siscomex',
                    'multa',
                    'taxa_siscomex',
                    'outras_taxas_agente',
                    'liberacao_bl',
                    'desconsolidacao',
                    'isps_code',
                    'handling',
                    'capatazia',
                    'tx_correcao_lacre',
                    'afrmm',
                    'armazenagem_sts',
                    'frete_dta_sts_ana',
                    'frete_sts_cgb',
                    'diarias',
                    'sda',
                    'rep_sts',
                    'armaz_cgb',
                    'rep_cgb',
                    'demurrage',
                    'armaz_ana',
                    'lavagem_container',
                    'rep_anapolis',
                    'desp_anapolis',
                    'correios',
                    'li_dta_honor_nix',
                    'honorarios_nix',
                    'desp_desenbaraco',
                    'diferenca_cambial_frete',
                    'diferenca_cambial_fob',
                    'opcional_1_valor',
                    'opcional_2_valor',
                    'custo_unitario_final',
                    'custo_total_final',
                ];

                foreach ($request->produtos_multa as $produtoMulta) {
                    $dadosProdutoMulta = [
                        'item' => $produtoMulta['item'],
                        'produto_id' => $produtoMulta['produto_id'],
                        'adicao' => isset($produtoMulta['adicao']) ? (int)$produtoMulta['adicao'] : null,
                        'quantidade' => isset($produtoMulta['quantidade']) ? $this->parseMoneyToFloat($produtoMulta['quantidade']) : null,
                        'peso_liquido_unitario' => isset($produtoMulta['peso_liquido_unitario']) ? $this->parseMoneyToFloat($produtoMulta['peso_liquido_unitario']) : null,
                        'peso_liquido_total' => isset($produtoMulta['peso_liquido_total']) ? $this->parseMoneyToFloat($produtoMulta['peso_liquido_total']) : null,
                        'fator_peso' => isset($produtoMulta['fator_peso']) ? $this->parseMoneyToFloat($produtoMulta['fator_peso']) : null,
                        'fob_unit_usd' => (isset($produtoMulta['fob_unit_usd']) && trim($produtoMulta['fob_unit_usd']) !== '') ? $this->parseMoneyToFloat($produtoMulta['fob_unit_usd']) : (array_key_exists('fob_unit_usd', $produtoMulta) ? null : null),
                        'fob_total_usd' => isset($produtoMulta['fob_total_usd']) ? $this->parseMoneyToFloat($produtoMulta['fob_total_usd']) : null,
                        'fob_total_brl' => isset($produtoMulta['fob_total_brl']) ? $this->parseMoneyToFloat($produtoMulta['fob_total_brl']) : null,
                        'service_charges' => isset($produtoMulta['service_charges']) && $produtoMulta['service_charges'] !== '' ? $this->parseMoneyToFloat($produtoMulta['service_charges']) : (isset($produtoMulta['processo_produto_multa_id']) && $produtoMulta['processo_produto_multa_id'] && isset($produtosMultaExistentes[$produtoMulta['processo_produto_multa_id']]) ? $produtosMultaExistentes[$produtoMulta['processo_produto_multa_id']]['service_charges'] ?? null : null),
                        'service_charges_brl' => isset($produtoMulta['service_charges_brl']) && $produtoMulta['service_charges_brl'] !== '' ? $this->parseMoneyToFloat($produtoMulta['service_charges_brl']) : (isset($produtoMulta['processo_produto_multa_id']) && $produtoMulta['processo_produto_multa_id'] && isset($produtosMultaExistentes[$produtoMulta['processo_produto_multa_id']]) ? $produtosMultaExistentes[$produtoMulta['processo_produto_multa_id']]['service_charges_brl'] ?? null : null),
                        'frete_usd' => isset($produtoMulta['frete_usd']) ? $this->parseMoneyToFloat($produtoMulta['frete_usd']) : null,
                        'frete_brl' => isset($produtoMulta['frete_brl']) ? $this->parseMoneyToFloat($produtoMulta['frete_brl']) : null,
                        'acresc_frete_usd' => isset($produtoMulta['acresc_frete_usd']) ? $this->parseMoneyToFloat($produtoMulta['acresc_frete_usd']) : null,
                        'acresc_frete_brl' => isset($produtoMulta['acresc_frete_brl']) ? $this->parseMoneyToFloat($produtoMulta['acresc_frete_brl']) : null,
                        'vlr_crf_unit' => isset($produtoMulta['vlr_crf_unit']) ? $this->parseMoneyToFloat($produtoMulta['vlr_crf_unit']) : null,
                        'vlr_crf_total' => isset($produtoMulta['vlr_crf_total']) ? $this->parseMoneyToFloat($produtoMulta['vlr_crf_total']) : null,
                        'seguro_usd' => isset($produtoMulta['seguro_usd']) ? $this->parseMoneyToFloat($produtoMulta['seguro_usd']) : null,
                        'seguro_brl' => isset($produtoMulta['seguro_brl']) ? $this->parseMoneyToFloat($produtoMulta['seguro_brl']) : null,
                        'thc_usd' => isset($produtoMulta['thc_usd']) ? $this->parseMoneyToFloat($produtoMulta['thc_usd']) : null,
                        'thc_brl' => isset($produtoMulta['thc_brl']) ? $this->parseMoneyToFloat($produtoMulta['thc_brl']) : null,
                        'valor_aduaneiro_usd' => isset($produtoMulta['valor_aduaneiro_usd']) ? $this->parseMoneyToFloat($produtoMulta['valor_aduaneiro_usd']) : null,
                        'valor_aduaneiro_brl' => isset($produtoMulta['valor_aduaneiro_brl']) ? $this->parseMoneyToFloat($produtoMulta['valor_aduaneiro_brl']) : null,
                    ];

                    foreach ($camposPercentuaisMulta as $campo) {
                        if (isset($produtoMulta[$campo]) && trim($produtoMulta[$campo]) !== '' && trim(str_replace(['%', ' '], '', $produtoMulta[$campo])) !== '') {
                            $dadosProdutoMulta[$campo] = $this->safePercentage($produtoMulta[$campo]);
                        } else {
                            $dadosProdutoMulta[$campo] = null;
                        }
                    }

                    foreach ($camposNumericosMulta as $campo) {
                        if (isset($produtoMulta[$campo]) && $produtoMulta[$campo] !== '') {
                            $dadosProdutoMulta[$campo] = $this->parseMoneyToFloat($produtoMulta[$campo]);
                        } else {
                            $dadosProdutoMulta[$campo] = null;
                        }
                    }

                    $dadosProdutoMulta['frete_moeda'] = $request->frete_internacional_moeda;
                    $dadosProdutoMulta['seguro_moeda'] = $request->seguro_internacional_moeda;
                    $dadosProdutoMulta['acrescimo_moeda'] = $request->acrescimo_frete_moeda;

                    if ($isAereo) {
                        $dadosProdutoMulta['processo_aereo_id'] = $id;
                    } elseif ($isRodoviario) {
                        $dadosProdutoMulta['processo_rodoviario_id'] = $id;
                    } else {
                        $dadosProdutoMulta['processo_id'] = $id;
                    }

                    $processoProdutoMulta = ProcessoProdutoMulta::updateOrCreate(
                        [
                            'id' => $produtoMulta['processo_produto_multa_id'] ?? null,
                            $isAereo ? 'processo_aereo_id' : ($isRodoviario ? 'processo_rodoviario_id' : 'processo_id') => $id ?? 0,
                        ],
                        $dadosProdutoMulta
                    );

                    $produtoMultaAntesId = $produtoMulta['processo_produto_multa_id'] ?? null;
                    $produtoMultaAntes = $produtoMultaAntesId && isset($produtosMultaExistentes[$produtoMultaAntesId])
                        ? $produtosMultaExistentes[$produtoMultaAntesId]
                        : null;

                    $auditEntries[] = [
                        'action' => $produtoMultaAntes ? 'update' : 'create',
                        'meta' => [
                            'auditable_type' => ProcessoProdutoMulta::class,
                            'auditable_id' => $processoProdutoMulta->id,
                            'process_type' => $processType,
                            'process_id' => $id,
                            'client_id' => $processo->cliente_id,
                            'context' => 'processo.produto_multa',
                        ],
                        'before' => $produtoMultaAntes,
                        'after' => $dadosProdutoMulta,
                    ];
                }
            }

            // Atualizar peso líquido total
            if ($request->has('peso_liquido_total_cabecalho')) {
                $pesoLiquidoFinal = $this->parseMoneyToFloat($request->peso_liquido_total_cabecalho);
            } else {
                $pesoLiquidoFinal = $pesoLiquidoTotal;
            }

            if ($isAereo) {
                ProcessoAereo::where('id', $id)->update(['peso_liquido' => $pesoLiquidoFinal]);
                $processoExistente = ProcessoAereo::find($id);
            } elseif ($isRodoviario) {
                ProcessoRodoviario::where('id', $id)->update(['peso_liquido' => $pesoLiquidoFinal]);
                $processoExistente = ProcessoRodoviario::find($id);
            } else {
                Processo::where('id', $id)->update(['peso_liquido' => $pesoLiquidoFinal]);
                $processoExistente = Processo::find($id);
            }

            // Preparar dados do processo
            $dadosProcesso = [];
            
            if ($request->has('numero_processo')) {
                $dadosProcesso['numero_processo'] = $this->normalizeNullableString($request->numero_processo);
            }

            if ($request->has('data_entrada')) {
                $dadosProcesso['data_entrada'] = $request->data_entrada ? \Carbon\Carbon::parse($request->data_entrada)->format('Y-m-d') : null;
            }

            if ($request->has('data_saida')) {
                $dadosProcesso['data_saida'] = $request->data_saida ? \Carbon\Carbon::parse($request->data_saida)->format('Y-m-d') : null;
            }

            if ($request->has('cliente_id')) {
                $dadosProcesso['cliente_id'] = $request->cliente_id;
            }

            if ($request->has('catalogo_id')) {
                $dadosProcesso['catalogo_id'] = $request->catalogo_id;
            }

            if ($request->has('frete_internacional_moeda')) {
                $dadosProcesso['frete_internacional_moeda'] = $request->frete_internacional_moeda;
            }

            if ($request->has('seguro_internacional_moeda')) {
                $dadosProcesso['seguro_internacional_moeda'] = $request->seguro_internacional_moeda;
            }

            if ($request->has('acrescimo_frete_moeda')) {
                $dadosProcesso['acrescimo_frete_moeda'] = $request->acrescimo_frete_moeda;
            }

            if ($request->has('cotacao_frete')) {
                $dadosProcesso['cotacao_frete'] = $this->parseMoneyToFloat($request->cotacao_frete, 4);
            }

            if ($request->has('cotacao_seguro')) {
                $dadosProcesso['cotacao_seguro'] = $this->parseMoneyToFloat($request->cotacao_seguro, 4);
            }

            if ($request->has('cotacao_acrescimo')) {
                $dadosProcesso['cotacao_acrescimo'] = $this->parseMoneyToFloat($request->cotacao_acrescimo, 4);
            }

            // Campos específicos para processos aéreos
            if ($isAereo) {
                // Sempre atualizar os campos se foram enviados, mesmo que sejam 0
                $camposAereos = [
                    'peso_liquido_total_cabecalho',
                    'outras_taxas_agente',
                    'delivery_fee',
                    'collect_fee',
                    'desconsolidacao',
                    'handling',
                    'dai',
                    'dape'
                ];
                
                foreach ($camposAereos as $campo) {
                    // Sempre incluir o campo no array quando enviado, mesmo que seja 0 ou vazio
                    if ($request->input($campo) !== null) {
                        $valor = $this->parseMoneyToFloat($request->$campo);
                        // Se o valor for null (campo vazio), salvar como 0
                        $dadosProcesso[$campo] = $valor !== null ? $valor : 0;
                    }
                }
            }
            
            // Campos específicos para processos rodoviários
            if ($isRodoviario) {
                // Sempre atualizar os campos se foram enviados, mesmo que sejam 0
                $camposRodoviarios = [
                    'dai',
                    'dape',
                    'desp_fronteira',
                    'das_fronteira',
                    'armazenagem',
                    'frete_foz_gyn',
                    'rep_fronteira',
                    'armaz_anapolis',
                    'mov_anapolis',
                    'rep_anapolis'
                ];
                
                foreach ($camposRodoviarios as $campo) {
                    // Sempre incluir o campo no array quando enviado, mesmo que seja 0 ou vazio
                    if ($request->has($campo)) {
                        $valor = $this->parseMoneyToFloat($request->$campo);
                        // Se o valor for null (campo vazio), salvar como 0
                        $dadosProcesso[$campo] = $valor !== null ? $valor : 0;
                    }
                }
            }

            if ($request->has('fornecedor_id')) {
                $dadosProcesso['fornecedor_id'] = $fornecedorValidado?->id ?: null;
            }

            if ($request->has('transportadora_nome')) {
                $dadosProcesso['transportadora_nome'] = $this->normalizeNullableString($request->transportadora_nome);
            }

            if ($request->has('transportadora_endereco')) {
                $dadosProcesso['transportadora_endereco'] = $this->normalizeNullableString($request->transportadora_endereco);
            }

            if ($request->has('transportadora_municipio')) {
                $dadosProcesso['transportadora_municipio'] = $this->normalizeNullableString($request->transportadora_municipio);
            }

            if ($request->has('transportadora_cnpj')) {
                $dadosProcesso['transportadora_cnpj'] = $this->sanitizeNumericString($request->transportadora_cnpj);
            }

            if ($request->has('info_complementar_nf')) {
                $dadosProcesso['info_complementar_nf'] = $this->normalizeNullableString($request->info_complementar_nf);
            }
            
            // Preservar campos de service_charges do processo se não foram enviados
            if ($request->has('service_charges_moeda') && $request->service_charges_moeda !== '' && $request->service_charges_moeda !== null) {
                $dadosProcesso['service_charges_moeda'] = $request->service_charges_moeda;
            } else {
                $dadosProcesso['service_charges_moeda'] = $processoExistente->service_charges_moeda ?? null;
            }
            
            if ($request->has('service_charges_usd') && $request->service_charges_usd !== '' && $request->service_charges_usd !== null) {
                $dadosProcesso['service_charges_usd'] = $this->parseMoneyToFloat($request->service_charges_usd);
            } else {
                $dadosProcesso['service_charges_usd'] = $processoExistente->service_charges_usd ?? null;
            }
            
            if ($request->has('service_charges_brl') && $request->service_charges_brl !== '' && $request->service_charges_brl !== null) {
                $dadosProcesso['service_charges_brl'] = $this->parseMoneyToFloat($request->service_charges_brl);
            } else {
                $dadosProcesso['service_charges_brl'] = $processoExistente->service_charges_brl ?? null;
            }

            // Processo rodoviário sempre usa nacionalização 'outros'
            if ($isRodoviario) {
                $dadosProcesso['nacionalizacao'] = 'outros';
            } elseif ($request->has('nacionalizacao') && $request->nacionalizacao !== null && $request->nacionalizacao !== '') {
                $dadosProcesso['nacionalizacao'] = $request->nacionalizacao;
            } else {
                // Se não foi enviado, manter o valor existente ou usar 'geral' como padrão para aéreo
                $dadosProcesso['nacionalizacao'] = $processoExistente->nacionalizacao ?? 'geral';
            }
            if ($request->has('cotacao_service_charges') && $request->cotacao_service_charges !== '' && $request->cotacao_service_charges !== null) {
                $dadosProcesso['cotacao_service_charges'] = $this->parseMoneyToFloat($request->cotacao_service_charges, 4);
            } else {
                $dadosProcesso['cotacao_service_charges'] = $processoExistente->cotacao_service_charges ?? null;
            }

            $auditProcessoChanges = array_intersect_key($dadosProcesso, $request->all());
            if (isset($pesoLiquidoFinal) && $request->has('peso_liquido_total_cabecalho')) {
                $auditProcessoChanges['peso_liquido'] = $pesoLiquidoFinal;
            }

            // Atualizar processo na tabela correta
            if ($isAereo) {
                ProcessoAereo::where('id', $id)->update($dadosProcesso);
            } elseif ($isRodoviario) {
                ProcessoRodoviario::where('id', $id)->update($dadosProcesso);
            } else {
                Processo::where('id', $id)->update($dadosProcesso);
            }

            // Salvar auditoria
            if (!empty($auditEntries)) {
                foreach ($auditEntries as $entry) {
                    if ($entry['action'] === 'create') {
                        $auditService->logCreate($entry['meta'], $entry['after'] ?? []);
                    } elseif ($entry['action'] === 'update') {
                        $auditService->logUpdate($entry['meta'], $entry['before'] ?? [], $entry['after'] ?? []);
                    } elseif ($entry['action'] === 'delete') {
                        $auditService->logDelete($entry['meta'], $entry['before'] ?? []);
                    }
                }
            }

            if (!empty($auditProcessoChanges)) {
                $auditService->logUpdate(
                    [
                        'auditable_type' => $isAereo ? ProcessoAereo::class : ($isRodoviario ? ProcessoRodoviario::class : Processo::class),
                        'auditable_id' => $id,
                        'process_type' => $processType,
                        'process_id' => $id,
                        'client_id' => $processo->cliente_id,
                        'context' => 'processo',
                    ],
                    $processoOriginal,
                    $auditProcessoChanges
                );
            }

            DB::commit();

            // Retornar resposta baseada no tipo de requisição
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Processo atualizado com sucesso!',
                    'processo' => $processoExistente,
                    'produtos_processados' => $produtosProcessados,
                ]);
            }

            return redirect()->route('processo.index')
                ->with('success', 'Processo atualizado com sucesso!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Processo não encontrado'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao atualizar processo: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao atualizar processo: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao atualizar processo: ' . $e->getMessage()]);
        }
    }

    public function updateProcesso(Request $request, $id)
    {
        try {
        // Detectar tipo de processo pelo query param ou tentar encontrar em todas as tabelas
        $tipoProcessoRequest = request()->get('tipo_processo', null);
        $processo = null;
        $isAereo = false;
        $isRodoviario = false;
        
        // Primeiro tentar na tabela Processo (marítimo)
        $processo = Processo::find($id);
            
     
                // Se não encontrou na tabela principal, tentar nas tabelas específicas
        if ($tipoProcessoRequest === 'aereo') {
                    $processo = ProcessoAereo::find($id);
                    if ($processo) {
            $isAereo = true;
                    }
        } elseif ($tipoProcessoRequest === 'rodoviario') {
                    $processo = ProcessoRodoviario::find($id);
                    if ($processo) {
            $isRodoviario = true;
                    }
        } else {
                    // Se tipo não foi especificado e não encontrou em Processo, tentar em todas as tabelas
                if (!$processo) {
                    $processo = ProcessoAereo::find($id);
                    if ($processo) {
                        $isAereo = true;
                    } else {
                        $processo = ProcessoRodoviario::find($id);
                        if ($processo) {
                            $isRodoviario = true;
                        }
                    }
                }
            }
        
            
            // Se não encontrou em nenhuma tabela, retornar erro
            if (!$processo) {
                return back()->with('messages', ['error' => ['Processo não encontrado!']])->withInput($request->all());
        }
        
        $this->ensureClienteAccess($processo->cliente_id);
            $auditService = app(ProcessoAuditService::class);
            $processoOriginal = $processo->getAttributes();
            $processType = $isAereo ? 'aereo' : ($isRodoviario ? 'rodoviario' : ($processo->tipo_processo ?? 'maritimo'));

        $validator = Validator::make($request->all(), [], []);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors->unique();
            return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
        }

        // Monta o JSON das cotações das moedas do processo
        $cotacoesMoedaProcesso = [];
            $objeto = null;

            // Primeiro, tentar obter dos campos individuais do formulário (prioridade)
            if ($request->has('cotacao_moeda_processo') && is_array($request->cotacao_moeda_processo)) {
                $objeto = $request->cotacao_moeda_processo;
            } 
            // Se não houver campos individuais, tentar usar o campo hidden JSON
            elseif ($request->has('cotacao_moeda_processo') && is_string($request->cotacao_moeda_processo)) {
                $cotacaoString = trim($request->cotacao_moeda_processo);
                if (!empty($cotacaoString)) {
                    $decoded = json_decode($cotacaoString, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $objeto = $decoded;
                    }
                }
            }

            // Processar o objeto se foi obtido com sucesso
            if ($objeto && is_array($objeto) && !empty($objeto)) {
            foreach ($objeto as $codigo => $cotacao) {
                    if (!is_array($cotacao)) {
                        continue;
                    }
                    $nome = $cotacao['nome'] ?? $codigo;
                    $compra = isset($cotacao['compra']) && $cotacao['compra'] !== '' ? $this->parseMoneyToFloat($cotacao['compra'], 6) : null;
                    $venda = isset($cotacao['venda']) && $cotacao['venda'] !== '' ? $this->parseMoneyToFloat($cotacao['venda'], 6) : null;
                    $data = $request->data_cotacao ?? ($cotacao['data'] ?? date('d-m-Y'));
                    
                    // Só adicionar se pelo menos venda ou compra estiver preenchido
                    if ($venda !== null || $compra !== null) {
                $cotacoesMoedaProcesso[$codigo] = [
                    'nome' => $nome,
                    'data' => $data,
                    'moeda' => $codigo,
                    'compra' => $compra,
                    'venda' => $venda,
                    'erro' => null,
                ];
                    }
            }
        }
        
        // Campos comuns entre marítimo e aéreo
        $dadosProcesso = [
            "frete_internacional" => $this->parseMoneyToFloat($request->frete_internacional),
            "seguro_internacional" => $this->parseMoneyToFloat($request->seguro_internacional),
            "acrescimo_frete" => $this->parseMoneyToFloat($request->acrescimo_frete),
            "service_charges" => $this->parseMoneyToFloat($request->service_charges),
            "service_charges_moeda" => $request->service_charges_moeda,
            "service_charges_usd" => $this->parseMoneyToFloat($request->service_charges_usd),
            "service_charges_brl" => $this->parseMoneyToFloat($request->service_charges_brl),
            "cotacao_service_charges" => $this->parseMoneyToFloat($request->cotacao_service_charges, 4),
            "peso_bruto" => $this->parseMoneyToFloat($request->peso_bruto),
            "peso_liquido" => $this->parseMoneyToFloat($request->peso_liquido),
            'frete_internacional_moeda' => $request->frete_internacional_moeda,
            'seguro_internacional_moeda' => $request->seguro_internacional_moeda,
            'acrescimo_frete_moeda' => $request->acrescimo_frete_moeda,
            "codigo_interno" => $request->codigo_interno ?? $id,
            "descricao" => $request->descricao,
            "canal" => $request->canal,
            // Processo rodoviário sempre usa nacionalização 'outros'
            // Para processos aéreos, usar 'geral' como padrão se não for especificado
            'nacionalizacao' => $isRodoviario ? 'outros' : ($request->nacionalizacao ?? $processo->nacionalizacao ?? 'geral'),
            'multa' => isset($request->multa) ? $this->parseMoneyToFloat($request->multa) : null,
            "status" => $request->status,
            "data_desembaraco_inicio" => $request->data_desembaraco_inicio,
            "data_desembaraco_fim" => $request->data_desembaraco_fim,
            'quantidade' => $this->parseMoneyToFloat($request->quantidade),
            'especie' => $request->especie,
            'cotacao_frete_internacional' => $this->parseMoneyToFloat($request->cotacao_frete_internacional, 4),
            'cotacao_seguro_internacional' => $this->parseMoneyToFloat($request->cotacao_seguro_internacional, 4),
            'cotacao_acrescimo_frete' => $this->parseMoneyToFloat($request->cotacao_acrescimo_frete, 4),
            'data_moeda_frete_internacional' => $request->data_cotacao,
            'data_moeda_seguro_internacional' => $request->data_cotacao,
            'data_moeda_acrescimo_frete' => $request->data_cotacao,
            'cotacao_moeda_processo' => !empty($cotacoesMoedaProcesso) ? $cotacoesMoedaProcesso : null,
            'data_cotacao_processo' => $request->data_cotacao,
            'moeda_processo' => $request->moeda_processo,
        ];
        
        // Adicionar campos específicos baseado no tipo de processo
        if ($isAereo) {
            // Campos específicos do transporte aéreo
            $dadosProcesso['valor_exw'] = isset($request->valor_exw) ? $this->parseMoneyToFloat($request->valor_exw) : null;
            $dadosProcesso['valor_exw_brl'] = isset($request->valor_exw_brl) ? $this->parseMoneyToFloat($request->valor_exw_brl) : null;
            $taxaDolar = $this->parseMoneyToFloat($request->cotacao_frete_internacional, 4) ?? $processo->taxa_dolar ?? 1;
            $dadosProcesso['dai'] = isset($request->dai) ? $this->parseMoneyToFloat($request->dai) : null;
            $dadosProcesso['dape'] = isset($request->dape) ? $this->parseMoneyToFloat($request->dape) : null;
            $dadosProcesso['delivery_fee'] = isset($request->delivery_fee) ? $this->parseMoneyToFloat($request->delivery_fee) : null;
            $dadosProcesso['delivery_fee_brl'] = isset($request->delivery_fee) ? $this->parseMoneyToFloat($request->delivery_fee) * $taxaDolar : null;
            $dadosProcesso['collect_fee'] = isset($request->collect_fee) ? $this->parseMoneyToFloat($request->collect_fee) : null;
            $dadosProcesso['collect_fee_brl'] = isset($request->collect_fee) ? $this->parseMoneyToFloat($request->collect_fee) * $taxaDolar : null;
            $dadosProcesso['outras_taxas_agente'] = isset($request->outras_taxas_agente) ? $this->parseMoneyToFloat($request->outras_taxas_agente) : null;
            $dadosProcesso['desconsolidacao'] = isset($request->desconsolidacao) ? $this->parseMoneyToFloat($request->desconsolidacao) : null;
            $dadosProcesso['handling'] = isset($request->handling) ? $this->parseMoneyToFloat($request->handling) : null;
            $dadosProcesso['correios'] = isset($request->correios) ? $this->parseMoneyToFloat($request->correios) : null;
            $dadosProcesso['li_dta_honor_nix'] = isset($request->li_dta_honor_nix) ? $this->parseMoneyToFloat($request->li_dta_honor_nix) : null;
            $dadosProcesso['honorarios_nix'] = isset($request->honorarios_nix) ? $this->parseMoneyToFloat($request->honorarios_nix) : null;
            $dadosProcesso['thc_capatazia'] = isset($request->thc_capatazia) ? $this->parseMoneyToFloat($request->thc_capatazia) : null;
            
            ProcessoAereo::where('id', $id)->update($dadosProcesso);
        } elseif ($isRodoviario) {
            // Campos específicos do transporte rodoviário
            $dadosProcesso['valor_exw'] = isset($request->valor_exw) ? $this->parseMoneyToFloat($request->valor_exw) : null;
            $dadosProcesso['valor_exw_brl'] = isset($request->valor_exw_brl) ? $this->parseMoneyToFloat($request->valor_exw_brl) : null;
            $dadosProcesso['dai'] = isset($request->dai) ? $this->parseMoneyToFloat($request->dai) : null;
            $dadosProcesso['dape'] = isset($request->dape) ? $this->parseMoneyToFloat($request->dape) : null;
            $dadosProcesso['outras_taxas_agente'] = isset($request->outras_taxas_agente) ? $this->parseMoneyToFloat($request->outras_taxas_agente) : null;
            $dadosProcesso['desconsolidacao'] = isset($request->desconsolidacao) ? $this->parseMoneyToFloat($request->desconsolidacao) : null;
            $dadosProcesso['handling'] = isset($request->handling) ? $this->parseMoneyToFloat($request->handling) : null;
            $dadosProcesso['correios'] = isset($request->correios) ? $this->parseMoneyToFloat($request->correios) : null;
            $dadosProcesso['li_dta_honor_nix'] = isset($request->li_dta_honor_nix) ? $this->parseMoneyToFloat($request->li_dta_honor_nix) : null;
            $dadosProcesso['honorarios_nix'] = isset($request->honorarios_nix) ? $this->parseMoneyToFloat($request->honorarios_nix) : null;
                // Campos específicos rodoviário (não incluir thc_capatazia pois não existe na tabela processo_rodoviarios)
            $dadosProcesso['desp_fronteira'] = isset($request->desp_fronteira) ? $this->parseMoneyToFloat($request->desp_fronteira) : null;
            $dadosProcesso['das_fronteira'] = isset($request->das_fronteira) ? $this->parseMoneyToFloat($request->das_fronteira) : null;
            $dadosProcesso['armazenagem'] = isset($request->armazenagem) ? $this->parseMoneyToFloat($request->armazenagem) : null;
            $dadosProcesso['frete_foz_gyn'] = isset($request->frete_foz_gyn) ? $this->parseMoneyToFloat($request->frete_foz_gyn) : null;
            $dadosProcesso['rep_fronteira'] = isset($request->rep_fronteira) ? $this->parseMoneyToFloat($request->rep_fronteira) : null;
            $dadosProcesso['armaz_anapolis'] = isset($request->armaz_anapolis) ? $this->parseMoneyToFloat($request->armaz_anapolis) : null;
            $dadosProcesso['mov_anapolis'] = isset($request->mov_anapolis) ? $this->parseMoneyToFloat($request->mov_anapolis) : null;
            $dadosProcesso['rep_anapolis'] = isset($request->rep_anapolis) ? $this->parseMoneyToFloat($request->rep_anapolis) : null;
            
                // Remover campos marítimos que não existem na tabela processo_rodoviarios
                $camposMaritimos = [
                    'capatazia',
                    'tx_correcao_lacre',
                    'afrmm',
                    'armazenagem_sts',
                    'armazenagem_porto',
                    'frete_dta_sts_ana',
                    'frete_rodoviario',
                    'dif_frete_rodoviario',
                    'sda',
                    'rep_sts',
                    'rep_porto',
                    'armaz_ana',
                    'lavagem_container',
                    'desp_anapolis'
                ];
                
                foreach ($camposMaritimos as $campo) {
                    unset($dadosProcesso[$campo]);
                }
            ProcessoRodoviario::where('id', $id)->update($dadosProcesso);
        } else {
            // Campos específicos do transporte marítimo (incluindo campos _usd e _brl que não existem no aéreo)
            $dadosProcesso['frete_internacional_usd'] = $this->parseMoneyToFloat($request->frete_internacional_usd);
            $dadosProcesso['frete_internacional_brl'] = $this->parseMoneyToFloat($request->frete_internacional_brl);
            $dadosProcesso['seguro_internacional_usd'] = $this->parseMoneyToFloat($request->seguro_internacional_usd);
            $dadosProcesso['seguro_internacional_brl'] = $this->parseMoneyToFloat($request->seguro_internacional_brl);
            $dadosProcesso['acrescimo_frete_usd'] = $this->parseMoneyToFloat($request->acrescimo_frete_usd);
            $dadosProcesso['acrescimo_frete_brl'] = $this->parseMoneyToFloat($request->acrescimo_frete_brl);
            $dadosProcesso['thc_capatazia'] = $this->parseMoneyToFloat($request->thc_capatazia);
            
            Processo::where('id', $id)->update($dadosProcesso);
        }

            $auditProcessoChanges = array_intersect_key($dadosProcesso, $request->all());
            if (!empty($auditProcessoChanges)) {
                DB::afterCommit(function () use ($auditService, $processo, $processType, $processoOriginal, $auditProcessoChanges) {
                    $auditService->logUpdate([
                        'auditable_type' => get_class($processo),
                        'auditable_id' => $processo->id,
                        'process_type' => $processType,
                        'process_id' => $processo->id,
                        'client_id' => $processo->cliente_id,
                        'context' => 'processo.update',
                    ], $processoOriginal, $auditProcessoChanges);
                });
        }
        
        return back()->with('messages', ['success' => ['Dados do processo atualizado com sucesso!']]);
        } catch (ModelNotFoundException $e) {
            return back()->with('messages', ['error' => ['Processo não encontrado!']])->withInput($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao atualizar processo: ' . $e->getMessage(), [
                'id' => $id,
                'tipo_processo' => request()->get('tipo_processo'),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('messages', ['error' => ['Erro ao atualizar o processo. Por favor, tente novamente.']])->withInput($request->all());
        }
    }

    public function camposCabecalho(Request $request, $id)
    {
        $processo = Processo::findOrFail($id);
        $this->ensureClienteAccess($processo->cliente_id);
        $auditService = app(ProcessoAuditService::class);
        $processoOriginal = $processo->getAttributes();
        $dadosProcesso = [
            'outras_taxas_agente' => $this->parseMoneyToFloat($request->outras_taxas_agente),
            'liberacao_bl' => $this->parseMoneyToFloat($request->liberacao_bl),
            'desconsolidacao' => $this->parseMoneyToFloat($request->desconsolidacao),
            'isps_code' => $this->parseMoneyToFloat($request->isps_code),
            'handling' => $this->parseMoneyToFloat($request->handling),
            'capatazia' => $this->parseMoneyToFloat($request->thc_capatazia),
            'tx_correcao_lacre' => $this->parseMoneyToFloat($request->tx_correcao_lacre),
            'service_charges' => $this->parseMoneyToFloat($request->service_charges),
            'service_charges_moeda' => $request->service_charges_moeda,
            'service_charges_usd' => $this->parseMoneyToFloat($request->service_charges_usd),
            'service_charges_brl' => $this->parseMoneyToFloat($request->service_charges_brl),
            'cotacao_service_charges' => $this->parseMoneyToFloat($request->cotacao_service_charges, 4),
            'afrmm' => $this->parseMoneyToFloat($request->afrmm),
            'armazenagem_sts' => $this->parseMoneyToFloat($request->armazenagem_sts),
            'frete_dta_sts_ana' => $this->parseMoneyToFloat($request->frete_dta_sts_ana),
            'sda' => $this->parseMoneyToFloat($request->sda),
            'rep_sts' => $this->parseMoneyToFloat($request->rep_sts),
            'armaz_ana' => $this->parseMoneyToFloat($request->armaz_ana),
            'lavagem_container' => $this->parseMoneyToFloat($request->lavagem_container),
            'rep_anapolis' => $this->parseMoneyToFloat($request->rep_anapolis),
            'desp_anapolis' => $this->parseMoneyToFloat($request->desp_anapolis),
            'correios' => $this->parseMoneyToFloat($request->correios),
            'li_dta_honor_nix' => $this->parseMoneyToFloat($request->li_dta_honor_nix),
            'honorarios_nix' => $this->parseMoneyToFloat($request->honorarios_nix),
            'peso_liquido' => $this->parseMoneyToFloat($request->peso_liquido),
        ];
        Processo::where('id', $id)->update($dadosProcesso);
        $auditProcessoChanges = array_intersect_key($dadosProcesso, $request->all());
        if (!empty($auditProcessoChanges)) {
            DB::afterCommit(function () use ($auditService, $processo, $processoOriginal, $auditProcessoChanges) {
                $auditService->logUpdate([
                    'auditable_type' => Processo::class,
                    'auditable_id' => $processo->id,
                    'process_type' => $processo->tipo_processo ?? 'maritimo',
                    'process_id' => $processo->id,
                    'client_id' => $processo->cliente_id,
                    'context' => 'processo.cabecalho',
                ], $processoOriginal, $auditProcessoChanges);
            });
        }
        return back()->with('messages', ['success' => ['Cabeçalho do processo atualizado com sucesso!']]);
    }

    /**
     * Salvar apenas os campos do cabeçalho (cabecalhoInputs) do processo aéreo
     */
    public function salvarCabecalhoInputsAereo(Request $request, $id)
    {
        try {
            $processo = ProcessoAereo::findOrFail($id);
            $this->ensureClienteAccess($processo->cliente_id);
            $auditService = app(ProcessoAuditService::class);
            $processoOriginal = $processo->getAttributes();
            
            DB::beginTransaction();
            
            // Preparar dados do cabeçalho
            $dadosCabecalho = [];
            
            // Campos do cabeçalho que devem ser salvos
            $camposCabecalho = [
                'multa',
                'tx_def_li',
                'taxa_siscomex',
                'outras_taxas_agente',
                'delivery_fee',
                'delivery_fee_brl',
                'collect_fee',
                'collect_fee_brl',
                'desconsolidacao',
                'handling',
                'dai',
                'dape',
                'rep_itj',
                'frete_nvg_x_gyn',
                'correios',
                'li_dta_honor_nix',
                'honorarios_nix',
                'diferenca_cambial_frete',
                'diferenca_cambial_fob',
                'opcional_1_valor',
                'opcional_1_descricao',
                'opcional_1_compoe_despesas',
                'opcional_2_valor',
                'opcional_2_descricao',
                'opcional_2_compoe_despesas'
            ];
            
            foreach ($camposCabecalho as $campo) {
                // Campos booleanos e strings são tratados separadamente
                if (in_array($campo, ['opcional_1_compoe_despesas', 'opcional_2_compoe_despesas'])) {
                    if ($request->has($campo)) {
                        $dadosCabecalho[$campo] = $request->$campo == 'on' || $request->$campo == '1' || $request->$campo === true;
                    }
                } elseif (in_array($campo, ['opcional_1_descricao', 'opcional_2_descricao'])) {
                    if ($request->has($campo)) {
                        $dadosCabecalho[$campo] = $request->$campo;
                    }
                } else {
                    // Para campos numéricos, sempre tentar processar usando input() que retorna null se não existir
                    // Isso garante que campos enviados com valor vazio ou 0 sejam salvos corretamente
                    $valorInput = $request->input($campo);
                    $valor = $this->parseMoneyToFloat($valorInput);
                    // Se o valor for null (campo não enviado ou vazio), salvar como 0
                    $dadosCabecalho[$campo] = $valor !== null ? $valor : 0;
                }
            }
            
            // Atualizar peso_liquido se peso_liquido_total_cabecalho foi enviado
            if ($request->has('peso_liquido_total_cabecalho')) {
                $pesoLiquido = $this->parseMoneyToFloat($request->peso_liquido_total_cabecalho);
                // Sempre salvar o peso_liquido, mesmo que seja 0 ou 1 (modo Kg)
                if ($pesoLiquido !== null) {
                    $dadosCabecalho['peso_liquido'] = $pesoLiquido;
                } else {
                    // Se for null, manter o valor atual do processo
                    $dadosCabecalho['peso_liquido'] = $processo->peso_liquido ?? 0;
                }
            }
            
            // Atualizar tipo_peso se foi enviado
            if ($request->has('tipo_peso_aereo')) {
                $dadosCabecalho['tipo_peso'] = $request->tipo_peso_aereo;
            }
            
            // Atualizar no banco (sempre atualizar, mesmo que dadosCabecalho tenha apenas tipo_peso)
            if (!empty($dadosCabecalho)) {
                ProcessoAereo::where('id', $id)->update($dadosCabecalho);
                \Log::info('CabecalhoInputs salvos para ProcessoAereo ID: ' . $id, ['dadosCabecalho' => $dadosCabecalho]);
                DB::afterCommit(function () use ($auditService, $processo, $processoOriginal, $dadosCabecalho) {
                    $auditService->logUpdate([
                        'auditable_type' => ProcessoAereo::class,
                        'auditable_id' => $processo->id,
                        'process_type' => 'aereo',
                        'process_id' => $processo->id,
                        'client_id' => $processo->cliente_id,
                        'context' => 'processo.cabecalho',
                    ], $processoOriginal, $dadosCabecalho);
                });
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Campos do cabeçalho salvos com sucesso!',
                'dados' => $dadosCabecalho
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao salvar cabecalhoInputs do ProcessoAereo ' . $id . ': ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao salvar campos do cabeçalho: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salvar apenas os campos do cabeçalho (cabecalhoInputs) do processo marítimo
     */
    public function salvarCabecalhoInputsMaritimo(Request $request, $id)
    {
        try {
            $processo = Processo::findOrFail($id);
            $this->ensureClienteAccess($processo->cliente_id);
            $auditService = app(ProcessoAuditService::class);
            $processoOriginal = $processo->getAttributes();
            
            DB::beginTransaction();
            
            // Preparar dados do cabeçalho
            $dadosCabecalho = [];
            
            // Campos do cabeçalho que devem ser salvos
            $camposCabecalho = [
                'outras_taxas_agente',
                'liberacao_bl',
                'desconsolidacao',
                'isps_code',
                'handling',
                'afrmm',
                'armazenagem_sts',
                'frete_dta_sts_ana',
                'frete_sts_cgb',
                'diarias',
                'sda',
                'rep_sts',
                'armaz_cgb',
                'rep_cgb',
                'demurrage',
                'armaz_ana',
                'lavagem_container',
                'rep_anapolis',
                'desp_anapolis',
                'correios',
                'li_dta_honor_nix',
                'honorarios_nix',
                'diferenca_cambial_frete',
                'diferenca_cambial_fob',
                'opcional_1_valor',
                'opcional_1_descricao',
                'opcional_1_compoe_despesas',
                'opcional_2_valor',
                'opcional_2_descricao',
                'opcional_2_compoe_despesas'
            ];
            
            foreach ($camposCabecalho as $campo) {
                if ($request->has($campo)) {
                    // Campos booleanos e strings são tratados separadamente
                    if (in_array($campo, ['opcional_1_compoe_despesas', 'opcional_2_compoe_despesas'])) {
                        $dadosCabecalho[$campo] = $request->$campo == 'on' || $request->$campo == '1' || $request->$campo === true;
                    } elseif (in_array($campo, ['opcional_1_descricao', 'opcional_2_descricao'])) {
                        $dadosCabecalho[$campo] = $request->$campo;
                    } else {
                        $valor = $this->parseMoneyToFloat($request->$campo);
                        // Se o valor for null (campo vazio), salvar como 0
                        $dadosCabecalho[$campo] = $valor !== null ? $valor : 0;
                    }
                }
            }
            
            // Tratar capatazia separadamente (vem do campo readonly que lê thc_capatazia)
            if ($request->has('capatazia')) {
                $dadosCabecalho['thc_capatazia'] = $this->parseMoneyToFloat($request->capatazia) ?? 0;
            }
            
            // Atualizar peso_liquido se peso_liquido_total_cabecalho foi enviado
            if ($request->has('peso_liquido_total_cabecalho')) {
                $pesoLiquido = $this->parseMoneyToFloat($request->peso_liquido_total_cabecalho);
                if ($pesoLiquido !== null) {
                    $dadosCabecalho['peso_liquido'] = $pesoLiquido;
                }
            }
            
            // Atualizar no banco
            if (!empty($dadosCabecalho)) {
                Processo::where('id', $id)->update($dadosCabecalho);
                \Log::info('CabecalhoInputs salvos para Processo ID: ' . $id, ['dadosCabecalho' => $dadosCabecalho]);
                DB::afterCommit(function () use ($auditService, $processo, $processoOriginal, $dadosCabecalho) {
                    $auditService->logUpdate([
                        'auditable_type' => Processo::class,
                        'auditable_id' => $processo->id,
                        'process_type' => $processo->tipo_processo ?? 'maritimo',
                        'process_id' => $processo->id,
                        'client_id' => $processo->cliente_id,
                        'context' => 'processo.cabecalho',
                    ], $processoOriginal, $dadosCabecalho);
                });
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Campos do cabeçalho salvos com sucesso!',
                'dados' => $dadosCabecalho
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao salvar cabecalhoInputs do Processo ' . $id . ': ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao salvar campos do cabeçalho: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salvar apenas os campos do cabeçalho (cabecalhoInputs) do processo rodoviário
     */
    public function salvarCabecalhoInputsRodoviario(Request $request, $id)
    {
        try {
            
            // Buscar o processo rodoviário diretamente na tabela específica
            $processo = ProcessoRodoviario::find($id);
            
            // Se não encontrou, retornar erro informativo
            if (!$processo) {
                \Log::error('Erro ao salvar cabecalhoInputs do ProcessoRodoviario ' . $id . ': Processo não encontrado na tabela processo_rodoviarios', [
                    'id' => $id,
                    'request_data' => $request->all(),
                    'existe_em_processos' => Processo::find($id) ? 'sim' : 'não'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Processo rodoviário não encontrado! O processo com ID ' . $id . ' não existe na tabela processo_rodoviarios. Por favor, certifique-se de que o processo foi criado corretamente.'
                ], 404);
            }
            
            $this->ensureClienteAccess($processo->cliente_id);
            $auditService = app(ProcessoAuditService::class);
            $processoOriginal = $processo->getAttributes();
            
            DB::beginTransaction();
            
            // Preparar dados do cabeçalho
            $dadosCabecalho = [];
            
            // Campos do cabeçalho que devem ser salvos (sem delivery_fee e collect_fee)
            $camposCabecalho = [
                'multa',
                'tx_def_li',
                'desp_fronteira',
                'das_fronteira',
                'armazenagem',
                'frete_foz_gyn',
                'rep_fronteira',
                'armaz_anapolis',
                'mov_anapolis',
                'rep_anapolis',
                'correios',
                'li_dta_honor_nix',
                'honorarios_nix',
                'outras_taxas_agente',
                'desconsolidacao',
                'handling',
                'dai',
                'dape',
                'diferenca_cambial_frete',
                'diferenca_cambial_fob',
                'opcional_1_valor',
                'opcional_1_descricao',
                'opcional_1_compoe_despesas',
                'opcional_2_valor',
                'opcional_2_descricao',
                'opcional_2_compoe_despesas'
            ];
            
            foreach ($camposCabecalho as $campo) {
                if ($request->has($campo)) {
                    // Campos booleanos e strings são tratados separadamente
                    if (in_array($campo, ['opcional_1_compoe_despesas', 'opcional_2_compoe_despesas'])) {
                        $dadosCabecalho[$campo] = $request->$campo == 'on' || $request->$campo == '1' || $request->$campo === true;
                    } elseif (in_array($campo, ['opcional_1_descricao', 'opcional_2_descricao'])) {
                        $dadosCabecalho[$campo] = $request->$campo;
                    } else {
                        $valor = $this->parseMoneyToFloat($request->$campo);
                        // Se o valor for null (campo vazio), salvar como 0
                        $dadosCabecalho[$campo] = $valor !== null ? $valor : 0;
                    }
                }
            }
            
            // Atualizar peso_liquido se peso_liquido_total_cabecalho foi enviado
            if ($request->has('peso_liquido_total_cabecalho')) {
                $pesoLiquido = $this->parseMoneyToFloat($request->peso_liquido_total_cabecalho);
                $dadosCabecalho['peso_liquido'] = $pesoLiquido !== null ? $pesoLiquido : 0;
            }
            
            // Processo rodoviário sempre usa nacionalização 'outros'
            $dadosCabecalho['nacionalizacao'] = 'outros';
            
            // Atualizar no banco
            if (!empty($dadosCabecalho)) {
                ProcessoRodoviario::where('id', $id)->update($dadosCabecalho);
                \Log::info('CabecalhoInputs salvos para ProcessoRodoviario ID: ' . $id, ['dadosCabecalho' => $dadosCabecalho]);
                DB::afterCommit(function () use ($auditService, $processo, $processoOriginal, $dadosCabecalho) {
                    $auditService->logUpdate([
                        'auditable_type' => ProcessoRodoviario::class,
                        'auditable_id' => $processo->id,
                        'process_type' => 'rodoviario',
                        'process_id' => $processo->id,
                        'client_id' => $processo->cliente_id,
                        'context' => 'processo.cabecalho',
                    ], $processoOriginal, $dadosCabecalho);
                });
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Campos do cabeçalho salvos com sucesso!',
                'dados' => $dadosCabecalho
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao salvar cabecalhoInputs do ProcessoRodoviario ' . $id . ': ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao salvar campos do cabeçalho: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(int $id)
    {
        try {
            // Obter o tipo de processo do query param ou tentar detectar
            $tipoProcessoRequest = request()->get('tipo_processo', null);
            $processo = null;
            $isAereo = false;
            $isRodoviario = false;
            
            // Se o tipo foi especificado no query param, buscar diretamente na tabela específica
            if ($tipoProcessoRequest === 'aereo') {
                $processo = ProcessoAereo::find($id);
                if ($processo) {
                    $isAereo = true;
                }
            } elseif ($tipoProcessoRequest === 'rodoviario') {
                $processo = ProcessoRodoviario::find($id);
                if ($processo) {
                    $isRodoviario = true;
                }
            } else {
                // Se tipo não foi especificado, tentar buscar em todas as tabelas
                // Primeiro na tabela principal Processo (marítimo)
                $processo = Processo::find($id);
                
                if (!$processo) {
                    // Se não encontrou na tabela principal, tentar aéreo
                    $processo = ProcessoAereo::find($id);
                    if ($processo) {
                        $isAereo = true;
                    } else {
                        // Se não encontrou, tentar rodoviário
                        $processo = ProcessoRodoviario::find($id);
                        if ($processo) {
                            $isRodoviario = true;
                        }
                    }
                }
            }
            
            // Se não encontrou em nenhuma tabela, retornar erro
            if (!$processo) {
                return back()->with('messages', ['error' => ['Processo não encontrado!']]);
            }
            
            $this->ensureClienteAccess($processo->cliente_id);
            $auditService = app(ProcessoAuditService::class);
            $processType = $isAereo ? 'aereo' : ($isRodoviario ? 'rodoviario' : ($processo->tipo_processo ?? 'maritimo'));
            $processoSnapshot = $processo->getAttributes();
            $produtosSnapshot = collect();
            if ($isAereo) {
                $produtosSnapshot = ProcessoAereoProduto::where('processo_aereo_id', $id)->get();
            } elseif ($isRodoviario) {
                $produtosSnapshot = ProcessoRodoviarioProduto::where('processo_rodoviario_id', $id)->get();
            } else {
                $produtosSnapshot = ProcessoProduto::where('processo_id', $id)->get();
            }
            
            // Excluir produtos da tabela correta
            if ($isAereo) {
                ProcessoAereoProduto::where('processo_aereo_id', $id)->delete();
            } elseif ($isRodoviario) {
                ProcessoRodoviarioProduto::where('processo_rodoviario_id', $id)->delete();
            } else {
                // Processo marítimo
            ProcessoProduto::where('processo_id', $id)->delete();
            }
            
            // Excluir o processo
            $processo->delete();

            foreach ($produtosSnapshot as $produto) {
                $auditService->logDelete([
                    'auditable_type' => get_class($produto),
                    'auditable_id' => $produto->id,
                    'process_type' => $processType,
                    'process_id' => $processo->id,
                    'client_id' => $processo->cliente_id,
                    'context' => 'processo.produto.delete',
                ], $produto->getAttributes());
            }

            $auditService->logDelete([
                'auditable_type' => get_class($processo),
                'auditable_id' => $processo->id,
                'process_type' => $processType,
                'process_id' => $processo->id,
                'client_id' => $processo->cliente_id,
                'context' => 'processo.delete',
            ], $processoSnapshot);
            
            return back()->with('messages', ['success' => ['Processo excluído com sucesso!']]);
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir processo: ' . $e->getMessage(), [
                'id' => $id,
                'tipo_processo' => request()->get('tipo_processo'),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('messages', ['error' => ['Não foi possível excluir o processo!']]);
        }
    }
    public function destroyProduto(int $id)
    {
        try {
            $tipoProcesso = request()->input('tipo_processo') ?? request()->query('tipo_processo');
            $resolverProduto = function (string $modelClass, string $relation, string $tipo) use ($id) {
                $item = $modelClass::with($relation)->find($id);
                if (!$item) {
                    return null;
                }
                $processo = $item->$relation ?? null;
                if (!$processo) {
                    return null;
                }
                return [
                    'item' => $item,
                    'processo' => $processo,
                    'type' => $tipo,
                    'auditable' => $modelClass,
                ];
            };

            $candidato = null;
            if ($tipoProcesso === 'aereo') {
                $candidato = $resolverProduto(ProcessoAereoProduto::class, 'processoAereo', 'aereo');
            } elseif ($tipoProcesso === 'rodoviario') {
                $candidato = $resolverProduto(ProcessoRodoviarioProduto::class, 'processoRodoviario', 'rodoviario');
            } elseif ($tipoProcesso === 'maritimo') {
                $candidato = $resolverProduto(ProcessoProduto::class, 'processo', 'maritimo');
            } else {
                $candidatos = array_values(array_filter([
                    $resolverProduto(ProcessoAereoProduto::class, 'processoAereo', 'aereo'),
                    $resolverProduto(ProcessoRodoviarioProduto::class, 'processoRodoviario', 'rodoviario'),
                    $resolverProduto(ProcessoProduto::class, 'processo', 'maritimo'),
                ]));

                if (count($candidatos) > 1) {
                    return back()->with('messages', [
                        'error' => ['Tipo de processo não informado. Atualize a página e tente novamente.'],
                    ]);
                }

                $candidato = $candidatos[0] ?? null;
            }

            if (!$candidato) {
                return back()->with('messages', ['error' => ['Produto não encontrado para o tipo de processo informado.']]);
            }

            $produtoProcesso = $candidato['item'];
            $processo = $candidato['processo'];
            $processType = $processo->tipo_processo ?? $candidato['type'];
            $clienteId = $processo->cliente_id ?? null;
            if ($clienteId !== null) {
                $this->ensureClienteAccess($clienteId);
            }
            $auditService = app(ProcessoAuditService::class);
            $produtoSnapshot = $produtoProcesso->getAttributes();
            $produtoProcesso->delete();
            $auditService->logDelete([
                'auditable_type' => $candidato['auditable'],
                'auditable_id' => $produtoSnapshot['id'] ?? $id,
                'process_type' => $processType,
                'process_id' => $processo->id ?? null,
                'client_id' => $clienteId,
                'context' => 'processo.produto.delete',
            ], $produtoSnapshot);
            return back()->with('messages', ['success' => ['Produto excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o Produto !']]);
        }
    }

    /**
     * Exclui em lote os ProcessoProdutoMulta informados por id.
     * Recebe: ids => [1,2,3]
     */
    public function batchDeleteMulta(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $ids = $request->input('ids', []);

        $user = $request->user();
        try {
            $produtosMulta = ProcessoProdutoMulta::with('processo.cliente')->whereIn('id', $ids)->get();

            foreach ($produtosMulta as $produtoMulta) {
                $clienteId = $produtoMulta->processo->cliente_id ?? null;
                if ($clienteId !== null && $user && !$user->canAccessCliente($clienteId)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cliente não autorizado para este usuário.',
                    ], 403);
                }
            }

            $deleted = ProcessoProdutoMulta::whereIn('id', $ids)->delete();
            $auditService = app(ProcessoAuditService::class);
            foreach ($produtosMulta as $produtoMulta) {
                $processo = $produtoMulta->processo;
                $auditService->logDelete([
                    'auditable_type' => ProcessoProdutoMulta::class,
                    'auditable_id' => $produtoMulta->id,
                    'process_type' => $processo->tipo_processo ?? 'maritimo',
                    'process_id' => $processo->id ?? null,
                    'client_id' => $processo->cliente_id ?? null,
                    'context' => 'processo.produto.multa.delete',
                ], $produtoMulta->getAttributes());
            }

            return response()->json([
                'success' => true,
                'deleted_count' => $deleted,
                'deleted_ids' => $ids,
            ]);
        } catch (\Exception $ex) {
            \Log::error('Erro ao excluir processo produtos multa: '.$ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir produtos multa. Contate o administrador.',
            ], 500);
        }
    }
    private function parsePercentageToFloat($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // Remove símbolo de porcentagem e espaços
        $cleanValue = str_replace(['%', ' '], '', trim($value));
        
        // Se após limpar ficou vazio, retorna null
        if ($cleanValue === '') {
            return null;
        }

        // Se já for numérico, retorna como está (o banco espera valores como 33.45, não 0.3345)
        if (is_numeric($cleanValue)) {
            return (float) $cleanValue;
        }

        // Substitui vírgula por ponto para decimal
        $cleanValue = str_replace(',', '.', $cleanValue);

        // Se ainda não for numérico após substituir vírgula, retorna null
        if (!is_numeric($cleanValue)) {
            return null;
        }

        // Converte para float
        return (float) $cleanValue;
    }

    private function safePercentage($value)
    {
        $percentage = $this->parsePercentageToFloat($value);

        if (is_null($percentage)) {
            return null;
        }

        // Retorna o valor como está, pois o campo decimal(5,2) suporta até 999.99
        return $percentage;
    }

    private function sanitizeNumericString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $clean = preg_replace('/\D+/', '', $value);
        return $clean !== '' ? $clean : null;
    }

    private function normalizeNullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }

    protected function ensureClienteAccess(int $clienteId): void
    {
        $user = auth()->user();
        if ($user && !$user->canAccessCliente($clienteId)) {
            abort(403, 'Cliente não autorizado para este usuário.');
        }
    }
}
