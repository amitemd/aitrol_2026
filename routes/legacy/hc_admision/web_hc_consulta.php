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



Route::get('historiaclinica/consulta/{hcid}', 'hc_admision\ConsultaController@consulta')->name('consulta.evolucion');
Route::match(['get', 'post'],'historiaclinica/consulta', 'hc_admision\ConsultaController@actualizar')->name('consulta.actualizar');
Route::get('historiaclinica/consulta/consulta_sig_ant/{id}/{ag}', 'hc_admision\ConsultaController@consulta_sig_ant')->name('consulta.consulta_sig_ant');
Route::match(['get', 'post'],'consulta/actualiza_historia', 'hc_admision\ConsultaController@actualiza_historia')->name('consulta.actualiza_historia');

Route::match(['get', 'post'],'consulta/actualiza_historia', 'hc_admision\ConsultaController@actualiza_historia')->name('consulta.actualiza_historia');

Route::get('empresa2/historiaclinica/cargar/{id_proc}/{id_agenda}/{id_seguro}', 'hc_admision\ConsultaController@cargar_empresa2')->name('historia.cargar_empresa2');

 