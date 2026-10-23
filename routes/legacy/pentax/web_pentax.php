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

Route::get('/pentax', 'PentaxController@pentax')->name('pentax.pentax');
Route::get('/pentax/buscar/{fecha}', 'PentaxController@pentax2')->name('pentax.pentax2'); //14/05/2018
Route::get('/pentax/index', 'PentaxController@index')->name('pentax.index');
Route::get('/pentax/index/{fecha}', 'PentaxController@index')->name('pentax.index2');
Route::get('/pentax/actualiza', 'PentaxController@actualiza')->name('pentax.actualiza');
Route::get('/pentax/edit', 'PentaxController@estado')->name('pentax.edit');
Route::get('/pentax/edit/{id}/{est}/{fec}', 'PentaxController@edit')->name('pentax.edit2');
Route::get('/pentax/doctor', 'PentaxController@doctor')->name('pentax.doctor');
Route::get('/pentax/doctor/{id}', 'PentaxController@doctor')->name('pentax.doctor2');
Route::get('/pentax/actualizasala', 'PentaxController@actualiza_sala')->name('pentax.actualiza_sala');
Route::get('/pentax/actualizasala/{id}/{vsala}/{fec}', 'PentaxController@actualiza_sala')->name('pentax.actualiza_sala2');
Route::get('/pentax/log/{id}', 'PentaxController@log')->name('pentax.log');
Route::get('/pentax/reporte', 'PentaxController@reporte_pentax')->name('pentax.reporte');
Route::get('/pentax/reporte/{fecha}', 'PentaxController@reporte_pentax')->name('pentax.reporte2');
//original
Route::get('/pentaxtv', 'PentaxController@pentaxtv')->name('pentax.pentaxtv');
Route::get('/pentaxtv/index', 'PentaxController@indextv')->name('pentax.indextv');
Route::get('/pentaxtv/index/{fecha}', 'PentaxController@indextv')->name('pentax.index2tv');

Route::get('/pentaxtv_dr', 'PentaxController@pentaxtv_dr')->name('pentax.pentaxtv_dr');
Route::get('/pentaxtv_dr/index', 'PentaxController@indextv_dr')->name('pentax.indextv_dr');
Route::get('/pentaxtv_dr/index/{fecha}', 'PentaxController@indextv_dr')->name('pentax.index2tv_dr');
//reporteagenda
Route::match(['get', 'post'], 'pentax/reporteagenda', 'PentaxController@reporteagenda')->name('pentax.reporteagenda');
Route::post('pentax/excel', 'PentaxController@excel')->name('pentax.excel');

Route::get('/procedimientos_dr', 'PentaxController@procedimientos_dr')->name('procedimientos_dr.procedimientos_dr');
Route::get('/procedimientos_dr/buscar/{fecha}', 'PentaxController@procedimientos_dr2')->name('procedimientos_dr.procedimientos_dr2');
Route::get('/procedimientos_dr/index', 'PentaxController@index_dr')->name('procedimientos_dr.index_dr');
Route::get('/procedimientos_dr/index/{fecha}', 'PentaxController@index_dr')->name('pentax.index_dr2');
Route::get('/procedimientos_dr/edit', 'PentaxController@estado_dr')->name('procedimientos_dr.edit_dr');
Route::get('/procedimientos_dr/edit/{id}/{est}/{fec}', 'PentaxController@edit_dr')->name('procedimientos_dr.edit_dr2');
Route::get('/procedimientos_dr/actualiza', 'PentaxController@actualiza_dr')->name('procedimientos_dr.actualiza_dr');

Route::get('/procedimientostv_dr', 'PentaxController@procedimientostv_dr')->name('procedimientostv_dr.procedimientostv_dr');
Route::get('/procedimientostv_dr/index', 'PentaxController@indextv_drH')->name('procedimientostv_dr.indextv_drH');
Route::get('/procedimientostv_dr/index/{fecha}', 'PentaxController@indextv_drH')->name('procedimientostv_dr.indextv_drH2');

//NUEVO 109 EN PENTAX
Route::get('/pentaxtv_dr/index_109/{fecha}', 'PentaxController@indextv_dr_109')->name('pentax.indextv_dr_109');

//PANTALLAS CONSULTORIOS PRMD. LOPEZ
Route::match(['get', 'post'], '/consulta_tv', 'PentaxController@consultatv')->name('pentax.consultatv');