<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\ProcessoAereo;
use App\Models\ProcessoAereoProduto;
use App\Models\Produto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessoAereoGIIIMP110Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dados do processo extraídos do CSV
        $di = '25/0664139-3';
        $processo = 'GIIIMP-110/25';
        $descricao = 'DESCRIÇÃO DA MERCADORIA CONFORME DI 25/0664139-3 DESEMBARAÇADA EM 24/03/2025';
        $valorExwUsd = 9662.84;
        $valorExwBrl = 55311.06;
        $freteUsd = 2110.60;
        $freteBrl = 12081.29;
        $seguroUsd = 0;
        $seguroBrl = 0;
        $acrescimoUsd = 0;
        $acrescimoBrl = 0;
        $valorCifUsd = 11773.44;
        $valorCifBrl = 67392.35;
        $taxaDolar = 5.7241;
        $thcCapatazia = 0; // Vazio no CSV
        $pesoBruto = 70.00;
        $pesoLiquido = 66.28;
        $quantidade = 651; // Total de produtos
        $especie = 'CAIXA DE PAPELÃO';
        $dataDesembaraco = '2025-03-24';
        $dataProcesso = '2025-04-09';
        
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
                'data_desembaraco_fim' => $dataDesembaraco,
                'valor_exw' => $valorExwUsd,
                'valor_exw_brl' => $valorExwBrl,
                'delivery_fee' => 0,
                'delivery_fee_brl' => 0,
                'collect_fee' => 0,
                'collect_fee_brl' => 0,
                'peso_bruto' => $pesoBruto,
                'peso_liquido' => $pesoLiquido,
                'multa' => 0,
                'quantidade' => 4, // Quantidade de adições
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
                'thc_capatazia' => $thcCapatazia,
                'service_charges' => null,
                'service_charges_moeda' => null,
                'service_charges_usd' => null,
                'service_charges_brl' => null,
                'cotacao_service_charges' => null,
            ]);
            
            $this->command->info("Processo aéreo criado: {$processoAereo->codigo_interno} (ID: {$processoAereo->id})");
            
            // Produtos hard coded do CSV (linhas 23-63)
            $produtosData = $this->processAllProductsFromCSV();
            
            // Processar produtos
            $produtosCriados = 0;
            foreach ($produtosData as $produtoData) {
                if (empty($produtoData['codigo']) || empty($produtoData['descricao'])) {
                    continue;
                }
                
                // Buscar ou criar produto
                $produto = Produto::where('codigo', $produtoData['codigo'])
                    ->where('catalogo_id', $catalogo->id)
                    ->first();
                
                if (!$produto) {
                    $descricaoProd = mb_substr($produtoData['descricao'], 0, 500, 'UTF-8');
                    $modelo = mb_substr($produtoData['descricao'], 0, 100, 'UTF-8');
                    
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
                    'origem' => $produtoData['origem'] ?? null,
                    'descricao' => $produtoData['descricao'],
                    'codigo_giiro' => $produtoData['codigo_giiro'] ?? null,
                    'quantidade' => $produtoData['quantidade'],
                    'peso_liq_lbs' => $produtoData['peso_liq_lbs'] ?? null,
                    'peso_liquido_unitario' => $produtoData['peso_liquido_unitario'],
                    'peso_liquido_total' => $produtoData['peso_liquido_total'],
                    'peso_liq_total_kg' => $produtoData['peso_liquido_total'],
                    'fator_peso' => $produtoData['fator_peso'],
                    'fob_unit_usd' => $produtoData['fob_unit_usd'],
                    'fob_total_usd' => $produtoData['fob_total_usd'],
                    'fob_total_brl' => $produtoData['fob_total_brl'],
                    'frete_usd' => $produtoData['frete_usd'],
                    'frete_brl' => $produtoData['frete_brl'],
                    'seguro_usd' => $produtoData['seguro_usd'] ?? 0,
                    'seguro_brl' => $produtoData['seguro_brl'] ?? 0,
                    'acresc_frete_usd' => $produtoData['acresc_frete_usd'] ?? 0,
                    'acresc_frete_brl' => $produtoData['acresc_frete_brl'] ?? 0,
                    'thc_usd' => $produtoData['thc_usd'] ?? 0,
                    'thc_brl' => $produtoData['thc_brl'] ?? 0,
                    'vlr_cfr_unit' => $produtoData['vlr_cfr_unit'],
                    'vlr_cfr_total' => $produtoData['vlr_cfr_total'],
                    'vlr_crf_total' => $produtoData['vlr_cfr_total'],
                    'vlr_crf_unit' => $produtoData['vlr_cfr_unit'],
                    'valor_aduaneiro_usd' => $produtoData['valor_aduaneiro_usd'],
                    'valor_aduaneiro_brl' => $produtoData['valor_aduaneiro_brl'],
                    'ii_percent' => $produtoData['ii_percent'],
                    'ipi_percent' => $produtoData['ipi_percent'],
                    'pis_percent' => $produtoData['pis_percent'],
                    'cofins_percent' => $produtoData['cofins_percent'],
                    'icms_percent' => $produtoData['icms_percent'],
                    'icms_reduzido_percent' => $produtoData['icms_reduzido_percent'] ?? null,
                    'valor_ii' => $produtoData['valor_ii'],
                    'base_ipi' => $produtoData['base_ipi'],
                    'valor_ipi' => $produtoData['valor_ipi'],
                    'base_pis_cofins' => $produtoData['base_pis_cofins'],
                    'valor_pis' => $produtoData['valor_pis'],
                    'valor_cofins' => $produtoData['valor_cofins'],
                    'despesa_aduaneira' => $produtoData['despesa_aduaneira'],
                    'base_icms_sem_reducao' => $produtoData['base_icms_sem_reducao'],
                    'valor_icms_sem_reducao' => $produtoData['valor_icms_sem_reducao'],
                    'base_icms_reduzido' => $produtoData['base_icms_reduzido'],
                    'valor_icms_reduzido' => $produtoData['valor_icms_reduzido'],
                    'valor_unit_nf' => $produtoData['valor_unit_nf'] ?? null,
                    'valor_total_nf' => $produtoData['valor_total_nf'],
                    'base_icms_st' => $produtoData['base_icms_st'] ?? null,
                    'mva' => $produtoData['mva'] ?? null,
                    'icms_st' => $produtoData['icms_st'] ?? null,
                    'valor_icms_st' => $produtoData['valor_icms_st'] ?? null,
                    'valor_total_nf_com_icms_st' => $produtoData['valor_total_nf_com_icms_st'] ?? null,
                    'fator_valor_fob' => $produtoData['fator_valor_fob'],
                    'fator_tx_siscomex' => $produtoData['fator_tx_siscomex'],
                    'multa' => $produtoData['multa'] ?? 0,
                    'tx_def_li' => $produtoData['tx_def_li'] ?? null,
                    'taxa_siscomex' => $produtoData['taxa_siscomex'] ?? null,
                    'outras_taxas_agente' => $produtoData['outras_taxas_agente'] ?? null,
                    'delivery_fee' => $produtoData['delivery_fee'] ?? null,
                    'delivery_fee_brl' => $produtoData['delivery_fee_brl'] ?? null,
                    'desconsolidacao' => $produtoData['desconsolidacao'] ?? null,
                    'collect_fee' => $produtoData['collect_fee'] ?? null,
                    'collect_fee_brl' => $produtoData['collect_fee_brl'] ?? null,
                    'handling' => $produtoData['handling'] ?? null,
                    'dai' => $produtoData['dai'] ?? null,
                    'dape' => $produtoData['dape'] ?? null,
                    'li_dta_honor_nix' => $produtoData['li_dta_honor_nix'] ?? null,
                    'honorarios_nix' => $produtoData['honorarios_nix'] ?? null,
                    'desp_desenbaraco' => $produtoData['desp_desenbaraco'] ?? null,
                    'diferenca_cambial_frete' => $produtoData['diferenca_cambial_frete'] ?? null,
                    'diferenca_cambial_fob' => $produtoData['diferenca_cambial_fob'] ?? null,
                    'custo_unitario_final' => $produtoData['custo_unitario_final'] ?? null,
                    'custo_total_final' => $produtoData['custo_total_final'] ?? null,
                ]);
                
                $produtosCriados++;
            }
            
            DB::commit();
            $this->command->info("Seeder concluído! {$produtosCriados} produtos criados.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Erro ao executar seeder: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Processa todos os produtos do CSV
     */
    private function processAllProductsFromCSV()
    {
        // Array completo hard-coded dos 41 produtos extraídos do CSV
        return require __DIR__ . '/produtos_giiimp110_array.php';
    }
}
