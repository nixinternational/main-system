<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\Processo;
use App\Models\ProcessoProduto;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        
        $processos = Processo::when(request()->search != '', function ($query) {})
            ->where('cliente_id', $cliente_id)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(request()->paginacao ?? 10)
            ->appends(request()->except('page'));
            
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
            
            $processo = Processo::create([
                'codigo_interno' => '-',
                'numero_processo' => '-',
                'cliente_id' => $cliente_id,
                'tipo_processo' => $tipo_processo
            ]);

            return redirect(route('processo.edit', $processo->id))->with('messages', ['success' => ['Processo criado com sucesso!']]);
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
            
            $processo = Processo::create([
                'codigo_interno' => '-',
                'numero_processo' => '-',
                'cliente_id' => $cliente_id,
                'tipo_processo' => $tipo_processo
            ]);

            return redirect(route('processo.edit', $processo->id))->with('messages', ['success' => ['Processo criado com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
        }
    }


    public function show($id)
    {
        //
    }
    public function esbocoPdf($id)
    {
        $processo = Processo::with(['cliente', 'processoProdutos.produto'])->findOrFail($id);
        $this->ensureClienteAccess($processo->cliente_id);
        
        // Parse dos campos monetários do processo
        $processo = $this->parseModelFieldsFromModel($processo);
        
        // Parse dos campos monetários dos produtos e manter a relação produto
        $processoProdutos = [];
        foreach ($processo->processoProdutos as $produto) {
            $produtoParsed = $this->parseModelFieldsFromModel($produto);
            // Manter a relação produto após o parse
            $produtoParsed->setRelation('produto', $produto->produto);
            $processoProdutos[] = $produtoParsed;
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
                'cotacao_acrescimo_frete'
            ])) {

                // apenas transforma string numérica em float, sem truncar casas decimais
                $model->$field = (float) $value;

                // se quiser, pode controlar arredondamento só para exibição:
                // $model->$field = round((float)$value, 7); // por exemplo, até 7 casas
            }
        }

        return $model;
    }

    public function edit($id)
    {

        try {
            $processoModel = Processo::findOrFail($id);
            $this->ensureClienteAccess($processoModel->cliente_id);
            $processo =  $this->parseModelFieldsFromModel($processoModel);
            $clientes = Cliente::select(['id', 'nome'])->get();
            $catalogo = Catalogo::where('cliente_id', $processo->cliente_id)->first();
            if (!$catalogo) {
                return redirect(route('processo.index'))->with('messages', ['error' => ['Não é possível acessar um processo com catálogo desde cliente vazio!']]);
            }
            $productsClient = $catalogo->produtos;
            $dolar = self::getBid();

            $moedasSuportadas = self::buscarMoedasSuportadas();
            $produtos = [];
            foreach ($processo->processoProdutos as $produto) {
                $produtos[] = $this->parseModelFieldsFromModel($produto);
            }
            $processoProdutos = $produtos;
            
            // Determinar a view baseada no tipo_processo
            $tipo_processo = $processo->tipo_processo ?? 'maritimo';
            $viewName = 'processo.form-' . $tipo_processo;
            
            // Se a view específica não existir, usar a view marítimo como fallback
            if (!view()->exists($viewName)) {
                $viewName = 'processo.form-maritimo';
            }
            
            return view($viewName, compact('processo', 'clientes', 'productsClient', 'dolar', 'processoProdutos', 'moedasSuportadas'));
        } catch (Exception $e) {
            dd($e);
            return redirect(route('processo.index'))->with('messages', ['error' => ['Processo não encontrado!']]);
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
    public function update(Request $request, $id)
    {
        try {
            $processo = Processo::findOrFail($id);
            $this->ensureClienteAccess($processo->cliente_id);
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
                    $produtosExistentes = ProcessoProduto::whereIn('id', $idsProdutos)
                        ->get()
                        ->keyBy('id')
                        ->toArray();
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

                foreach ($request->produtos as $key => $produto) {
                    $pesoLiquidoTotal += isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : 0;

                    $processoProduto = ProcessoProduto::updateOrCreate(
                        [
                            'id' => $produto['processo_produto_id'] ?? null,
                            'processo_id' => $id ?? 0,
                        ],
                        [
                            'item' => $produto['item'],
                            'produto_id' => $produto['produto_id'],
                            'adicao' => isset($produto['adicao']) ? (int)$produto['adicao'] : null,
                            'quantidade' => isset($produto['quantidade']) ? $this->parseMoneyToFloat($produto['quantidade']) : null,
                            'peso_liquido_unitario' => isset($produto['peso_liquido_unitario']) ? $this->parseMoneyToFloat($produto['peso_liquido_unitario']) : null,
                            'peso_liquido_total' => isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : null,
                            'fator_peso' => isset($produto['fator_peso']) ? $this->parseMoneyToFloat($produto['fator_peso']) : null,
                            'fob_unit_usd' => isset($produto['fob_unit_usd']) ? $this->parseMoneyToFloat($produto['fob_unit_usd']) : null,
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
                            'tx_def_li' => isset($produto['tx_def_li']) ? $this->safePercentage($produto['tx_def_li']) : null,
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
                            'sda' => isset($produto['sda']) ? $this->parseMoneyToFloat($produto['sda']) : null,
                            'rep_sts' => isset($produto['rep_sts']) ? $this->parseMoneyToFloat($produto['rep_sts']) : null,
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
                            'custo_unitario_final' => isset($produto['custo_unitario_final']) ? $this->parseMoneyToFloat($produto['custo_unitario_final']) : null,
                            'custo_total_final' => isset($produto['custo_total_final']) ? $this->parseMoneyToFloat($produto['custo_total_final']) : null,
                            "descricao" => $produto['descricao'],
                            'fob_unit_moeda_estrangeira' => isset($produto['fob_unit_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_unit_moeda_estrangeira']) : null,
                            'fob_total_moeda_estrangeira' => isset($produto['fob_total_moeda_estrangeira']) ? $this->parseMoneyToFloat($produto['fob_total_moeda_estrangeira']) : null,
                            'vlr_crf_total' => isset($produto['vlr_crf_total']) ? $this->parseMoneyToFloat($produto['vlr_crf_total']) : null,
                            'vlr_crf_unit' => isset($produto['vlr_crf_unit']) ? $this->parseMoneyToFloat($produto['vlr_crf_unit']) : null,
                            // Preservar valores existentes de service_charges se não foram enviados ou estão vazios
                            'service_charges' => isset($produto['service_charges']) && $produto['service_charges'] !== '' ? $this->parseMoneyToFloat($produto['service_charges']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges'] ?? null : null),
                            'service_charges_brl' => isset($produto['service_charges_brl']) && $produto['service_charges_brl'] !== '' ? $this->parseMoneyToFloat($produto['service_charges_brl']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges_brl'] ?? null : null),
                            'service_charges_moeda_estrangeira' => isset($produto['service_charges_moeda_estrangeira']) && $produto['service_charges_moeda_estrangeira'] !== '' ? $this->parseMoneyToFloat($produto['service_charges_moeda_estrangeira']) : (isset($produto['processo_produto_id']) && $produto['processo_produto_id'] && isset($produtosExistentes[$produto['processo_produto_id']]) ? $produtosExistentes[$produto['processo_produto_id']]['service_charges_moeda_estrangeira'] ?? null : null),
                        ]
                    );

                    $produtosProcessados++;
                }
            }

            Processo::where('id', $id)->update(['peso_liquido' => $pesoLiquidoTotal]);
            
            // Carregar processo existente para preservar valores quando salvar apenas produtos
            $processoExistente = Processo::find($id);
            
            $dadosProcesso = [
                'outras_taxas_agente' => $this->parseMoneyToFloat($request->outras_taxas_agente),
                'liberacao_bl' => $this->parseMoneyToFloat($request->liberacao_bl),
                'desconsolidacao' => $this->parseMoneyToFloat($request->desconsolidacao),
                'isps_code' => $this->parseMoneyToFloat($request->isps_code),
                'handling' => $this->parseMoneyToFloat($request->handling),
                'capatazia' => $this->parseMoneyToFloat($request->thc_capatazia),
                'tx_correcao_lacre' => $this->parseMoneyToFloat($request->tx_correcao_lacre),
                // Preservar service_charges do processo se não foi enviado ou está vazio
                'service_charges' => ($request->has('service_charges') && $request->service_charges !== '' && $request->service_charges !== null) ? $this->parseMoneyToFloat($request->service_charges) : ($processoExistente->service_charges ?? null),
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
                'diferenca_cambial_frete' => $this->parseMoneyToFloat($request->diferenca_cambial_frete),
                'diferenca_cambial_fob' => $this->parseMoneyToFloat($request->diferenca_cambial_fob),
            ];
            
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

            if ($request->has('nacionalizacao') && $request->nacionalizacao !== null) {
                $dadosProcesso['nacionalizacao'] = $request->nacionalizacao;
            }
            
            if ($request->has('cotacao_service_charges') && $request->cotacao_service_charges !== '' && $request->cotacao_service_charges !== null) {
                $dadosProcesso['cotacao_service_charges'] = $this->parseMoneyToFloat($request->cotacao_service_charges, 4);
            } else {
                $dadosProcesso['cotacao_service_charges'] = $processoExistente->cotacao_service_charges ?? null;
            }

            Processo::where('id', $id)->update($dadosProcesso);
            DB::commit();

            // Retorno para requisições AJAX (salvamento por blocos)
            if ($request->ajax() || $request->has('salvar_apenas_produtos')) {
                return response()->json([
                    'success' => true,
                    'produtos_processados' => $produtosProcessados,
                    'peso_liquido_total' => $pesoLiquidoTotal,
                    'bloco' => $request->bloco_indice ?? 1,
                    'total_blocos' => $request->total_blocos ?? 1
                ]);
            }

            // Retorno para requisições normais
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao salvar processo ' . $id . ': ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Retorno para requisições AJAX
            if ($request->ajax() || $request->has('salvar_apenas_produtos')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro interno do servidor: ' . $e->getMessage()
                ]);
            }

            // Retorno para requisições normais
            return response()->json([
                'success' => false,
                'error' => 'Erro ao salvar: ' . $e->getMessage()
            ]);
        }
    }

    public function updateProcesso(Request $request, $id)
    {
        $processo = Processo::findOrFail($id);
        $this->ensureClienteAccess($processo->cliente_id);

        $validator = Validator::make($request->all(), [], []);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = $errors->unique();
            return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
        }

        // Monta o JSON das cotações das moedas do processo
        $cotacoesMoedaProcesso = [];

        if ($request->has('cotacao_moeda_processo')) {

            $objeto = is_array($request->cotacao_moeda_processo) ? $request->cotacao_moeda_processo : json_decode($request->cotacao_moeda_processo, true);

            foreach ($objeto as $codigo => $cotacao) {
                $nome = $cotacao['nome'] ?? null;
                $compra = isset($cotacao['compra']) ? $this->parseMoneyToFloat($cotacao['compra'], 6) : null;
                $venda = isset($cotacao['venda']) ? $this->parseMoneyToFloat($cotacao['venda'], 6) : null;
                $data = $request->data_cotacao ?? date('d-m-Y');
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
        $dadosProcesso = [
            "frete_internacional" => $this->parseMoneyToFloat($request->frete_internacional),
            "frete_internacional_usd" => $this->parseMoneyToFloat($request->frete_internacional_usd),
            "frete_internacional_brl" => $this->parseMoneyToFloat($request->frete_internacional_brl),
            "seguro_internacional" => $this->parseMoneyToFloat($request->seguro_internacional),
            "seguro_internacional_usd" => $this->parseMoneyToFloat($request->seguro_internacional_usd),
            "seguro_internacional_brl" => $this->parseMoneyToFloat($request->seguro_internacional_brl),
            "acrescimo_frete" => $this->parseMoneyToFloat($request->acrescimo_frete),
            "acrescimo_frete_usd" => $this->parseMoneyToFloat($request->acrescimo_frete_usd),
            "acrescimo_frete_brl" => $this->parseMoneyToFloat($request->acrescimo_frete_brl),
            "thc_capatazia" => $this->parseMoneyToFloat($request->thc_capatazia),
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
            'nacionalizacao' => $request->nacionalizacao ?? $processo->nacionalizacao ?? 'outros',
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
            'cotacao_moeda_processo' => !empty($cotacoesMoedaProcesso) ? json_encode($cotacoesMoedaProcesso, JSON_UNESCAPED_UNICODE) : null,
            'data_cotacao_processo' => $request->data_cotacao,
            'moeda_processo' => $request->moeda_processo,
        ];
        Processo::where('id', $id)->update($dadosProcesso);
        return back()->with('messages', ['success' => ['Dados do processo atualizado com sucesso!']]);
    }

    public function camposCabecalho(Request $request, $id)
    {
        $processo = Processo::findOrFail($id);
        $this->ensureClienteAccess($processo->cliente_id);
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
        return back()->with('messages', ['success' => ['Cabeçalho do processo atualizado com sucesso!']]);
    }
    public function destroy(int $id)
    {
        try {
            $processo = Processo::findOrFail($id);
            $this->ensureClienteAccess($processo->cliente_id);
            ProcessoProduto::where('processo_id', $id)->delete();
            $processo->delete();
            return back()->with('messages', ['success' => ['Processo excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o processo!']]);
        }
    }
    public function destroyProduto(int $id)
    {
        try {
            $produtoProcesso = ProcessoProduto::with('processo')->findOrFail($id);
            $clienteId = $produtoProcesso->processo->cliente_id ?? null;
            if ($clienteId !== null) {
                $this->ensureClienteAccess($clienteId);
            }
            $produtoProcesso->delete();
            return back()->with('messages', ['success' => ['Produto excluído com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o Produto !']]);
        }
    }
    private function parsePercentageToFloat($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        // Se já for numérico, retorna como está (o banco espera valores como 33.45, não 0.3345)
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Remove símbolo de porcentagem e espaços
        $cleanValue = str_replace(['%', ' '], '', trim($value));

        // Substitui vírgula por ponto para decimal
        $cleanValue = str_replace(',', '.', $cleanValue);

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

    protected function ensureClienteAccess(int $clienteId): void
    {
        $user = auth()->user();
        if ($user && !$user->canAccessCliente($clienteId)) {
            abort(403, 'Cliente não autorizado para este usuário.');
        }
    }
}
