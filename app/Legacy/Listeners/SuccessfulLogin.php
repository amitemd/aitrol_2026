<?php

namespace Sis_medico\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Sis_medico\Log_usuario;
use Illuminate\Support\Facades\Auth;

class SuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        //
        $ip_cliente= $_SERVER["REMOTE_ADDR"];

        Log_usuario::create([
                'id_usuario' => $event->user->id,
                'ip_usuario' => $ip_cliente,
                'descripcion' => "INICIO DE SESION",
                ]);
        
        
    }
}
