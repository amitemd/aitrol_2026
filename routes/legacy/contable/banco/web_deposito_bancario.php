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
Route::match(['get', 'post'], 'contable/Banco/depositobancario', 'contable\DepositoBancarioController@index')->name('depositobancario.index');
Route::get('contable/Banco/depositobancario/create/', 'contable\DepositoBancarioController@create')->name('depositobancario.create');
Route::post('contable/Banco/depositobancario/store', 'contable\DepositoBancarioController@store')->name('depositobancario.store');
Route::match(['get', 'post'], 'contable/Banco/depositobancario/buscar', 'contable\DepositoBancarioController@buscar')->name('depositobancario.buscar');
Route::match(['get', 'post'], 'contable/Banco/depositobancario/buscarformapago', 'contable\DepositoBancarioController@buscarformapago')->name('depositobancario.buscarformapago');
Route::get('contable/Banco/depositobancario/anular/{id}', 'contable\DepositoBancarioController@anular')->name('depositobancario.anular');
Route::get('contable/Banco/depositobancario/updates/{id}', 'contable\DepositoBancarioController@update')->name('depositobancario.update');
Route::get('contable/Banco/depositobancario/show/{id}', 'contable\DepositoBancarioController@show')->name('depositobancario.show');
Route::get('contable/Banco/depositobancario/imprimir/{id}', 'contable\DepositoBancarioController@imprimir')->name('depositobancario.imprimir');

Route::match(['get', 'post'], 'contable/Banco/depositobancario/devuelvefloat', 'contable\DepositoBancarioController@devuelvefloat')->name('depositobancario.devuelvefloat');
Route::get('contable/Banco/depositobancario/exportar_excel', 'contable\DepositoBancarioController@exportar_excel')->name('depositobancario.exportar_excel');
