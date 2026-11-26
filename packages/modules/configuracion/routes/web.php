<?php

use Aitrol\Configuracion\Infrastructure\Http\MenuController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session', 'can:administrar-menu'])
    ->prefix('configuraciones/menu')->name('configuracion.menu.')
    ->group(function (): void {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::put('/{menu}', [MenuController::class, 'update'])->whereNumber('menu')->name('update');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->whereNumber('menu')->name('destroy');
    });
