<?php

// encuestas laboratorio
Route::get('formato_encuestalabs', 'SinLoginController@formato_encuestalabs')->name('formato.encuestalabs');
Route::post('laboratorio_encuesta/guardar/datos', 'SinLoginController@encuestalabsguardar')->name('laboratorio_encuesta.guardar');
Route::get('laboratorio/resultados_labs', 'laboratorio\Preguntas_LabsController@resultados_labs')->name('laboratorio.resultados_labs');
Route::match(['get', 'post'], 'laboratorio/estadisticalabs', 'laboratorio\Preguntas_LabsController@estadisticalabs')->name('laboratorio.estadisticalabs');
Route::post('laboratorio/detalle/mes_labs', 'laboratorio\Preguntas_LabsController@detalle_mes_labs')->name('laboratorio.detalle_mes_labs');
Route::resource('pregunta_labs', 'laboratorio\Preguntas_LabsController')->only([
    'index', 'create', 'store', 'edit', 'update',
]);
// preguntas para la encuensta
// Route::match(['get', 'post'], 'preguntas/resultados/search', 'laboratorio\Preguntas_LabsController@search')->name('preguntas.search');
// /Route::resource('preguntas', 'laboratorio\Preguntas_LabsController');
// Route::resource('grupopreguntaslabs', 'laboratorio\GrupoPreguntas_LabsController');

// 15/5/2023 nueva encuesta labs
Route::get('nuevo_formato_encuesta', 'SinLoginController@nuevo_formato_encuesta')->name('nuevo.encuestalabs');
Route::get('encuesta/labs/{id}', 'SinLoginController@encuesta_labs')->name('nuevo.encuesta_labs');
Route::get('encuesta/labs/{id}/{cedula}/{id_orden}', 'SinLoginController@encuesta_labs_app')->name('nuevo.encuesta_labs_app');

Route::post('nuevo_formato_encuesta/guardar/nueva', 'SinLoginController@nuevo_formato_guardar')->name('nuevo_formato_guardar.guardar');
Route::get('nuevo_formato_encuesta/excel', 'SinLoginController@excel_nuevo_formato')->name('excel_nuevo_formato.excel');

// 1/8/2023 Estadistico labs Nuevo

Route::get('estadistico/labs/nuevo_formato', 'encuesta_nueva\Encuesta_LabsController@index')->name('nueva.encuestalabs');
Route::post('estadistico/labs/buscador', 'encuesta_nueva\Encuesta_LabsController@buscador')->name('nueva.buscador');

// admin encuestas
Route::match(['get', 'post'], 'admin/encuestas/index', 'encuesta_nueva\Encuesta_LabsController@admin_index')->name('encuesta_labs.admin_index');
