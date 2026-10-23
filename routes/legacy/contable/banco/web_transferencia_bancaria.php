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
Route::match(['get', 'post'], 'contable/Banco/transferenciabancaria', 'contable\TransferenciaBancariaController@index')->name('transferenciabancaria.index');

Route::get('contable/Banco/transferenciabancaria/create/', 'contable\TransferenciaBancariaController@create')->name('transferenciabancaria.create');
Route::post('contable/Banco/transferenciabancaria/store', 'contable\TransferenciaBancariaController@store')->name('transferenciabancaria.store');
Route::post('contable/Banco/transferenciabancaria/store_autorizacion', 'contable\TransferenciaBancariaController@store_autorizacion')->name('transferenciabancaria.store_autorizacion');

Route::match(['get', 'post'],'contable/Banco/transferenciabancaria/buscar', 'contable\TransferenciaBancariaController@buscar')->name('transferenciabancaria.buscar');
Route::get('contable/Banco/transferenciabancaria/anular', 'contable\TransferenciaBancariaController@anular')->name('transferenciabancaria.anular');
Route::get('contable/Banco/transferenciabancaria/show/{id}', 'contable\TransferenciaBancariaController@show')->name('transferenciabancaria.show');
Route::get('contable/Banco/transferenciabancaria/imprimir/{id}', 'contable\TransferenciaBancariaController@imprimir')->name('transferenciabancaria.imprimir');

Route::match(['get', 'post'], 'contable/Banco/transferenciabancaria/devuelvefloat', 'contable\TransferenciaBancariaController@devuelvefloat')->name('transferenciabancaria.devuelvefloat');

Route::get('contable/Banco/transferenciabancaria/exportar_excel', 'contable\TransferenciaBancariaController@exportar_excel')->name('transferenciabancaria.exportar_excel');

Route::get('contable/Banco/transferenciabancaria/buscar/cuenta_destino', 'contable\TransferenciaBancariaController@buscar_cuenta_destino')->name('transferenciabancaria.buscar_cuenta_destino');
Route::match(['get', 'post'], 'contable/Banco/transferenciabancaria/guardar_autorizacion', 'contable\TransferenciaBancariaController@guardar_autorizacion')->name('transferenciabancaria.guardar_autorizacion');

//Actualizacion de retencion Bancaria
Route::post('contable/Banco/transferenciabancaria/actualizarRetencionBancaria', 'contable\TransferenciaBancariaController@actualizarRetencionBancaria')->name('transferenciabancaria.actualizarRetencionBancaria');

Route::post('contable/Banco/transferenciabancaria/save_autorizacion', 'contable\TransferenciaBancariaController@save_autorizacion')->name('transferenciabancaria.save_autorizacion');

Route::post('contable/Banco/transferenciabancaria/traer_autorizaciones', 'contable\TransferenciaBancariaController@traer_autorizaciones')->name('transferenciabancaria.traer_autorizaciones');

Route::post('contable/Banco/transferenciabancaria/traer_datos_genera', 'contable\TransferenciaBancariaController@traer_datos_generales')->name('transferenciabancaria.traer_datos_genera');





