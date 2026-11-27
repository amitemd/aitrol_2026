<?php

use Aitrol\CajaBancos\Infrastructure\Http\CajaBancosController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

foreach ([false, true] as $api) {
    Route::prefix(($api ? 'api/v1/' : '').'caja-bancos')->name(($api ? 'api.v1.' : '').'caja-bancos.')
        ->middleware($api ? ['api', 'auth:sanctum', 'empresa', 'throttle:60,1'] : ['web', 'auth', 'auth.session', 'empresa'])
        ->group(function () use ($api): void {
            foreach (config('caja-bancos.secciones') as $seccion => [, , $alta]) {
                $permiso = fn (string $accion): array => [
                    'permiso:caja-bancos.'.$seccion.'.index,'.$accion,
                    ...($api ? [CheckAbilities::class.':caja-bancos:'.($accion === 'leer' ? 'leer' : 'escribir')] : []),
                ];
                Route::get($seccion, [CajaBancosController::class, 'index'])->defaults('seccion', $seccion)
                    ->middleware($permiso('leer'))->name($seccion.'.index');
                if (! $api && $seccion !== 'cuentas') {
                    Route::get($seccion.'/filtros/cuentas', [CajaBancosController::class, 'opciones'])
                        ->defaults('tipo', 'todas')->defaults('filtrar', true)
                        ->middleware($permiso('leer'))->name($seccion.'.filtro-cuentas');
                }
                if ($seccion === 'reportes') {
                    Route::get('reportes/exportar', [CajaBancosController::class, 'exportar'])->middleware($permiso('leer'))->name('reportes.exportar');
                    continue;
                }
                if ($alta !== null) {
                    Route::post($seccion, [CajaBancosController::class, 'store'])->defaults('seccion', $seccion)
                        ->middleware($permiso('escribir'))->name($seccion.'.store');
                    if (! $api) {
                        Route::get($seccion.'/crear', [CajaBancosController::class, 'crear'])->defaults('seccion', $seccion)
                            ->middleware($permiso('escribir'))->name($seccion.'.create');
                        Route::get($seccion.'/opciones/{tipo}', [CajaBancosController::class, 'opciones'])
                            ->whereIn('tipo', ['caja', 'banco', 'todas'])->defaults('seccion', $seccion)
                            ->middleware($permiso('escribir'))->name($seccion.'.opciones');
                    }
                }
                Route::get($seccion.'/{registro}', [CajaBancosController::class, 'show'])->where('registro', '[1-9][0-9]{0,17}')
                    ->defaults('seccion', $seccion)->middleware($permiso('leer'))->name($seccion.'.show');

                $acciones = match ($seccion) {
                    'cuentas' => ['editar'], 'cierres' => ['cerrar'], 'extractos' => [],
                    'conciliacion' => ['conciliar', 'desconciliar'], default => ['revertir'],
                };
                foreach ($acciones as $accion) {
                    Route::match([$accion === 'editar' ? 'PUT' : 'POST'], $seccion.'/{registro}'.($accion === 'editar' ? '' : '/'.$accion), [CajaBancosController::class, 'accion'])
                        ->where('registro', '[1-9][0-9]{0,17}')->defaults('seccion', $seccion)->defaults('accion', $accion)
                        ->middleware($permiso('actualizar'))->name($seccion.'.'.$accion);
                    if (! $api) {
                        Route::get($seccion.'/{registro}/'.$accion, [CajaBancosController::class, 'formularioAccion'])
                            ->where('registro', '[1-9][0-9]{0,17}')->defaults('seccion', $seccion)->defaults('accion', $accion)
                            ->middleware($permiso('actualizar'))->name($seccion.'.'.$accion.'.formulario');
                    }
                }
                if ($seccion === 'conciliacion') {
                    Route::get('conciliacion/{registro}/candidatos', [CajaBancosController::class, 'candidatos'])
                        ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso('actualizar'))->name('conciliacion.candidatos');
                }
            }
        });
}
