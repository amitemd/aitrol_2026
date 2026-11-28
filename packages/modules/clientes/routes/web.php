<?php

use Aitrol\Clientes\Infrastructure\Http\ClienteWebController;
use Aitrol\Comercial\Infrastructure\Http\ComercialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'auth.session', 'empresa'])->group(function (): void {
    Route::get('/clientes', [ClienteWebController::class, 'index'])->middleware('permiso:clientes.lista,leer')->name('clientes.index');
    Route::get('/clientes/create', [ClienteWebController::class, 'create'])->middleware('permiso:clientes.lista,escribir')->name('clientes.create');
    Route::get('/clientes/{cliente}', [ClienteWebController::class, 'show'])
        ->where('cliente', '[1-9][0-9]{0,17}')->middleware('permiso:clientes.lista,leer')->name('clientes.show');
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
    Route::get('/clientes/{cliente}/estado-de-cuenta', [ClienteWebController::class, 'estadoCuenta'])
        ->where('cliente', '[1-9][0-9]{0,17}')->middleware('permiso:clientes.reportes.cuentas-por-cobrar,leer')->name('clientes.estado-cuenta');

    foreach (config('clientes.secciones') as $ruta => $titulo) {
        [, $grupo, $seccion] = explode('.', $ruta);
        $url = '/'.str_replace('.', '/', $ruta);
        if ($seccion === 'comprobantes-de-pago') {
            Route::get($url, [ClienteWebController::class, 'comprobantesPago'])->middleware('permiso:'.$ruta.',leer')->name($ruta);

            continue;
        }
        if ($grupo === 'reportes') {
            Route::get($url, [ClienteWebController::class, 'cartera'])->defaults('reporte', $seccion)
                ->middleware('permiso:'.$ruta.',leer')->name($ruta);

            continue;
        }
        if ($grupo === 'documentos' && isset(ComercialController::SECCIONES_ARCHIVO[$seccion])) {
            Route::get($url, [ComercialController::class, 'archivo'])
                ->defaults('modulo', 'ventas')->defaults('seccion', ComercialController::SECCIONES_ARCHIVO[$seccion])
                ->middleware('permiso:'.$ruta.',leer')->name($ruta);

            continue;
        }
        Route::view($url, 'clientes::seccion', [
            'titulo' => $titulo,
            'grupo' => config('clientes.grupos.'.$grupo),
        ])->middleware('permiso:'.$ruta.',leer')->name($ruta);
    }
});
