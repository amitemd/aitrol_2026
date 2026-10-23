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
 
Route::get('orden_proc/crear_editar/{hcid}', 'hc_admision\Orden_ProcController@crear_editar')->name('orden_proc.crear_editar');

Route::get('orden_proc/imprimir_orden/{id}', 'hc_admision\Orden_ProcController@imprimir_orden')->name('orden_proc.imprimir_orden');
Route::match(['get', 'post'],'orden_proc/imprimir_orden/{id}/guardar', 'hc_admision\Orden_ProcController@guardar')->name('orden_proc.guardar');
Route::get('orden_proc/imprimir_orden/{hcid}/validaexiste/{id}', 'hc_admision\Orden_ProcController@existe')->name('orden_proc.existe');
Route::get('orden_proc/imprimir_orden/crear_detalle/{hcid}/{id}', 'hc_admision\Orden_ProcController@crear_detalle')->name('orden_proc.crear_detalle');
Route::get('orden_proc/imprimir_orden/elimina/{hcid}/{id}', 'hc_admision\Orden_ProcController@eliminar')->name('orden_proc.eliminar');


//Nueva Funcionalidad
//ORDENES DE PROCEDIMIENTO
//Ruta para Historial de Ordenes de Procedimiento Endoscopico, Funcional, Imagenes
Route::get('orden_proc/historial/ordenes/{id_paciente}', 'hc_admision\Orden_ProcController@historial_ordenes')->name('paciente.historial_ordenes');

//Para Imprimir la Orden de Procedimientos
Route::get('imprimir/orden_hc3/general/{id_orden}','hc_admision\Orden_ProcController@imprimir_orden_hc3')->name('imprimir.ordenes_hc3');

/* Excel para 053 */
Route::get('imprimir/orden_053/general/excel/{id}','hc_admision\Orden_ProcController@excel_053_nuevo')->name('imprimir.excel_053_nuevo');

//LABORATORIO
//Ruta para Historial de Ordenes de Laboratorio
Route::get('orden_lab/historial/ordenes/laboratorio/{id_paciente}', 'hc_admision\Orden_ProcController@historial_ordenes_Laboratorio')->name('paciente.historial_orden_lab');

Route::get('paciente/historial/ordenes/laboratorio', 'hc_admision\Orden_ProcController@historial_ordenes_paciente')->name('paciente.historial_orden_lab_paciente');
//Ordenes de laboratorio
Route::get('paciente/historial/ordenes/examenes', 'hc_admision\OrdenesExamenesController@historial_examenes')->name('paciente.historial_examenes');
Route::get('orden_012/cargar/{evol}', 'hc_admision\Orden_ProcController@carga_012')->name('orden_012.carga_012');
Route::match(['get', 'post'],'orden_012/cargar/actualizar', 'hc_admision\Orden_ProcController@actualizar')->name('orden_012.actualizar');
Route::post('orden_012/cargar/ci10', 'hc_admision\Orden_ProcController@carga_012_c10')->name('orden_012.carga_012_c10');
Route::get('orden_012/cargar/ci10/eliminar/{id}', 'hc_admision\Orden_ProcController@c012_c10eli')->name('orden_012.012_c10eli');
Route::get('orden_012/imprimir/{id}', 'hc_admision\Orden_ProcController@imprimir_012')->name('orden_012.imprimir_012');
Route::get('orden_012/imprimir_excel/{id}', 'hc_admision\Orden_ProcController@imprimir_012_excel')->name('orden_012.imprimir_012_excel');

//ORDENES DE BIOPSIAS PACIENTE
Route::get('orden_biopsias/historial_orden/biopsias/{id_paciente}', 'hc_admision\Orden_ProcController@historial_ordenes_biopsias')->name('paciente.historial_orden_biopsias');

//IMPRIMIR ORDEN DE BIOPSIA
Route::get('orden_biopsia/imprimir/biopsias/{id}/{id_hcid}/{id_doct}', 'hc_admision\Orden_ProcController@imprime_orden_biopsia')->name('imprimir.orden_biopsias_recepcion');

Route::get('orden_biopsia/seleccionar/modal/{id_orden}', 'hc_admision\Orden_ProcController@modal_biopsia')->name('orden_proc.modal_biopsia');

Route::match(['get', 'post'], 'nuevo_orden_procedimientos', 'hc_admision\Orden_ProcController@nuevo_historial_ordenes')->name('orden_proc.nuevo_historial_ordenes');
Route::get('nuevo_orden_procedimientos/{id_paciente}',  'hc_admision\Orden_ProcController@index_historial_ordenes')->name('orden_proc.index_historial_ordenes');
Route::get('nuevo_orden_procedimientos/orden/{id_orden}', 'hc_admision\Orden_ProcController@nuevo_orden')->name('orden_proc.nuevo_orden');
Route::match(['get', 'post'], 'ordenes/biopsia', 'hc_admision\Orden_ProcController@orden_biopsia')->name('ordenes_biopsia.orden_biopsia');
Route::get('consulta/ordenes/estado/{id}/{tipo}', 'OrdenesListadoController@estado')->name('ordeneslistado.estado');
Route::get('orden/cambio/seguro/privado/{id}/{id_tipo}', 'OrdenesListadoController@modal_seguro')->name('ordeneslistado.modal_seguro');
Route::post('orden/guardar/seguro/pdf', 'OrdenesListadoController@guardar_seguro')->name('ordeneslistado.guardar_seguro');

Route::post('orden/solicitar/credito/{id_orden_tipo}', 'OrdenesListadoController@solicitar_credito')->name('ordeneslistado.solicitar_credito');

Route::get('orden/cambio/seguro/credito/{id}/{id_tipo}', 'OrdenesListadoController@modal_credito')->name('ordeneslistado.modal_credito');
