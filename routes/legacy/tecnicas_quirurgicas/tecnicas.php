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

// Route::resource('tecnicas/{agenda}', 'tecnicas\TecnicasController');
Route::get('tecnicas/{agenda?}', 'tecnicas\TecnicasController@index')->name('tecnicas.index');
Route::get('tecnicas/{agenda}/create', 'tecnicas\TecnicasController@create')->name('tecnicas.create');
Route::get('tecnicas/{agenda}/{id}/edit', 'tecnicas\TecnicasController@edit')->name('tecnicas.edit');
Route::post('tecnicas/agenda/store', 'tecnicas\TecnicasController@store')->name('tecnicas.store');
Route::match(['put', 'patch'], 'tecnicas/{agenda}/procedimientos/update', 'tecnicas\TecnicasController@update')->name('tecnicas.update');
Route::get('tecnicas/documentos/{id}', 'tecnicas\TecnicasController@documentos')->name('tecnicas.documentos');
Route::get('tecnicas/procedimientos/{id}', 'tecnicas\TecnicasController@procedimiento')->name('tecnicas.procedimiento');
Route::post('tecnicas/procedimientos_guardar/', 'tecnicas\TecnicasController@procedimientoguardar')->name('tecnicas.procedimientoguardar');
Route::match(['get', 'post'], 'tecnicas/{agenda}/procedimientos/search/', 'tecnicas\TecnicasController@search')->name('tecnicas.search');
