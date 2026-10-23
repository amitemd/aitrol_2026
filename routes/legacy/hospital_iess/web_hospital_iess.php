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

//06-04-2018
Route::get('iess_consultorio/{id}/{unix}', 'hospital_iess\Iess_consultorioController@crear_consulta')->name('iess_consultorio.crear_consulta');
Route::post('iess_consultorio/agendar', 'hospital_iess\Iess_consultorioController@crear')->name('iess_consultorio.crear');

Route::resource('hospitalizados', 'hospital_iess\HospitalizadosController');
Route::get('hospitalizados/inactivar/{id}','hospital_iess\HospitalizadosController@inactivar')->name('hospitalizados.inactivar');
Route::get('hospitalizados/update/{id}','hospital_iess\HospitalizadosController@update2')->name('hospitalizados.update2');
Route::get('hospitalizados/alta/{id}','hospital_iess\HospitalizadosController@alta')->name('hospitalizados.alta');
Route::get('hospitalizados/altas/index','hospital_iess\HospitalizadosController@altas')->name('hospitalizados.altas');
Route::get('hospitalizados/buscapaciente/{id}','hospital_iess\HospitalizadosController@buscapaciente')->name('hospitalizados.buscapaciente');
Route::match(['get', 'post'],'hospitalizados/buscar', 'hospital_iess\HospitalizadosController@buscar')->name('hospitalizados.buscar');
Route::match(['get', 'post'],'hospitalizados/buscar2', 'hospital_iess\HospitalizadosController@buscar2')->name('hospitalizados.buscar2');
Route::get('hospitalizados/altas/log/{log}','hospital_iess\HospitalizadosController@log')->name('hospitalizados.log');
Route::match(['get', 'post'],'hospitalizados/reporte/index', 'hospital_iess\HospitalizadosController@reporte')->name('hospitalizados.reporte');
Route::match(['get', 'post'],'hospitalizados/reporte/excel', 'hospital_iess\HospitalizadosController@excel')->name('hospitalizados.excel');
