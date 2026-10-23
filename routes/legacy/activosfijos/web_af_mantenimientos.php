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

//tipo
Route::get('activofjo/afTipo/af_cuentas_find', 'activosfijos\TipoController@find_cta_activofijo')->name('tipo.find_cta_activofijo');

Route::resource('afTipo', 'activosfijos\TipoController');

Route::match(['get', 'post'], 'activosfijos/tipo/search', 'activosfijos\TipoController@search')->name('activofjo.tipo.search');
//grupo
Route::get('contable/activosfijos/mantenimiento', 'activosfijos\GrupoController@index')->name('afGrupo.index');
Route::post('afGrupo/store', 'activosfijos\GrupoController@store')->name('afGrupo.store');
Route::match(['get', 'post'], 'activosfijos/grupo/search', 'activosfijos\GrupoController@search')->name('activofjo.grupo.search');
Route::match(['get', 'post'], 'activosfijos/grupo/info', 'activosfijos\GrupoController@info')->name('activofjo.grupo.info');
Route::match(['get', 'post'], 'activosfijos/grupo/nuevo', 'activosfijos\GrupoController@nuevo')->name('activofjo.grupo.nuevo');
Route::match(['get', 'post'], 'activosfijos/grupo/reload', 'activosfijos\GrupoController@reload')->name('activofjo.grupo.reload');
//activo fijo
//Route::resource('afActivo', 'activosfijos\ActivoFijoController');
Route::get('afactivo/index', 'activosfijos\ActivoFijoController@index')->name('afActivo.index');
Route::get('afactivo/create', 'activosfijos\ActivoFijoController@create')->name('afActivo.create');
Route::get('afactivo/edit/{id_activo}', 'activosfijos\ActivoFijoController@edit')->name('afActivo.edit');
Route::match(['get', 'post'], 'acf/activofijo/store', 'activosfijos\ActivoFijoController@store')->name('afActivo.store');


Route::match(['get', 'post'], 'activosfijos/activofijo/search', 'activosfijos\ActivoFijoController@search')->name('activofjo.activofijo.search');
Route::post('activosfijos/activofijo/guardar_color', 'activosfijos\ActivoFijoController@guardar_color')->name('activofjo.guardar_color');
Route::post('activosfijos/activofijo/guardar_serie', 'activosfijos\ActivoFijoController@guardar_serie')->name('activofjo.guardar_serie');
Route::post('activosfijos/activofijo/serie_ac/{id}', 'activosfijos\ActivoFijoController@serie_ac')->name('activofjo.serie_ac');
Route::post('activosfijos/activofijo/guardar_marca', 'activosfijos\ActivoFijoController@guardar_marca')->name('activofjo.guardar_marca');
Route::post('activosfijos/activofijo/marca_ac/{id}', 'activosfijos\ActivoFijoController@marca_ac')->name('activofjo.marca_ac');
Route::post('activosfijos/activofijo/guardar_responsable', 'activosfijos\ActivoFijoController@guardar_responsable')->name('activofjo.guardar_responsable');
Route::match(['get', 'post'], 'activofijo/buscar_activo', 'activosfijos\ActivoFijoController@buscar_activo')->name('activofjo.buscar_activo');
Route::post('activofijo/excel/activos', 'activosfijos\ActivoFijoController@excel_activos')->name('activofjo.excel_activos');

Route::get('activosfijo/eliminar_activo/{id_activo}', 'activosfijos\ActivoFijoController@eliminar_activo')->name('activofjo.eliminar_activo');
Route::match(['get', 'post'], 'acf/activofijo/update_activo/{id_activo}', 'activosfijos\ActivoFijoController@update_activo')->name('activofjo.update_activo');

//excel activp fijo
Route::get('activofjo/informe/index_listado', 'activosfijos\ActivoFijoController@index_listado')->name('activofjo.index_listado');
Route::match(['get', 'post'], 'activofjo/informe/index_listado_tipo', 'activosfijos\ActivoFijoController@index_listado_tipo')->name('activofjo.index_listado_tipo');
Route::match(['get', 'post'], 'activofijo/informe/index','activosfijos\ActivoFijoController@buscar_listado_activos')->name('activofjo.buscar_listado_activos');
Route::match(['get', 'post'], 'activofijo/excel/listado_general', 'activosfijos\ActivoFijoController@excel_listado_general')->name('activofjo.excel_listado_general');
Route::match(['get', 'post'], 'activofijo/excel/depreciacion_acumulada/{id_activo}', 'activosfijos\ActivoFijoController@excel_depreciacion_acumulada')->name('activofjo.excel_depreciacion_acumulada');
Route::match(['get','post'],'activofijo/mantenimientos/activofijo/pdf_activo/{id}', 'activosfijos\ActivoFijoController@pdf_activo')->name('activofjo.pdf_activo');
Route::get('activofjo/masivo/crear_activo/{archivo}', 'activosfijos\ActivoFijoController@crear_activo')->name('activofjo.crear_activo');
Route::get('imprimir/codigo_barras/activofijo/{id}', 'activosfijos\InformesController@codigo_activo')->name('activofjo.codigo_activo');

//pdf informes
Route::match(['get', 'post'], 'pdf/activofijo/listado', 'activosfijos\ActivoFijoController@pdf_listado_general')->name('activofjo.pdf_listado_general');
Route::match(['get', 'post'], 'pdf/activofijo/listado_tipo', 'activosfijos\ActivoFijoController@pdf_listado_tipo')->name('activofjo.pdf_listado_tipo');
Route::match(['get', 'post'], 'pdf/activofijo/depreciacion/{id_activo}', 'activosfijos\ActivoFijoController@pdf_depreciacion')->name('activofjo.pdf_depreciaciacion');

Route::match(['get', 'post'], 'activofijo/excel/depreciacion', 'activosfijos\ActivoFijoController@excel_depreciacion')->name('activofjo.excel_depreciacion');
Route::match(['get', 'post'], 'activofijo/excel/depreciacion/corte', 'activosfijos\ActivoFijoController@excel_depreciacion_corte')->name('activofjo.excel_depreciacion_corte');


Route::get('contable/activo/masivo_dep_acumulada/{name}','activosfijos\DepreciacionController@masivo_dep_acumulada')->name('depreciacion.masivo_dep_acumulada');
Route::match(['get', 'post'],'contable/activos/depreciaciones','activosfijos\DepreciacionController@excel_masivo_depreciacion')->name('depreciacion.excel_masivo_depreciacion');
