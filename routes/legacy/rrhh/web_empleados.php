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

Route::resource('empleados', 'rrhh\EmpleadosController')->names([
    'index' => 'rrhh.empleados.index',
    'create' => 'rrhh.empleados.create',
    'store' => 'rrhh.empleados.store',
    'show' => 'rrhh.empleados.show',
    'edit' => 'rrhh.empleados.edit',
    'update' => 'rrhh.empleados.update',
    'destroy' => 'rrhh.empleados.destroy',
]);
Route::get('empleados/documentos/{id}', 'rrhh\EmpleadosController@documentos')->name('empleados.documentos');

Route::get('contable/rol/pago/envio/correo/{id}', 'contable\RolPagoController@rol_pago_envio')->name('rolpago.rol_pago_envio');

// rutas de preparcion de emily
Route::match(['get', 'post'], 'preparaciones/index', 'preparaciones\preparacionesController@index')->name('preparaciones.index');

Route::match(['get', 'post'], 'preparaciones/mostrar/pdf', 'preparaciones\preparacionesController@mostrar_pdf')->name('preparaciones.mostrar_pdf');
Route::get('preparaciones/crear', 'preparaciones\preparacionesController@crear')->name('preparaciones.crear');
Route::post('preparaciones/guardar', 'preparaciones\preparacionesController@guardar_preparaciones')->name('preparaciones.guardar');

Route::get('rrhh/rubros/detalles/nomina/index', 'rrhh\RubrosDetalleNominaController@index')->name('rubrosDetalles.index');
Route::get('rrhh/rubros/detalles/nomina/create', 'rrhh\RubrosDetalleNominaController@create')->name('rubrosDetalles.create');
Route::get('rrhh/rubros/detalles/nomina/edit/{id}', 'rrhh\RubrosDetalleNominaController@edit')->name('rubrosDetalles.edit');
Route::post('rrhh/rubros/detalles/nomina/store', 'rrhh\RubrosDetalleNominaController@store')->name('rubrosDetalles.store');
Route::post('rrhh/rubros/detalles/nomina/update', 'rrhh\RubrosDetalleNominaController@update')->name('rubrosDetalles.update');
Route::get('rrhh/rubros/detalles/asignar/cuenta/{id}', 'rrhh\RubrosDetalleNominaController@asignarCuenta')->name('rubrosDetalles.asignarCuenta');
Route::post('rrhh/rubros/detalles/cuenta/store', 'rrhh\RubrosDetalleNominaController@storeCuenta')->name('rubrosDetalles.storeCuenta');
