<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atualizar:moedas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';



    public function handle()
    {
        $cacheKey = 'cotacoes_bids_' . now()->format('Y-m-d');

        $resultado = Cache::remember($cacheKey, now()->endOfDay(), function () {
            $moedasSuportadas = $this->buscarMoedasSuportadas();
            $dataCotacao = $this->obterDataUtil();
            $resultado = [];

            foreach ($moedasSuportadas as $codigo => $nome) {
                $resultado[$codigo] = $this->buscarCotacao($codigo, $nome, $dataCotacao);
                usleep(100000); // respeita limites da API
            }
            dd($resultado);
            return $resultado;
        });

        $this->info('Cotações atualizadas e salvas no cache.');
        $this->line(json_encode($resultado, JSON_PRETTY_PRINT));
    }

    private function obterDataUtil(): string
    {
        $data = now();

        while (in_array($data->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
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

            dump($resposta->status(), $resposta->body());

            $dados = $resposta->json()['value'][0] ?? null;

            if ($dados) {
                return [
                    'nome'   => $nome,
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
