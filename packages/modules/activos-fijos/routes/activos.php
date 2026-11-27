<?php

use Aitrol\ActivosFijos\Infrastructure\Http\ActivosController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

foreach ([false, true] as $api) {
    Route::prefix(($api ? 'api/v1/' : '').'activos-fijos')
        ->name(($api ? 'api.v1.' : '').'activos-fijos.')
        ->middleware($api ? ['api', 'auth:sanctum', 'empresa', 'throttle:60,1'] : ['web', 'auth', 'auth.session', 'empresa'])
        ->group(function () use ($api): void {
            foreach (config('activos-fijos.secciones') as $seccion => [, , $alta]) {
                $permisos = fn (string $accion): array => [
                    'permiso:activos-fijos.'.$seccion.'.index,'.$accion,
                    ...($api ? [CheckAbilities::class.':activos-fijos:'.($accion === 'leer' ? 'leer' : 'escribir')] : []),
                ];
                Route::get($seccion, [ActivosController::class, 'index'])->defaults('seccion', $seccion)
                    ->middleware($permisos('leer'))->name($seccion.'.index');
                if ($seccion === 'reportes') {
                    Route::get('reportes/exportar', [ActivosController::class, 'exportar'])
                        ->middleware($permisos('leer'))->name('reportes.exportar');
                    continue;
                }
                if ($alta !== null) {
                    Route::post($seccion, [ActivosController::class, 'store'])->defaults('seccion', $seccion)
                        ->middleware($permisos('escribir'))->name($seccion.'.store');
                    if (! $api) {
                        Route::get($seccion.'/crear', [ActivosController::class, 'formulario'])->defaults('seccion', $seccion)
                            ->middleware($permisos('escribir'))->name($seccion.'.create');
                        Route::get($seccion.'/opciones/{origen}', [ActivosController::class, 'opciones'])
                            ->defaults('seccion', $seccion)->middleware($permisos('escribir'))->name($seccion.'.opciones');
                    }
                }
                Route::get($seccion.'/{registro}', [ActivosController::class, 'show'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')->middleware($permisos('leer'))->name($seccion.'.show');
                if (in_array($seccion, [...config('activos-fijos.catalogos'), 'activos', 'ordenes'], true)) {
                    Route::put($seccion.'/{registro}', [ActivosController::class, 'update'])->defaults('seccion', $seccion)
                        ->where('registro', '[1-9][0-9]{0,17}')->middleware($permisos('actualizar'))->name($seccion.'.update');
                    if (! $api) {
                        Route::get($seccion.'/{registro}/editar', [ActivosController::class, 'formulario'])->defaults('seccion', $seccion)
                            ->where('registro', '[1-9][0-9]{0,17}')->middleware($permisos('actualizar'))->name($seccion.'.edit');
                    }
                }
            }
        });
}
