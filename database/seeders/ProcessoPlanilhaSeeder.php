<?php

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\Processo;
use App\Models\ProcessoProduto;
use App\Models\Produto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcessoPlanilhaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Carregando dados da planilha
        $dataPath = __DIR__ . '/data/processo_planilha_data.php';
        
        if (!file_exists($dataPath)) {
            $this->command->error("Arquivo de dados não encontrado: {$dataPath}");
            return;
        }
        
        $data = require $dataPath;
        
        if (!$data || !isset($data['processo']) || !isset($data['produtos'])) {
            $this->command->error("Erro ao carregar dados ou estrutura inválida.");
            return;
        }
        
        $processoData = $data['processo'];
        $produtosData = $data['produtos'];
        
        // Buscando apenas o cliente com nome "TESTE"
        $cliente = Cliente::where('nome', 'TESTE')->first();
        
        if (!$cliente) {
            $this->command->error('Cliente "TESTE" não encontrado no banco de dados!');
            return;
        }
        
        $this->command->info("Criando processo para o cliente: {$cliente->nome} (ID: {$cliente->id})...");
        
        // Preparando cotações de moedas
        $cotacaoMoedaProcesso = [
            'USD' => [
                'nome' => 'Dólar Americano',
                'venda' => $processoData['cotacao_frete_internacional']
            ]
        ];
        
        try {
            DB::beginTransaction();
            
            // Criando processo
            $processo = Processo::create([
                'cliente_id' => $cliente->id,
                'codigo_interno' => $processoData['codigo_interno'] . '-' . $cliente->id,
                'di' => $processoData['di'],
                'descricao' => $processoData['descricao'] ?: 'DESCRIÇÃO DA MERCADORIA CONFORME DI ' . $processoData['di'] . ' - DESEMBARAÇADA EM 07/07/2025',
                'data_desembaraco_fim' => $processoData['data_desembaraco_fim'],
                'thc_capatazia' => $processoData['thc_capatazia'],
                'peso_bruto' => $processoData['peso_bruto'],
                'peso_liquido' => $processoData['peso_liquido'],
                'multa' => $processoData['multa'],
                'quantidade' => $processoData['quantidade'],
                'especie' => $processoData['especie'] ?: 'OUTROS',
                'frete_internacional' => $processoData['frete_internacional'],
                'frete_internacional_moeda' => $processoData['frete_internacional_moeda'],
                'cotacao_frete_internacional' => $processoData['cotacao_frete_internacional'],
                'seguro_internacional' => $processoData['seguro_internacional'],
                'seguro_internacional_moeda' => $processoData['seguro_internacional_moeda'],
                'cotacao_seguro_internacional' => $processoData['cotacao_seguro_internacional'],
                'acrescimo_frete' => $processoData['acrescimo_frete'],
                'acrescimo_frete_moeda' => $processoData['acrescimo_frete_moeda'],
                'cotacao_acrescimo_frete' => $processoData['cotacao_acrescimo_frete'],
                'moeda_processo' => $processoData['moeda_processo'],
                'data_moeda_frete_internacional' => $processoData['data_moeda_frete_internacional'],
                'cotacao_moeda_processo' => $cotacaoMoedaProcesso,
                'status' => 'andamento',
                'canal' => 'verde',
                'tipo_processo' => 'maritimo', // Processos da seeder são sempre marítimos
            ]);
            
            // Adicionando campos do cabeçalho (campos únicos do processo)
            // O campo capatazia deve ser igual ao thc_capatazia
            $updateData = [
                'capatazia' => $processoData['thc_capatazia'], // Capatazia é igual ao thc_capatazia
            ];
            
            $camposCabecalho = [
                'outras_taxas_agente',
                'liberacao_bl',
                'desconsolidacao',
                'isps_code',
                'handling',
                'afrmm',
                'armazenagem_sts',
                'frete_dta_sts_ana',
                'sda',
                'rep_sts',
                'armaz_ana',
                'lavagem_container',
                'rep_anapolis',
                'li_dta_honor_nix',
                'honorarios_nix',
                'diferenca_cambial_frete',
                'diferenca_cambial_fob',
            ];
            
            foreach ($camposCabecalho as $campo) {
                if (isset($processoData[$campo])) {
                    $updateData[$campo] = $processoData[$campo] ?? 0;
                }
            }
            
            // Sempre atualiza, mesmo que alguns valores sejam 0
            $processo->update($updateData);
            
            // Buscando ou criando catálogo para o cliente
            $catalogo = Catalogo::where('cliente_id', $cliente->id)->first();
            
            if (!$catalogo) {
                // Criando catálogo para o cliente
                $catalogo = Catalogo::create([
                    'cliente_id' => $cliente->id,
                    'cpf_cnpj' => $cliente->cnpj ?? null,
                ]);
                $this->command->info("Catálogo criado para cliente: {$cliente->nome} (ID: {$catalogo->id})");
            }
            
            // Criando produtos do processo
            foreach ($produtosData as $produtoData) {
                // Buscando produto pelo NCM e catálogo do cliente
                $ncm = $produtoData['ncm'];
                $produto = Produto::where('ncm', $ncm)
                    ->where('catalogo_id', $catalogo->id)
                    ->first();
                
                if (!$produto) {
                    // Criando produto no catálogo do cliente
                    $codigoUnico = 'PROD-' . str_replace('.', '-', $ncm) . '-' . uniqid();
                    $produto = Produto::create([
                        'catalogo_id' => $catalogo->id,
                        'codigo' => $codigoUnico,
                        'ncm' => $ncm,
                        'descricao' => substr($produtoData['descricao'], 0, 500),
                        'fornecedor_id' => null, // Pode ser ajustado depois
                        'modelo' => substr($produtoData['descricao'], 0, 100), // Primeiros 100 caracteres como modelo
                    ]);
                    
                    $this->command->info("Produto criado: {$produto->codigo} (NCM: {$ncm}) no catálogo do cliente {$cliente->nome}");
                }
                
                // Tratando valores vazios - adicao e item são integers nullable
                // Função auxiliar para converter para int ou null
                $toIntOrNull = function($value) {
                    if ($value === null || $value === '') {
                        return null;
                    }
                    $trimmed = trim((string)$value);
                    return $trimmed === '' ? null : (int)$trimmed;
                };
                
                $adicao = $toIntOrNull($produtoData['adicao'] ?? null);
                $item = $toIntOrNull($produtoData['item'] ?? null);
                
                // Convertendo porcentagens de decimal (0.18) para percentual (18)
                // O banco espera valores como 18.00, não 0.18
                $ii_percent = isset($produtoData['ii_percent']) ? ($produtoData['ii_percent'] * 100) : 0;
                $ipi_percent = isset($produtoData['ipi_percent']) ? ($produtoData['ipi_percent'] * 100) : 0;
                $pis_percent = isset($produtoData['pis_percent']) ? ($produtoData['pis_percent'] * 100) : 0;
                $cofins_percent = isset($produtoData['cofins_percent']) ? ($produtoData['cofins_percent'] * 100) : 0;
                $icms_percent = isset($produtoData['icms_percent']) ? ($produtoData['icms_percent'] * 100) : 0;
                $icms_reduzido_percent = isset($produtoData['icms_reduzido_percent']) && $produtoData['icms_reduzido_percent'] > 0 
                    ? ($produtoData['icms_reduzido_percent'] * 100) 
                    : 0;
                
                // Criando processo_produto
                ProcessoProduto::create([
                    'processo_id' => $processo->id,
                    'produto_id' => $produto->id,
                    'adicao' => $adicao, // null se vazio
                    'item' => $item, // null se vazio
                    'descricao' => $produtoData['descricao'] ?? '',
                    'quantidade' => $produtoData['quantidade'] ?? 0,
                    'peso_liquido_unitario' => $produtoData['peso_liquido_unitario'] ?? 0,
                    'peso_liquido_total' => $produtoData['peso_liquido_total'] ?? 0,
                    'fob_unit_usd' => $produtoData['fob_unit_usd'] ?? 0,
                    'ii_percent' => $ii_percent,
                    'ipi_percent' => $ipi_percent,
                    'pis_percent' => $pis_percent,
                    'cofins_percent' => $cofins_percent,
                    'icms_percent' => $icms_percent,
                    'icms_reduzido_percent' => $icms_reduzido_percent,
                    'mva' => 0, // Campo editável - deixar 0 para o usuário preencher
                    'icms_st' => 0, // Campo editável - deixar 0 para o usuário preencher (porcentagem)
                    'multa' => 0, // Campo editável - valores da planilha parecem calculados, deixar 0
                    'tx_def_li' => 0, // Campo editável - valores da planilha parecem calculados, deixar 0
                ]);
            }
            
            DB::commit();
            
            $this->command->info("Processo criado para cliente: {$cliente->nome} (ID: {$processo->id})");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Erro ao criar processo para cliente {$cliente->nome}: " . $e->getMessage());
        }
        
        $this->command->info('Seeder concluída!');
    }
}

