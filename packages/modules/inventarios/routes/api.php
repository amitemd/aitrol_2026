<?php

use Aitrol\Inventarios\Infrastructure\Http\InventarioController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

Route::prefix('api/v1/inventarios')->name('api.v1.inventarios.')
    ->middleware(['api', 'auth:sanctum', 'empresa', 'throttle:60,1'])
    ->group(function (): void {
        foreach (array_keys(config('inventarios.secciones')) as $seccion) {
            $permiso = 'permiso:inventarios.'.$seccion.'.index,';
            $lectura = [CheckAbilities::class.':inventarios:leer', $permiso.'leer'];
            Route::get($seccion, [InventarioController::class, 'index'])->defaults('seccion', $seccion)
                ->middleware($lectura)->name($seccion.'.index');

            if (in_array($seccion, ['productos', 'bodegas', 'movimientos', 'ajustes'], true)) {
                Route::get($seccion.'/{registro}', [InventarioController::class, 'show'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')->middleware($lectura)->name($seccion.'.show');
                Route::post($seccion, [InventarioController::class, 'store'])->defaults('seccion', $seccion)
                    ->middleware([CheckAbilities::class.':inventarios:escribir', $permiso.'escribir'])->name($seccion.'.store');
            }

            if (in_array($seccion, ['productos', 'bodegas'], true)) {
                Route::put($seccion.'/{registro}', [InventarioController::class, 'update'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')
                    ->middleware([CheckAbilities::class.':inventarios:escribir', $permiso.'actualizar'])->name($seccion.'.update');
            }
        }
    });
