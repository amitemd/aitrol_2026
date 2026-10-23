<?php
Route::match(['get', 'post'],'limpieza/index/{id_sala}/{id_paciente}/{id_pentax}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@index')->name('limpieza.index');
Route::match(['get', 'post'],'limpieza/index_paciente/{id_sala}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@index_paciente')->name('limpieza.index_paciente');
Route::get('limpieza/crear/{id_paciente}/{id_pentax}/{id_sala}','limpieza_desinfeccion\LimpiezaDesinfeccionController@crear')->name('limpieza.crear');
Route::get('limpieza/crear2/{id_sala}','limpieza_desinfeccion\LimpiezaDesinfeccionController@crear2')->name('limpieza.crear2');
Route::post('limpieza/guardar','limpieza_desinfeccion\LimpiezaDesinfeccionController@guardar')->name('limpieza.guardar');
Route::get('limpieza/editar/{id}','limpieza_desinfeccion\LimpiezaDesinfeccionController@editar')->name('limpieza.editar');
Route::post('limpieza/update','limpieza_desinfeccion\LimpiezaDesinfeccionController@update')->name('limpieza.update');
Route::get('limpieza/eliminar/{id}','limpieza_desinfeccion\LimpiezaDesinfeccionController@eliminar')->name('limpieza.eliminar');
Route::match(['get', 'post'],'limpieza/imprimir_excel/{id_sala}/{tipo}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@imprimir_excel')->name('limpieza.imprimir_excel');
Route::match(['get', 'post'],'limpieza/salas', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@salas')->name('limpieza.salas');

Route::match(['get', 'post'], 'limpieza/paciente_nombre', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@paciente_nombre')->name('limpieza.paciente_nombre');
Route::match(['get', 'post'], 'limpieza/paciente_nombre2', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@paciente_nombre2')->name('limpieza.paciente_nombre2');

Route::post('limpieza/guardar2','limpieza_desinfeccion\LimpiezaDesinfeccionController@guardar2')->name('limpieza.guardar2');