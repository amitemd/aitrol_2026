<?php
Route::get('ticket_soporte_tecnico/evidencia/{token}', 'GatewayAttachmentController@download')->where('token', '[a-f0-9]{64}')->name('gateway_tickets.attachment');
Route::get('ticket_soporte_tecnico/gateway/listado', 'GatewayTicketController@listing')->name('gateway_tickets.list');
Route::post('ticket_soporte_tecnico/gateway/actualizar/{id}', 'GatewayTicketController@refresh')->name('gateway_tickets.refresh');
Route::post('ticket_soporte_tecnico/gateway/importar', 'GatewayTicketController@import')->name('gateway_tickets.import');
Route::get('ticket_soporte_tecnico/gateway', 'GatewayTicketController@create')->name('gateway_tickets.create');
Route::post('ticket_soporte_tecnico/gateway', 'GatewayTicketController@store')->name('gateway_tickets.store');

//Registro de Limpieza de Baños
Route::get('limpieza_banos/index', 'servicios_generales\LimpiezaBanosController@salas')->name('limpieza_banos.index');
Route::get('limpieza_banos/sala', 'servicios_generales\LimpiezaBanosController@index')->name('limpieza_banos.index_2');
Route::match(['get', 'post'], 'limpieza_banos/create/{id_sala}', 'servicios_generales\LimpiezaBanosController@create')->name('limpieza_banos.create');
Route::match(['get', 'post'], 'limpieza_banos/store', 'servicios_generales\LimpiezaBanosController@guardar')->name('limpieza_banos.store');
Route::match(['get', 'post'], 'limpieza_banos/buscar_fecha', 'servicios_generales\LimpiezaBanosController@buscar_fecha')->name('limpieza_banos.buscar_fecha');
Route::post('limpieza_banos/subir_imagen', 'servicios_generales\LimpiezaBanosController@subir_imagen')->name('limpieza_banos.subir_imagen');
Route::match(['get', 'post'], 'limpieza_banos/edit/{id}', 'servicios_generales\LimpiezaBanosController@editar')->name('limpieza_banos.edit');
Route::match(['get', 'post'], 'limpieza_banos/update', 'servicios_generales\LimpiezaBanosController@actualizar')->name('limpieza_banos.update');
Route::post('limpieza_banos/excel', 'servicios_generales\LimpiezaBanosController@excel')->name('limpieza_banos.excel');
Route::get('limpieza_banos/reportes/nuevo_reporte', 'servicios_generales\LimpiezaBanosController@vistareportes')->name('limpieza_banos.nuevo_reporte');

Route::get('limpieza_banos/agregarhoras', 'servicios_generales\LimpiezaBanosController@agregarhoras')->name('limpieza_banos.agregarhoras');

Route::get('limpieza_banos/pisos', 'servicios_generales\LimpiezaBanosController@pisos')->name('limpieza_banos.pisos');

//Mantenimientos

Route::match(['get', 'post'], 'mantenimientos/index', 'servicios_generales\MantenimientosGeneralesController@index')->name('mantenimientos_generales.index');
Route::match(['get', 'post'], 'mantenimientos/create', 'servicios_generales\MantenimientosGeneralesController@crear')->name('mantenimientos_generales.create');
Route::match(['get', 'post'], 'mantenimientos/edit/{id}', 'servicios_generales\MantenimientosGeneralesController@editar')->name('mantenimientos_generales.edit');
Route::match(['get', 'post'], 'mantenimientos/store', 'servicios_generales\MantenimientosGeneralesController@guardar')->name('mantenimientos_generales.store');
Route::match(['get', 'post'], 'mantenimientos/update', 'servicios_generales\MantenimientosGeneralesController@actualizar')->name('mantenimientos_generales.update');
Route::match(['get', 'post'], 'mantenimientos/buscar_piso', 'servicios_generales\MantenimientosGeneralesController@buscar_piso')->name('mantenimientos_generales.buscar_piso');

Route::resource('servicios_generales', 'MantenimientosGeneralesController');

//Mantenimientos Salas
Route::get('mantenimientos_oficinas/{mantenimientos_generales}/crear', 'servicios_generales\Mantenimientos_OficinasController@crear')->name('mantenimientos_oficinas.crear');
Route::get( 'mantenimientos_oficinas/{mantenimientos_generales}/listasoficinas', 'servicios_generales\Mantenimientos_OficinasController@listasoficinas')->name('mantenimientos_oficinas.listasoficinas');
Route::post('mantenimientos_oficinas/{mantenimientos_generales}/grabar', 'servicios_generales\Mantenimientos_OficinasController@grabar')->name('mantenimientos_oficinas.grabar');
Route::get('mantenimientos_oficinas/{mantenimientos_generales}/{oficina}/editar', 'servicios_generales\Mantenimientos_OficinasController@editar')->name('mantenimientos_oficinas.editar');
Route::match(array('PUT', 'PATCH'), 'mantenimientos_oficinas/{mantenimientos_generales}/{oficna}', 'servicios_generales\Mantenimientos_OficinasController@actualizar')->name('mantenimientos_oficinas.actualizar');
//Mantenimientos baños
Route::get('mantenimientos_banos/{mantenimientos_generales}/listasbanos', 'servicios_generales\Mantenimientos_BanosController@listasbanos')->name('mantenimientos_banos.listasbanos');
Route::get('mantenimientos_banos/{mantenimientos_generales}/crear', 'servicios_generales\Mantenimientos_BanosController@crear')->name('mantenimientos_banos.crear');
Route::post('mantenimientos_banos/{mantenimientos_generales}/grabar', 'servicios_generales\Mantenimientos_BanosController@grabar')->name('mantenimientos_banos.grabar');
Route::get('mantenimientos_banos/{mantenimientos_generales}/{banos}/editar', 'servicios_generales\Mantenimientos_BanosController@editar')->name('mantenimientos_banos.editar');
Route::match(array('PUT', 'PATCH'), 'mantenimientos_banos/{mantenimientos_generales}/{banos}', 'servicios_generales\Mantenimientos_BanosController@actualizar')->name('mantenimientos_banos.actualizar');
//Registro de Limpieza de Equipos
Route::get('limpieza_equipo/index', 'servicios_generales\LimpiezaEquipoController@index')->name('limpieza_equipo.index');
Route::get('limpieza_equipo/registro/{id}/{id_sala}/{id_pentax}', 'servicios_generales\LimpiezaEquipoController@registro')->name('limpieza_equipo.registro');
Route::get('limpieza_equipo/autocomplete', 'servicios_generales\LimpiezaEquipoController@autocomplete')->name('limpieza_equipo.autocomplete');
Route::get('limpieza_equipo/guardar', 'servicios_generales\LimpiezaEquipoController@guardar')->name('limpieza_equipo.guardar');
Route::get('limpieza_equipo/editar/{id}', 'servicios_generales\LimpiezaEquipoController@editar')->name('limpieza_control.editar');
Route::get('limpieza_equipo/marca', 'servicios_generales\LimpiezaEquipoController@marca')->name('limpieza_control.marca');
Route::get('limpieza_equipo/actualizar', 'servicios_generales\LimpiezaEquipoController@actualizar')->name('limpieza_control.actualizar');
Route::post('limpieza_equipo/buscar_fecha', 'servicios_generales\LimpiezaEquipoController@buscar_fecha')->name('limpieza_control.buscar_fecha');
Route::post('limpieza_equipo/excel', 'servicios_generales\LimpiezaEquipoController@excel')->name('limpieza_control.excel');
//Route::get('limpieza_equipo/vista_nueva/{id}', 'servicios_generales\LimpiezaEquipoController@vista_nueva')->name('limpieza_equipo.vista_nueva');

//Mantenimiento IECED Horarios
Route::get('mantenimiento_horarios/index', 'servicios_generales\MantenimientoHorarioController@index')->name('mantenimientohorario.index');
Route::get('mantenimiento_horarios/registrar', 'servicios_generales\MantenimientoHorarioController@registrar')->name('mantenimientohorario.registrar');
Route::get('mantenimiento_horarios/guardar', 'servicios_generales\MantenimientoHorarioController@guardar')->name('mantenimientohorario.guardar');
Route::get('mantenimiento_horarios/editar/{id}', 'servicios_generales\MantenimientoHorarioController@editar')->name('mantenimientohorario.editar');
Route::get('mantenimiento_horarios/update', 'servicios_generales\MantenimientoHorarioController@update')->name('mantenimientohorario.update');
//nuevo
Route::get('mantenimiento_horarios/nombre_piso', 'servicios_generales\MantenimientoHorarioController@nombre_piso')->name('riesgo.nombre_piso');
Route::get('mantenimiento_horarios/agregar', 'servicios_generales\MantenimientoHorarioController@agregar')->name('mantenimientohorario.agregar');
Route::get('mantenimiento_horarios/agregarhor', 'servicios_generales\MantenimientoHorarioController@agregarhor')->name('mantenimientohorario.agregarhor');
Route::get('mantenimiento_horarios/modalobs/{id}', 'servicios_generales\MantenimientoHorarioController@modalobs')->name('mantenimientohorario.modalobs');
Route::get('mantenimiento_horarios/agragsobs', 'servicios_generales\MantenimientoHorarioController@agragsobs')->name('mantenimientohorario.agragsobs');
Route::match(['get', 'post'], 'mantenimiento_horarios/buscar', 'servicios_generales\MantenimientoHorarioController@buscar')->name('mantenimientohorario.buscar');
Route::post('mantenimiento_horarios/excel', 'servicios_generales\MantenimientoHorarioController@excel')->name('mantenimientohorario.excel');
Route::get('mantenimiento_horarios/modaleditar', 'servicios_generales\MantenimientoHorarioController@modaleditar')->name('mantenimientohorario.modaleditar'); //modaleditar
Route::get('mantenimiento_horarios/reporte', 'servicios_generales\MantenimientoHorarioController@reporte')->name('mantenimientohorario.reporte'); //modaleditar

//limpieza y desinfeccion

Route::match(['get', 'post'], 'limpieza/index/{id_sala}/{id_paciente}/{id_pentax}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@index')->name('limpieza.index');
Route::match(['get', 'post'], 'limpieza/index_paciente/{id_sala}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@index_paciente')->name('limpieza.index_paciente');
Route::get('limpieza/crear/{id_paciente}/{id_pentax}/{id_sala}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@crear')->name('limpieza.crear');
Route::get('limpieza/crear2/{id_sala}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@crear2')->name('limpieza.crear2');
Route::post('limpieza/guardar', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@guardar')->name('limpieza.guardar');
Route::get('limpieza/editar/{id}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@editar')->name('limpieza.editar');
Route::post('limpieza/update', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@update')->name('limpieza.update');
Route::get('limpieza/eliminar/{id}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@eliminar')->name('limpieza.eliminar');
Route::match(['get', 'post'], 'limpieza/imprimir_excel/{id_sala}/{tipo}', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@imprimir_excel')->name('limpieza.imprimir_excel');
Route::match(['get', 'post'], 'limpieza/salas', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@salas')->name('limpieza.salas');

Route::match(['get', 'post'], 'limpieza/paciente_nombre', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@paciente_nombre')->name('limpieza.paciente_nombre');
Route::match(['get', 'post'], 'limpieza/paciente_nombre2', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@paciente_nombre2')->name('limpieza.paciente_nombre2');

Route::post('limpieza/guardar2', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@guardar2')->name('limpieza.guardar2');

//Riesgo Caida
Route::match(['get', 'post'], 'gestion/camilla/index', 'enfermeria\Control_CaidaController@index')->name('camilla.index');
Route::match(['get', 'post'], 'gestion/camilla/buscar', 'enfermeria\Control_CaidaController@search')->name('riesgo.search');
Route::match(['get', 'post'], 'gestion/camilla/modal_riesgo/{id}', 'enfermeria\Control_CaidaController@modal_riesgo_caida')->name('riesgo_caida.modal');
Route::get('gestion/camilla/verificar', 'enfermeria\Control_CaidaController@verificar')->name('riesgo.verificar');
Route::get('gestion/camilla/calc_edad', 'enfermeria\Control_CaidaController@calc_edad')->name('riesgo.calc_edad');
Route::get('gestion/camilla/form_mayor/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@form_mayor')->name('riesgo.form_mayor');
Route::get('gestion/camilla/form_menor/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@form_menor')->name('riesgo.form_menor');
Route::post('gestion/camilla/form_menor/guardar_datos', 'enfermeria\Control_CaidaController@guardar_datos')->name('riesgo.guardar_datos');
Route::match(['get', 'post'], 'gestion/camilla/modal_cambio/{id}', 'enfermeria\Control_CaidaController@modal_cambio')->name('riesgo_cambio.modal');
Route::get('gestion/camilla/cambio_estado', 'enfermeria\Control_CaidaController@cambio_estado')->name('riesgo.cambio_estado');
Route::get('gestion/camilla/buscar_estado', 'enfermeria\Control_CaidaController@buscar_estado')->name('riesgo.buscar_estado');
Route::post('gestion/camilla/form_menor/guardar_datos_menor', 'enfermeria\Control_CaidaController@guardar_datos_menor')->name('guardar_datos_menor');
Route::match(['get', 'post'], 'gestion/camilla/modal_estado/{id}', 'enfermeria\Control_CaidaController@modal_estado')->name('riesgo_cambio.modal_estado');
Route::get('gestion/camilla/cambio_estado_uno', 'enfermeria\Control_CaidaController@cambio_estado_uno')->name('cambio_estado_uno');
Route::get('gestion/camilla/gaurdar_estados/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@gaurdar_estados')->name('riesgo.gaurdar_estados');
Route::get('gestion/camilla/camas_estado', 'enfermeria\Control_CaidaController@camas_estado')->name('camas_estado');
Route::get('gestion/camilla/camas_estado_hab', 'enfermeria\Control_CaidaController@camas_estado_hab')->name('camas_estado_hab');
Route::get('gestion/camilla/background_estado/{id_cama}', 'enfermeria\Control_CaidaController@background_estado')->name('background_estado');
Route::match(['get', 'post'], 'gestion/camillas/actualizar_estado', 'enfermeria\Control_CaidaController@actualizar_estado')->name('actualizar_estado');
Route::get('gestion/camilla/registro', 'enfermeria\Control_CaidaController@registro')->name('registro_camas');
Route::get('gestion/camilla/tabla', 'enfermeria\Control_CaidaController@tabla')->name('riesgo.tabla');
Route::get('gestion/camilla/pdf/{id_agenda}', 'enfermeria\Control_CaidaController@pdf_mayor_edad')->name('riesgo.pdf');
Route::get('gestion/camilla/pdf_menor/{id_agenda}', 'enfermeria\Control_CaidaController@pdf_menor_edad')->name('riesgo_menor.pdf');
//guardar paciente sin riesgo
Route::get('gestion/camilla/guardar_sinriesgo/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@guardar_sinriesgo')->name('camilla.guardar_sinriesgo');
Route::get('gestion/camilla/guardar_sinriesgo/ocupar_por_sala', 'enfermeria\Control_CaidaController@ocupar_por_sala')->name('camilla.ocupar_por_sala');
//ocupar sala sin camilla cominezo
Route::get('gestion/camilla/buscar_estado_sin_cama', 'enfermeria\Control_CaidaController@buscar_estado_sin_cama')->name('buscar_estado_sin_cama');
//Actualizar update
Route::match(['get', 'post'], 'gestion/camilla/actualizar_masivo', 'enfermeria\Control_CaidaController@actualizar_masivo')->name('camilla.actualizar_masivo');
Route::match(['get', 'post'], 'gestion/camilla/comprobar_sesion', 'enfermeria\Control_CaidaController@comprobar_sesion')->name('camilla.comprobar_sesion');
Route::get('gestion/camilla/form_mayor_sincama/{id}/{id_agenda}', 'enfermeria\Control_CaidaController@mayor_sincama')->name('riesgo.mayorsincama');
Route::get('gestion/camilla/form_menor_sincama/{id}/{id_agenda}', 'enfermeria\Control_CaidaController@menor_sincama')->name('riesgo.menorsincama');

//equipoNuevo
Route::post('buscar/sala/completo', 'servicios_generales\LimpiezaEquipoController@buscar_sala')->name('buscarcompleto_sala');
//editar horario
Route::get('horario/editar/{id}', 'servicios_generales\MantenimientoHorarioController@editar_horario')->name('mantenimiento.editar_horario');
Route::post('editar/frecuencia/completo', 'servicios_generales\MantenimientoHorarioController@editarcompleto');
//nueva vista excel
Route::get('limpieza_equipo/reportes/excel', 'servicios_generales\LimpiezaEquipoController@vistaexcel')->name('limpieza_equipo.reporteexcel');
//Soporte tecnico ticket
//Route::resource('tecnicas/{agenda}', 'tecnicas\TecnicasController');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/index', 'Ticket_Soporte_TecnicoController@index')->name('ticket_soporte_tecnico.index');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/create', 'Ticket_Soporte_TecnicoController@create')->name('ticket_soporte_tecnico.create');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/guardar', 'Ticket_Soporte_TecnicoController@guardar')->name('ticket_soporte_tecnico.guardar');
Route::match(['get', 'post'], 'ticket_soporte_tecnico/admin_control', 'Ticket_Soporte_TecnicoController@admin_control')->name('ticket_soporte_tecnico.admin_control'); //actualizar
Route::match(['get', 'post'], 'ticket_soporte_tecnico/control_req/{id}', 'Ticket_Soporte_TecnicoController@control_req')->name('ticket_soporte_tecnico.control_req'); //editar
Route::match(['get', 'post'], 'ticket_soporte_tecnico/excel', 'Ticket_Soporte_TecnicoController@excel_soporte_tecnico')->name('ticket_soporte_tecnico.excel'); //excel
Route::post('ticket_soporte_tecnico/autocompletar', 'Ticket_Soporte_TecnicoController@autocompletar')->name('ticket_soporte_tecnico.autocompletar'); //excel
Route::match(['get', 'post'], 'ticket_soporte_tecnico/buscador', 'Ticket_Soporte_TecnicoController@buscador')->name('ticket_soporte_tecnico.buscador'); //buscador

//modal
Route::get('limpieza_banos/foto/grande', 'servicios_generales\LimpiezaBanosController@modal_foto')->name('limpieza_banos.modal_foto');
Route::get('limpieza_banos/foto2/grande2', 'servicios_generales\LimpiezaBanosController@modal_foto2')->name('limpieza_banos.modal_foto2');

//masivos
Route::get('ticket_permisos/execute', 'ticket_permisos\TicketPermisosController@execute')->name('ticketpermisos.execute');
Route::get('mantenimientos/actualizar', 'servicios_generales\MantenimientosGeneralesController@actualizar_inventario')->name('actualizar.actualizar_inventario');
Route::get('mantenimientos/actualizar_bodega', 'servicios_generales\MantenimientosGeneralesController@actualizar_bodega')->name('actualizar_b.actualizar_bodega');

//masivos
Route::get('limpieza_equipo/masivo/arreglar', 'servicios_generales\LimpiezaEquipoController@arreglar_masivo');
//mantenimiento examenes
Route::get('examenes/documento/editar', 'servicios_generales\LimpiezaEquipoController@excel_index')->name('documento.excel_index');
Route::get('examenes/documento/actualizar', 'servicios_generales\LimpiezaEquipoController@excel_actualizar')->name('documento.excel_actualizar');
Route::match(['get', 'post'], 'examenes/documento/update', 'servicios_generales\LimpiezaEquipoController@excel_update')->name('documento.update_excel');
Route::match(['get', 'post'], 'examenes/documento/buscar', 'servicios_generales\LimpiezaEquipoController@buscar')->name('documento.buscar');

//
Route::post('recibo/cobro/app/reporte', 'servicios_generales\LimpiezaEquipoController@reporte_apps')->name('excel.reporte_apps');
//examen tubo
Route::get('mantenimiento/examenes/tubos', 'servicios_generales\MantenimientoController@index')->name('mantenimientoexcel.index');
Route::get('mantenimiento/examenes/actualizar', 'servicios_generales\MantenimientoController@actualizar')->name('mantenimientoexcel.actualizar');
Route::post('mantenimiento/examenes/update', 'servicios_generales\MantenimientoController@update')->name('mantenimientoexcel.update');
Route::match(['get', 'post'],'mantenimiento/examenes/buscador', 'servicios_generales\MantenimientoController@buscador')->name('mantenimientoexcel.buscador');
//excel tubo
Route::get('subir/examenes/excel', 'servicios_generales\MantenimientoController@excel_tubos');
//imprimir codigo barras
Route::get('imprimir/codigo/barras/{id}', 'servicios_generales\MantenimientoController@imprimir_barra')->name('imprimir_codigo_barra');
//10-08-2022 modal de limpieza_banos
Route::get('limpieza/salas/limpieza', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@modalServiciosGenerales')->name('mantenimientohorario.limpieza_registro');
Route::post('limpieza/salas/limpieza/guardar', 'limpieza_desinfeccion\LimpiezaDesinfeccionController@serviciosGenerales_guardar')->name('mantenimientohorario.limpieza_guardar');
//11-08-2022 limpieza salas crud
Route::get('limpieza/pentax/index', 'limpieza_pentax\LimpiezaPentaxController@index_pentax')->name('index_pentax_limpieza');
Route::get('limpieza/pentax/created', 'limpieza_pentax\LimpiezaPentaxController@created_pentax')->name('created_pentax_limpieza');
Route::post('limpieza/pentax/save', 'limpieza_pentax\LimpiezaPentaxController@save_pentax')->name('save_pentax_limpieza');
Route::get('limpieza/pentax/updated', 'limpieza_pentax\LimpiezaPentaxController@updated_pentax')->name('updated_pentax_limpieza');
Route::post('limpieza/pentax/edit', 'limpieza_pentax\LimpiezaPentaxController@edit_pentax')->name('edit_pentax_limpieza');
Route::post('limpieza/pentax/buscar/sala', 'limpieza_pentax\LimpiezaPentaxController@buscar_sala')->name('buscar_sala_pentax_limpieza');
Route::post('limpieza/pentax/buscar/todo', 'limpieza_pentax\LimpiezaPentaxController@buscador')->name('buscador_pentax_limpieza');
Route::get('limpieza/pentax/modal/foto', 'limpieza_pentax\LimpiezaPentaxController@modal_foto')->name('modal_foto_pentax_limpieza');
Route::post('limpieza/pentax/excel', 'limpieza_pentax\LimpiezaPentaxController@excel_foto')->name('excel_limpiezaPentax');
//17-08-2022
Route::get('limpieza/horario/index', 'servicios_generales\MantenimientoHorarioController@registro_new')->name('registro_new_limpieza');
Route::post('limpieza/eleccion/tipo', 'limpieza_pentax\LimpiezaPentaxController@eleccion_tipo')->name('eleccion-tipo');
// Route::post('limpieza/eleccion2/tipo2', 'limpieza_pentax\LimpiezaPentaxController@eleccion_tipo2')->name('eleccion-tipo2');
Route::match(['get', 'post'],'limpieza/eleccion2/tipo2', 'limpieza_pentax\LimpiezaPentaxController@eleccion_tipo2')->name('eleccion-tipo2');
//24/8/2022
Route::get('limpieza/area/created', 'servicios_generales\LimpiezaAreaController@created')->name('created_limpieza_area');
Route::post('limpieza/area/created/save', 'servicios_generales\LimpiezaAreaController@save')->name('save_limpieza_area');
Route::get('limpieza/area/created/index', 'servicios_generales\LimpiezaAreaController@index')->name('index_limpieza_area');
Route::get('limpieza/area/created/edit', 'servicios_generales\LimpiezaAreaController@edit')->name('edit_limpieza_area');
Route::post('limpieza/area/created/edit_save', 'servicios_generales\LimpiezaAreaController@edit_save')->name('edit_save_limpieza_area');
Route::match(['get', 'post'], 'limpieza/area/created/buscador', 'servicios_generales\LimpiezaAreaController@buscador')->name('buscador_limpieza_area');
Route::post('limpieza/areas/excel', 'servicios_generales\LimpiezaAreaController@excel')->name('limpieza_areas.excel');
Route::get('limpieza_area/reporte_areas', 'servicios_generales\LimpiezaAreaController@vistareportesareas')->name('limpieza_area.reporte_area');
//30/8/2022
Route::get('limpieza/area/modal', 'servicios_generales\LimpiezaAreaController@modal_foto')->name('modal_foto_limpieza_area');
// /
//Route::get('limpieza/area/reporte_areas', 'servicios_generales\LimpiezaAreaController@vistareportesareas')->name('limpieza_areas.reporte_areas');
//Mantenimientos Dotacion
Route::match(['get', 'post'], 'mantenimientos_d/index', 'servicios_generales\Mantenimientos_DotacionController@index')->name('mantenimientos_d.index');
Route::match(['get', 'post'], 'mantenimientos_d/create', 'servicios_generales\Mantenimientos_DotacionController@crear')->name('mantenimientos_d.create');
Route::match(['get', 'post'], 'mantenimientos_d/edit/{id}', 'servicios_generales\Mantenimientos_DotacionController@editar')->name('mantenimientos_d.edit');
Route::match(['get', 'post'], 'mantenimientos_d/store', 'servicios_generales\Mantenimientos_DotacionController@guardar')->name('mantenimientos_d.store');
Route::match(['get', 'post'], 'mantenimientos_d/update', 'servicios_generales\Mantenimientos_DotacionController@actualizar')->name('mantenimientos_d.update');
Route::match(['get', 'post'], 'mantenimientos_d/buscar_piso', 'servicios_generales\Mantenimientos_DotacionController@buscar_dotacion')->name('mantenimientos_d.buscar_piso');
//Mantenimientos Insumos limpieza
Route::match(['get', 'post'], 'mantenimientos_inlimpieza/index', 'servicios_generales\Mantenimientos_Insumos_LimpiezaController@index')->name('mantenimientos_inlimpieza.index');
Route::match(['get', 'post'], 'mantenimientos_inlimpieza/create', 'servicios_generales\Mantenimientos_Insumos_LimpiezaController@crear')->name('mantenimientos_inlimpieza.create');
Route::match(['get', 'post'], 'mantenimientos_inlimpieza/edit/{id}', 'servicios_generales\Mantenimientos_Insumos_LimpiezaController@editar')->name('mantenimientos_inlimpieza.edit');
Route::match(['get', 'post'], 'mantenimientos_inlimpieza/store', 'servicios_generales\Mantenimientos_Insumos_LimpiezaController@guardar')->name('mantenimientos_inlimpieza.store');
Route::match(['get', 'post'], 'mantenimientos_inlimpieza/update', 'servicios_generales\Mantenimientos_Insumos_LimpiezaController@actualizar')->name('mantenimientos_inlimpieza.update');
Route::match(['get', 'post'], 'mantenimientos_inlimpieza/buscar_piso', 'servicios_generales\Mantenimientos_Insumos_LimpiezaController@inusmoslimpieza')->name('mantenimientos_inlimpieza.buscar_piso');
//Victor Labs
Route::get('laboratorio/subir/documento', 'limpieza_pentax\LimpiezaPentaxController@subir_documento')->name('subir_documento_laboratorio');
Route::post('laboratorio/subir/subir/documento', 'limpieza_pentax\LimpiezaPentaxController@subir_documento_save')->name('subir_documento_laboratorio_save');
Route::get('laboratorio/subir/documento/ver', 'limpieza_pentax\LimpiezaPentaxController@vizualizar_docs_subidos')->name('vizualizar_docs_subidos');
//Route::match(['get', 'post'], 'laboratorio/subir/documento/ver/{id}', 'limpieza_pentax\LimpiezaPentaxController@vizualizar_docs_subidos')->name('vizualizar_docs_subidos');
Route::get('laboratorio/subir/documento/vizualizar_pdf', 'limpieza_pentax\LimpiezaPentaxController@vizualizar_pdf')->name('vizualizar_pdf_docs_subidos');
Route::get('laboratorio/subir/documento/cambiar_estado', 'limpieza_pentax\LimpiezaPentaxController@cambiar_estado')->name('cambiar_estado_documentos');
Route::match(['get', 'post'], 'laboratorio/subir/documento/buscar/documento', 'limpieza_pentax\LimpiezaPentaxController@buscar_documento')->name('cambiar_buscar_documento');
Route::get('masivo/examenes/tubo/excel', 'ImportarController@masivo_examenes_tubo');
Route::post('buscar/ocupacion/camilla', 'enfermeria\Control_CaidaController@ocupacionCamilla')->name('camilla.verificacioncamilla');
Route::post('buscar/ocupacion/estado', 'enfermeria\Control_CaidaController@ocupacionEstadoCamilla')->name('camilla.estadoCamilla');
Route::get('gestion/camilla/update_detalle/{id_detalle}/{valor}', 'enfermeria\Control_CaidaController@update_camilla_detalle')->name('riesgo.update_camilla_detalle');
Route::get('caducidad/codigo/index', 'auditoria\Caducidad_CodigoController@index')->name('caducidad_codigo.index');
Route::get('caducidad/codigo/buscador', 'auditoria\Caducidad_CodigoController@buscador')->name('caducidad_codigo.buscador');
Route::get('caducidad/codigo/actualizar', 'auditoria\Caducidad_CodigoController@actualizar')->name('caducidad_codigo.actualizar');
Route::match(['get', 'post'], 'caducidad/codigo/buscar', 'auditoria\Caducidad_CodigoController@buscar_codigo')->name('caducidad_codigo.buscar');
Route::match(['get', 'post'], 'caducidad/codigo/buscar_codigo', 'auditoria\Caducidad_CodigoController@buscar_codigo_no_valido')->name('caducidad_codigo.buscar_codigo_no_valido');
