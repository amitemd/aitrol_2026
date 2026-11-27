<?php

use Aitrol\Clientes\Infrastructure\Http\ClienteApiController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

Route::prefix('api/v1/clientes')->name('api.v1.clientes.')
    ->middleware(['api', 'auth:sanctum', 'empresa', 'throttle:clientes-api'])
    ->group(function (): void {
        Route::get('/', [ClienteApiController::class, 'index'])
            ->middleware([CheckAbilities::class.':clientes:leer', 'permiso:clientes.lista,leer'])->name('index');
        Route::get('/{cliente}', [ClienteApiController::class, 'show'])->where('cliente', '[1-9][0-9]{0,17}')
            ->middleware([CheckAbilities::class.':clientes:leer', 'permiso:clientes.lista,leer'])->name('show');
        Route::post('/', [ClienteApiController::class, 'store'])
            ->middleware([CheckAbilities::class.':clientes:escribir', 'permiso:clientes.lista,escribir'])->name('store');
        Route::put('/{cliente}', [ClienteApiController::class, 'update'])->where('cliente', '[1-9][0-9]{0,17}')
            ->middleware([CheckAbilities::class.':clientes:escribir', 'permiso:clientes.lista,actualizar'])->name('update');
    });
