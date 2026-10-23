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

//Documento Factura
Route::resource('afInformes', 'activosfijos\InformesController');
Route::match(['get', 'post'],'activojifos/informe/buscar', 'activosfijos\InformesController@buscar')->name('activosfijos.informe.buscar');
// Route::match(['get', 'post'],'activojifos/documentofactura/anular/{id}', 'activosfijos\DocumentoFacturaController@anular')->name('activosfijos.documentofactura.anular');



Route::match(['get', 'post'],'contable/kardex', 'contable\KardexController@kardex')->name('kardex');


// web_sensor cambio de archivo
Route::get('sensor/establecimiento/crear', 'sensor\SensorController@sensor_establecimiento_create')->name('sensor.establecimiento_crear');
Route::post('sensor/establecimiento/store', 'sensor\SensorController@sensor_establecimiento_store')->name('sensor.establecimiento_store');
Route::get('sensor/establecimiento/index', 'sensor\SensorController@sensor_establecimiento_index')->name('sensor.establecimiento_index');
Route::get('sensor/establecimiento/edit/{id}', 'sensor\SensorController@sensor_establecimiento_edit')->name('sensor.establecimiento_edit');
Route::post('sensor/establecimiento/update', 'sensor\SensorController@sensor_establecimiento_update')->name('sensor.establecimiento_update');
Route::get('sensor/establecimiento/delete/{id}','sensor\SensorController@sensor_establecimiento_delete')->name('sensor.establecimiento_delete');

Route::get('sensor/dispositivo/crear', 'sensor\SensorController@sensor_dispositivo_create')->name('sensor.dispositivo_crear');
Route::post('sensor/dispositivo/store', 'sensor\SensorController@sensor_dispositivo_store')->name('sensor.dispositivo_store');
Route::get('sensor/dispositivo/index', 'sensor\SensorController@sensor_dispositivo_index')->name('sensor.dispositivo_index');
Route::get('sensor/dispositivo/edit/{id}', 'sensor\SensorController@sensor_dispositivo_edit')->name('sensor.dispositivo_edit');
Route::post('sensor/dispositivo/update', 'sensor\SensorController@sensor_dispositivo_update')->name('sensor.dispositivo_update');
Route::get('sensor/dispositivo/delete/{id}','sensor\SensorController@sensor_dispositivo_delete')->name('sensor.dispositivo_delete');


//estadistico
Route::match(['get', 'post'],'sensor/estadistico','sensor\SensorController@estadistico_sensor')->name('sensor.estadistico_sensor');

Route::match(['get', 'post'],'sensor/cargar/data','sensor\SensorController@cargar_data')->name('sensor.cargar_data');

Route::post('sensor/buscador', 'sensor\SensorController@buscador_sensor')->name('sensor.buscar_data');