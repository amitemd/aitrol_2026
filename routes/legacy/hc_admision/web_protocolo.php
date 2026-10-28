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

Route::get('historiaclinica/protocolo/{ag}', 'hc_admision\ProtocoloController@mostrar')->name('protocolo.mostrar');
Route::post('historiaclinica/protocolo/crea_actualiza/', 'hc_admision\ProtocoloController@crea_actualiza')->name('protocolo.crea_actualiza');

Route::post('historiaclinica/protocolo/fotos2/ingresar/', 'hc_admision\ProtocoloController@ingreso_foto')->name('hc_protocolo.fotos');

Route::get('historiaclinica/protocolo/imprime/{id}', 'hc_admision\ProtocoloController@imprime')->name('hc_protocolo.imprime');

// rutas para Registro de Biopsias
Route::post('biopsias_paciente/actualizar', 'Paciente_BiopsiaController@guardar_actualizar')->name('ciudad.guardar_actualizar');
Route::resource('biopsias_paciente', 'Paciente_BiopsiaController')->only(['index', 'create']);
Route::match(['get', 'post'], 'biopsias_paciente/search', 'BiopsiaPacienteController@search')->name('biopsias_paciente.search');

Route::match(['get', 'post'], 'masterhc', 'hc_admision\ProtocoloController@masterhc')->name('masterhc.masterhc');
Route::get('masterhc/detalle/{id}', 'hc_admision\ProtocoloController@detallehc')->name('masterhc.detallehc');
Route::match(['get', 'post'], 'masterhc/search', 'hc_admision\ProtocoloController@search')->name('masterhc.search');
Route::get('masterhc/detalle/consulta/{id}', 'hc_admision\ProtocoloController@detalle_consulta')->name('masterhc.detalle_consulta');

Route::get('protocolo_oper/modal/{id}', 'hc_admision\ProtocoloController@pr_modal')->name('protocolo.pr_modal');
Route::post('protocolo_oper/modal/guardar_op', 'hc_admision\ProtocoloController@guardar_op')->name('protocolo.guardar_op');
Route::post('protocolo_cpre_eco/modal/guardar_op_cpre_eco', 'hc_admision\ProtocoloController@guardar_op_cpre_eco')->name('protocolo_cpre_eco.guardar_op_cpre_eco');

// 06/02/2019 modulos de cpre y eco

Route::get('protocolo_cpre_eco/modal/{hcid}', 'hc_admision\ProtocoloController@modal_cpre_eco')->name('protocolo_cpre_eco.modal_cpre_eco');

// 07/02/2019

Route::post('protocolo_cpre_eco/modal/', 'hc_admision\ProtocoloController@modal_crear_editar')->name('protocolo_cpre_eco.modal_crear_editar');

Route::get('protocolo_training/{training}/{protocolo}/{n}', 'hc_admision\ProtocoloController@crear_training')->name('protocolo_training.crear_training');

// PARA HC4
Route::get('procedimiento/selecciona/{tipo}/{paciente}', 'hc_admision\ProtocoloController@selecciona_procedimiento')->name('hc4_procedimiento.selecciona_procedimiento');
Route::post('procedimiento/crear', 'hc_admision\ProtocoloController@crear_procedimiento')->name('hc4_procedimiento.crear.protocolo');

Route::get('empresa/historiaclinica/cargar/{id_proc}/{id_agenda}/{id_seguro}', 'hc_admision\ProtocoloController@cargar_empresa')->name('historia.cargar_empresa');

// produccion Doctores
Route::get('produccion/doctores/', 'hc_admision\ProtocoloController@produccion_mes')->name('produccion.produccion_mes');
Route::get('subir/excel_valores/', 'hc_admision\ProtocoloController@subir_excel')->name('subir.excel_valores');
Route::get('cruzar/historiaclinica', 'hc_admision\ProtocoloController@cruzar_historiaclinica')->name('cruzar.historiaclinica');

Route::get('produccion/estadistico/index', 'hc_admision\ProtocoloController@produccion_estad')->name('produccion.produccion_estad');
Route::get('produccion/estadistico/index_js', 'hc_admision\ProtocoloController@estad_index')->name('produccion.estad_index');
Route::get('produccion/estadistico/mes/{anio}/{mes}', 'hc_admision\ProtocoloController@estad_mes')->name('produccion.estad_mes');
Route::get('produccion/estadistico/orden/mes/{anio}/{mes}', 'hc_admision\ProtocoloController@estad_mes_orden')->name('produccion.estad_mes_orden');

Route::match(['get', 'post'], 'ordenes/master', 'hc_admision\ProtocoloController@ordenes_master')->name('ordenes.master');
Route::match(['get', 'post'], 'ordenes/master/excel', 'hc_admision\ProtocoloController@ordenes_excel')->name('ordenes.master_xls');
Route::match(['get', 'post'], 'valores/master', 'hc_admision\ProtocoloController@valores_master')->name('valores.master');

Route::get('valores/proceso_cuadre', 'hc_admision\ProtocoloController@proceso_cuadre')->name('valores.proceso_cuadre');
Route::get('produccion/estadistico/anio_mes_doc/{anio}/{mes}', 'hc_admision\ProtocoloController@anio_mes_doc')->name('produccion.anio_mes_doc');
// Factura de Venta
Route::get('contable/fact/venta/{id}', 'contable\VentasController@crear')->name('factura.venta');
// Ordenes reporte/ordenes/doctor/1306579234
Route::get('reporte/ordenes/doctor/{id}', 'hc_admision\ProtocoloController@reporte_ordenes')->name('produccion.reporte_ordenes');

// Reporte de procedimientos Solicitado por Roger
Route::get('hc_procedimientos/reporte/general', 'hc_admision\ProcedimientosController@reporte_procedimientos')->name('procedimientos.reporte_procedimientos');
