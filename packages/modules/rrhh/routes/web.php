<?php

use Aitrol\Rrhh\Infrastructure\Http\RrhhController;
use Illuminate\Support\Facades\Route;

Route::prefix('rrhh')->name('rrhh.')->middleware(['web', 'auth', 'auth.session', 'empresa'])
    ->group(function (): void {
        foreach (array_keys((require config_path('rrhh.php'))['secciones']) as $seccion) {
                $permiso = 'permiso:rrhh.'.$seccion.'.index,';
                Route::get($seccion, [RrhhController::class, 'index'])->defaults('seccion', $seccion)
                    ->middleware($permiso.'leer')->name($seccion.'.index');
                if (in_array($seccion, ['panel', 'reportes', 'roles'], true)) {
                    continue;
                }
                Route::get($seccion.'/crear', [RrhhController::class, 'formulario'])->defaults('seccion', $seccion)
                    ->middleware($permiso.'escribir')->name($seccion.'.create');
                Route::post($seccion, [RrhhController::class, 'guardar'])->defaults('seccion', $seccion)
                    ->middleware($permiso.'escribir')->name($seccion.'.store');
                if ($seccion !== 'nomina') {
                    Route::get($seccion.'/{registro}', [RrhhController::class, 'detalle'])->whereNumber('registro')
                        ->defaults('seccion', $seccion)->middleware($permiso.'leer')->name($seccion.'.show');
                    Route::get($seccion.'/{registro}/editar', [RrhhController::class, 'formulario'])->whereNumber('registro')
                        ->defaults('seccion', $seccion)->middleware($permiso.'actualizar')->name($seccion.'.edit');
                    Route::put($seccion.'/{registro}', [RrhhController::class, 'guardar'])->whereNumber('registro')
                        ->defaults('seccion', $seccion)->middleware($permiso.'actualizar')->name($seccion.'.update');
                }
                if (! in_array($seccion, ['empleados', 'contratos', 'organizacion'], true)) {
                    Route::patch($seccion.'/{registro}/estado', [RrhhController::class, 'estado'])->whereNumber('registro')
                        ->defaults('seccion', $seccion)->middleware($permiso.'actualizar')->name($seccion.'.estado');
                }
            }
        Route::get('nomina/{registro}', [RrhhController::class, 'nomina'])->whereNumber('registro')
            ->middleware('permiso:rrhh.nomina.index,leer')->name('nomina.show');
        Route::get('roles/{registro}', [RrhhController::class, 'rol'])->whereNumber('registro')
            ->middleware('permiso:rrhh.roles.index,leer')->name('roles.show');
        Route::get('nomina/{registro}/exportar', [RrhhController::class, 'exportar'])->whereNumber('registro')
            ->middleware('permiso:rrhh.nomina.index,leer')->name('nomina.exportar');
    });
