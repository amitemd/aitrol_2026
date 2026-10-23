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
Route::match(['get', 'post'], 'contable/Banco/notadebito', 'contable\NotaDebitoController@index')->name('notadebito.index');


Route::get('contable/Banco/notadebito/crear/', 'contable\NotaDebitoController@crear')->name('notadebito.crear');
Route::post('contable/Banco/notadebito/guardar', 'contable\NotaDebitoController@store')->name('notadebito.store');
Route::get('contable/Banco/notadebito/revisar/{id}', 'contable\NotaDebitoController@revisar')->name('notadebito.revisar');
Route::match(['get', 'post'],'contable/Banco/notadebito/buscar', 'contable\NotaDebitoController@buscar')->name('notadebito.buscar');
Route::post('contable/Banco/notadebito/buscar/asiento', 'contable\NotaDebitoController@buscarasiento')->name('notadebito.buscar_asiento');

Route::get('contable/Banco/notadebito/imprimir/{id}', 'contable\NotaDebitoController@imprimir')->name('notadebito.imprimir');
Route::get('contable/banco/notadebito/imprimir_nuevo/{id}', 'contable\NotaDebitoController@imprimir_nuevo')->name('notadebito.imprimir_nuevo');


Route::get('contable/Banco/notadebito/anular/{id}', 'contable\NotaDebitoController@anular')->name('notadebito.anular');
Route::get('contable/Banco/notadebito/exportar_excel', 'contable\NotaDebitoController@exportar_excel')->name('notadebito.exportar_excel');
