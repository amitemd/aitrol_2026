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
Route::match(['get', 'post'], 'contable/Banco/debitobancario', 'contable\DebitoBancarioController@index')->name('debitobancario.index');
Route::get('contable/Banco/debitobancario/crear/', 'contable\DebitoBancarioController@crear')->name('debitobancario.crear');
Route::get('contable/Banco/debitobancario/anulacion/{id}', 'contable\DebitoBancarioController@anulacion')->name('debitobancario.anulacion');
Route::get('contable/Banco/debitobancario/buscarproveedor/', 'contable\DebitoBancarioController@buscarproveedor')->name('debitobancario.buscarproveedor');
Route::match(['get', 'post'], 'contable/Banco/debito/acreedores/buscar', 'contable\DebitoBancarioController@search')->name('debitobancario.buscar');
Route::post('contable/Banco/debitobancario/comprasproveedor/', 'contable\DebitoBancarioController@comprasproveedor')->name('debitobancario.comprasproveedor');
Route::post('contable/Banco/debitobancario/buscardatosproveedor/', 'contable\DebitoBancarioController@buscardatosproveedor')->name('debitobancario.buscardatosproveedor');
//Route::get('contable/Banco/debitobancario/exportar_excel', 'contable\DebitoBancarioController@exportar_excel')->name('debitobancario.exportar_excel');

Route::post('contable/Banco/debitobancario/superavit/', 'contable\DebitoBancarioController@superavit')->name('debitobancario.superavit');
Route::post('contable/Banco/debitobancario/buscarcodigo/', 'contable\DebitoBancarioController@buscarcodigo')->name('debitobancario.buscarcodigo');

Route::post('contable/Banco/debitobancario/generar/', 'contable\DebitoBancarioController@generardebito')->name('debitobancario.generar');

Route::get('contable/Banco/debitobancario/revisar/{id}', 'contable\DebitoBancarioController@revisar')->name('debitobancario.revisar');
Route::post('contable/Banco/debitobancario/update/{id}', 'contable\DebitoBancarioController@update')->name('debitobancario.update');
Route::post('contable/Banco/notacredito/guardar', 'contable\NotaCreditoController@store')->name('notacredito.store');
Route::get('contable/Banco/notacredito/revisar/{id}', 'contable\NotaCreditoController@revisar')->name('notacredito.revisar');
Route::match(['get', 'post'], 'contable/Banco/notacredito/buscar', 'contable\NotaCreditoController@buscar')->name('notacredito.buscar');
Route::post('contable/Banco/notacredito/buscar/asiento', 'contable\NotaCreditoController@buscarasiento')->name('notacredito.buscar_asiento');

Route::get('contable/Banco/notacredito/anular/{id}', 'contable\NotaCreditoController@anular')->name('notacredito.anular');
//pdf Anthony 24/11/2020
Route::match(['get', 'post'],'contable/Banco/debitobancario/pdf/{id}', 'contable\DebitoBancarioController@imprimir_pdf')->name('imprimir_pdf');
//pdf Fausto 05/03/2021
Route::get('contable/Banco/debitobancario/pdf_proveedor/{id}', 'contable\DebitoBancarioController@pdf_proveedor')->name('imprimir_pdf_proveedor');
//excel Debito Bancario
Route::get('contable/Banco/debitobancario/excel', 'contable\DebitoBancarioController@excel')->name('debitobancario.excel');