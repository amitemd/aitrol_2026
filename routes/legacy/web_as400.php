<?php

//rutas para la conexion con el AS400

Route::get('/ingreso/orden/AS400', 'As400Controller@index')->name('as400.index');
Route::post('/validar/codigo/AS400/', 'As400Controller@validar_codigo')->name('as400.validar_codigo');
Route::post('/as400/guardar/examen/', 'As400Controller@guardar_orden')->name('as400.guardar_orden');
Route::post('/as400/enviar/resultados/', 'As400Controller@enviar_orden')->name('as400.enviar_orden');
Route::get('/ingreso/orden_hc4/AS400', 'As400Controller@index_hc4')->name('as400.index_hc4');

Route::get('orden/visualizar/publico/{id}', 'As400Controller@descargar_orden')->name('as400.visualizar');

#Receta-Usuario | view Paciente-recetas_usuario.
Route::get('recetas/paciente', 'RecetaUsuarioController@recetas_usuario')->name('recetas_usuario');
Route::get('recetas/paciente/imprime/{id}/{tipo}', 'RecetaUsuarioController@imprime')->name('receta_imprime');
Route::get('agenda/paciente/descarga/{nombre}', 'As400Controller@descargar_agenda');
Route::get('vivokey/{id_paciente}', 'SinLoginController@vivokey');

Route::get('importe/archivo/compras/{nombre}', 'ImportarController@cuadre_compras');
Route::get('eduardo/subida/datos', 'DashboardController@plan_cuentas_subida');
