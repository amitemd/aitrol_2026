<?php

namespace App\Jobs;

use App\Services\MantenimientoServidor;

final readonly class EjecutarMantenimientoServidor
{
    public function __construct(public int $ejecucion) {}

    public function handle(MantenimientoServidor $mantenimiento): void
    {
        ignore_user_abort(true);
        set_time_limit(720);
        $mantenimiento->ejecutar($this->ejecucion);
    }
}
