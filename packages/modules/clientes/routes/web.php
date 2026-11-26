<?php

use Aitrol\Clientes\Infrastructure\Http\ClienteWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session'])->group(function (): void {
    Route::get('/clientes', [ClienteWebController::class, 'index'])->middleware('permiso:clientes.lista,leer')->name('clientes.index');
    Route::get('/clientes/create', [ClienteWebController::class, 'create'])->middleware('permiso:clientes.lista,escribir')->name('clientes.create');
    Route::post('/clientes', [ClienteWebController::class, 'store'])->middleware('permiso:clientes.lista,escribir')->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteWebController::class, 'edit'])
        ->where('cliente', '[1-9][0-9]{0,17}')->middleware('permiso:clientes.lista,actualizar')->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteWebController::class, 'update'])
        ->where('cliente', '[1-9][0-9]{0,17}')->middleware('permiso:clientes.lista,actualizar')->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteWebController::class, 'destroy'])
        ->where('cliente', '[1-9][0-9]{0,17}')->middleware('permiso:clientes.lista,eliminar')->name('clientes.destroy');
    Route::get('/clientes/{cliente}/foto', [ClienteWebController::class, 'foto'])
        ->where('cliente', '[1-9][0-9]{0,17}')->middleware('permiso:clientes.lista,leer')->name('clientes.foto');
    Route::get('/clientes/{cliente}/documentos/{archivo}', [ClienteWebController::class, 'documento'])
        ->where('cliente', '[1-9][0-9]{0,17}')->whereUuid('archivo')->middleware('permiso:clientes.lista,leer')->name('clientes.documento');

    foreach (config('clientes.secciones') as $ruta => $titulo) {
        [, $grupo] = explode('.', $ruta);
        Route::view('/'.str_replace('.', '/', $ruta), 'clientes::seccion', [
            'titulo' => $titulo,
            'grupo' => config('clientes.grupos.'.$grupo),
        ])->middleware('permiso:'.$ruta.',leer')->name($ruta);
    }
});
