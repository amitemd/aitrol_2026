<?php

use Aitrol\Inventarios\Infrastructure\Http\InventarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('inventarios')->name('inventarios.')
    ->middleware(['web', 'auth', 'auth.session', 'empresa'])
    ->group(function (): void {
        foreach (array_keys(config('inventarios.secciones')) as $seccion) {
            $permiso = 'permiso:inventarios.'.$seccion.'.index,';
            Route::get($seccion, [InventarioController::class, 'index'])->defaults('seccion', $seccion)
                ->middleware($permiso.'leer')->name($seccion.'.index');

            if (in_array($seccion, [...config('inventarios.catalogos'), 'movimientos', 'ajustes'], true)) {
                Route::get($seccion.'/crear', [InventarioController::class, 'formulario'])->defaults('seccion', $seccion)
                    ->middleware($permiso.'escribir')->name($seccion.'.create');
                Route::post($seccion, [InventarioController::class, 'store'])->defaults('seccion', $seccion)
                    ->middleware($permiso.'escribir')->name($seccion.'.store');
            }

            if (in_array($seccion, config('inventarios.catalogos'), true)) {
                Route::get($seccion.'/{registro}/editar', [InventarioController::class, 'formulario'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso.'actualizar')->name($seccion.'.edit');
                Route::put($seccion.'/{registro}', [InventarioController::class, 'update'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso.'actualizar')->name($seccion.'.update');
            }
            if (in_array($seccion, [...config('inventarios.catalogos'), 'movimientos', 'ajustes'], true)) {
                Route::get($seccion.'/{registro}', [InventarioController::class, 'show'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')->middleware($permiso.'leer')->name($seccion.'.show');
            }

            Route::get($seccion.'/opciones/{catalogo}', [InventarioController::class, 'opciones'])->defaults('seccion', $seccion)
                ->whereIn('catalogo', ['productos', 'bodegas'])->name($seccion.'.opciones');
        }
        Route::get('reportes/exportar', [InventarioController::class, 'exportar'])
            ->middleware('permiso:inventarios.reportes.index,leer')->name('reportes.exportar');
    });
