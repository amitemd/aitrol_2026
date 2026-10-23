<?php

use Illuminate\Support\Facades\Route;

//preanestessico antecedentes victor Pilozo
// Route::get('formato/preanestesico/crear', 'rc_pre_anestesica\rc_pre_anestesicaController@preanestesico_create')->name('formato.preanestesico_crear');

// Route::get('formato/preanestesico/index', 'rc_pre_anestesica\rc_pre_anestesicaController@preanestesico_index')->name('formato.preanestesico_index');

// Route::get('formato/preanestesico/edit/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@preanestesico_edit')->name('formato.preanestesico_edit');

// Route::post('formato/preanestesico/update', 'rc_pre_anestesica\rc_pre_anestesicaController@preanestesico_update')->name('formato.preanestesico_update');




// //preanestesico examen

// Route::get('examen/preanestesico/crear', 'rc_pre_anestesica\rc_pre_anestesicaController@examen_preanestesico_create')->name('formato.examen_preanestesico_crear');

// Route::get('examen/preanestesico/index', 'rc_pre_anestesica\rc_pre_anestesicaController@examen_preanestesico_index')->name('formato.examen_preanestesico_index');
// Route::get('examen/preanestesico/edit/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@examen_preanestesico_edit')->name('formato.examen_preanestesico_edit');
// Route::post('examen/preanestesico/update', 'rc_pre_anestesica\rc_pre_anestesicaController@examen_preanestesico_update')->name('formato.examen_preanestesico_update');


// //rc_preanestesico

// Route::post('rc/preanestesico/store', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_preanestesico_store')->name('formato.rc_preanestesico_store');
// Route::get('rc/preanestesico/index', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_preanestesico_index')->name('formato.rc_preanestesico_index');


// Route::get('rc/preanestesico/delete/{id}','rc_pre_anestesica\rc_pre_anestesicaController@rc_preanestesico_delete')->name('formato.rc_preanestesico_delete');



//usado
Route::get('rc/preanestesico/crear/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_preanestesico_crea_registro')->name('formato.rc_preanestesico_crear');
Route::match(['get','post'],'rc/preanestesico/edit/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_preanestesico_edit')->name('formato.rc_preanestesico_edit');
Route::post('formato/preanestesico/store', 'rc_pre_anestesica\rc_pre_anestesicaController@preanestesico_store')->name('formato.preanestesico_store');
Route::post('rc/preanestesico/update', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_preanestesico_update')->name('formato.rc_preanestesico_update');
Route::post('examen/preanestesico/store', 'rc_pre_anestesica\rc_pre_anestesicaController@examen_preanestesico_store')->name('formato.examen_preanestesico_store');
Route::get('examen/preanestesico/delete/{id}','rc_pre_anestesica\rc_pre_anestesicaController@examen_preanestesico_delete')->name('formato.examen_preanestesico_delete');
Route::post('rc/indicaciones/agregar', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_indicaciones_agregar')->name('formato.indicaciones_agregar'); 
Route::get('formulario018/pdf/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@formulario018')->name('formulario.018');
Route::get('indicaciones/delete/{id}','rc_pre_anestesica\rc_pre_anestesicaController@indicaciones_delete')->name('formato.indicaciones_delete');
Route::get('formato/preanestesico/delete/{id}','rc_pre_anestesica\rc_pre_anestesicaController@preanestesico_delete')->name('formato.preanestesico_delete');
Route::get('vt_crear_anestesico/{id_procedimiento}','rc_pre_anestesica\rc_pre_anestesicaController@crear_anestesico')->name('rc_pre_anestesica.crear_anestesico');
//transanestesico
Route::get('formulario018A/pdf/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@formulario018A')->name('formulario.018A');
Route::get('rc/transanestesico/crear/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@transanestesico_crea_registro')->name('formato.transanestesico_crear');
Route::get('rc/transanestesico/edit/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@transanestesico_edit')->name('formato.transanestesico_edit');
Route::post('formato/transanestesico/store', 'rc_pre_anestesica\rc_pre_anestesicaController@transanestesico_store')->name('formato.transanestesico_store');
Route::post('formato/transanestesico/guardar', 'rc_pre_anestesica\rc_pre_anestesicaController@transanestesico_guardar')->name('formato.transanestesico_guardar');

//imagenes

Route::get('imagene/pre/anestesico', 'rc_pre_anestesica\rc_pre_anestesicaController@imagen_pre_anestesico')->name('formulario.imagenpreanestico');


Route::match(['get', 'post'], 'preeanestesico/canvas', 'rc_pre_anestesica\rc_pre_anestesicaController@saveCanvas')->name('preeanestesico.saveCanvas');





///postanestésico
Route::get('rc/postanestesico/crear/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_postanestesico_crea_registro')->name('formato.rc_postanestesico_crear');
Route::match(['get','post'],'rc/postanestesico/edit/{id_hc_anesteciologia}/{id_hc_evolucion_anes}', 'rc_pre_anestesica\rc_pre_anestesicaController@rc_postanestesico_edit')->name('formato.rc_postanestesico_edit');
Route::post('formato/postanestesico/guardar', 'rc_pre_anestesica\rc_pre_anestesicaController@postanestesico_guardar')->name('formato.postanestesico_guardar');

Route::get('record_pre/indicacion/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@carga_tabla_indicacion')->name('rc_pre-anestesica.carga_tabla_indicacion');
Route::get('record_pre/examenes/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@carga_tabla_examenes')->name('rc_pre-anestesica.carga_tabla_examenes');
Route::get('record_pre/diagnosticos/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@carga_tabla_diagnosticos')->name('rc_pre-anestesica.carga_tabla_diagnosticos');


Route::get('trans_record/{name}', 'rc_pre_anestesica\rc_pre_anestesicaController@load');

Route::post('trans_op/anestesia/actualiza/tecnicas', 'rc_pre_anestesica\rc_pre_anestesicaController@actualiza_tecnicas')->name('rc_pre_anestesica.actualiza_tecnicas');

Route::post('trans_record/anestesia/actualiza/guardar/trans', 'rc_pre_anestesica\rc_pre_anestesicaController@guardar_trans')->name('rc_pre_anestesica.guardar_trans');
Route::get('record_pre/lastOrder/{id_paciente}/{fecha}', 'rc_pre_anestesica\rc_pre_anestesicaController@getLastOrder')->name('rc_pre-anestesica.getLastOrder');
Route::get('drogas_administradas/transanestesico/delete/{id}','rc_pre_anestesica\rc_pre_anestesicaController@drogas_administradas_delete')->name('rc_pre_anestesica.drogas_administradas_delete');

Route::get('record_trans/drogas_adminitradas/{id}', 'rc_pre_anestesica\rc_pre_anestesicaController@carga_tabla_drogas')->name('rc_pre_anestesica.carga_tabla_drogas');
