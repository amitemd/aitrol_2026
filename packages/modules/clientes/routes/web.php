<?php

use Aitrol\Clientes\Infrastructure\Http\ClienteWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session'])->group(function (): void {
    Route::get('/clientes', [ClienteWebController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteWebController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteWebController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteWebController::class, 'edit'])
        ->where('cliente', '[1-9][0-9]{0,17}')->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteWebController::class, 'update'])
        ->where('cliente', '[1-9][0-9]{0,17}')->name('clientes.update');
});
