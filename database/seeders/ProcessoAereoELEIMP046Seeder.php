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
        // Tentar diferentes caminhos possíveis (local e container Docker)
        // O base_path() já aponta para /var/www/html dentro do container
        $possiblePaths = [
            base_path('Planilha para emissão da NF - ELEIMP-046-25.csv'),
            storage_path('app/Planilha para emissão da NF - ELEIMP-046-25.csv'),
            __DIR__ . '/../../Planilha para emissão da NF - ELEIMP-046-25.csv',
            '/var/www/html/Planilha para emissão da NF - ELEIMP-046-25.csv',
        ];
        
        $csvFile = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $csvFile = $path;
                break;
            }
        }
        
        if (!$csvFile) {
            $this->command->error("Arquivo CSV não encontrado. Caminhos tentados:");
            foreach ($possiblePaths as $path) {
                $exists = file_exists($path) ? '✓' : '✗';
                $this->command->error("  {$exists} {$path}");
            }
            $this->command->warn("Por favor, copie o arquivo CSV para um dos caminhos acima ou ajuste o caminho na seeder.");
            return;
        }
        
        $this->command->info("✓ Arquivo CSV encontrado: {$csvFile}");
        
        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            $this->command->error("Erro ao abrir arquivo CSV");
            return;
        }
        
        $linhas = [];
        while (($data = fgetcsv($handle, 10000, ',')) !== FALSE) {
            $linhas[] = $data;
        }
        fclose($handle);
        
        // Função auxiliar para limpar valores numéricos
        $limparValor = function($valor) {
            if (empty($valor) || $valor === '-  ' || $valor === '-') {
                return 0;
            }
            return floatval(str_replace([' USD ', ' R$ ', ' ', ',', '"'], '', trim($valor)));
        };
        
        // Função auxiliar para limpar percentuais
        $limparPercentual = function($valor) {
            if (empty($valor) || $valor === '-  ' || $valor === '-') {
                return null;
            }
            return floatval(str_replace(['%', ' '], '', trim($valor)));
        };
        
        // Extrair dados do processo (linhas 3-14, índice 2-13)
        $di = trim($linhas[2][1] ?? '');
        $processo = trim($linhas[3][1] ?? '');
        $descricao = trim($linhas[3][5] ?? '');
        $valorExwUsd = $limparValor($linhas[4][1] ?? '0');
        $valorExwBrl = $limparValor($linhas[4][2] ?? '0');
        $freteUsd = $limparValor($linhas[5][1] ?? '0');
        $freteBrl = $limparValor($linhas[5][2] ?? '0');
        $seguroUsd = $limparValor($linhas[6][1] ?? '0');
        $seguroBrl = $limparValor($linhas[6][2] ?? '0');
        $acrescimoUsd = $limparValor($linhas[7][1] ?? '0');
        $acrescimoBrl = $limparValor($linhas[7][2] ?? '0');
        $valorCifUsd = $limparValor($linhas[8][1] ?? '0');
        $valorCifBrl = $limparValor($linhas[8][2] ?? '0');
        $taxaDolar = floatval($linhas[9][1] ?? 0);
        $pesoBruto = floatval($linhas[11][1] ?? 0);
        $pesoLiquido = floatval($linhas[12][1] ?? 0);
        $quantidade = intval($linhas[10][3] ?? 0);
        $especie = trim($linhas[11][5] ?? '');
        
        // Extrair totais da linha 54 (índice 53)
        $totais = $linhas[53] ?? [];
        $outrasTxAgente = $limparValor($totais[56] ?? '0');
        $dai = $limparValor($totais[58] ?? '0');
        $dape = $limparValor($totais[59] ?? '0');
        $correios = $limparValor($totais[60] ?? '0');
        $liDtaHonorNix = $limparValor($totais[61] ?? '0');
        $honorariosNix = $limparValor($totais[62] ?? '0');
        $despDesembaraco = $limparValor($totais[64] ?? '0');
        
        // Extrair data da linha 1
        $dataStr = trim($linhas[0][1] ?? '7/23/2025');
        $dataParts = explode('/', $dataStr);
        $dataProcesso = $dataParts[2] . '-' . str_pad($dataParts[0], 2, '0', STR_PAD_LEFT) . '-' . str_pad($dataParts[1], 2, '0', STR_PAD_LEFT);
        
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
                'dai_brl' => $dai * $taxaDolar,
                'dape' => $dape,
                'dape_brl' => $dape * $taxaDolar,
                'correios' => $correios,
                'li_dta_honor_nix' => $liDtaHonorNix,
                'honorarios_nix' => $honorariosNix,
            ]);
            
            $this->command->info("Processo aéreo criado: {$processoAereo->codigo_interno} (ID: {$processoAereo->id})");
            
            // Processar produtos (linhas 17-53, índice 16-52)
            $produtosCriados = 0;
            for ($i = 16; $i < 53; $i++) {
                if (!isset($linhas[$i]) || empty(trim($linhas[$i][0] ?? ''))) {
                    continue;
                }
                
                $row = $linhas[$i];
                
                // Extrair dados do produto
                $produtoDesc = trim($row[0] ?? '');
                $codigo = trim($row[1] ?? '');
                $adicao = intval($row[2] ?? 0);
                $origem = trim($row[3] ?? '');
                $item = intval($row[4] ?? 0);
                $ncm = trim(str_replace(' ', '', $row[5] ?? ''));
                $quantidade = floatval($row[6] ?? 0);
                $pesoLiqLbs = floatval($row[7] ?? 0);
                $pesoLiqUnit = floatval($row[8] ?? 0);
                $pesoLiqTotalKg = floatval($row[9] ?? 0);
                $fatorPeso = floatval($row[10] ?? 0);
                $fobUnitUsd = floatval($row[11] ?? 0);
                $fobTotalUsd = floatval($row[12] ?? 0);
                $fobTotalBrl = $limparValor($row[13] ?? '0');
                $freteIntUsd = floatval($row[14] ?? 0);
                $freteIntBrl = $limparValor($row[15] ?? '0');
                $vlrCfrUnit = floatval($row[16] ?? 0);
                $vlrCfrTotal = floatval($row[17] ?? 0);
                $seguroIntUsd = $limparValor($row[18] ?? '0');
                $seguroIntBrl = $limparValor($row[19] ?? '0');
                $vlrAduaneiroUsd = floatval($row[20] ?? 0);
                $vlrAduaneiroBrl = $limparValor($row[21] ?? '0');
                $iiPercent = $limparPercentual($row[22] ?? '');
                $ipiPercent = $limparPercentual($row[23] ?? '');
                $pisPercent = $limparPercentual($row[24] ?? '');
                $cofinsPercent = $limparPercentual($row[25] ?? '');
                $icmsPercent = $limparPercentual($row[26] ?? '');
                $icmsReduzidoPercent = $limparPercentual($row[27] ?? '');
                $vlrIi = $limparValor($row[29] ?? '0');
                $bcIpi = $limparValor($row[30] ?? '0');
                $vlrIpi = $limparValor($row[31] ?? '0');
                $bcPisCofins = $limparValor($row[32] ?? '0');
                $vlrPis = $limparValor($row[33] ?? '0');
                $vlrCofins = $limparValor($row[34] ?? '0');
                $despAduaneira = $limparValor($row[35] ?? '0');
                $bcIcmsSemReducao = $limparValor($row[36] ?? '0');
                $vlrIcmsSemReducao = $limparValor($row[37] ?? '0');
                $bcIcmsReduzido = $limparValor($row[38] ?? '0');
                $vlrIcmsReduzido = $limparValor($row[39] ?? '0');
                $vlrUnitNf = $limparValor($row[40] ?? '0');
                $vlrTotalNf = $limparValor($row[41] ?? '0');
                $vlrTotalNfSemIcms = $limparValor($row[42] ?? '0');
                $bcIcmsSt = $limparValor($row[43] ?? '0');
                $mva = $limparPercentual($row[44] ?? '');
                $icmsSt = $limparPercentual($row[45] ?? '');
                $vlrIcmsSt = $limparValor($row[46] ?? '0');
                $vlrTotalNfComIcmsSt = $limparValor($row[47] ?? '0');
                $fatorVlrFob = floatval($row[49] ?? 0);
                $fatorTxSiscomex = floatval($row[50] ?? 0);
                $taxaSiscomex = $limparValor($row[53] ?? '0');
                $outrasTxAgenteProd = $limparValor($row[54] ?? '0');
                $deliveryFeeProd = $limparValor($row[55] ?? '0');
                $collectFeeProd = $limparValor($row[56] ?? '0');
                $desconsolidacao = $limparValor($row[57] ?? '0');
                $handling = $limparValor($row[58] ?? '0');
                $daiProd = $limparValor($row[59] ?? '0');
                $dapeProd = $limparValor($row[60] ?? '0');
                $correiosProd = $limparValor($row[61] ?? '0');
                $liDtaHonorNixProd = $limparValor($row[62] ?? '0');
                $honorariosNixProd = $limparValor($row[63] ?? '0');
                $difCambialFrete = $limparValor($row[65] ?? '0');
                $custoUnitFinal = $limparValor($row[66] ?? '0');
                $custoTotalFinal = $limparValor($row[67] ?? '0');
                
                if (empty($codigo) || empty($produtoDesc)) {
                    continue;
                }
                
                // Buscar ou criar produto
                $produto = Produto::where('codigo', $codigo)
                    ->where('catalogo_id', $catalogo->id)
                    ->first();
                
                if (!$produto) {
                    $descricaoProd = mb_substr($produtoDesc, 0, 500, 'UTF-8');
                    $modelo = mb_substr($produtoDesc, 0, 100, 'UTF-8');
                    
                    $produto = Produto::create([
                        'catalogo_id' => $catalogo->id,
                        'codigo' => $codigo,
                        'ncm' => $ncm,
                        'descricao' => $descricaoProd,
                        'modelo' => $modelo,
                    ]);
                }
                
                // Criar processo_aereo_produto
                ProcessoAereoProduto::create([
                    'processo_aereo_id' => $processoAereo->id,
                    'produto_id' => $produto->id,
                    'adicao' => $adicao,
                    'item' => $item,
                    'origem' => $origem,
                    'descricao' => $produtoDesc,
                    'quantidade' => $quantidade,
                    'peso_liq_lbs' => $pesoLiqLbs,
                    'peso_liquido_unitario' => $pesoLiqUnit,
                    'peso_liquido_total' => $pesoLiqTotalKg,
                    'peso_liq_total_kg' => $pesoLiqTotalKg,
                    'fator_peso' => $fatorPeso,
                    'fob_unit_usd' => $fobUnitUsd,
                    'fob_total_usd' => $fobTotalUsd,
                    'fob_total_brl' => $fobTotalBrl,
                    'frete_usd' => $freteIntUsd,
                    'frete_brl' => $freteIntBrl,
                    'seguro_usd' => $seguroIntUsd,
                    'seguro_brl' => $seguroIntBrl,
                    'vlr_cfr_unit' => $vlrCfrUnit,
                    'vlr_cfr_total' => $vlrCfrTotal,
                    'vlr_crf_total' => $vlrCfrTotal,
                    'vlr_crf_unit' => $vlrCfrUnit,
                    'valor_aduaneiro_usd' => $vlrAduaneiroUsd,
                    'valor_aduaneiro_brl' => $vlrAduaneiroBrl,
                    'ii_percent' => $iiPercent,
                    'ipi_percent' => $ipiPercent,
                    'pis_percent' => $pisPercent,
                    'cofins_percent' => $cofinsPercent,
                    'icms_percent' => $icmsPercent,
                    'icms_reduzido_percent' => $icmsReduzidoPercent,
                    'valor_ii' => $vlrIi,
                    'base_ipi' => $bcIpi,
                    'valor_ipi' => $vlrIpi,
                    'base_pis_cofins' => $bcPisCofins,
                    'valor_pis' => $vlrPis,
                    'valor_cofins' => $vlrCofins,
                    'despesa_aduaneira' => $despAduaneira,
                    'base_icms_sem_reducao' => $bcIcmsSemReducao,
                    'valor_icms_sem_reducao' => $vlrIcmsSemReducao,
                    'base_icms_reduzido' => $bcIcmsReduzido,
                    'valor_icms_reduzido' => $vlrIcmsReduzido,
                    'valor_unit_nf' => $vlrUnitNf,
                    'valor_total_nf' => $vlrTotalNf,
                    'base_icms_st' => $bcIcmsSt,
                    'mva' => $mva,
                    'icms_st' => $icmsSt,
                    'valor_icms_st' => $vlrIcmsSt,
                    'valor_total_nf_com_icms_st' => $vlrTotalNfComIcmsSt,
                    'fator_valor_fob' => $fatorVlrFob,
                    'fator_tx_siscomex' => $fatorTxSiscomex,
                    'taxa_siscomex' => $taxaSiscomex,
                    'outras_taxas_agente' => $outrasTxAgenteProd,
                    'desconsolidacao' => $desconsolidacao,
                    'handling' => $handling,
                    'dai' => $daiProd,
                    'dai_brl' => $daiProd * $taxaDolar,
                    'dape' => $dapeProd,
                    'dape_brl' => $dapeProd * $taxaDolar,
                    'correios' => $correiosProd,
                    'li_dta_honor_nix' => $liDtaHonorNixProd,
                    'honorarios_nix' => $honorariosNixProd,
                    'diferenca_cambial_frete' => $difCambialFrete,
                    'custo_unitario_final' => $custoUnitFinal,
                    'custo_total_final' => $custoTotalFinal,
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
