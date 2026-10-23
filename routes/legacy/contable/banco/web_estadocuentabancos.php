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
Route::match(['get', 'post'], 'contable/Banco/estadocuentabancos', 'contable\EstadoCuentaBancosController@index')->name('estadocuentabancos.index');
Route::get('contable/Banco/estadocuentabancos/exportar_excel', 'contable\EstadoCuentaBancosController@exportar_excel')->name('estadocuentabancos.exportar_excel');