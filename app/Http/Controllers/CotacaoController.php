<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CotacaoController extends Controller
{
    public function obterCotacao($dataCotacao = null)
    {
        try {
            
            $cacheKey = 'cotacoes_bids_' . now()->format('Y-m-d');

            Cache::forget($cacheKey); // limpa antes de salvar de novo

            $resultado = Cache::remember($cacheKey, now()->endOfDay(), function () use ($dataCotacao) {
                $moedasSuportadas = $this->buscarMoedasSuportadas();
                $data_cotacao = $this->obterDataUtil();
                if ($dataCotacao) {
                    $data_cotacao = Carbon::parse($dataCotacao)->format('m-d-Y');
                }
                $resultado = [];
                foreach ($moedasSuportadas as $codigo => $nome) {
                    $resultado[$codigo] = $this->buscarCotacao($codigo, $nome, $data_cotacao);
                    usleep(100000);
                }
                return $resultado;
            });

            return response()->json(['data' => $resultado]);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['error' => 'Erro ao obter cotações: ' . $e->getMessage()], 500);
        }
    }

    private function obterDataUtil(): string
    {
        // começa por "ontem" e recua até achar um dia não-final-de-semana
        $data = now()->subDay();

        while ($data->isWeekend()) {
            $data->subDay();
        }

        return $data->format('m-d-Y');
    }


    private function buscarMoedasSuportadas(): array
    {
        try {
            $resposta = Http::timeout(10)->get(
                'https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?$format=json'
            );
            $dados = $resposta->json()['value'] ?? [];

            return collect($dados)
                ->pluck('nomeFormatado', 'simbolo')
                ->toArray();
        } catch (\Exception $e) {
            Log::error("Erro ao buscar moedas suportadas: " . $e->getMessage());
            return [];
        }
    }

    private function buscarCotacao(string $codigo, string $nome, string $dataCotacao): array
    {
        try {
            $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/" .
                "CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?";

            $params = [
                '@moeda'       => "'$codigo'",
                '@dataCotacao' => "'$dataCotacao'",
                '$top'         => 1,
                '$format'      => 'json',
            ];

            // monta a query string normal
            $query = http_build_query($params);

            // adiciona o orderby manualmente, com espaço em vez de +
            $query .= "&\$orderby=dataHoraCotacao desc";

            $resposta = Http::timeout(10)->get($url . $query);


            $dados = $resposta->json()['value'][0] ?? null;

            if ($dados) {
                return [
                    'nome'   => $nome,
                    'data' => $dataCotacao,
                    'moeda'  => $codigo,
                    'compra' => $dados['cotacaoCompra'],
                    'venda'  => $dados['cotacaoVenda'],
                    'erro'   => null,
                ];
            }

            Log::warning("Nenhum dado retornado para moeda {$codigo} em {$dataCotacao}");
            return $this->cotacaoVazia($codigo, $nome, "Sem dados retornados");
        } catch (\Throwable $e) {
            Log::error("Erro ao buscar cotação da moeda {$codigo}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return $this->cotacaoVazia($codigo, $nome, $e->getMessage());
        }
    }

    private function cotacaoVazia(string $codigo, string $nome, ?string $erro = null): array
    {
        return [
            'nome'   => $nome,
            'moeda'  => $codigo,
            'compra' => null,
            'venda'  => null,
            'erro'   => $erro,
        ];
    }
}
