<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\ProcessoAereo;
use App\Models\ProcessoAereoProduto;
use App\Models\Produto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessoAereoELEIMP046Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dados do processo (hard coded)
        $di = '25/1624342-0';
        $processo = 'ELEIMP-046/25';
        $descricao = 'REAL';
        $valorExwUsd = 11583.60;
        $valorExwBrl = 64531.08;
        $freteUsd = 1162.00;
        $freteBrl = 6473.39;
        $seguroUsd = 0;
        $seguroBrl = 0;
        $acrescimoUsd = 0;
        $acrescimoBrl = 0;
        $valorCifUsd = 12745.60;
        $valorCifBrl = 71004.46;
        $taxaDolar = 5.5709;
        $pesoBruto = 34.0000;
        $pesoLiquido = 30.2064;
        $quantidade = 5;
        $especie = 'CAIXAS DE PAPELÃO';
        $outrasTxAgente = 431.88;
        $dai = 551.18;
        $dape = 282.00;
        $correios = 0;
        $liDtaHonorNix = 959.00;
        $honorariosNix = 759.00;
        $despDesembaraco = 1241.00;
        $dataProcesso = '2025-07-23';
        
        try {
            DB::beginTransaction();
            
            // Buscar ou criar cliente
            $cliente = Cliente::where('nome', 'TESTE')->first();
            if (!$cliente) {
                $cliente = Cliente::create([
                    'nome' => 'TESTE',
                    'cnpj' => '00.000.000/0001-00',
                ]);
                $this->command->info("Cliente criado: {$cliente->nome} (ID: {$cliente->id})");
            }
            
            // Buscar ou criar catálogo
            $catalogo = Catalogo::where('cliente_id', $cliente->id)->first();
            if (!$catalogo) {
                $catalogo = Catalogo::create([
                    'cliente_id' => $cliente->id,
                    'cpf_cnpj' => $cliente->cnpj ?? null,
                ]);
                $this->command->info("Catálogo criado para cliente: {$cliente->nome} (ID: {$catalogo->id})");
            }
            
            // Criar processo aéreo
            $processoAereo = ProcessoAereo::create([
                'cliente_id' => $cliente->id,
                'codigo_interno' => $processo,
                'di' => $di,
                'descricao' => $descricao,
                'data_desembaraco_fim' => '2025-07-24',
                'valor_exw' => $valorExwUsd,
                'valor_exw_brl' => $valorExwBrl,
                'delivery_fee' => 0,
                'delivery_fee_brl' => 0,
                'collect_fee' => 0,
                'collect_fee_brl' => 0,
                'peso_bruto' => $pesoBruto,
                'peso_liquido' => $pesoLiquido,
                'multa' => 0,
                'quantidade' => $quantidade,
                'especie' => $especie,
                'frete_internacional' => $freteUsd,
                'frete_internacional_moeda' => 'USD',
                'cotacao_frete_internacional' => $taxaDolar,
                'seguro_internacional' => $seguroUsd,
                'seguro_internacional_moeda' => 'USD',
                'cotacao_seguro_internacional' => $taxaDolar,
                'acrescimo_frete' => $acrescimoUsd,
                'acrescimo_frete_moeda' => 'USD',
                'cotacao_acrescimo_frete' => $taxaDolar,
                'valor_cif' => $valorCifUsd,
                'taxa_dolar' => $taxaDolar,
                'moeda_processo' => 'USD',
                'data_moeda_frete_internacional' => $dataProcesso,
                'data_moeda_seguro_internacional' => $dataProcesso,
                'data_moeda_acrescimo_frete' => $dataProcesso,
                'data_cotacao_processo' => $dataProcesso,
                'cotacao_moeda_processo' => json_encode(['USD' => ['venda' => $taxaDolar]]),
                'status' => 'andamento',
                'canal' => 'verde',
                'nacionalizacao' => 'outros',
                'outras_taxas_agente' => $outrasTxAgente,
                'dai' => $dai,
                'dape' => $dape,
                'correios' => $correios,
                'li_dta_honor_nix' => $liDtaHonorNix,
                'honorarios_nix' => $honorariosNix,
            ]);
            
            $this->command->info("Processo aéreo criado: {$processoAereo->codigo_interno} (ID: {$processoAereo->id})");
            
            // Produtos hard coded
            $produtos = require __DIR__ . '/produtos_array.php';
            
            // Processar produtos
            $produtosCriados = 0;
            foreach ($produtos as $produtoData) {
                if (empty($produtoData['codigo']) || empty($produtoData['produtoDesc'])) {
                    continue;
                }
                
                // Buscar ou criar produto
                $produto = Produto::where('codigo', $produtoData['codigo'])
                    ->where('catalogo_id', $catalogo->id)
                    ->first();
                
                if (!$produto) {
                    $descricaoProd = mb_substr($produtoData['produtoDesc'], 0, 500, 'UTF-8');
                    $modelo = mb_substr($produtoData['produtoDesc'], 0, 100, 'UTF-8');
                    
                    $produto = Produto::create([
                        'catalogo_id' => $catalogo->id,
                        'codigo' => $produtoData['codigo'],
                        'ncm' => $produtoData['ncm'],
                        'descricao' => $descricaoProd,
                        'modelo' => $modelo,
                    ]);
                }
                
                // Criar processo_aereo_produto
                ProcessoAereoProduto::create([
                    'processo_aereo_id' => $processoAereo->id,
                    'produto_id' => $produto->id,
                    'adicao' => $produtoData['adicao'],
                    'item' => $produtoData['item'],
                    'origem' => $produtoData['origem'],
                    'descricao' => $produtoData['produtoDesc'],
                    'quantidade' => $produtoData['quantidade'],
                    'peso_liq_lbs' => $produtoData['pesoLiqLbs'],
                    'peso_liquido_unitario' => $produtoData['pesoLiqUnit'],
                    'peso_liquido_total' => $produtoData['pesoLiqTotalKg'],
                    'peso_liq_total_kg' => $produtoData['pesoLiqTotalKg'],
                    'fator_peso' => $produtoData['fatorPeso'],
                    'fob_unit_usd' => $produtoData['fobUnitUsd'],
                    'fob_total_usd' => $produtoData['fobTotalUsd'],
                    'fob_total_brl' => $produtoData['fobTotalBrl'],
                    'frete_usd' => $produtoData['freteIntUsd'],
                    'frete_brl' => $produtoData['freteIntBrl'],
                    'seguro_usd' => $produtoData['seguroIntUsd'],
                    'seguro_brl' => $produtoData['seguroIntBrl'],
                    'vlr_cfr_unit' => $produtoData['vlrCfrUnit'],
                    'vlr_cfr_total' => $produtoData['vlrCfrTotal'],
                    'vlr_crf_total' => $produtoData['vlrCfrTotal'],
                    'vlr_crf_unit' => $produtoData['vlrCfrUnit'],
                    'valor_aduaneiro_usd' => $produtoData['vlrAduaneiroUsd'],
                    'valor_aduaneiro_brl' => $produtoData['vlrAduaneiroBrl'],
                    'ii_percent' => $produtoData['iiPercent'],
                    'ipi_percent' => $produtoData['ipiPercent'],
                    'pis_percent' => $produtoData['pisPercent'],
                    'cofins_percent' => $produtoData['cofinsPercent'],
                    'icms_percent' => $produtoData['icmsPercent'],
                    'icms_reduzido_percent' => $produtoData['icmsReduzidoPercent'],
                    'valor_ii' => $produtoData['vlrIi'],
                    'base_ipi' => $produtoData['bcIpi'],
                    'valor_ipi' => $produtoData['vlrIpi'],
                    'base_pis_cofins' => $produtoData['bcPisCofins'],
                    'valor_pis' => $produtoData['vlrPis'],
                    'valor_cofins' => $produtoData['vlrCofins'],
                    'despesa_aduaneira' => $produtoData['despAduaneira'],
                    'base_icms_sem_reducao' => $produtoData['bcIcmsSemReducao'],
                    'valor_icms_sem_reducao' => $produtoData['vlrIcmsSemReducao'],
                    'base_icms_reduzido' => $produtoData['bcIcmsReduzido'],
                    'valor_icms_reduzido' => $produtoData['vlrIcmsReduzido'],
                    'valor_unit_nf' => $produtoData['vlrUnitNf'],
                    'valor_total_nf' => $produtoData['vlrTotalNf'],
                    'base_icms_st' => $produtoData['bcIcmsSt'],
                    'mva' => $produtoData['mva'],
                    'icms_st' => $produtoData['icmsSt'],
                    'valor_icms_st' => $produtoData['vlrIcmsSt'],
                    'valor_total_nf_com_icms_st' => $produtoData['vlrTotalNfComIcmsSt'],
                    'fator_valor_fob' => $produtoData['fatorVlrFob'],
                    'fator_tx_siscomex' => $produtoData['fatorTxSiscomex'],
                    'taxa_siscomex' => $produtoData['taxaSiscomex'],
                    'outras_taxas_agente' => $produtoData['outrasTxAgenteProd'],
                    'desconsolidacao' => $produtoData['desconsolidacao'],
                    'handling' => $produtoData['handling'],
                    'dai' => $produtoData['daiProd'],
                    'dape' => $produtoData['dapeProd'],
                    'correios' => $produtoData['correiosProd'],
                    'li_dta_honor_nix' => $produtoData['liDtaHonorNixProd'],
                    'honorarios_nix' => $produtoData['honorariosNixProd'],
                    'diferenca_cambial_frete' => $produtoData['difCambialFrete'],
                    'custo_unitario_final' => $produtoData['custoUnitFinal'],
                    'custo_total_final' => $produtoData['custoTotalFinal'],
                ]);
                
                $produtosCriados++;
            }
            
            DB::commit();
            
            $this->command->info("Seeder executado com sucesso!");
            $this->command->info("Processo criado: {$processoAereo->codigo_interno}");
            $this->command->info("Produtos criados: {$produtosCriados}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Erro ao executar seeder: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
