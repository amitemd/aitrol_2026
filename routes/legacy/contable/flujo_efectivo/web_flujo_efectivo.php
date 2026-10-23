<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
#Route::match(['get', 'post'], 'contable/Banco/notacredito', 'contable\NotaCreditoController@index')->name('notacredito.index');

#Route::get('contable/balance_comprobacion/index', 'contable\BalanceComprobacionController@index')->name('balance_comprobacion.index');
#Route::match(['get', 'post'], 'contable/libro_mayor', 'contable\LibroDiarioController@libro_mayor')->name('libro_mayor.index');
Route::match(['get', 'post'], 'contable/contabilidad/flujo/efectivo_1', 'contable\FlujoEfectivoController@index')->name('flujoefectivo.index');
Route::match(['get', 'post'], 'contable/contabilidad/flujo/efectivo_1/show', 'contable\FlujoEfectivoController@show')->name('flujoefectivo.show');

Route::match(['get', 'post'], 'contable/contabilidad/flujo/efectivo/comparativo_1', 'contable\FlujoEfectivoComparativoController@index')->name('flujoefectivocomparativo.index');
Route::match(['get', 'post'], 'contable/contabilidad/flujo/efectivo/comparativo_1/show', 'contable\FlujoEfectivoComparativoController@show')->name('flujoefectivocomparativo.show');

Route::match(['get', 'post'], 'contable/contabilidad/flujo/efectivo/comparativo/dos', 'contable\FlujoEfectivoComparativoController@index2')->name('flujoefectivocomparativo.index2');
Route::match(['get', 'post'], 'contable/contabilidad/flujo/efectivo/comparativo/show/grupos', 'contable\FlujoEfectivoComparativoController@show2')->name('flujoefectivocomparativo.show2');

Route::match(['get', 'post'], 'contable/estructura/flujo/efectivo/', 'contable\EstructuraFlujoEfectivoController@index')->name('estructuraflujoefectivo.index');
Route::match(['get', 'post'], 'contable/estructura/flujo/efectivo/create', 'contable\EstructuraFlujoEfectivoController@create')->name('estructuraflujoefectivo.create');
Route::match(['get', 'post'], 'contable/estructura/flujo/efectivo/store', 'contable\EstructuraFlujoEfectivoController@store')->name('estructuraflujoefectivo.store');
Route::match(['get', 'post'], 'contable/estructura/flujo/efectivo/edit/{id}', 'contable\EstructuraFlujoEfectivoController@edit')->name('estructuraflujoefectivo.edit');
Route::match(['get', 'post'], 'contable/estructura/flujo/efectivo/update/{id}', 'contable\EstructuraFlujoEfectivoController@update')->name('estructuraflujoefectivo.update');
Route::match(['get', 'post'], 'contable/estructura/flujo/efectivo/destroy/{id}', 'contable\EstructuraFlujoEfectivoController@destroy')->name('estructuraflujoefectivo.destroy');

Route::match(['get', 'post'], 'contable/estructura/flujo/efectivo/buscar', 'contable\EstructuraFlujoEfectivoController@buscar')->name('estructuraflujoefectivo.buscar');
