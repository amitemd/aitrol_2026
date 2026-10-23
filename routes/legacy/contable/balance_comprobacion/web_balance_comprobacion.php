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
Route::match(['get', 'post'], 'contable/contabilidad/balance_comprobacion', 'contable\BalanceComprobacionController@index')->name('balance_comprobacion.index');
Route::match(['get', 'post'], 'contable/contabilidad/balance_comprobacion/show', 'contable\BalanceComprobacionController@show')->name('balance_comprobacion.show');
