<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('custom:command', function () {
    $empresa = \Sis_medico\Empresa::where('prioridad', 1)->first();
    $id_empresa = $empresa->id;
    $t = strtotime("-1 day");
    $panillas = \Sis_medico\Planilla::where('estado', 1)
        ->where('aprobado', 1)
        ->whereNull('id_asiento_cabecera')
        ->where('updated_at', '<=',  date('Y-m-d', $t) . ' 23:59:59')
        ->get();
    foreach ($panillas as $value) {
        $valor = \Sis_medico\Http\Controllers\Insumos\PlantillaControlController::generar_asiento($value->id, $id_empresa);
    }
})->describe('Guardado planilla');
