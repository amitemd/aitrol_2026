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
 


Route::get('historiaclinica/evolucion/{hcid}', 'hc_admision\EvolucionController@evolucion')->name('evolucion.evolucion');
Route::get('evolucion/mostrar/{hcid}/{ev}', 'hc_admision\EvolucionController@mostrar')->name('evolucion.mostrar');
Route::match(['get', 'post'],'evolucion/indicaciones', 'hc_admision\EvolucionController@indicaciones')->name('evolucion.indicaciones');
Route::match(['get', 'post'],'evolucion/crea_actualiza', 'hc_admision\EvolucionController@crea_actualiza')->name('evolucion.crea_actualiza');
Route::match(['get', 'post'],'evolucion/crea_indicacion', 'hc_admision\EvolucionController@crea_indicacion')->name('evolucion.crea_indicacion');
Route::get('historiaclinica/evolucion/imprimir/{id}', 'hc_admision\EvolucionController@imprimir')->name('evolucion.imprimir');

Route::get('sin_agenda/evolucion/{id}/{ag}', 'hc_admision\EvolucionController@crear_evolucion')->name('sin_agenda.crear_evolucion');

Route::get('historiaclinica/evolucion/imprimir_stream/{id}', 'hc_admision\EvolucionController@imprimir_stream')->name('evolucion.imprimir_stream');



Route::get('evolucion_desc/modal/{id}', 'hc_admision\EvolucionController@pr_modal')->name('evolucion.pr_modal');
Route::get('oxigeno/paciente/modal/{id}', 'hc_admision\EvolucionController@oxigeno_modal')->name('oxigeno.oxigeno_modal');
Route::get('descargo_producto/modelos/modal/{id}', 'hc_admision\EvolucionController@productos_modal')->name('descargoProducto.productos_modal');
Route::post('oxigeno/excel/preview', 'hc_admision\EvolucionController@oxigenExcel')->name('oxigeno.oxigenExcel');
Route::post('descargo/producto/model/excel/preview', 'hc_admision\EvolucionController@descargo_productos')->name('descargoProducto.excel');
Route::post('evolucion_desc/modal/guardar_op', 'hc_admision\EvolucionController@guardar_op')->name('evolucion.guardar_op');
Route::match(['get','post'],'evolucion_desc/nuevo/modal/guardar_op', 'hc_admision\EvolucionController@nuevo_guardar_op')->name('evolucion.nuevo_guardar_op');

Route::get('cardio/formato/{id}', 'hc_admision\EvolucionController@formato')->name('cardio.formato');
Route::get('cardio/evolucion/{id}/{cardio}', 'hc_admision\EvolucionController@cargar')->name('evolucion.cargar');

Route::post('cardio/actualizar', 'hc_admision\EvolucionController@actualizar_cardio')->name('evolucion.actu_cardio');
Route::get('cardio/cie10/{id}', 'hc_admision\EvolucionController@cargar_cie10')->name('cardio.cargar_cie10');

Route::get('cardio/cie10/eliminar/{id}', 'hc_admision\EvolucionController@eliminar_cie10_cardio')->name('cardio.eliminar_cie10_cardio');
Route::post('cardio/cie10/crear', 'hc_admision\EvolucionController@agregar_cie10_ev')->name('cardio.agregar_cie10_ev');
Route::post('cardio/actualizar_evolucion', 'hc_admision\EvolucionController@actualizar_evolucion')->name('cardio.actualizar_evolucion');
Route::post('cardio/actualizar2', 'hc_admision\EvolucionController@actualizar_cardio2')->name('cardio.actualizar_cardio2');

Route::get('imprimir_007/{id}', 'hc_admision\EvolucionController@imprimir_007')->name('formato007.imprimir');

//crud eduardo 


Route::get('formatos/productos/valores', 'hc_admision\FormatoController@index')->name('formatosProductos.index');
Route::get('formatos/productos/valores/edit/{id}', 'hc_admision\FormatoController@edit')->name('formatosProductos.edit');
Route::get('formatos/productos/crear', 'hc_admision\FormatoController@create')->name('formatosProductos.create');
Route::match(['get','post'],'formatos/productos/valores/buscar', 'hc_admision\FormatoController@search')->name('formatosProductos.search');
Route::post('formatos/productos/store', 'hc_admision\FormatoController@store')->name('formatosProductos.store');
Route::post('formatos/productos/update/{id}', 'hc_admision\FormatoController@update')->name('formatosProductos.update');

