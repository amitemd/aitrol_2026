<?php

use Sis_medico\ControlSintoma;
// Rutas Turnero
Route::post('turnero/sala', 'turnero\TurneroController@index')->name('turnero_index');
Route::get('turnero/tabla/{id_hospital}/{id_sala}', 'turnero\TurneroController@tabla')->name('turnero_teclado_numerico');
Route::match(['get', 'post'], 'turnero/imprimir', 'turnero\TurneroController@imprimir')->name('turnero_teclado_imprimir');
Route::match(['get', 'post'], 'turnero/documentos', 'turnero\TurneroController@documentos')->name('turnero_teclado_documentos');
Route::match(['get', 'post'], 'turnero/imprimir/boleto', 'turnero\TurneroController@imprimirboleto')->name('turnero_teclado_imprimirboleto');
Route::get('sala/espera/turno', 'turnero\TurneroController@salaespera')->name('turnero_sala_espera');
Route::match(['get', 'post'], 'sala/espera/tabla', 'turnero\TurneroController@tabla_espera')->name('turnero_sala_tabla');
Route::get('sala/espera/administracion', 'turnero\TurneroController@administracion')->name('turnero_sala_administracion');
Route::get('sala/espera/cambio_estado', 'turnero\TurneroController@cambio_estado')->name('turnero_cambio_estado');
Route::get('sala/espera/finalizar', 'turnero\TurneroController@finalizar')->name('turnero_finalizar');
Route::match(['get', 'post'], 'sala/espera/turnos', 'turnero\TurneroController@turnos')->name('turnero_turnos');

//sin cedula
Route::post('turnero/sala/identificacion', 'turnero\TurneroController@index_2')->name('turnero_index_sincedula');
Route::get('turnero/cache', 'turnero\TurneroController@guardar_cache')->name('guardar_cache');
Route::match(['get', 'post'], 'turnero/verificacion/turno', 'turnero\TurneroController@turnero_verificacion')->name('turnero_verificacion');

//sala de espera sin loguin
Route::get('turnero/sala/espera', 'turnero\TurneroController@sala_espera')->name('sala_esperasindocumentos');
Route::match(['get', 'post'], 'sala/espera/nuevo_turnos', 'turnero\TurneroController@nuevo_turnos')->name('nuevo_turnos');
Route::match(['get', 'post'], 'sala/espera/turno_lista', 'turnero\TurneroController@turno_lista')->name('turno_lista');
Route::match(['get', 'post'], 'sala/espera/verficacion_turno', 'turnero\TurneroController@verficacion_turno')->name('verficacion_turno');
Route::match(['get', 'post'], 'sala/espera/verficacion_boleto', 'turnero\TurneroController@verficacion_boleto')->name('verficacion_boleto');
//Excel
Route::post('turnero/sala/excel_buscar', 'turnero\TurneroController@excel_buscar')->name('excel_buscarturnero');
//disposito externo
Route::get('sala/espera/turno_pantalla', 'turnero\TurneroController@turno_pantalla')->name('turno_pantalla_sala_espera');
Route::match(['get', 'post'], 'sala/espera/nuevo_turnos_pantalla', 'turnero\TurneroController@nuevo_turnos_pantalla')->name('nuevo_turnos_pantalla');
Route::match(['get', 'post'], 'sala/espera/turno_lista_pantalla', 'turnero\TurneroController@turno_lista_pantalla')->name('turno_lista_pantalla');



//trabajo de campo
Route::get('trabajo/campo', 'trabajo_campo\TrabajoCampoController@index')->name('trabajo_campo_index');
Route::get('trabajo/campo/create', 'trabajo_campo\TrabajoCampoController@create')->name('trabajo_campo_create');
Route::match(['get', 'post'], 'trabajo/campo/save', 'trabajo_campo\TrabajoCampoController@save')->name('trabajo_campo_save');
Route::get('trabajo/campo/save/{id}', 'trabajo_campo\TrabajoCampoController@editar')->name('trabajo_campo_editar');
Route::match(['get', 'post'], 'trabajo/campo/edit_form', 'trabajo_campo\TrabajoCampoController@edit_form')->name('trabajo_campo_edit_form');
Route::match(['get', 'post'], 'trabajo/campo/buscador', 'trabajo_campo\TrabajoCampoController@buscador')->name('trabajo_campo_buscador');
//select2
Route::match(['get', 'post'], 'trabajo/campo/usuarios', 'trabajo_campo\TrabajoCampoController@usuarios')->name('trabajo_campo_usuarios');
//control sintoma
Route::get('control/sintomas', 'control_sintoma\ControlSintomasController@index')->name('index_control');
Route::get('control/sintomas/create', function () {
    return view('control_sintoma/create');
})->name('create_control');
Route::match(['get', 'post'],'control/sintomas/save', 'control_sintoma\ControlSintomasController@save')->name('save_control');
Route::match(['get', 'post'],'control/sintomas/edit/{id}',function(ControlSintoma $id){
    return view('control_sintoma/edit',['user'=>$id]);
})->name('edit_control');

Route::match(['get', 'post'],'control/sintomas/editar', 'control_sintoma\ControlSintomasController@editar')->name('editar_control');


Route::match(['get', 'post'],'control/sintomas/buscar', 'control_sintoma\ControlSintomasController@buscar')->name('buscar_control');
Route::get('control/sintomas', 'control_sintoma\ControlSintomasController@index')->name('index_control');
Route::match(['get', 'post'],'control/sintomas/buscarusuario', 'control_sintoma\ControlSintomasController@usuarios')->name('buscarusuario_control');


Route::post('sala/espera/imprimir/boleto', 'turnero\TurneroController@imprimir_boleto')->name('imprimir_boleto_turnero');

Route::get('actividad/comercial/index', 'actividad_comercial\ActividadComercialController@index')->name('actividad_comercial.index');
Route::get('actividad/comercial/create', 'actividad_comercial\ActividadComercialController@create')->name('actividad_comercial.create');
Route::post('actividad/comercial/store', 'actividad_comercial\ActividadComercialController@store')->name('actividad_comercial.store');
Route::get('actividad/comercial/view/{id}', 'actividad_comercial\ActividadComercialController@viewActividad')->name('actividad_comercial.viewActividad');


Route::post('actividad/comercial/store/prospecto', 'actividad_comercial\ActividadComercialController@storeProspecto')->name('actividad_comercial.storeProspecto');
Route::post('actividad/comercial/store/estado', 'actividad_comercial\ActividadComercialController@storeEstado')->name('actividad_comercial.storeEstado');
Route::post('actividad/comercial/delete', 'actividad_comercial\ActividadComercialController@deleteActividad')->name('actividad_comercial.delete');

//excel

Route::post('actividad/comercial/excel', 'actividad_comercial\ActividadComercialController@excelactividad')->name('actividad_comercial.excel_actividad');
Route::post('actividad/comercial/busacador', 'actividad_comercial\ActividadComercialController@buscador')->name('actividad_comercial.buscador');

//victor
Route::get('actividad/estados/comercial/', 'ticket_permisos\TicketPermisosController@actividad_comercial_index')->name('ticketpermisos.actividad_comercial_index');
Route::get('actividad/comercial', 'ticket_permisos\TicketPermisosController@actividad_comercial')->name('ticketpermisos.actividad_comercial');
Route::post('actividad/estados/comercial/guardar', 'ticket_permisos\TicketPermisosController@guardar')->name('ticketpermisos.actividad_comercial_guardar');
Route::get('actividad/comercial/eliminar/{id}','ticket_permisos\TicketPermisosController@eliminar_actividad')->name('ticketpermisos.actividad_comercial_eliminar');
Route::get('actividad/comercial/editar/{id}', 'ticket_permisos\TicketPermisosController@actividad_comercial_editar')->name('ticketpermisos.actividad_comercial_editar');
Route::post('actividad/comercial/actualizar','ticket_permisos\TicketPermisosController@actualizar')->name('ticketpermisos.actividad_comercial_actualizar');
Route::post('actividad/comercial/excel_diferencia', 'actividad_comercial\ActividadComercialController@excel_diferencia')->name('actividad_comercial.excel_diferencia');
Route::match(['get', 'post'], 'actividad/comercial/estadistico', 'actividad_comercial\ActividadComercialController@actividad_estadistico')->name('actividad_comercial.estadistico');
Route::post('actividad/comercial/buscador_añio', 'actividad_comercial\ActividadComercialController@buscador_anio')->name('actividad_comercial.buscador_anio');




