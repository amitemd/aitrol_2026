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
Route::match(['get', 'post'], 'contable/Banco/notacredito', 'contable\NotaCreditoController@index')->name('notacredito.index');


Route::get('contable/Banco/notacredito/crear/', 'contable\NotaCreditoController@crear')->name('notacredito.crear');
Route::post('contable/Banco/notacredito/guardar', 'contable\NotaCreditoController@store')->name('notacredito.store');
Route::get('contable/Banco/notacredito/revisar/{id}', 'contable\NotaCreditoController@revisar')->name('notacredito.revisar');
Route::match(['get', 'post'],'contable/Banco/notacredito/buscar', 'contable\NotaCreditoController@buscar')->name('notacredito.buscar');
Route::post('contable/Banco/notacredito/buscar/asiento', 'contable\NotaCreditoController@buscarasiento')->name('notacredito.buscar_asiento');
Route::get('contable/Banco/notacredito/imprimir/{id}', 'contable\NotaCreditoController@imprimir')->name('notacredito.imprimir');

Route::get('contable/Banco/notacredito/anular/{id}', 'contable\NotaCreditoController@anular')->name('notacredito.anular');

Route::get('contable/Banco/notacredito/exportar_excel', 'contable\NotaCreditoController@exportar_excel')->name('notacredito.exportar_excel');