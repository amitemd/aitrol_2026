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
//BALANCE GENERALs
Route::match(['get', 'post'], 'contable/contabilidad/balance_general', 'contable\BalanceGeneralController@index')->name('balance_general.index');

//Route::match(['get', 'post'], 'contable/balance_general', 'contable\BalanceComprobacionController@index')->name('balance_general.index');
Route::match(['get', 'post'], 'contable/contabilidad/balance_general/show', 'contable\BalanceGeneralController@show')->name('balance_general.show');