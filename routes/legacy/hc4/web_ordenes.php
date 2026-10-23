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

//ORDENES DE PROCEDIMIENTO ENDOSCOPICO

//Para el historial de las Ordenes de Procedimientos Endoscopicos
Route::get('historial_hc4/orden/endoscopicos/{id_paciente}', 'hc4\ordenes\Orden_Proc_EndosController@index')->name('paciente.orden_proc_endoscopico');

//Para crear una Orden de Procedimiento Endoscopico
Route::get('orden_hc4/procedimiento/endoscopico/{tipo}/{paciente}', 'hc4\ordenes\Orden_Proc_EndosController@crear_orden_endoscopico')->name('hc4_orden_proc_endoscopico');

//Para guardar Orden de Procedimiento Endoscopico
Route::post('orden_hc4/guardar/endoscopico','hc4\ordenes\Orden_Proc_EndosController@guardar_orden_endoscopico')->name('guarda.ordenhc4_proendoscopica');

//Editar Orden de Procedimiento Endoscopico
route::get('orden_hc4/ingresar/historial_hc4/', 'hc4\ordenes\Orden_Proc_EndosController@editar_orden')->name('editar.orden_procedimiento_endoscopico');
route::get('orden_hc4/ingresar/historial_hc4/{id_orden}/{id_paciente}', 'hc4\ordenes\Orden_Proc_EndosController@editar_orden');


//Para actualizar Orden de Procedimiento Endoscopico
Route::post('orden_hc4/actualiza/endoscopico','hc4\ordenes\Orden_Proc_EndosController@actualiza_orden_endoscopico')->name('actualiza.ordenhc4_proendoscopica');

//Para Imprimir la Orden de Procedimiento Endoscopico
Route::get('imprimir/orden_hc4/endoscopico/{id_orden}','hc4\ordenes\Orden_Proc_EndosController@imprimir_orden_endoscopica')->name('imprimir.ordenhc4_endoscopica');

//Para Imprimir la Orden de Procedimiento Endoscopico CIR
Route::get('imprimir/orden_hc4/endoscopico/cir/{id_orden}','hc4\ordenes\Orden_Proc_EndosController@imprimir_orden_endoscopica_cir')->name('imprimir.ordenhc4_endoscopica_cir');

//ORDENES DE PROCEDIMIENTO FUNCIONALES

//Para el historial de las Ordenes de Procedimientos Funcionales
Route::get('historial_hc4/orden/funcional/{id_paciente}', 'hc4\ordenes\Orden_Proc_FunController@index')->name('paciente.orden_proc_funcional');

//Para crear una Orden de Procedimiento Funcional
Route::get('orden_hc4/procedimiento/funcional/{tipo}/{paciente}', 'hc4\ordenes\Orden_Proc_FunController@crear_orden_funcional')->name('hc4_orden_proc_funcional');

//Para guardar Orden de Procedimiento Funcional
/*Route::post('orden_hc4/guardar/funcional','hc4\ordenes\Orden_Proc_FunController@guardar_orden_funcional')->name('guarda.ordenhc4_profuncional');*/

//Editar Orden de Procedimiento Funcional
route::get('edita_orden/funcional/historial_hc4/', 'hc4\ordenes\Orden_Proc_FunController@editar_orden_funcional')->name('editar.orden_procedimiento_funcional');
route::get('edita_orden/funcional/historial_hc4/{id_orden}/{id_paciente}', 'hc4\ordenes\Orden_Proc_FunController@editar_orden_funcional');

//Para actualizar Orden de Procedimiento Funcional
Route::post('orden_hc4/actualiza/funcional','hc4\ordenes\Orden_Proc_FunController@actualiza_orden_funcional')->name('actualiza.ordenhc4_profuncional');


//Para Imprimir la Orden de Procedimiento Funcional
Route::get('imprimir/orden_hc4/funcional/{id_orden}','hc4\ordenes\Orden_Proc_FunController@imprimir_orden_funcional')->name('imprimir.ordenhc4_funcional');

//Para Imprimir la Orden de Procedimiento Funcional
Route::get('imprimir/orden_hc4/funcional/cir/{id_orden}','hc4\ordenes\Orden_Proc_FunController@imprimir_orden_funcional_cir')->name('imprimir.ordenhc4_funcional_cir');



//ORDENES DE IMAGENES
//Muestra el historial de las Imagenes
Route::get('historial_hc4/orden/imagenes/{id_paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@index')->name('paciente.orden_proc_imagenes');

//Para crear una Orden de Procedimiento Imagenes
Route::get('orden_hc4/procedimiento/imagenes/{tipo}/{paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@crear_orden_imagenes')->name('hc4_orden_proc_imagenes');

//Para guardar Orden de Imagenes
/*Route::post('orden_hc4/guardar/imagenes','hc4\ordenes\Orden_Proc_ImagenesController@guardar_orden_imagenes')->name('guarda.ordenhc4_proimagenes');*/

//Editar Orden de Procedimiento Imagenes
route::get('edita_orden/imagenes/historial_hc4/', 'hc4\ordenes\Orden_Proc_ImagenesController@editar_orden_imagenes')->name('editar.orden_procedimiento_imagenes');
route::get('edita_orden/imagenes/historial_hc4/{id_orden}/{id_paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@editar_orden_imagenes');

//Para actualizar Orden de Procedimiento Imagenes
Route::post('orden_hc4/actualiza/imagenes','hc4\ordenes\Orden_Proc_ImagenesController@actualiza_orden_imagenes')->name('actualiza.ordenhc4_procedimagenes');

//Para Imprimir la Orden de Procedimiento Imagenes
Route::get('imprimir/orden_hc4/imagenes/{id_orden}','hc4\ordenes\Orden_Proc_ImagenesController@imprimir_orden_imagenes')->name('imprimir.ordenhc4_imagenes');


//Busca Evolucion Llena
route::get('busca_evolucion_imagenes/imagenes', 'hc4\ordenes\Orden_Proc_ImagenesController@buscar_evolucion_imagenes')->name('busca.evolucion_imag');
route::get('busca_evolucion_imagenes/imagenes/{id_paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@buscar_evolucion_imagenes');

//ordenes de laboratorio
route::get('hc4/orden/laboratorio/doctor/index/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@index')->name('hc4_orden_lab.index');
route::get('hc4/orden/laboratorio/doctor/index2/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@index2')->name('hc4_orden_lab.index2');
route::get('hc4/orden/laboratorio/doctor/crear/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@crear')->name('hc4_orden_lab.crear');
route::get('hc4/orden/laboratorio/doctor/editar/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@editar')->name('hc4_orden_lab.editar');
Route::post('hc4/orden/laboratorio/doctor/buscar/{orden}','hc4\ordenes\Orden_LaboratorioController@buscar')->name('hc4_orden_lab.buscar');
Route::post('hc4/orden/laboratorio/doctor/perfil/seleccionar/{orden}','hc4\ordenes\Orden_LaboratorioController@cambia_perfil')->name('hc4_orden_lab.cambia_perfil');
Route::get('deseleccionar/perfil/{id}','hc4\ordenes\Orden_LaboratorioController@deseleccionar_perfil')->name('hc4_orden_lab.deseleccionar_perfil');

//examenes_favoritos
route::get('hc4/laboratorio/examenes/favoritos', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos')->name('hc4_examenes.favoritos');
route::post('hc4/laboratorio/examenes/favoritos/buscar', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_buscar')->name('hc4_examenes.buscar');
route::get('hc4/laboratorio/examenes/favoritos/crear', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_crear')->name('hc4_examenes.crear');
route::get('hc4/laboratorio/examenes/favoritos/editar/{id}', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_editar')->name('hc4_examenes.editar');
route::get('hc4/laboratorio/examenes/favoritos/ver/{id}', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_ver')->name('hc4_examenes.ver');
route::post('hc4/laboratorio/examenes/favoritos/crear/guardar', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_guardar')->name('hc4_examenes.guardar');
route::post('hc4/laboratorio/examenes/favoritos/editar/actualizar', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_actualizar')->name('hc4_examenes.actualizar');
route::get('hc4/laboratorio/examenes/favoritos/crear/guardar/listado/{id}', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_listado')->name('hc4_examenes.listado');
route::post('hc4/laboratorio/examenes/favoritos/editar/actualizar/buscador/{id}', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_buscador')->name('hc4_examenes.buscador');
route::get('hc4/laboratorio/examenes/favoritos/editar/actualizar/seleccionar/{protocolo}/{id}', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_seleccionar')->name('hc4_examenes.seleccionar');
route::get('hc4/laboratorio/examenes/favoritos/editar/actualizar/eliminar/{protocolo}/{id}', 'hc4\ordenes\Orden_LaboratorioController@examenes_favoritos_eliminar')->name('hc4_examenes.eliminar');




//Formato_012 en lado de Doctor
Route::get('hc4/formato012/{id}/{orden}', 'hc4\ordenes\Orden_formato012Controller@formato012')->name('formato_012');
Route::match(['get', 'post'],'orden_012/cargar/actualizar', 'hc4\ordenes\Orden_formato012Controller@actualizar_formato012')->name('orden_012.actualizar');
Route::get('orden/ingresada/formato012', 'hc4\ordenes\Orden_formato012Controller@orden_ingresada_formato012')->name('orden_ingresada_formato012');
//Formato_012 en lado de sistema medico prb
Route::match(['get', 'post'], 'formato012/search', 'hc4\ordenes\Orden_formato012Controller@search')->name('formato012.search');
Route::get('orden_012/cargar/Editar/{id}', 'hc4\ordenes\Orden_formato012Controller@Editar_formato012')->name('orden_012.editar');
Route::match(['get', 'post'],'orden_012/cargar/actualizar/orden_12', 'hc4\ordenes\Orden_formato012Controller@Actualizar_formato_012')->name('actualizar.orden_012');


//pdf cir 
Route::get('hc4/imprimir/cir/{id}', 'hc4\ordenes\Orden_formato012Controller@cirPdf')->name('cir.pdfimprimir');







//Formulario 053
Route::match(['get', 'post'], 'formato_053/pdf/{id_orden}', 'hc4\ordenes\Orden_formato012Controller@pdf_053')->name('orden_012.pdf_053');
Route::match(['get', 'post'],'formato_053/index/{id_orden}', 'hc4\ordenes\Orden_formato012Controller@formato_053')->name('orden_012.index.pdf_053');
Route::match(['get', 'post'],'formato_053/store', 'hc4\ordenes\Orden_formato012Controller@store_053')->name('orden_012.store_053');
Route::get('orden/procedimientos/eliminar/{id_orden}', 'hc4\ordenes\Orden_Proc_EndosController@eliminar_orden_procedimiento');

Route::get('vt_ptv_laboratorio_iess/crear/{id}', 'hc4\ordenes\OrdenIess2Controller@crear')->name('laboratorio.ordenIess.crear');
Route::get('vt_ptv_laboratorio_iess/edit/{id}', 'hc4\ordenes\OrdenIess2Controller@edit')->name('laboratorio.ordenIess.edit');
Route::get('vt_ptv_laboratorio_iess/crear/{id}/{id_examen}', 'hc4\ordenes\OrdenIess2Controller@crear_examen_publico')->name('ordenIess.crear_examen_publico');
Route::get('vt_ptv_laboratorio_iess/eliminar/{id}/{id_examen}', 'hc4\ordenes\OrdenIess2Controller@eliminar_examen_publico')->name('ordenIess.eliminar_examen_publico');
Route::get('nuevo_laboratorio/publico/crear/agenda/{id_agenda}', 'hc4\ordenes\OrdenIess2Controller@crear_agenda')->name('laboratorio.ordenIess.crear_agenda');
