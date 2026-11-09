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
Route::post('historiaclinica/epicrisis/actualiza', 'hc_admision\EpicrisisController@actualiza')->name('epicrisis.actualiza');
Route::get('historiaclinica/epicrisis/imprimir/pdf/{hcid}', 'hc_admision\EpicrisisController@imprimir')->name('epicrisis.imprimir');
Route::get('cie10/seleccion', 'hc_admision\EpicrisisController@seleccion')->name('epicrisis.seleccion');
Route::get('historiaclinica/diagnostico/{hcid}', 'hc_admision\EpicrisisController@diagnostico')->name('epicrisis.diagnostico');
Route::match(['get', 'post'],'historiaclinica/cie10/nombre/1', 'hc_admision\EpicrisisController@cie10_nombre')->name('epicrisis.cie10_nombre');
Route::match(['get', 'post'],'historiaclinica/cie10/nombre/2', 'hc_admision\EpicrisisController@cie10_nombre2')->name('epicrisis.cie10_nombre2');
Route::post('cie10/agregar', 'hc_admision\EpicrisisController@agregar_cie10')->name('epicrisis.agregar_cie10');

Route::get('cie10/cargar/{id}', 'hc_admision\EpicrisisController@cargar')->name('epicrisis.cargar');
Route::delete('cie10/eliminar/{id}', 'hc_admision\EpicrisisController@eliminar')->name('epicrisis.eliminar');

Route::get('historiaclinica/epicrisis/imprimir_stream/pdf/{hcid}', 'hc_admision\EpicrisisController@imprimir_stream')->name('epicrisis.imprimir_stream');

//epicrisis final
Route::get('epicrisis/final/{id_solicitud}', 'hc_admision\EpicrisisController@mostrar_final')->name('epicrisis.mostrar_final');
Route::post('epicrisis/final/{id_solicitud}/inicializar', 'hc_admision\EpicrisisController@inicializar_final')->name('epicrisis.inicializar_final');
Route::get('epicrisis/final/{id_solicitud}/pdf', 'hc_admision\EpicrisisController@pdf_final')->name('epicrisis.pdf_final');

Route::post('epicrisis/final/actualiza', 'hc_admision\EpicrisisController@actualiza_final')->name('epicrisis.actualiza_final');
Route::post('epicrisis/final/cerrar', 'hc_admision\EpicrisisController@cerrar_final')->name('epicrisis.cerrar_final');
Route::post('epicrisis/final/confirmar-salida', 'hc_admision\EpicrisisController@confirmar_salida_final')->name('epicrisis.confirmar_salida_final');
Route::post('cie10/final/agregar', 'hc_admision\EpicrisisController@agregar_cie10_final')->name('epicrisis.agregar_cie10_final');
Route::delete('cie10/final/eliminar/{id}', 'hc_admision\EpicrisisController@eliminar_final')->name('epicrisis.eliminar_final');
Route::get('cie10/final/cargar/{id}', 'hc_admision\EpicrisisController@cargar_final')->name('epicrisis.cargar_final');









