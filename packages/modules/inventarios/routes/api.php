<?php

use Aitrol\Inventarios\Infrastructure\Http\InventarioController;
use App\Http\Controllers\ProductoImportacionController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

Route::prefix('api/v1/inventarios')->name('api.v1.inventarios.')
    ->middleware(['api', 'auth:sanctum', 'empresa', 'throttle:60,1'])
    ->group(function (): void {
        Route::middleware('can:administrar-configuracion')->prefix('productos/importaciones')
            ->name('productos.importaciones.')->group(function (): void {
                Route::post('/', [ProductoImportacionController::class, 'store'])
                    ->middleware([CheckAbilities::class.':inventarios:escribir', 'throttle:6,1'])->name('store');
                Route::get('/{importacion}', [ProductoImportacionController::class, 'show'])->whereUuid('importacion')
                    ->middleware(CheckAbilities::class.':inventarios:leer')->name('show');
            });

        foreach (array_keys(config('inventarios.secciones')) as $seccion) {
            $permiso = 'permiso:inventarios.'.$seccion.'.index,';
            $lectura = [CheckAbilities::class.':inventarios:leer', $permiso.'leer'];
            Route::get($seccion, [InventarioController::class, 'index'])->defaults('seccion', $seccion)
                ->middleware($lectura)->name($seccion.'.index');

            if (in_array($seccion, [...config('inventarios.catalogos', ['productos', 'bodegas']), 'movimientos', 'ajustes'], true)) {
                Route::get($seccion.'/{registro}', [InventarioController::class, 'show'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')->middleware($lectura)->name($seccion.'.show');
                Route::post($seccion, [InventarioController::class, 'store'])->defaults('seccion', $seccion)
                    ->middleware([CheckAbilities::class.':inventarios:escribir', $permiso.'escribir'])->name($seccion.'.store');
            }

            if (in_array($seccion, config('inventarios.catalogos', ['productos', 'bodegas']), true)) {
                Route::put($seccion.'/{registro}', [InventarioController::class, 'update'])->defaults('seccion', $seccion)
                    ->where('registro', '[1-9][0-9]{0,17}')
                    ->middleware([CheckAbilities::class.':inventarios:escribir', $permiso.'actualizar'])->name($seccion.'.update');
            }
        }
        Route::get('reportes/exportar', [InventarioController::class, 'exportar'])
            ->middleware([CheckAbilities::class.':inventarios:leer', 'permiso:inventarios.reportes.index,leer'])
            ->name('reportes.exportar');
    });
