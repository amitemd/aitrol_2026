<?php


Route::get('disponibilidad/disponibilidad_menu', 'disponibilidad\DisponibilidadController@disponibilidad_menu')->name('disponibilidad.disponibilidad_menu');
Route::get('disponibilidad/sala_opciones/{id}', 'disponibilidad\DisponibilidadController@sala_opciones')->name('disponibilidad.sala_opciones');
Route::match(['get', 'post'],'disponibilidad/salas_todas/{id_hospital}', 'disponibilidad\DisponibilidadController@salas_todas')->name('disponibilidad.salas_todas');
Route::match(['get', 'post'],'disponibilidad/sala_agenda/{id}', 'disponibilidad\DisponibilidadController@sala_agenda')->name('disponibilidad.sala_agenda');
Route::match(['get', 'post'],'disponibilidad/sala_ajax/{id}', 'disponibilidad\DisponibilidadController@sala_ajax')->name('disponibilidad.sala_ajax');

/*
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
