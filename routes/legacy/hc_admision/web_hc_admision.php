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

Route::match(['get', 'post'], 'controldoc/archivo/re/oi/s/a/update', 'hc_admision\ControlDocController@update2')->name('controldoc.update2');
Route::match(['get', 'post'], 'controldoc/search', 'hc_admision\ControlDocController@search')->name('controldoc.search');
Route::get('controldoc/act_sec/{id}/{value}', 'hc_admision\ControlDocController@act_sec')->name('controldoc.act_sec');
Route::get('controldoc/admision/{id}/{doc}', 'hc_admision\ControlDocController@crea_doc')->name('controldoc.crea_doc');

Route::get('controldoc/agenda/archivo/{id_agenda}', 'hc_admision\ControlDocController@agenda_scan');
Route::match(['get', 'post'], 'controldoc/agenda_update/archivo', 'hc_admision\ControlDocController@update_scan')->name('controldoc.agendascan');
Route::get('scan/descarga/{id}', 'hc_admision\ControlDocController@descarga_scan2')->name('controldoc.descarga_scan2');

Route::post('controldoc/admision', 'hc_admision\ControlDocController@control_doc')->name('controldoc.control_doc');
Route::get('controldoc/admision/{hcid}/{url_doctor}/{unix}', 'hc_admision\ControlDocController@continua')->name('controldoc.continua');
Route::get('controldoc/index_tb/{hcid}/{proc_consul}/{tipo}', 'hc_admision\ControlDocController@control_tb')->name('controldoc.control_tb');
Route::get('control/documentos/admision/sube/archivo/{hcid}/{doc}', 'hc_admision\ControlDocController@sube_archivo')->name('controldoc.sube_archivo');
Route::get('controldoc/imprime/{ahid}', 'hc_admision\ControlDocController@imprimirpdf')->name('controldoc.imprimirpdf');
Route::get('controldoc/imprime_datos_paciente/{id_paciente}', 'hc_admision\ControlDocController@imprimirdatos_paciente')->name('controldoc.imprimirdatos_paciente');
Route::get('controldoc/imprime_resumen/{id}', 'hc_admision\ControlDocController@imprimirpdf_resumen')->name('controldoc.imprimirpdf_resumen');
Route::resource('controldoc', 'hc_admision\ControlDocController');

Route::get('controldoc/form_cert/{id}', 'hc_admision\ControlDocController@form_cert')->name('controldoc.form_cert');

Route::get('hc4/certificado/form_cert/{id_agenda}', 'hc_admision\ControlDocController@form_cert_hc4')->name('controldoc.form_cert_hc4');

Route::get('controldoc/documentos_pdf/{id_paciente}/{id_empresa}/{id_agenda}', 'hc_admision\ControlDocController@documentos_pdf')->name('documentos_pdf');
Route::get('controldoc/preparacion_modal/{id_paciente}/{id_empresa}/{fecha_ini}', 'hc_admision\ControlDocController@preaparacion_modal')->name('preaparacion_modal');
Route::post('controldoc/documentos_pdf/descargar_pdf/{id_empresa}', 'hc_admision\ControlDocController@descargar_pdf')->name('descargar_pdf');
Route::post('controldoc/preparacion_modal/descargar/{id_empresa}', 'hc_admision\ControlDocController@descargar')->name('descargar');
Route::post('controldoc/form_cert/generar_cert', 'hc_admision\ControlDocController@generar_cert')->name('controldoc.generar_cert');
Route::post('controldoc/consulta/reporte_documentos', 'hc_admision\ControlDocController@reporte_doc')->name('controldoc.reporte_doc');
Route::post('controldoc/consulta/reporte_documentos/seguros', 'hc_admision\ControlDocController@reporte_doc_seguros')->name('controldoc.reporte_doc_seguros');
Route::post('controldoc/consulta/reporte_documentos_seguros2', 'hc_admision\ControlDocController@reporte_documentos_seguros2')->name('controldoc.reporte_documentos_seguros2');

Route::get('historia/{id}', 'hc_admision\HistoriaController@historia')->name('historia.historia');
Route::get('historia/drogasadministradas/{id_record}', 'hc_admision\HistoriaController@drogasadministradas')->name('historia.drogasadministradas');

Route::get('admisiones/cie_10/{cie}', 'AdmisionController@busca_cie_10')->name('admision.busca_cie_10');

// rutas de tecnicas anestesicas
Route::resource('tecnicas_anestesicas', 'hc_admision\TecnicasAnestesicasController');

Route::get('convenios/admision/{seg}/{cita}/{old}', 'AdmisionController@valida_convenio')->name('admision.valida_convenio');

Route::match(['get', 'post'], 'controldoc/search', 'hc_admision\ControlDocController@search')->name('record.anestesiologo');

Route::get('historiaclinica/anestesiologia/{ag}', 'hc_admision\AnestesiologiaController@mostrar')->name('anestesiologia.mostrar');

Route::match(['get', 'post'], 'anestesiologia/crea_actualiza/{hc_id}', 'hc_admision\AnestesiologiaController@crea_actualiza')->name('anestesiologia.crea_actualiza');
Route::match(['get', 'post'], 'anestesiologia/canvas', 'hc_admision\AnestesiologiaController@saveCanvas')->name('anestesiologia.saveCanvas');

// nueva ruta para agregar los csv de anestesiologia
Route::get('historiaclinica/anestesiologia/csv/{id}/{agenda}', 'hc_admision\AnestesiologiaController@mostrarcsv')->name('anestesiologia.mostrarcsv');
Route::post('anestesiologia/crea_actualizacsv/', 'hc_admision\AnestesiologiaController@creacsv')->name('anestesiologia.creacsv');
Route::get('historiaclinica/anestesiologia/imprime/{id}', 'hc_admision\AnestesiologiaController@imprime')->name('anestesiologia.imprime');

Route::get('control/admision/valida/{id}/{doc}', 'hc_admision\ControlDocController@valida_existe')->name('controldoc.valida_existe');
Route::get('control/admision/actu/{id}/{doc}/{tipo}', 'hc_admision\ControlDocController@actu_doc')->name('controldoc.actu_doc');

Route::match(['get', 'post'], 'admision/suspension/{url}', 'hc_admision\HistoriaController@suspension')->name('admision.suspension');

Route::match(['get', 'post'], 'reporte_hc', 'hc_admision\HistoriaController@reporte_hc')->name('historia_clinica.reporte_hc');

Route::match(['get', 'post'], 'reporte_hc_iess', 'hc_admision\HistoriaController@reporte_hc_iess')->name('historia_clinica.reporte_hc_iess');

Route::post('historiaclinica/procedimiento/cambio_doctor_seguro/', 'hc_admision\ProcedimientosController@actualizar_doctor_seguro')->name('hc_procedimientos.actualizar_doctor_seguro');

Route::get('formato/actadeentrega/imprime/{id}', 'hc_admision\ControlDocController@imprime_actadeentrega')->name('actadeentraga.imprime');
// registro planilla iees
Route::get('historiaclinica/planilla_iess/', 'hc_admision\HistoriaController@planilla_iess')->name('historiaclinica.planilla_iess');

// consentimientos

Route::get('consentimiento/imprimir_consentimiento', 'hc_admision\FullcontrolController@imprimir_consentimiento')->name('consentimiento.imprimir_consentimiento');

Route::get('consentimiento/imprimir_consentimiento_colono', 'hc_admision\FullcontrolController@imprimir_consentimiento_colono')->name('consentimiento.imprimir_colono');
Route::get('consentimiento/imprimir_consentimiento_cprm', 'hc_admision\FullcontrolController@imprimir_consentimiento_cprm')->name('consentimiento.imprimir_cprm');
Route::get('consentimiento/imprimir_consentimiento_eco', 'hc_admision\FullcontrolController@imprimir_consentimiento_eco')->name('consentimiento.imprimir_eco');
Route::get('consentimiento/imprimir_consentimiento_balon', 'hc_admision\FullcontrolController@imprimir_consentimiento_balon')->name('consentimiento.imprimir_balon');
Route::get('consentimiento/imprimir_consentimiento_balonretiro', 'hc_admision\FullcontrolController@imprimir_consentimiento_balonretiro')->name('consentimiento.imprimir_balonretiro');
Route::get('consentimiento/imprimir_consentimiento_poem', 'hc_admision\FullcontrolController@imprimir_consentimiento_poem')->name('consentimiento.imprimir_poem');
Route::get('consentimiento/imprimir_consentimiento_gastro', 'hc_admision\FullcontrolController@imprimir_consentimiento_gastro')->name('consentimiento.imprimir_gastro');
Route::get('consentimiento/imprimir_consentimiento_enteroscopia', 'hc_admision\FullcontrolController@imprimir_consentimiento_enteroscopia')->name('consentimiento.imprimir_enteroscopia');
Route::get('consentimiento/imprimir_consentimiento_enteroscopia_retrograda', 'hc_admision\FullcontrolController@imprimir_consentimiento_enteroscopia_retrograda')->name('consentimiento.imprimir_enteroscopia_retrograda');
Route::get('consentimiento/imprimir_consentimiento_anorectal', 'hc_admision\FullcontrolController@imprimir_consentimiento_anorectal')->name('consentimiento.imprimir_anorectal');
Route::get('consentimiento/balon_colocacionv2', 'hc_admision\FullcontrolController@balon_colocacionv2')->name('balon_colocacionv2');
Route::get('consentimiento/balon_retirov2', 'hc_admision\FullcontrolController@balon_retirov2')->name('balon_retirov2');
Route::get('consentimiento/colonov2', 'hc_admision\FullcontrolController@colonov2')->name('colonov2');
Route::get('consentimiento/cprev2', 'hc_admision\FullcontrolController@cprev2')->name('cprev2');
Route::get('consentimiento/anov2', 'hc_admision\FullcontrolController@anov2')->name('anov2');
Route::get('consentimiento/ecov2', 'hc_admision\FullcontrolController@ecov2')->name('ecov2');
Route::get('consentimiento/edav2', 'hc_admision\FullcontrolController@edav2')->name('edav2');
Route::get('consentimiento/enteroscopiav2', 'hc_admision\FullcontrolController@enteroscopiav2')->name('enteroscopiav2');
Route::get('consentimiento/enteroscopia_retrogadav2', 'hc_admision\FullcontrolController@enteroscopia_retrogadav2')->name('enteroscopia_retrogadav2');
Route::get('consentimiento/gastrostomiav2', 'hc_admision\FullcontrolController@gastrostomiav2')->name('gastrostomiav2');
Route::get('consentimiento/cpmv2', 'hc_admision\FullcontrolController@cpmv2')->name('cpmv2');
Route::get('consentimiento/autorizacion_uso_imagen_endoscopica', 'hc_admision\FullcontrolController@uso_imagen')->name('uso_imagen');
Route::get('consentimiento/manometria_ano_rectal', 'hc_admision\FullcontrolController@manometria_ano_rectal')->name('manometria_ano_rectal');
Route::get('consentimiento/anestesia', 'hc_admision\FullcontrolController@anestesia')->name('anestesia');
Route::get('consentimiento/manometria_esofagica', 'hc_admision\FullcontrolController@manometria_esofagica')->name('manometria_esofagica');
Route::get('consentimiento/ph_esofagica', 'hc_admision\FullcontrolController@ph_esofagica')->name('ph_esofagica');
Route::get('consentimiento/capsula_endoscopica', 'hc_admision\FullcontrolController@capsula_endoscopica')->name('capsula_endoscopica');
Route::get('consentimiento/phmetria_cap', 'hc_admision\FullcontrolController@phmetria_cap')->name('phmetria_cap');
Route::get('consentimiento/consentimiento_encuesta', 'hc_admision\FullcontrolController@consentimiento_encuesta')->name('consentimiento_encuesta');
Route::get('consentimiento/anexo_informativo', 'hc_admision\FullcontrolController@anexo_informativo')->name('anexo_informativo');
Route::get('consentimiento/anexo_anastesico', 'hc_admision\FullcontrolController@anexo_anastesico')->name('anexo_anastesico');
Route::get('consentimiento/endoscopica_percutanea', 'hc_admision\FullcontrolController@endoscopica_percutanea')->name('endoscopica_percutanea');
Route::get('consentimiento/broncoscopia', 'hc_admision\FullcontrolController@broncoscopia')->name('broncoscopia');

Route::post('controldoc/convenios/seguro_empresa', 'hc_admision\ControlDocController@seguro_empresa')->name('controldoc.seguro_empresa');

Route::post('revision/agenda/procedimientos/doctor', 'hc_admision\FullcontrolController@revisar_procedimientos')->name('fullcontrolController.revisar_procedimientos');

Route::post('guardar/observacion/administrativa', 'PacienteController@guardarObservacionAdministrativa')->name('paciente.guardarObservacionAdministrativa');

// adelantado
Route::resource('adelantado', 'AdelantadoController');
Route::get('adelantado/detalle/{id}', 'AdelantadoController@detalle')->name('adelantado.detalle');
Route::get('adelantado/detalle_ag/{id}/{unix}', 'AdelantadoController@detalle_ag')->name('AdelantadoController.detalle_ag');
Route::get('adelantado/detalle/consulta_documentos/{hcid}', 'AdelantadoController@consulta_documentos')->name('adelantado.consulta_documentos');
Route::match(['get', 'post'], 'adelantado/search', 'AdelantadoController@search')->name('adelantado.search.match');
Route::match(['get', 'post'], 'adelantado/index/reporte', 'AdelantadoController@reporte')->name('adelantado.reporte');
Route::get('adelantado/search', 'AdelantadoController@search')->name('adelantado.search');
Route::match(['get', 'post'], 'adelantado/log_agenda/{id}', 'AdelantadoController@log_agenda')->name('adelantado.log_agenda');

// MUESTRAS DE BIOPSIAS
Route::match(['get', 'post'], 'listado/muestras_biopsia', 'hc_admision\MuestraBiopiasController@index_muestras')->name('muestrabiopsias.index');
Route::match(['get', 'post'], 'muestras_biopsia/update', 'hc_admision\MuestraBiopiasController@update')->name('muestrabiopsias.update');
Route::get('nuevo_record/csv/eliminar/{id}', 'hc_admision\AnestesiologiaController@eliminar_csv')->name('anestesiologia.eliminar_csv');
// 4/4/2022
Route::get('nuevo_record/csv/editar', 'hc_admision\AnestesiologiaController@editar_csv')->name('anestesiologia.editar_csv');

Route::match(['get', 'post'], 'reporte/excel/formulario/preparacion/{id}/{id_protocolo}', 'hc_admision\FormularioPreparacionController@excelPreparacion')->name('formularioPreparacion.excelPreparacion');
