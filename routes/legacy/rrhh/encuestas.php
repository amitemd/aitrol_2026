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

//areas de la encuesta
Route::resource('area', 'rrhh\AreaController');

//ruta de acceso a sugerencia sin acceso de login
Route::get('encuestas', 'SinLoginController@encuestas')->name('sugerencia.encuesta');
Route::get('encuestas2', 'SinLoginController@encuestas2')->name('sugerencia.encuesta2');
Route::get('sugerencias', 'SinLoginController@sugerencia')->name('sugerencia.ingreso');
Route::get('encuesta_sugerencia', 'SinLoginController@encuesta_sugencia');
Route::get('sugerencias/resultados', 'rrhh\TipoSugerenciaController@resultados')->name('sugerencia.resultados');
Route::match(['get', 'post'], 'sugerencias/resultados/search', 'rrhh\TipoSugerenciaController@search')->name('sugerencia.search');
Route::post('sugerencias/guardar/datos', 'SinLoginController@sugerenciaguardar')->name('sugerencia.guardar');
Route::match(['get','post'],'encuesta/guardar/datos', 'SinLoginController@encuestaguardar')->name('encuesta.guardar');

//tipo de sugerencias
Route::resource('tipo_sugerencia', 'rrhh\TipoSugerenciaController');

//preguntas para la encuensta
Route::resource('preguntas', 'rrhh\PreguntasController');
Route::match(['get', 'post'], 'preguntas/resultados/search', 'rrhh\PreguntasController@search')->name('preguntas.search');

//preguntas para la encuensta
Route::resource('grupopreguntas', 'rrhh\GrupoPreguntasController');
Route::match(['get', 'post'], 'grupodepreguntas/resultados/search', 'rrhh\GrupoPreguntasController@search')->name('grupopreguntas.search');

Route::get('rrhh/encuesta/{id}', 'SinLoginController@rrhh_encuesta')->name('rrhh.encuesta');
Route::get('formato_encuesta/{id}', 'SinLoginController@formato_encuesta')->name('formato.encuesta');
Route::get('rrhh/resultados_ok', 'rrhh\TipoSugerenciaController@resultados_ok')->name('rrhh.resultados_ok');
Route::match(['get','post'], 'rrhh/estadisticas', 'rrhh\TipoSugerenciaController@rrhh_estadisticas')->name('rrhh.estadisticas');
Route::post('rrhh/detalle/mes', 'rrhh\TipoSugerenciaController@detalle_mes')->name('rrhh.detalle_mes');
//CAMBIOS EN LA VISTA DE ESTADISTICA Y EXCEL
Route::get('rrhh/encuesta_estadistica', 'rrhh\TipoSugerenciaController@encuesta_estadistica')->name('rrhh.encuesta_estadistica');
Route::match(['get','post'],'rrhh/estadisticas_2/{id}', 'rrhh\TipoSugerenciaController@rrhh_estadisticas_2')->name('rrhh.estadisticas_2');
Route::post('rrhh/detalle/mes_2/{id}', 'rrhh\TipoSugerenciaController@detalle_mes_2')->name('rrhh.detalle_mes_2');

//LABORATORIO EXTERNO
Route::get('laboratorio/externo/web', 'SinLoginController@externo_web')->name('lab_externo.web');
Route::get('laboratorio/externo/web/promo/{id}', 'SinLoginController@promo')->name('lab_externo.promo');
Route::post('laboratorio/externo/web/promo/guardar/formulario', 'SinLoginController@externo_guardar')->name('lab_externo.guardar');
Route::get('laboratorio/externo/web/promo/buscarpaciente/{id}', 'SinLoginController@buscapaciente')->name('lab_externo.buscapaciente');
Route::get('laboratorio/externo/web/promo/buscar/numero/{orden}','SinLoginController@buscar_orden')->name('lab_externo.buscar_orden');
Route::post('labs/externo/web/promo/buscar/numero/pagar/orden', 'SinLoginController@pagar_orden')->name('lab_externo.pagar_orden');
Route::get('mail/laboratorio/externo/{pac}/{user}', 'SinLoginController@mail_externo_web')->name('lab_externo.mail_externo');
Route::match(['get', 'post'], 'carrito/paciente', 'SinLoginController@carrito_paciente')->name('carrito.paciente');


//encuestas laboratorio

Route::get('labs/encuestas_labs','SinLoginController@encuestas_labs')->name('sinlogin.encuestas_labs');

//nueva encuesta
Route::get('encuestas/index/{id}', 'SinLoginController@encuesta_boton')->name('sinlogin.encuesta_boton');
Route::get('encuestas/index/formato/{id}', 'SinLoginController@nuevo_formato')->name('sinlogin.nuevo_formato');
Route::match(['get', 'post'], 'encuestas/nueva/guardar', 'SinLoginController@encuestas_nuevo_guardar')->name('sinlogin.encuestas_nuevo_guardar');

Route::get('encuestas/estadisticas', 'rrhh\TipoSugerenciaController@new_estadisticas')->name('tipo_sugerencia.new_estadisticas');
Route::match(['get','post'],'encuestas/estadisticas/tipo/{id}', 'rrhh\TipoSugerenciaController@estadisticas_tipo')->name('tipo_sugerencia.estadisticas_tipo');

//nueva vista de listado de encuesta
Route::match(['get', 'post'], 'encuestas/listado/index', 'rrhh\TipoSugerenciaController@listado_index')->name('tiposugerencia.listado_index');
Route::match(['get', 'post'], 'encuestas/listado/detalle/{id}', 'rrhh\TipoSugerenciaController@listado_detalle')->name('tiposugerencia.listado_detalle');

Route::post('encuesta/reporte/encuesta/{id}', 'rrhh\TipoSugerenciaController@reporte_new_encuesta')->name('rrhh.reporte_new_encuesta');



