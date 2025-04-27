<?php

namespace Database\Seeders;

use App\Models\Atributo;
use App\Models\AtributoDetalhes;
use App\Models\Ncm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AtributosNcm extends Seeder
{
    public function run()
    {
        foreach ($this->yieldNcms() as $ncmItem) {
            $ncm = Ncm::where('codigo', $ncmItem['codigoNcm'])->first();
            
            if (!$ncm) {
                info(json_encode($ncmItem));
                continue;
            }

            foreach ($ncmItem['listaAtributos'] as $atributoData) {
                $atributo = Atributo::firstOrCreate(
                    ['codigo' => $atributoData['codigo']],
                    [
                        'ncm_id' => $ncm->id,
                        'modalidade' => $atributoData['modalidade'],
                        'obrigatorio' => $atributoData['obrigatorio'],
                        'multivalorado' => $atributoData['multivalorado'],
                        'data_inicio_vigencia' => $atributoData['dataInicioVigencia']
                    ]
                );

                if (!$ncm->atributos()->where('id', $atributo->id)->exists()) {
                    $ncm->atributos()->save($atributo);
                }
            }
        }

        foreach ($this->yieldDetalhes() as $detalhesAtributos) {
            $atributo = Atributo::where('codigo', $detalhesAtributos['codigo'])->first();

            if (!$atributo) continue;

            AtributoDetalhes::firstOrCreate(
                ['codigo' => $detalhesAtributos['codigo']],
                [
                    'dados' => json_encode($detalhesAtributos),
                    'atributo_ncm_id' => $atributo->id
                ]
            );
        }
    }

    protected function yieldNcms(): \Generator
    {
        $json = json_decode(Storage::get('atributos.json'), true);

        foreach ($json['listaNcm'] as $ncm) {
            yield $ncm;
        }
    }

    protected function yieldDetalhes(): \Generator
    {
        $json = json_decode(Storage::get('atributos.json'), true);

        foreach ($json['detalhesAtributos'] as $detalhe) {
            yield $detalhe;
        }
    }
}
