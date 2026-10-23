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
//FINANCIERO
//Route::resource('financiero', 'financiero\FinancieroController');

//rutas de indicador financiero consolidado
Route::get('financiero/indicador/consolidado', 'financiero\IndicadorFinancieroController@indicadorconsolidado')->name('financiero.indicadorconsolidado');
Route::post('financiero/indicador/consolidado/resultado', 'financiero\IndicadorFinancieroController@resultado')->name('indicadorconsolidado.resultado');

Route::get('financiero/proyeccion/financiera', 'financiero\ProyeccionFinancieraController@proyeccionfinanciera')->name('financiero.proyeccionfinanciera');
Route::post('financiero/proyeccion/financiera/resultado', 'financiero\ProyeccionFinancieraController@resultado')->name('proyeccionfinanciera.resultado');
//rutas de estado de resultados consolidados
Route::get('financiero/estado/resultados/consolidado', 'financiero\FinancieroController@estadoresultados')->name('financiero.estadoresultados');
Route::post('financiero/estado/resultados/consolidado/resultado', 'financiero\FinancieroController@resultado')->name('estadoresultados.resultado');

Route::get('financiero/estado_esi', 'financiero\FinancieroController@estado_esi')->name('financiero.estado_esi');
//rutas de estado de situacion
Route::get('financiero/estado/situacion/consolidado', 'financiero\EstadoSituacionController@index')->name('financiero.estadosituacion');
Route::match(['get', 'post'], 'financiero/estado/situacion/consolidado/show', 'financiero\EstadoSituacionController@show')->name('financiero.estadosituacion.show');
//Indice_financiero
Route::match(['get', 'post'], 'financiero/indice/financiero/', 'financiero\Indice_FinancieroController@indicefinanciero_index')->name('financiero.indicefinanciero_index');
Route::match(['get', 'post'], 'financiero/indice/financiero/excel', 'financiero\Indice_FinancieroController@excel_indicefinanciero_index')->name('indicefinanciero_index.excel');
//Proyeccion financiera II

Route::match(['get', 'post'], 'financiero/proyeccion_financiera2/proyeccionfinanciera2_index', 'financiero\Proyeccion_Financiera2Controller@proyeccionfinanciera2_index')->name('financiero.proyeccionfinanciera2_index');
Route::post('financiero/proyeccion_financiera2/proyeccionfinanciera2_index/resultado', 'financiero\Proyeccion_Financiera2Controller@resultado')->name('proyeccionfinanciera2_index.resultado');
//PROYECCION FINNACIERA III
Route::match(['get', 'post'], 'financiero/proyeccionfinanciera3', 'financiero\Proyeccion_Financiera3Controller@proyeccionfinanciera3')->name('financiero.proyeccionfinanciera3');
Route::post('financiero/proyeccionfinanciera3/resultado', 'financiero\Proyeccion_Financiera3Controller@resultado')->name('proyeccionfinanciera3.resultado');
