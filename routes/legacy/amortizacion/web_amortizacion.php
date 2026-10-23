<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('contable/amortizacion', 'amortizacion\AmortizacionController@index')->name('amortizacion.index');
    Route::get('contable/amortizacion/crear', 'amortizacion\AmortizacionController@create')->name('amortizacion.create');
    Route::post('contable/amortizacion/preview', 'amortizacion\AmortizacionController@preview')->name('amortizacion.preview');
    Route::get('contable/amortizacion/cuentas', 'amortizacion\AmortizacionController@cuentas')->name('amortizacion.cuentas');
    Route::post('contable/amortizacion', 'amortizacion\AmortizacionController@store')->name('amortizacion.store');
    Route::post('contable/amortizacion/{id}/asiento-desembolso', 'amortizacion\AmortizacionController@generateDisbursementEntry')->where('id', '[0-9]+')->name('amortizacion.asiento_desembolso');
    Route::post('contable/amortizacion/{id}/anular', 'amortizacion\AmortizacionController@cancel')->where('id', '[0-9]+')->name('amortizacion.cancel');
    Route::get('contable/amortizacion/{id}', 'amortizacion\AmortizacionController@show')->where('id', '[0-9]+')->name('amortizacion.show');
    Route::post('contable/amortizacion/{id}/pagar', 'amortizacion\AmortizacionController@pay')->where('id', '[0-9]+')->name('amortizacion.pay');
});
