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
use Illuminate\Support\Facades\Http;

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


        $resultado = Cache::get($cacheKey);

        if (!$resultado) {
            Artisan::call('atualizar:moedas'); // dispara a command
            $resultado = Cache::get($cacheKey, []); // pega do cache após atualização
        }

        return $resultado;
    }

    private static function buscarMoedasSuportadas(): array
    {
        try {
            $resposta = Http::timeout(10)->get('https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?$format=json');
            $dados = $resposta->json()['value'] ?? [];

            return collect($dados)
                ->pluck('nomeFormatado', 'simbolo')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
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
        $processos =    Cliente::when(request()->search != '', function ($query) {
            // $query->where('name','like','%'.request()->search.'%');
        })->paginate(request()->paginacao ?? 10);;
        return view('processo.index', compact('processos'));
    }

    public function processoCliente($cliente_id)
    {

        $processos =    Processo::when(request()->search != '', function ($query) {})->where('cliente_id', $cliente_id)
            ->paginate(request()->paginacao ?? 10);;
        $cliente = Cliente::find($cliente_id);
        return view('processo.processos', compact('processos', 'cliente'));
    }


    public function create()
    {
        $clientes = Cliente::select(['id', 'nome'])->get();
        $dolar = self::getBid();

        return view('processo.form', compact('clientes', 'dolar'));
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
            $cliente_id = $request->cliente_id;
            $processo = Processo::create([
                'codigo_interno' => 'Processo',
                'numero_processo' => '45',
                'cliente_id' => $cliente_id
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
        $processo = Processo::findOrFail($id);
        // todos os dados necessários
        $dados = [
            'processo' => $processo,
            // outros dados como clientes, etc
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


        $processo =  $this->parseModelFieldsFromModel(Processo::find($id));
        $clientes = Cliente::select(['id', 'nome'])->get();
        $catalogo = Catalogo::where('cliente_id', $processo->cliente_id)->first();
        $productsClient = $catalogo->produtos;
        $dolar = self::getBid();

        $moedasSuportadas = self::buscarMoedasSuportadas();
        $produtos = [];
        foreach ($processo->processoProdutos as $produto) {
            $produtos[] = $this->parseModelFieldsFromModel($produto);
        }
        $processoProdutos = $produtos;
        return view('processo.form', compact('processo', 'clientes', 'productsClient', 'dolar', 'processoProdutos', 'moedasSuportadas'));
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
            $validator = Validator::make($request->all(), [], []);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $message = $errors->unique();
                return back()->with('messages', ['error' => [implode('<br> ', $message)]])->withInput($request->all());
            }
            
            $dadosProcesso = [
                "frete_internacional" => $this->parseMoneyToFloat($request->frete_internacional),
                "seguro_internacional" => $this->parseMoneyToFloat($request->seguro_internacional),
                "acrescimo_frete" => $this->parseMoneyToFloat($request->acrescimo_frete),
                "thc_capatazia" => $this->parseMoneyToFloat($request->thc_capatazia),
                "peso_bruto" => $this->parseMoneyToFloat($request->peso_bruto),
                'frete_internacional_moeda' => $request->frete_internacional_moeda,
                'seguro_internacional_moeda' => $request->seguro_internacional_moeda,
                'acrescimo_frete_moeda' => $request->acrescimo_frete_moeda,
                "codigo_interno" => $request->codigo_interno ?? $id,
                "descricao" => $request->descricao,
                "canal" => $request->canal,
                "status" => $request->status,
                "data_desembaraco_inicio" => $request->data_desembaraco_inicio,
                "data_desembaraco_fim" => $request->data_desembaraco_fim,
                'outras_taxas_agente' => $this->parseMoneyToFloat($request->outras_taxas_agente),
                'liberacao_bl' => $this->parseMoneyToFloat($request->liberacao_bl),
                'desconsolidacao' => $this->parseMoneyToFloat($request->desconsolidacao),
                'isps_code' => $this->parseMoneyToFloat($request->isps_code),
                'handling' => $this->parseMoneyToFloat($request->handling),
                'capatazia' => $this->parseMoneyToFloat($request->thc_capatazia),
                'afrmm' => $this->parseMoneyToFloat($request->afrmm),
                'armazenagem_sts' => $this->parseMoneyToFloat($request->armazenagem_sts),
                'frete_dta_sts_ana' => $this->parseMoneyToFloat($request->frete_dta_sts_ana),
                'sda' => $this->parseMoneyToFloat($request->sda),
                'rep_sts' => $this->parseMoneyToFloat($request->rep_sts),
                'armaz_ana' => $this->parseMoneyToFloat($request->armaz_ana),
                'lavagem_container' => $this->parseMoneyToFloat($request->lavagem_container),
                'rep_anapolis' => $this->parseMoneyToFloat($request->rep_anapolis),
                'li_dta_honor_nix' => $this->parseMoneyToFloat($request->li_dta_honor_nix),
                'honorarios_nix' => $this->parseMoneyToFloat($request->honorarios_nix),
                'quantidade' => $this->parseMoneyToFloat($request->quantidade),
                'especie' => $request->especie,
                'cotacao_frete_internacional' => $this->parseMoneyToFloat($request->cotacao_frete_internacional),
                'cotacao_seguro_internacional' => $this->parseMoneyToFloat($request->cotacao_seguro_internacional),
                'cotacao_acrescimo_frete' => $this->parseMoneyToFloat($request->cotacao_acrescimo_frete),
                'data_moeda_frete_internacional' => $request->data_moeda_frete_internacional,
                'data_moeda_seguro_internacional' => $request->data_moeda_seguro_internacional,
                'data_moeda_acrescimo_frete' => $request->data_moeda_acrescimo_frete,
            ];
            Processo::where('id', $id)->update($dadosProcesso);
            $pesoLiquidoTotal = 0;
            if ($request->produtos  && count($request->produtos) > 0) {
                foreach ($request->produtos as $key => $produto) {
                    if (!isset($produto['produto_id']) || empty($produto['produto_id'])) {
                        return back()->with('messages', ['error' => ['Todos as linhas devem ter um produto selecionado!']])
                            ->withInput($request->all());
                    }
                }

                foreach ($request->produtos as $key => $produto) {
                        

                    $pesoLiquidoTotal +=  isset($produto['peso_liquido_total']) ? $this->parseMoneyToFloat($produto['peso_liquido_total']) : 0;


                    $processoProduto =   ProcessoProduto::updateOrCreate(
                        [
                            'id' => $produto['processo_produto_id'],
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
                            'valor_total_nf_com_icms_st' => isset($produto['valor_total_nf_com_icms_st']) ? $this->parseMoneyToFloat($produto['valor_total_nf_com_icms_st']) : null,
                            'fator_valor_fob' => isset($produto['fator_valor_fob']) ? $this->parseMoneyToFloat($produto['fator_valor_fob']) : null,
                            'fator_tx_siscomex' => isset($produto['fator_tx_siscomex']) ? $this->parseMoneyToFloat($produto['fator_tx_siscomex']) : null,
                            'multa' => isset($produto['multa']) ? $this->parseMoneyToFloat($produto['multa']) : null,
                            'tx_def_li' => isset($produto['tx_def_li']) ? $this->parseMoneyToFloat($produto['tx_def_li']) : null,
                            'taxa_siscomex' => isset($produto['taxa_siscomex']) ? $this->parseMoneyToFloat($produto['taxa_siscomex']) : null,
                            'outras_taxas_agente' => isset($produto['outras_taxas_agente']) ? $this->parseMoneyToFloat($produto['outras_taxas_agente']) : null,
                            'liberacao_bl' => isset($produto['liberacao_bl']) ? $this->parseMoneyToFloat($produto['liberacao_bl']) : null,
                            'desconsolidacao' => isset($produto['desconsolidacao']) ? $this->parseMoneyToFloat($produto['desconsolidacao']) : null,
                            'isps_code' => isset($produto['isps_code']) ? $this->parseMoneyToFloat($produto['isps_code']) : null,
                            'handling' => isset($produto['handling']) ? $this->parseMoneyToFloat($produto['handling']) : null,
                            'capatazia' => isset($produto['capatazia']) ? $this->parseMoneyToFloat($produto['capatazia']) : null,
                            'afrmm' => isset($produto['afrmm']) ? $this->parseMoneyToFloat($produto['afrmm']) : null,
                            'armazenagem_sts' => isset($produto['armazenagem_sts']) ? $this->parseMoneyToFloat($produto['armazenagem_sts']) : null,
                            'frete_dta_sts_ana' => isset($produto['frete_dta_sts_ana']) ? $this->parseMoneyToFloat($produto['frete_dta_sts_ana']) : null,
                            'sda' => isset($produto['sda']) ? $this->parseMoneyToFloat($produto['sda']) : null,
                            'rep_sts' => isset($produto['rep_sts']) ? $this->parseMoneyToFloat($produto['rep_sts']) : null,
                            'armaz_ana' => isset($produto['armaz_ana']) ? $this->parseMoneyToFloat($produto['armaz_ana']) : null,
                            'lavagem_container' => isset($produto['lavagem_container']) ? $this->parseMoneyToFloat($produto['lavagem_container']) : null,
                            'rep_anapolis' => isset($produto['rep_anapolis']) ? $this->parseMoneyToFloat($produto['rep_anapolis']) : null,
                            'li_dta_honor_nix' => isset($produto['li_dta_honor_nix']) ? $this->parseMoneyToFloat($produto['li_dta_honor_nix']) : null,
                            'honorarios_nix' => isset($produto['honorarios_nix']) ? $this->parseMoneyToFloat($produto['honorarios_nix']) : null,
                            'desp_desenbaraco' => isset($produto['desp_desenbaraco']) ? $this->parseMoneyToFloat($produto['desp_desenbaraco']) : null,
                            'diferenca_cambial_frete' => isset($produto['diferenca_cambial_frete']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_frete']) : null,
                            'diferenca_cambial_fob' => isset($produto['diferenca_cambial_fob']) ? $this->parseMoneyToFloat($produto['diferenca_cambial_fob']) : null,
                            'custo_unitario_final' => isset($produto['custo_unitario_final']) ? $this->parseMoneyToFloat($produto['custo_unitario_final']) : null,
                            'custo_total_final' => isset($produto['custo_total_final']) ? $this->parseMoneyToFloat($produto['custo_total_final']) : null,
                            "descricao" => $produto['descricao'],

                        ]
                    );
                }
            }
            Processo::where('id', $id)->update(['peso_liquido' => $pesoLiquidoTotal]);


            return back()->with('messages', ['success' => ['Processo atualizado com sucesso!']]);
        } catch (\Exception $e) {
            dd($e);
            return back()->with('messages', ['error' => ['Não foi possível cadastrar o tipo de documento!']])->withInput($request->all());
        }
    }

    public function destroy(int $id)
    {
        try {

            return back()->with('messages', ['success' => ['Tipo de documento desativado com sucesso!']]);
        } catch (\Exception $e) {
            return back()->with('messages', ['error' => ['Não foi possível excluír o tipo de documento!']]);
        }
    }
    public function destroyProduto(int $id)
    {
        try {
            ProcessoProduto::find($id)->delete();
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
}
