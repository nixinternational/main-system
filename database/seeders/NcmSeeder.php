<?php

namespace Database\Seeders;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Ncm;
use Illuminate\Support\Facades\Storage;

class NcmSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/ncm.json');

        foreach ($this->parseLargeJsonFile($path) as $nomenclatura) {
            if (strlen($nomenclatura['Codigo']) < strlen('0101.29.00')) {
                continue; // Ignora códigos que não têm exatamente 10 caracteres
            }
        
            Ncm::updateOrCreate(
                ['codigo' => $nomenclatura['Codigo']],
                [
                    'descricao'      => $nomenclatura['Descricao'],
                    'data_inicio'    => Carbon::createFromFormat('d/m/Y', $nomenclatura['Data_Inicio'])->format('Y-m-d'),
                    'data_fim'       => Carbon::createFromFormat('d/m/Y', $nomenclatura['Data_Fim'])->format('Y-m-d'),
                    'tipo_ato_ini'   => $nomenclatura['Tipo_Ato_Ini'],
                    'numero_ato_ini' => $nomenclatura['Numero_Ato_Ini'],
                    'ano_ato_ini'    => $nomenclatura['Ano_Ato_Ini'],
                ]
            );
        }
    }

    private function parseLargeJsonFile(string $path): \Generator
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException("Não foi possível abrir o arquivo: $path");
        }

        // Lê tudo de uma vez (só essa parte usa memória, mas é necessário para acessar "Nomenclaturas")
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }

        fclose($handle);

        $json = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        foreach ($json['Nomenclaturas'] as $item) {
            yield $item;
        }
    }
}
