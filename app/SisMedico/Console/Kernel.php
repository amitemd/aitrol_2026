<?php

namespace Sis_medico\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Sis_medico\Http\Controllers\BdLabsController;
use Sis_medico\Console\Commands\VerificarSaludV3;
use Sis_medico\Console\Commands\VerificarDespliegueV3;
use Sis_medico\Console\Commands\VerificarApiTributariaV3;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        VerificarSaludV3::class,
        VerificarDespliegueV3::class,
        VerificarApiTributariaV3::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $variable = config('app.url_variable');
        if ($variable != 'nulo') {
            $schedule->exec('wget ' . config('app.url_variable') . 'api_doc_electronico ' . ' --no-check-certificate --delete-after')
                ->everyMinute()
                ->between('04:00', '23:50');
        }
        
        $schedule->call(function () {
            BdLabsController::calcula_porcentaje(date('Y'), date('m'));
            // Tu lógica de función aquí
            // Puedes llamar a cualquier función de tu aplicación
            // por ejemplo: MiClase::miMetodo();
        })->weekdays()
            ->dailyAt('20:00');

        $schedule->command('custom:command')
            ->weekdays()
            ->dailyAt('13:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
