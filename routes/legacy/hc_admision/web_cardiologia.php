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




Route::get('historiaclinica/cardiologia/{ag}', 'hc_admision\CardiologiaController@mostrar')->name('cardiologia.mostrar');
Route::match(['get', 'post'],'historiaclinica/cardiologia/crea_actualiza', 'hc_admision\CardiologiaController@crea_actualiza')->name('cardiologia.crea_actualiza');
Route::get('cardiologia/agenda/{paciente}/{url}', 'hc_admision\CardiologiaController@agenda')->name('cardiologia.agenda');
Route::match(['get', 'post'],'cardiologia/agenda/calendario', 'hc_admision\CardiologiaController@calendario')->name('cardiologia.calendario');
Route::match(['get', 'post'],'cardiologia/agenda/calendario/agendar', 'hc_admision\CardiologiaController@agendar')->name('cardiologia.agendar');
Route::get('cardiologia/agenda/asignacion/{procedimiento}/{cardio}', 'hc_admision\CardiologiaController@asignacion')->name('cardiologia.asignacion');












