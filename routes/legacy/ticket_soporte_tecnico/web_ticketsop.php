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

//Route::resource('tecnicas/{agenda}', 'tecnicas\TecnicasController');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/index', 'Ticket_Soporte_TecnicoController@index')->name('ticket_soporte_tecnico.index');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/create', 'Ticket_Soporte_TecnicoController@create')->name('ticket_soporte_tecnico.create');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/guardar', 'Ticket_Soporte_TecnicoController@guardar')->name('ticket_soporte_tecnico.guardar');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/admin_control', 'Ticket_Soporte_TecnicoController@admin_control')->name('ticket_soporte_tecnico.admin_control'); //actualizar
Route::match(['get', 'post'], 'ticket_soporte_tecnico/control_req/{id}', 'Ticket_Soporte_TecnicoController@control_req')->name('ticket_soporte_tecnico.control_req'); //editar
Route::match(['get', 'post'], 'ticket_soporte_tecnico/excel', 'Ticket_Soporte_TecnicoController@excel_soporte_tecnico')->name('ticket_soporte_tecnico.excel'); //excel
Route::post('ticket_soporte_tecnico/autocompletar', 'Ticket_Soporte_TecnicoController@autocompletar')->name('ticket_soporte_tecnico.autocompletar'); //excel
Route::match(['get', 'post'], 'ticket_soporte_tecnico/buscador', 'Ticket_Soporte_TecnicoController@buscador')->name('ticket_soporte_tecnico.buscador'); //buscador
Route::post('ticket_soporte_tecnico/autocompletar_apellido', 'Ticket_Soporte_TecnicoController@autocompletar_apellido')->name('ticket_soporte_tecnico.autocompletar_apellido');

// mantenimiento

