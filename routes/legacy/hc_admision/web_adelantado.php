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

//consulta 
Route::get('adelantado', 'v3\clinical\AdvancedIntegralController@legacyIndex')->name('adelantado.index');
Route::get('adelantado/detalle/{id}', 'v3\clinical\AdvancedIntegralController@legacyDetail')->name('adelantado.detalle');
Route::get('adelantado/detalle_ag/{id}/{unix}', 'AdelantadoController@detalle_ag')->name('AdelantadoController.detalle_ag');
Route::get('adelantado/detalle/consulta_documentos/{hcid}', 'AdelantadoController@consulta_documentos')->name('adelantado.consulta_documentos');
Route::match(['get', 'post'], 'adelantado/search', 'v3\clinical\AdvancedIntegralController@legacySearch')->name('adelantado.search');
Route::match(['get', 'post'], 'adelantado/index/reporte', 'v3\clinical\AdvancedIntegralController@legacyReport')->name('adelantado.reporte');
Route::match(['get', 'post'], 'adelantado/log_agenda/{id}', 'AdelantadoController@log_agenda')->name('adelantado.log_agenda');


//Reporte de Biopsias
