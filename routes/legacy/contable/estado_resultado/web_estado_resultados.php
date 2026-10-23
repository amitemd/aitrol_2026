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
Route::match(['get', 'post'], 'contable/contabilidad/estado/resultados', 'contable\EstadoResultadosController@index')->name('estadoresultados.index');
Route::match(['get', 'post'], 'contable/contabilidad/estado/resultados/sshow', 'contable\EstadoResultadosController@show')->name('estadoresultados.show');


Route::match(['get', 'post'], 'contable/contabilidad/estado/resultados/sshow/pdf', 'contable\EstadoResultadosController@pdf_estado_integral')->name('estadoresultados.pdf_estado_integral');



Route::get('contable/contabilidad/modalcontableUtilidad', 'contable\EstadoResultadosController@modalcontableUtilidad')->name('estadoresultados.modalcontableUtilidad');
Route::get('contable/contabilidad/modalcontablegravable', 'contable\EstadoResultadosController@modalcontablegravable')->name('estadoresultados.modalcontablegravable');
