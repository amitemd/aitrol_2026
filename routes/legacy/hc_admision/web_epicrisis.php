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
 
//consulta máster



Route::get('historiaclinica/epicrisis/{hcid}/{proc}', 'hc_admision\EpicrisisController@mostrar')->name('epicrisis.mostrar');
Route::match(['get', 'post'],'historiaclinica/epicrisis/actualiza', 'hc_admision\EpicrisisController@actualiza')->name('epicrisis.actualiza');
Route::get('historiaclinica/epicrisis/imprimir/pdf/{hcid}', 'hc_admision\EpicrisisController@imprimir')->name('epicrisis.imprimir');
Route::get('cie10/seleccion', 'hc_admision\EpicrisisController@seleccion')->name('epicrisis.seleccion');
Route::get('historiaclinica/diagnostico/{hcid}', 'hc_admision\EpicrisisController@diagnostico')->name('epicrisis.diagnostico');
Route::match(['get', 'post'],'historiaclinica/cie10/nombre/1', 'hc_admision\EpicrisisController@cie10_nombre')->name('epicrisis.cie10_nombre');
Route::match(['get', 'post'],'historiaclinica/cie10/nombre/2', 'hc_admision\EpicrisisController@cie10_nombre2')->name('epicrisis.cie10_nombre2');
Route::match(['get', 'post'],'cie10/agregar', 'hc_admision\EpicrisisController@agregar_cie10')->name('epicrisis.agregar_cie10');

Route::get('cie10/cargar/{id}', 'hc_admision\EpicrisisController@cargar')->name('epicrisis.cargar');
Route::get('cie10/eliminar/{id}', 'hc_admision\EpicrisisController@eliminar')->name('epicrisis.eliminar');

Route::get('historiaclinica/epicrisis/imprimir_stream/pdf/{hcid}', 'hc_admision\EpicrisisController@imprimir_stream')->name('epicrisis.imprimir_stream');














