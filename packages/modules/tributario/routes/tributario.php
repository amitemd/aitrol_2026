<?php

use Aitrol\Tributario\Infrastructure\Http\TributarioController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

foreach ([false, true] as $api) {
    Route::prefix(($api ? 'api/v1/' : '').'tributario')->name(($api ? 'api.v1.' : '').'tributario.')
        ->middleware($api ? ['api', 'auth:sanctum', 'empresa', 'throttle:60,1'] : ['web', 'auth', 'auth.session', 'empresa'])
        ->group(function () use ($api): void {
            $permiso = fn ($s, $a) => ['permiso:tributario.'.$s.'.index,'.$a,
                ...($api ? [CheckAbilities::class.':tributario:'.($a === 'leer' ? 'leer' : 'escribir')] : [])];
            foreach (config('tributario.secciones') as $s => [, , $alta]) {
                Route::get($s, [TributarioController::class, 'index'])->defaults('seccion', $s)
                    ->middleware($permiso($s, 'leer'))->name($s.'.index');
                if ($alta !== null) {
                    Route::post($s, [TributarioController::class, 'guardar'])->defaults('seccion', $s)
                        ->middleware($permiso($s, 'escribir'))->name($s.'.store');
                    if (! $api) Route::get($s.'/crear', [TributarioController::class, 'formulario'])->defaults('seccion', $s)
                        ->middleware($permiso($s, 'escribir'))->name($s.'.create');
                }
                if (in_array($s, [...config('tributario.formatos'), 'declaraciones'], true)) {
                    Route::get($s.'/{registro}', [TributarioController::class, 'show'])->defaults('seccion', $s)
                        ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso($s, 'leer'))->name($s.'.show');
                    Route::get($s.'/{registro}/exportar', [TributarioController::class, 'exportar'])->defaults('seccion', $s)
                        ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso($s, 'leer'))->name($s.'.exportar');
                    foreach (['revisar', 'reabrir', 'registrar-entrega'] as $accion) {
                        Route::post($s.'/{registro}/'.$accion, [TributarioController::class, 'transicion'])->defaults('seccion', $s)->defaults('accion', $accion)
                            ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso($s, 'actualizar'))->name($s.'.'.$accion);
                        if (! $api) Route::get($s.'/{registro}/'.$accion, [TributarioController::class, 'formulario'])->defaults('seccion', $s)->defaults('accion', $accion)
                            ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso($s, 'actualizar'))->name($s.'.'.$accion.'.formulario');
                    }
                }
            }
            Route::get('formulario-107/nomina', [TributarioController::class, 'nomina'])->middleware($permiso('formulario-107', 'leer'))->name('nomina.index');
            Route::post('formulario-107/nomina', [TributarioController::class, 'guardarNomina'])->middleware($permiso('formulario-107', 'escribir'))->name('nomina.store');
            if (! $api) {
                Route::get('formulario-107/nomina/crear', [TributarioController::class, 'formulario'])->defaults('seccion', 'nomina')
                    ->middleware($permiso('formulario-107', 'escribir'))->name('nomina.create');
                Route::get('documentos/opciones/declaraciones', [TributarioController::class, 'opciones'])->middleware($permiso('documentos', 'escribir'))->name('documentos.opciones');
            }
            Route::get('documentos/{registro}/descargar', [TributarioController::class, 'archivo'])->where('registro', '[1-9][0-9]{0,17}')
                ->middleware($permiso('documentos', 'leer'))->name('documentos.archivo');
        });
}
