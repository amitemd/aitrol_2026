<?php

namespace Sis_medico\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Sis_medico\Log_usuario;

class SuccessfulLogin
{
    public function __construct(private Request $request) {}

    public function handle(Login $event): void
    {
        Log_usuario::create([
            'id_usuario' => $event->user->id,
            'ip_usuario' => $this->request->ip() ?? 'unknown',
            'descripcion' => 'INICIO DE SESION',
        ]);
    }
}
