<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{


      /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule){

        $schedule->command('atualizar:moedas')
        ->cron('0 3,5,8 * * *') 
        ->withoutOverlapping() 
        ->onFailure(function () {
            Log::error('Falha ao buscar cotações via atualizar:moedas');
        })
        ->onSuccess(function () {
            Log::info('Cotações atualizadas com sucesso via atualizar:moedas');
        });
    
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
