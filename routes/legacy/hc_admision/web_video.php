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




Route::get('historiaclinica/video/captura/{protocolo_id}/{agendas}/{ruta}', 'hc_admision\VideoController@mostrar')->name('hc_video.mostrar');
//donde se almacena el video
Route::post('historiaclinica/video10/grabacion_captura10/', 'hc_admision\VideoController@guardar_video');

Route::match(['get', 'post'],'historiaclinica/video/grabacion_captura/{id_protocolo}', 'hc_admision\VideoController@guardado_foto')->name('hc_video.guardado_foto');
Route::post('historiaclinica/video2/grabacion_captura2/', 'hc_admision\VideoController@guardado_foto2')->name('hc_video.guardado_foto2');
Route::post('historiaclinica/documentos/grabacion_captura_documentos/', 'hc_admision\VideoController@guardado_foto2_documento')->name('hc_video.guardado_foto2_documento');
Route::post('historiaclinica/estudios/grabacion_captura_estudios/', 'hc_admision\VideoController@guardado_foto2_estudios')->name('hc_video.guardado_foto2_estudios');
Route::post('historiaclinica/biopsias/grabacion_captura_biopsias/', 'hc_admision\VideoController@guardado_foto2_biopsias')->name('hc_video.guardado_foto2_biopsias');

Route::get('historiaclinica/video/mostar_foto/{id}', 'hc_admision\VideoController@mostrar_foto')->name('hc_video.mostrar_foto');
Route::get('historiaclinica/video/mostar_foto/', 'hc_admision\VideoController@mostrar_foto')->name('hc_video.mostrar_foto2');

Route::get('historiaclinica/video/eliminar_foto/{id}', 'hc_admision\VideoController@eliminar_foto')->name('hc_video.eliminar_foto');

Route::post('historiaclinica/video_subida_historia/subida_fotos_historial/grabacion_captura2/', 'hc_admision\VideoController@guardar_antiguas')->name('hc_video.nuevas_ima');

Route::post('historiaclinica/video_subida_historia2/subida_fotos_historial_biopsias/grabacion_captura2/', 'hc_admision\VideoController@guardar_antiguas2')->name('hc_video.nuevas_ima_biopsias');

//ruta para documentos
Route::get('historiaclinica/documentos/subida/{protocolo_id}/{agendas}/{ruta}', 'hc_admision\VideoController@mostrar_documento')->name('hc_video.mostrar_documento');
Route::get('historiaclinica/biopsias/subida/{protocolo_id}/{agendas}/{ruta}', 'hc_admision\VideoController@mostrar_biopsias')->name('hc_video.mostrar_biopsias');
Route::get('historiaclinica/estudios/subida/{protocolo_id}/{agendas}/{ruta}', 'hc_admision\VideoController@mostrar_estudios')->name('hc_video.mostrar_estudios');
Route::get('historiaclinica/video/regreso/{id}/{agenda}/{ruta}', 'hc_admision\VideoController@regreso')->name('hc_video.regreso');

//guardado de bioposia lado de Silvia
Route::post('historiaclinica2/biopsias2/grabacion_captura_biopsias_recepcion/', 'hc_admision\VideoController@guardado_foto3_biopsias')->name('hc_video2.guardado_foto2_biopsias2');


//cambio de fecha
Route::post('historia_clinica/convenios/revision/fecha', 'hc_admision\VideoController@fecha_convenios')->name('hc_foto.fecha_convenios');




Route::get('hc_ima_nombre/{name}', 'hc_admision\ProcedimientosController@load3');

//para examenes anteriores
Route::get('images_lab_externos/{id_paciente}', 'hc_admision\VideoController@examenes_externos')->name('laboratorio.externo');
Route::get('compr_cobros_hc/{id_paciente}', 'hc_admision\VideoController@cobro_hc')->name('paciente.cobro_hc');

Route::post('imagenes_lab_externo/ingreso/documento', 'hc_admision\VideoController@guardar_examenes_externos')->name('laboratorio_externo.guardar');
Route::post('compr_cobros_hc/ingreso/documento', 'hc_admision\VideoController@guardar_cobro_hc')->name('paciente.guardar_cobro_hc');

Route::get('historiaclinica/lab_externo/mosrtar_foto/{id}', 'hc_admision\VideoController@mostrar_lab_externo')->name('hc_video.mostrar_lab_externo');

Route::get('laboratorio_externo_descarga/{name}', 'hc_admision\VideoController@descarga_externo');

//para de biopsias
Route::get('images_ingreso_biopsias/{id_paciente}', 'hc_admision\VideoController@examenes_biopsias')->name('ingreso.biopsias2');
Route::post('images_ingreso_biopsias/ingreso/documento', 'hc_admision\VideoController@guardar_biopsias_nuevo')->name('ingreso_biopsias.guardar');


// seleccionar todas las fotos para recortar
Route::get('seleccion_imagenes_recortar/{id_paciente}', 'hc_admision\VideoController@recortar_todas')->name('seleccionar_todas.recortar');

//descargar multiples en zip
Route::post('historiaclinica/descargar_subida_fotos/subida_fotos_historial2/descarga/', 'hc_admision\VideoController@descargar_zip')->name('hc_video.descargar_zip');

//historial de imagenes de paciente
Route::get('paciente/historial/imagenes/{id_paciente}', 'PacienteController@historial_imagenes')->name('paciente.historial_imagenes');


//historial de imagenes de paciente
Route::get('paciente/historial/documentos/{id_paciente}', 'PacienteController@historial_documentos')->name('paciente.historial_documentos');

//historial de imagenes de estudios
Route::get('paciente/historial/estudios/{id_paciente}', 'PacienteController@historial_estudios')->name('paciente.historial_estudios');

// eliminar biopsias 
Route::get('paciente/eliminar/biopsia/{id}',  'hc_admision\VideoController@eliminar_biopsia')->name('eliminar.biopsia');

//historial de imagenes de paciente desde la agenda
Route::get('paciente/agenda/{id_paciente}', 'PacienteController@historial_agenda')->name('paciente.historial_agenda');
Route::get('paciente/imagen/{id}', 'PacienteController@stream_image')->name('paciente.stream_image');

Route::match(['get', 'post'], 'video/ver/archivo/biopsia/{id}', 'hc_admision\VideoController@ver_archivo_biopsia')->name('video.ver_archivo_biopsia');