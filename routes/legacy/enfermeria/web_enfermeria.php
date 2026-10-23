<?php
Route::match(['get', 'post'], 'enfermeria/signos_vitales/revision', 'EnfermeriaController@index')->name('enfermeria.index');
Route::match(['get', 'post'], 'enfermeria/signos_vitales/reportes/{id}', 'EnfermeriaController@procedimiento')->name('enfermeria.procedimiento');
Route::post('enfermeria/signos_vitales/guardar/', 'EnfermeriaController@guardar')->name('enfermeria.guardar');
Route::post('enfermeria/observacion/', 'EnfermeriaController@guardar_observacion')->name('enfermeria.guardar_observacion');
Route::get('enfermeria/insumos/{id}', 'EnfermeriaController@insumos')->name('enfermeria.insumos');
Route::get('enfermeria/uso/paciente_insumo/eliminar/{id}', 'EnfermeriaController@eliminar_insumo')->name('enfermeria.eliminar_insumo');
Route::get('enfermeria/uso/paciente_insumo/mover/{id}/{id_hc_procedimiento}', 'EnfermeriaController@mover_insumo')->name('enfermeria.mover_insumo');
//Route::get('enfermeria/signos_vitales')->name('enfermeria.buscador')

Route::match(['get', 'post'], 'enfermeria/insumos_usados', 'EnfermeriaController@index_insumos')->name('enfermeria.index_insumos');
Route::get('enfermeria/insumos_uso/{id}', 'EnfermeriaController@insumos_uso')->name('enfermeria.insumos_uso');
Route::get('enfermeria/selec_prod/{id}', 'EnfermeriaController@selec_prod')->name('enfermeria.selec_prod');
Route::get('enfermeria/productos/{id_producto}/{id_procedimiento}', 'EnfermeriaController@productos')->name('enfermeria.productos}');
Route::get('enfermeria/serie_enfermeroget/{codigo}/{procedimiento}', 'EnfermeriaController@serie_enfermeroget')->name('enfermeria.serie_enfermeroget');
Route::get('enfermeria/listado_prod/{proc}', 'EnfermeriaController@listado_prod')->name('enfermeria.listado_prod');
Route::match(['get', 'post'], 'enfermeria/buscar/nombre', 'EnfermeriaController@nombre')->name('enfermeria.nombre');
Route::match(['get', 'post'], 'enfermeria/buscador/nombre', 'EnfermeriaController@nombre2')->name('enfermeria.nombre2');
Route::match(['get', 'post'], 'enfermeria/nombre_plantilla', 'EnfermeriaController@nombre_plantilla')->name('enfermeria.nombre_plantilla');
Route::match(['get', 'post'], 'enfermeria/nombre_plantilla2', 'EnfermeriaController@nombre_plantilla2')->name('enfermeria.nombre_plantilla2');
Route::post('vhenfermeria/guarda_plantilla_basica','EnfermeriaController@vhguardar_plantilla_basica')->name('enfermeria.vhguardar_plantilla_basica');

//7/4/2021
Route::get('enfermeria/insumos_uso/guardar/plantilla', 'EnfermeriaController@guardar_plantilla')->name('enfermeria.guardar_plantilla_ok');
Route::get('enfermeria/insumosuso/obtener/plantilla', 'EnfermeriaController@obtener_plantilla')->name('enfermeria.obtener_plantilla_get');
// inventario - descargo procedimiento paciente
// Route::get('enfermeria/descargo/insumos/{id_agenda}', 'Insumos\InsumosController@descargo_insumos')->name('insumos.descargo.paciente');
//F 9/2/2022
Route::post('enfermeria/insumos/excel/subir', 'EnfermeriaController@insumos_excel')->name('enfermeria.subir_excel');

Route::get('enfermeria/insumos_uso2/{id}', 'EnfermeriaController@insumos_uso2')->name('enfermeria.insumos_uso2');

Route::get('enfermeria/evolucion/{id}', 'EnfermeriaController@evolucion')->name('enfermeria.evolucion_anestesiologo');


Route::match(['get', 'post'],'enfermeria/signos_vitales/guardar_evolucionar/', 'EnfermeriaController@actualiza_evolucionar')->name('enfermeria.guardar_evolucionar');
Route::post('enfermeria/evolucion/eliminar', 'EnfermeriaController@eliminar')->name('enfermeria.evolucion_eliminar');