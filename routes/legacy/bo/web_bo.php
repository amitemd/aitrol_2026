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

// consulta máster

Route::get('bo/solicitud', 'bo\SolicitudController@index')->name('solicitud.index');
Route::get('bo/listado', 'bo\SolicitudController@listado')->name('solicitud.listado');
Route::get('bo/data', 'bo\SolicitudController@data')->name('solicitud.data');
Route::post('bo/solicitud/crear', 'contable\FacturaController@factura_store')->name('factura.store');

Route::get('privados/agenda', 'bo\SolicitudController@agenda')->name('solicitud.agenda');
Route::get('privados/calendario/{id}/{fecha}', 'bo\SolicitudController@calendario')->name('solicitud.calendario');
Route::get('privados/agendar/{doc}/{fecha}/{pac}', 'bo\SolicitudController@agendar')->name('solicitud.agendar');
Route::post('privados/store', 'bo\SolicitudController@guarda_agenda')->name('solicitud.guarda_agenda');
Route::post('privados/paciente/store', 'bo\SolicitudController@guarda_paciente')->name('solicitud.guarda_paciente');
Route::match(['GET', 'POST'], 'privados/paciente/search', 'bo\SolicitudController@search_paciente')->name('solicitud.search_paciente');
Route::get('privados/agendar/paciente/{id}/{i}/{fecha}', 'bo\SolicitudController@paciente')->name('solicitud.paciente');
Route::get('privados/agendar/nombre/paciente/{id}/{fecha}', 'bo\SolicitudController@nombre_paciente')->name('solicitud.nombre_paciente');
Route::get('privados/editar/{id}/{doc}', 'bo\SolicitudController@editar_agenda')->name('solicitud.editar_agenda');
Route::match(['GET', 'POST'], 'privados/actualiza/{id}/{doctor}', 'bo\SolicitudController@actualiza_agenda')->name('solicitud.actualiza_agenda');
Route::match(['GET', 'POST'], 'privados/search_consulta', 'bo\SolicitudController@search_consulta')->name('solicitud.search_consulta');
Route::match(['GET', 'POST'], 'privados_ag/reporte', 'bo\SolicitudController@reporte')->name('solicitud.reporte');
Route::get('privados/consulta', 'bo\SolicitudController@consulta')->name('solicitud.consulta');

Route::get('contable/empresas/editar/{id}', 'contable\FacturaController@empresa_editar')->name('empresa.editar');
Route::get('contable/empresas/facturacion/{id}', 'contable\FacturaController@facturas')->name('factura.index');
Route::get('contable/empresas/facturacion/crear/{id}', 'contable\FacturaController@factura_crear');
