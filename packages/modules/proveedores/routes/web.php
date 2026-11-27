<?php

use Aitrol\Proveedores\Infrastructure\Http\ProveedorWebController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session', 'empresa'])->group(function (): void {
    Route::get('/proveedores', [ProveedorWebController::class, 'index'])->middleware('permiso:proveedores.lista,leer')->name('proveedores.index');
    Route::get('/proveedores/create', [ProveedorWebController::class, 'create'])->middleware('permiso:proveedores.lista,escribir')->name('proveedores.create');
    Route::get('/proveedores/{proveedor}', [ProveedorWebController::class, 'show'])
        ->where('proveedor', '[1-9][0-9]{0,17}')->middleware('permiso:proveedores.lista,leer')->name('proveedores.show');
    Route::post('/proveedores', [ProveedorWebController::class, 'store'])->middleware('permiso:proveedores.lista,escribir')->name('proveedores.store');
    Route::get('/proveedores/{proveedor}/edit', [ProveedorWebController::class, 'edit'])
        ->where('proveedor', '[1-9][0-9]{0,17}')->middleware('permiso:proveedores.lista,actualizar')->name('proveedores.edit');
    Route::put('/proveedores/{proveedor}', [ProveedorWebController::class, 'update'])
        ->where('proveedor', '[1-9][0-9]{0,17}')->middleware('permiso:proveedores.lista,actualizar')->name('proveedores.update');
    Route::delete('/proveedores/{proveedor}', [ProveedorWebController::class, 'destroy'])
        ->where('proveedor', '[1-9][0-9]{0,17}')->middleware('permiso:proveedores.lista,eliminar')->name('proveedores.destroy');
    Route::get('/proveedores/{proveedor}/foto', [ProveedorWebController::class, 'foto'])
        ->where('proveedor', '[1-9][0-9]{0,17}')->middleware('permiso:proveedores.lista,leer')->name('proveedores.foto');
    Route::get('/proveedores/{proveedor}/documentos/{archivo}', [ProveedorWebController::class, 'documento'])
        ->where('proveedor', '[1-9][0-9]{0,17}')->whereUuid('archivo')->middleware('permiso:proveedores.lista,leer')->name('proveedores.documento');

    foreach (config('proveedores.secciones') as $ruta => $titulo) {
        [, $grupo] = explode('.', $ruta);
        Route::view('/'.str_replace('.', '/', $ruta), 'proveedores::seccion', [
            'titulo' => $titulo,
            'grupo' => config('proveedores.grupos.'.$grupo),
        ])->middleware('permiso:'.$ruta.',leer')->name($ruta);
    }
});
