<?php
Route::get('guia/remision/created', 'guia\GuiaRemisionController@created')->name('index_guia_remision');
Route::post('guia/ci/nombres', 'guia\GuiaRemisionController@ci_nombres')->name('ci_nombres_function');
Route::post('guia/ci/nombres/destinatario', 'guia\GuiaRemisionController@ci_nombres_destinatario')->name('ci_nombres_destinatario');
Route::post('guia/remision/guardar', 'guia\GuiaRemisionController@guardar')->name('guia_remision_guardar');
Route::get('guia/remision/index', 'guia\GuiaRemisionController@index')->name('guia_remision_index');
Route::post('guia/remision/buscador', 'guia\GuiaRemisionController@buscador')->name('guia_remision_buscador');
Route::get('guia/remision/update', 'guia\GuiaRemisionController@update')->name('guia_remision_update');
Route::post('guia/remision/save_update', 'guia\GuiaRemisionController@save_update')->name('guia_remision_save_update');
Route::post('guia/remision/codigo', 'guia\GuiaRemisionController@codigo')->name('guia_remision_codigo');
Route::post('guia/remision/send/information', 'guia\GuiaRemisionController@send_information')->name('send_information_guia');
Route::post('guia/remision/send/destinatario', 'guia\GuiaRemisionController@destinatario')->name('destinatario_guia');
Route::get('guia/remision/send/datos/transportista', 'guia\GuiaRemisionController@transportista_datos')->name('transportista_datos_guia');

Route::get('guia/remision/send/datos/destinatario', 'guia\GuiaRemisionController@destinatario_datos')->name('destinatario_datos_guia');
Route::get('guia/remision/modal/crear', 'guia\GuiaRemisionController@crear_transportista')->name('crear_transportista_datos_guia');
Route::post('guia/remision/modal/crear/save', 'guia\GuiaRemisionController@save_transportista')->name('save_transportista_datos_guia');
Route::get('guia/remision/modal/crear/validar_campos', 'guia\GuiaRemisionController@validar_campos')->name('validar_campos_transportista_datos_guia');
Route::get('guia/remision/modal/crear/validar_cedula', 'guia\GuiaRemisionController@validar_cedula')->name('validar_cedula_datos_guia');
Route::post('guia/remision/modal/crear/agregar_opcion', 'guia\GuiaRemisionController@agregar_opcion')->name('agregar_opcion_cedula_datos_guia');

//Transportistas

Route::get('contable/Guia_Remision/Transportistas/index', 'guia\TransportistasController@index')->name('transportistas.index');
Route::get('contable/Guia_Remision/Transportistas/crear', 'guia\TransportistasController@crear')->name('transportistas.crear');
Route::post('contable/Guia_Remision/Transportistas/store', 'guia\TransportistasController@store')->name('transportistas.store');
Route::get('contable/Guia_Remision/Transportistas/editar/{id}', 'guia\TransportistasController@editar')->name('transportistas.editar');
Route::post('contable/Guia_Remision/Transportistas/update', 'guia\TransportistasController@update')->name('transportistas.update');
Route::match(['get', 'post'], 'contable/Guia_Remision/Transportistas/delete', 'guia\TransportistasController@delete')->name('transportistas.delete');

Route::post('guia/remision/modal/llenar/campos', 'guia\GuiaRemisionController@llenar_campos')->name('llenar_campos_transportista_datos_guia');
Route::post('guia/remision/modal/llenar/campos_destinatario', 'guia\GuiaRemisionController@llenar_campos_destinatario')->name('llenar_campos_destinatario_datos_guia');

Route::post('llenar/productos/guia/remision', 'guia\GuiaRemisionController@llenar_productos')->name('llenar_productos_guia_remision');

Route::get('guiaremision/{submodulo}', 'guia\GuiaRemisionController@index');
Route::post('guiaremision/{submodulo}', 'guia\GuiaRemisionController@index');
Route::get('contable/consultar/productos', 'guia\GuiaRemisionController@cargar_productos')->name('consultar_productos.guia');

Route::get('contable/consultar/llenar/campos', 'guia\GuiaRemisionController@autocompletar_campos')->name('autocompletar_campos.guia');
