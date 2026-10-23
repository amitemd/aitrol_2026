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

Route::match(['get', 'post'], 'consultam/pastelpentax', 'ConsultaMController@pastelpentax')->name('consultam.pastelpentax');
Route::match(['get', 'post'], 'consultam/search', 'ConsultaMController@search')->name('consultam.search');
Route::match(['get', 'post'], 'consultam/pastelpentax/buscar', 'ConsultaMController@search2')->name('consultam.search2');
Route::get('consultam/pentax', 'ConsultaMController@pentax')->name('consultam.pentax');
Route::match(['get', 'post'], 'consultam/pentax', 'ConsultaMController@pentax')->name('consultam.pentax');
Route::match(['get', 'post'], 'consultam/index/reporte', 'ConsultaMController@reporte')->name('consultam.reporte');
Route::match(['get', 'post'], 'pconsultam/index/reporte_paquetes', 'ConsultaMController@reporte_paquetes')->name('consultam.reporte_paquetes');
Route::match(['get', 'post'], 'econsultam/reporte_estados', 'ConsultaMController@reporte_estados')->name('consultam.reporte_estados');

//Reporte de Biopsias
Route::match(['get', 'post'], 'consultam/index/reporte/biopsias', 'ConsultaMController@reporte_biosias')->name('consultam.reporte_biopsias');

Route::match(['get', 'post'], 'consultam/index/reporte2', 'ConsultaMController@reporte2')->name('consultam.reporte2');

Route::resource('consultam', 'ConsultaMController');
Route::get('consultam/detalle/{id}', 'ConsultaMController@detalle')->name('consultam.detalle');
Route::get('consultam/detalle/consulta_documentos/{hcid}', 'ConsultaMController@consulta_documentos')->name('consultam.consulta_documentos');

Route::get('consultam/detalle_ag/{id}/{unix}', 'ConsultaMController@detalle_ag')->name('consultam.detalle_ag');

//reporteagenda cambios 08052018
Route::match(['get', 'post'], 'consultam/procedimiento/reporte', 'ConsultaMController@reporteagenda')->name('consultam.reporteagenda');
Route::match(['get', 'post'], 'consultam/procedimiento/reporte2', 'ConsultaMController@reporteagenda2')->name('consultam.reporteagenda2');
Route::post('consultam/excel', 'ConsultaMController@excel')->name('consultam.excel');
Route::post('consultam/excel2', 'ConsultaMController@excel2')->name('consultam.excel2');

Route::get('consultam/log_agenda/{id}', 'ConsultaMController@log_agenda')->name('consultam.log_agenda');
//Reporte Consultas
Route::match(['get', 'post'], 'consultam/index/reporte/control_consultas', 'ConsultaMController@control_consultas')->name('consultam.control_consultas');
//Reporte Calidad 27/05/2021
Route::match(['get', 'post'], 'consultam/reportetiempo', 'ConsultaMController@tiempoReporte')->name('consultam.reportetiempo');

Route::match(['get', 'post'], 'consultam/reporte/excel', 'ConsultaMController@excel_reporte')->name('consultam.reporte_excel');
//Ordenes Fausto 7/06/2021
Route::match(['get', 'post'],'consulta/ordernes/revisarhoy', 'OrdenesListadoController@index')->name('consulta_ordenes.index');
Route::get('consulta/ordernes/numero', 'OrdenesListadoController@cantidad')->name('consulta_ordenes.numero'); 

Route::get('training/procedimeintos/{id_fellow}', 'ConsultaMController@reporte_fellows')->name('consultam.reporte_fellows'); 

//Reportes Fellows
Route::get('consultas/reporte/index', 'ConsultaMController@index_rfellows')->name('consultas.index_rfellows');
Route::match(['get', 'post'], 'consultas/reporte/descargar', 'ConsultaMController@descargar_rfellows')->name('consultas.descargar_rfellows');

Route::match(['get', 'post'], 'consultam/reporte/tiempo', 'ConsultaMController@vistaReporte')->name('consultam.vista_reporte_tiempo');
Route::match(['get', 'post'], 'consultam/reporte/tiempo/search', 'ConsultaMController@searchReporte')->name('consultam.search_reporte_tiempo');
//Reporte insumos utilizados
Route::get('consultam/reporte/insumos/utilizados/{id}', 'ConsultaMController@reporte_insumos_procedimientos')->name('consultam.reporte_insumos_procedimientos');

Route::match(['get', 'post'], 'consultam/reporte/tiempos', 'ConsultaMController@vistaReporteSucursales')->name('consultam.vista_reporte_tiempo_sucursal');
Route::match(['get', 'post'], 'consultam/reporte/tiempos/search', 'ConsultaMController@searchReporteSucursales')->name('consultam.search_reporte_tiempo_sucursal');
Route::match(['get', 'post'], 'consultam/reportetiempos', 'ConsultaMController@tiempoReporteSucursales')->name('consultam.reportetiempo_sucursal');
