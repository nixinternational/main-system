<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schema;

class InitSystem extends Command
{
    protected $signature = 'inicializar:sistema';
    protected $description = 'Inicializa o sistema';


    public function handle()
    {
        echo '----------------------- INICIANDO --------------------';

        try{
            if (!Schema::hasTable('users') && !Schema::hasTable('clientes') && !Schema::hasTable('produtos')) {
                Artisan::call('migrate:fresh --seed --force');
                echo '----------------------- FINALIZADO --------------------';

            } else {
                Artisan::call('migrate');
            }
        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }


}