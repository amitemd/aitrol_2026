<?php

use Aitrol\Proveedores\Infrastructure\Http\ProveedorApiController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

Route::prefix('api/v1/proveedores')->name('api.v1.proveedores.')
    ->middleware(['api', 'auth:sanctum', 'throttle:proveedores-api'])
    ->group(function (): void {
        Route::get('/', [ProveedorApiController::class, 'index'])
            ->middleware([CheckAbilities::class.':proveedores:leer', 'permiso:proveedores.lista,leer'])->name('index');
        Route::get('/{proveedor}', [ProveedorApiController::class, 'show'])->where('proveedor', '[1-9][0-9]{0,17}')
            ->middleware([CheckAbilities::class.':proveedores:leer', 'permiso:proveedores.lista,leer'])->name('show');
        Route::post('/', [ProveedorApiController::class, 'store'])
            ->middleware([CheckAbilities::class.':proveedores:escribir', 'permiso:proveedores.lista,escribir'])->name('store');
        Route::put('/{proveedor}', [ProveedorApiController::class, 'update'])->where('proveedor', '[1-9][0-9]{0,17}')
            ->middleware([CheckAbilities::class.':proveedores:escribir', 'permiso:proveedores.lista,actualizar'])->name('update');
        Route::delete('/{proveedor}', [ProveedorApiController::class, 'destroy'])->where('proveedor', '[1-9][0-9]{0,17}')
            ->middleware([CheckAbilities::class.':proveedores:eliminar', 'permiso:proveedores.lista,eliminar'])->name('destroy');
    });
