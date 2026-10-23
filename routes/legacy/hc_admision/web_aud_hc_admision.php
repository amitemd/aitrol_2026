<?php

Route::get('auditoria/admision/duplicar_registros/{id_agenda}', 'auditoria_hc_admision\AuditoriaHcAdmisionController@duplicar_registros')->name('auditoria_admision.duplicar_registros');

Route::get('auditoria_no_mostrar/auditoria_admision/auditoria_duplicar_registros/{id_agenda}', 'auditoria_hc_admision\AuditoriaHcAdmisionController@duplicar_registros_no_mostrar')->name('auditoria_admision.duplicar_registros_no_mostrar');


//pagina principal mostrando todas las opciones para despliegue
Route::get('auditoria_agenda/horario/doctores/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@detalle_auditoria')->name('auditoria_agenda.detalle');

//actualizacion de cortesia historia clinica en el bloque de filiacion
Route::get('auditoria_agenda/auditoria_editarcortesia/{id}/{c}', 'auditoria_hc_admision\AuditoriaAgendaController@actualizacortesia')->name('auditoria_vdoctor.cortesia');

//Documentos agenda
//1.mostrar documentos
Route::get('auditoria_agenda/foto/mostrar', 'auditoria_hc_admision\AuditoriaAgendaController@auditoria_foto567')->name('auditoria_agenda.imagen567');

//Examenes Externos
//2.mostrar documentos
Route::get('auditoria_historiaclinica/lab_externo/mosrtar_foto/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@auditoria_mostrar_lab_externo')->name('auditoria_hc_video.mostrar_lab_externo');

//Historial de Biopsias
//3.mostrar documentos
Route::get('auditoria_biopsias/histo_biopsias/mostrar_foto/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@auditoria_mostrar_biopsias')->name('auditoria_hc_video_biopsias.mostrar_biopsias');

//3.1 dentro de historial de Biopsias existe una condicion donde se encuentra la siguiente ruta
Route::get('auditoria/historiaclinica_auditoria/video_aud/mostar_foto_hc3/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@mostrar_foto')->name('auditoria_hc_video.mostrar_foto');

//Procedimientos 
//Botón de Imagenes
Route::get('auditoria_historiaclinica/video_auditoria/captura_aud/{protocolo_id}/{agendas}/{ruta}', 'auditoria_hc_admision\AuditoriaAgendaController@auditoria_mostrar')->name('auditoria_hc_video.mostrar');

// desglose de empresa para procedimientos
Route::get('auditoria_empresa2/auditoria_historiaclinica/auditoria_cargar/{id_proc}/{id_agenda}/{id_seguro}', 'auditoria_hc_admision\AuditoriaAgendaController@cargar_empresa2')->name('auditoria_historia.cargar_empresa2');

// desglose de epicrisis Cie10
Route::match(['get', 'post'],'auditoria_cie10/auditoria_agregar', 'auditoria_hc_admision\AuditoriaAgendaController@agregar_cie10')->name('auditoria_epicrisis.agregar_cie10');

Route::get('auditoria_cie10/auditoria_cargar/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@cargar_cie10_aud')->name('auditoria_epicrisis.cargar');

Route::get('auditoria_historiaclinica/auditoria_epicrisis/auditoria_imprimir/pdf/{hcid}', 'auditoria_hc_admision\AuditoriaAgendaController@imprimir_epicrisis')->name('auditoria_epicrisis.imprimir');

Route::get('auditoria_cie10/auditoria_eliminar/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@eliminar')->name('auditoria_epicrisis.eliminar');

Route::match(['get', 'post'],'auditoria_historiaclinica/auditoria_cie10/nombre/1', 'auditoria_hc_admision\AuditoriaAgendaController@cie10_nombre')->name('auditoria_epicrisis.cie10_nombre');

Route::match(['get', 'post'],'auditoria_historiaclinica/auditoria_cie10/nombre/2', 'auditoria_hc_admision\AuditoriaAgendaController@cie10_nombre2')->name('auditoria_epicrisis.cie10_nombre2');

Route::get('auditoria_cardio/auditoria_formato/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@formato')->name('auditoria_cardio.formato');

//Fausto Reporte de Cardiologia
Route::get('auditoria_cardio/reporte_cardiologia/{id}/{id_agenda}', 'auditoria_hc_admision\AuditoriaAgendaController@reporte_cardiologia')->name('auditoria_cardio.reporte_cardiologia');


//Evoluciones editar
Route::get('auditoria_historialclinico/visitas_ingreso_editar/{id_protocolo}/{agenda}', 'auditoria_hc_admision\AuditoriaAgendaController@ingreso_actualiza_visita')->name('auditoria_visita.crea_actualiza_funcion');

//cuando proc=0
Route::get('auditoria_orden_proc/crear_editar_orden_procedimiento/{hcid}', 'auditoria_hc_admision\AuditoriaAgendaController@crear_editar_orden_procedimiento')->name('auditoria_orden_proc.crear_editar');

Route::get('auditoria_orden_proc/auditoria_imprimir_orden/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@imprimir_orden_procedimiento')->name('auditoria_orden_proc.imprimir_orden');

Route::match(['get', 'post'],'guardar_auditoria_orden_proc/auditoria_imprimir_orden/{id}/auditoria_guardar', 'auditoria_hc_admision\AuditoriaAgendaController@guardar_orden_procedimiento')->name('auditoria_orden_proc.guardar');

Route::get('auditoria_orden_proc/auditoria_imprimir_orden/{hcid}/auditoria_validaexiste/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@existe_orden_procedimiento')->name('auditoria_orden_proc.existe');

Route::get('auditoria_orden_proc/auditoria_imprimir_orden/auditoria_elimina/{hcid}/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@eliminar_orden_procedimiento')->name('auditoria_orden_proc.eliminar');

Route::get('auditoria_orden_proc/auditoria_imprimir_orden/crear_detalle_orden_procedimiento/{hcid}/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@crear_detalle_orden_procedimiento')->name('auditoria_orden_proc.crear_detalle_orden_procedimiento');

//Historial de Procedimientos Auditoria
//desglose de editar procedimientos original
Route::get('auditoria_estudio/auditoria_editar/{id}/{agenda}', 'auditoria_hc_admision\AuditoriaAgendaController@estudio_editar')->name('auditoria_estudio.editar');

//desglose de formulario de procedimientos
Route::post('auditoria_procedimientos/auditoria_actualiza/paciente', 'auditoria_hc_admision\AuditoriaAgendaController@actualiza_paciente')->name('auditoria_procedimiento.paciente');

//formulario de procedimientos para guardados en historia clinica principal
Route::post('historia_procedimientos_auditoria_2/aud_actualiza_1/aud_paciente_1', 'auditoria_hc_admision\AuditoriaAgendaController@actualiza_paciente_aud')->name('auditoria_procedimiento.paciente_aud');

Route::post('auditoria_historiaclinica/procedimiento/cambio_doctor_seguro/', 'auditoria_hc_admision\AuditoriaAgendaController@actualizar_doctor_seguro')->name('auditoria_hc_procedimientos.actualizar_doctor_seguro');

Route::get('auditoria_empresa/auditoria_historiaclinica/cargar/{id_proc}/{id_agenda}/{id_seguro}', 'auditoria_hc_admision\AuditoriaAgendaController@cargar_empresa')->name('auditoria_historia.cargar_empresa');

//Evoluciones guardar

Route::post('auditoria_historiaclinica/auditoria_consulta_actualizar', 'auditoria_hc_admision\AuditoriaAgendaController@actualizar')->name('consulta.actualizar_auditoria');


//Botón verde de Epicrisis

Route::get('auditoria_historiaclinica/auditoria_epicrisis/{hcid}/{proc}', 'auditoria_hc_admision\AuditoriaAgendaController@mostrar_epicrisis')->name('auditoria_epicrisis.mostrar');

Route::match(['get', 'post'],'auditoria_historiaclinica/auditoria_epicrisis/auditoria_actualiza', 'auditoria_hc_admision\AuditoriaAgendaController@actualiza_epicrisis')->name('auditoria_epicrisis.actualiza');

//Botón verde de Estudio
Route::get('auditoria_historia/admision/seleccion/imagen/{id}/{agenda_ori}/{ruta}', 'auditoria_hc_admision\AuditoriaAgendaController@descarga_seleccion')->name('auditoria_hc_reporte.seleccion');

//boton de descargar dentro de seleccion
Route::get('auditoria_procedimiento/auditoria_seleccion_descargar/auditoria_resumen/{id_protocolo}/', 'auditoria_hc_admision\AuditoriaAgendaController@seleccion_descargar')->name('auditoria_hc_reporte.seleccion_descargar');

//para descargar resumen de procedimiento
Route::get('auditoria_procedimiento/auditoria_descargar/auditoria_resumen/{id}/{tipo}', 'auditoria_hc_admision\AuditoriaAgendaController@descarga_resumen')->name('auditoria_hc_reporte.descargar');

Route::post('auditoria_historia_clinica/auditoria_convenios/auditoria_revision/auditoria_fecha', 'auditoria_hc_admision\AuditoriaAgendaController@fecha_convenios')->name('auditoria_hc_foto.fecha_convenios');



//Botón verde de Evolución
Route::get('auditoria_evolucion_desc/modal/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@pr_modal')->name('auditoria_evolucion.pr_modal');

//para descargar el formato de evolucion
Route::post('auditoria_evolucion_desc/auditoria_modal/auditoria_guardar_op', 'auditoria_hc_admision\AuditoriaAgendaController@guardar_op')->name('auditoria_evolucion.guardar_op'); 

//para descargar el formato de evolucion NUEVO
Route::post('auditoria_evolucion_desc/auditoria_modal/nuevo_evolucio_aud/auditoria_guardar_op', 'auditoria_hc_admision\AuditoriaAgendaController@nuevo_guardar_op')->name('auditoria_evolucion.nuevo_guardar_op'); 

// para descargar protocolo CPRE+ECO
Route::post('auditoria_protocolo_cpre_eco/auditoria_modal/auditoria_guardar_op_cpre_eco', 'auditoria_hc_admision\AuditoriaAgendaController@guardar_op_cpre_eco')->name('auditoria_protocolo_cpre_eco.guardar_op_cpre_eco');

//Botón verde de Protocolo
Route::get('auditoria_protocolo_oper/modal/{id}', 'auditoria_hc_admision\AuditoriaAgendaController@pr_modal_protocolo')->name('auditoria_protocolo.pr_modal');

//guardado de protocolo dentro de la modal
Route::post('auditoria_protocolo_oper/auditoria_modal/auditoria_guardar_op', 'auditoria_hc_admision\AuditoriaAgendaController@guardar_op_2')->name('auditoria_protocolo.guardar_op');

//Botón verde de CPRE-ECO
Route::get('auditoria_protocolo_cpre_eco/modal/{hcid}', 'auditoria_hc_admision\AuditoriaAgendaController@modal_cpre_eco')->name('auditoria_protocolo_cpre_eco.modal_cpre_eco');

//guardado de desglose de CPRE-ECO
Route::post('auditoria_protocolo_cpre_eco/auditoria_modal/', 'auditoria_hc_admision\AuditoriaAgendaController@modal_crear_editar')->name('auditoria_protocolo_cpre_eco.modal_crear_editar');

//Botón verde de Documentos
Route::post('auditoria_controldoc/admision', 'auditoria_hc_admision\AuditoriaAgendaController@control_doc')->name('auditoria_controldoc.control_doc');

//cambio de fecha
Route::post('auditoria_historia_clinica/auditoria_convenios/auditoria_revision/fecha', 'auditoria_hc_admision\AuditoriaAgendaController@fecha_convenios_documentos')->name('auditoria_hc_foto.fecha_convenios');
//cambio de seguro empresa
Route::post('auditoria_controldoc/auditoria_convenios/auditoria_seguro_empresa', 'auditoria_hc_admision\AuditoriaAgendaController@seguro_empresa')->name('auditoria_controldoc.seguro_empresa');