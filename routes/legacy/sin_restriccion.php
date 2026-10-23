<?php

Route::post('/app/interconexion/sistemas/consultas', 'servicios\InterconexionController@consultas')->name('interconexion.consultas');
// ruta para la conexion de las maquinas del laboratorio1
Route::match(['get', 'post'], 'maquina/hemograma/prueba', 'SinLoginController@maquina1');

// ruta para la conexion de las maquinas del laboratorio2
Route::match(['get', 'post'], 'maquina/bioquimica/prueba', 'SinLoginController@maquina2');

Route::match(['get', 'post'], 'maquina/bha200/prueba', 'SinLoginController@bha200');
// ultima maquina
Route::match(['get', 'post'], 'maquina/luminicencia/prueba', 'SinLoginController@maquina3');

Route::match(['get', 'post'], 'examenes/laboratorio/dato', 'PaginaLabsController@examenes');

// maquina nueva

Route::match(['get', 'post'], 'maquina/lum/prueba_demonio', 'SinLoginController@maquina4');

// ruta
Route::post('carrito/paciente/guardar/orden', 'SinLoginController@carrito_guardar_orden')->name('carrito.guardar_orden');
// POST PROCESO PAGO EN LINEA
Route::match(['get', 'post'], 'laboratorio/externo/web_postproceso_pago', 'SinLoginController@web_postproceso_pago');
Route::get('laboratorio/externo/web_retorno_pago', 'SinLoginController@web_retorno_pago');
Route::get('laboratorio/externo/web_cancelacion_pago', 'SinLoginController@web_cancelacion_pago');
Route::get('laboratorio/externo/carrito/pagar/{id_orden}', 'SinLoginController@carrito_pago')->name('sinlogin.carrito_pago');
Route::post('paquete/paciente/guardar/orden', 'SinLoginController@paquete_guardar_orden')->name('sinlogin.paquete_guardar_orden');
Route::match(['get', 'post'], 'laboratorio/externo/web/buscar_clientes/{id}', 'SinLoginController@buscar_clientes')->name('sinlogin.buscar_clientes');

// RUTA PARA DESCARGA DEL COMPROBANTE PDF
Route::get('facturacion/descarga/cliente/externo/{comprobante}', 'PaginaLabsController@comprobante_externo')->name('servicios.comprobante_externo');

Route::post('facturacion/sucursales/aitrolbilling/ride/{recurso}', 'PaginaLabsController@ride_externo');

// RUTA PARA DESCARGA DE LA COTIZACION
Route::match(['get', 'post'], 'facturacion/descarga/cotizacion/externo/{id}', 'PaginaLabsController@cotizacion_externo')->name('servicios.cotizacion_externo');

Route::get('app/laboratorio/externo/carrito/pagar/{id_orden}', 'SinLoginController@app_carrito_pago')->name('sinlogin.app_carrito_pago');

// RUTA PARA FACTURACION ELECTRONICA SUCURSALES
Route::post('facturacion/sucursales/aitrol', 'PaginaLabsController@facturacion_externo')->name('Pagina.fatura');
Route::post('facturacion/sucursales/varios', 'PaginaLabsController@facturacion_externo_varios')->name('Pagina.fatura_varios');

Route::match(['get', 'post'], 'bd/consulta/rc', 'SinLoginController@consulta_rc')->name('sinlogin.consulta_rc');
Route::match(['get', 'post'], 'bd/consulta/total_anio', 'BigDataController@total_anio')->name('total_anio');
Route::post('big_data/consulta', 'BigDataController@consulta');
Route::match(['get', 'post'], 'bd/consulta_labs/labs_meses', 'BigDataController@labs_meses')->name('labs_meses');

Route::post('app/buscar/usuario', 'BigDataController@buscar_usuario')->name('laboratorio_login');
Route::post('app/buscar/examenes', 'BigDataController@buscar_examenes')->name('bd.buscar_examenes');
Route::post('app/buscar_examenes_new', 'BigDataController@buscar_examenes_new')->name('bd.buscar_examenes_new');

Route::post('app/listado/examenes', 'BigDataController@listado_examenes')->name('bd.listado_examenes');
Route::post('app/crear/orden_labs', 'BigDataController@crear_orden_labs')->name('bd.crear_orden_labs');
Route::post('labs/busqueda/usuario/paciente', 'LabsDoctorController@busqueda_user_paciente')->name('bd.busqueda_user_paciente');
Route::post('app/domicilios', 'BigDataController@domicilios')->name('bd.domicilios');
Route::post('app/labs/sectores', 'BigDataController@sectores')->name('bd.sectores');
Route::post('app/domicilios/sector', 'BigDataController@domicilios_sector')->name('bd.domicilios_sector');
Route::post('app/validate/agenda/labs', 'BigDataController@validate_agenda_labs')->name('bd.validate_agenda_labs');
// ruta para recibir la factura
Route::post('labs_gestion_servicios', 'PaginaLabsController@labs_gestion_servicios')->name('Pagina.labs_gestion_servicios');
Route::post('app/buscar/referido', 'BigDataController@buscar_referido')->name('buscar_referido');
Route::post('app/buscar/orden/paciente', 'BigDataController@buscar_orden')->name('buscar_orden');
Route::post('buscarUserSucursal', 'BigDataController@buscarUserSucursal')->name('buscarUserSucursal');
Route::post('crear/user/sucursal', 'BigDataController@creaUsuarioSucursal')->name('creaUsuarioSucursal');
Route::post('update/usuario/paciente', 'BigDataController@actualizar_usuario')->name('actualizar_usuario');
Route::post('bd/consulta/ventas', 'BigDataController@consulta_ventas')->name('bd.consulta_ventas');

Route::post('bd/consulta/procedimientos', 'BigDataController@ConsultaProcedimientos')->name('bd.ConsultaProcedimientos');

Route::post('bd/consulta/cie', 'BigDataController@consultaCie')->name('bd.ConsultaCie');

Route::post('app/buscador/paciente', 'SinLoginController@verificar_paciente')->name('sinlogin.verificar_paciente');

Route::match(['get', 'post'], 'laboratorio/bajarinsumos', 'ApiHumanlabs@bajarinsumos');

Route::post('maquina/consultar/orden', 'MaquinasController@consultar_orden');
Route::post('maquina/resultado/orden', 'MaquinasController@resultado_orden');
Route::match(['get', 'post'], 'apihumanlabs/{opcion}', 'ApiHumanlabs@index');
Route::post('app/new/orden/pago', 'BigDataController@new_orden_pago')->name('bd.new_orden_pago');
Route::post('labs/orden/doctor', 'LabsDoctorController@orden_labs_doctor')->name('bd.orden_labs_doctor');
Route::post('bd/buscador/ticket/permisos', 'BigDataController@buscador_ticket_permisos')->name('bd.buscador_ticket_permisos');
Route::post('bd/ticket/permiso/edit', 'BigDataController@ticket_permiso_edit')->name('bd.ticket_permiso_edit');
Route::post('bd/ticket/permiso/guardar', 'BigDataController@ticket_permiso_guardar')->name('bd.ticket_permiso_guardar');
Route::post('labs/doctor/buscar/examenes', 'LabsDoctorController@buscar_examenes_labsdoctor')->name('bd.buscar_examenes_labsdoctor');
Route::post('vt_pagos_en_linea/actualizar_orden', 'BigDataController@actualizar_orden_pago_en_linea')->name('bigdata.actualizar_orden_pago_en_linea');
Route::post('labs/doctor/crear/doctor', 'LabsDoctorController@crear_doctor_labs')->name('bd.crear_doctor_labs');
Route::post('labs/doctor/crear/protocolo', 'LabsDoctorController@crear_protocolo')->name('labsdoctor.crear_protocolo');
Route::post('labs/doctor/protocolos', 'LabsDoctorController@doctor_protocolos')->name('labsdoctor.doctor_protocolos');
Route::post('labs/doctor/ordenes/realizadas', 'LabsDoctorController@ordenes_realizadas')->name('labsdoctor.ordenes_realizadas');
Route::post('labs/doctor/calculo/comision', 'LabsDoctorController@calculo_comision')->name('labsdoctor.calculo_comision');
Route::post('labs/doctor/ordenes/paciente', 'LabsDoctorController@ordenes_paciente')->name('labsdoctor.ordenes_paciente');
Route::post('labs/doctor/total/comision', 'LabsDoctorController@total_comision')->name('labsdoctor.total_comision');
Route::post('labs/doctor/total/ordenes', 'LabsDoctorController@total_ordenes')->name('labsdoctor.total_ordenes');
Route::post('labs/doctor/comisiones/mes', 'LabsDoctorController@comisiones_mes')->name('labsdoctor.comisiones_mes');
Route::post('labs/doctor/user/delete', 'LabsDoctorController@user_delete')->name('labsdoctor.user_delete');
Route::post('bd/estadistico/rc/pacientes', 'BigDataController@estadistico_rc_pacientes')->name('bd.estadistico_rc_pacientes');
Route::post('bd/consulta/examen/orden', 'BigDataController@consulta_examen_orden')->name('bd.consulta_examen_orden');
Route::post('bd/query/ventas', 'BigDataController@query_ventas')->name('bd.query_ventas');
Route::post('bd/busqueda/cie10', 'BigDataController@busqueda_cie10')->name('bd.busqueda_cie10');
Route::post('labs/examen/ventas', 'BdLabsController@examen_ventas')->name('examen_ventas');
Route::post('examenventas', 'BdLabsController@ordenExamenes')->name('ordenExamenes');
Route::post('labs/crea/orden/venta', 'BdLabsController@crea_orden_venta')->name('bd.crea_orden_venta');
Route::post('labs/master/preguntas', 'BdLabsController@master_preguntas')->name('bd.master_preguntas');
Route::post('labs/guardar/encuesta', 'BdLabsController@guardar_labs_encuesta')->name('bd.guardar_encuesta');
Route::post('labs/valida/encuesta', 'BdLabsController@valida_encuesta')->name('bd.valida_encuesta');
Route::post('bd/buscar/en/ventas', 'BdLabsController@buscar_en_ventas')->name('bd.buscar_en_ventas');
// nuevas rutas para descargar pdf archivo plano

Route::match(['get', 'post'], 'api/bodegas/getBodegas', 'ApiHumanlabs@getBodegas')->name('sin.ApiHuman.getBodega');
Route::match(['get', 'post'], 'api/producto/getProductos', 'ApiHumanlabs@getProductos')->name('sin.ApiHuman.getProductos');
Route::match(['get', 'post'], 'api/producto/getProductosUnico', 'ApiHumanlabs@getProductosUnico')->name('sin.ApiHuman.getProductosUnico');
Route::match(['get', 'post'], 'api/producto/getCostoPromedio', 'ApiHumanlabs@getCostoPromedio')->name('sin.ApiHuman.getCostoPromedio');

// formato de preadmision
Route::match(['get', 'post'], 'formato_preadmision/index', 'SinLoginController@index_form_preadmision')->name('sinlogin.index_form_preadmision');
Route::get('formato_preadmision/buscar_paciente/{id}', 'SinLoginController@buscar_paciente')->name('sinlogin.buscar_paciente');
Route::match(['get', 'post'], 'formato_preadmision/form_admision_guardar', 'SinLoginController@form_admision_guardar')->name('sinlogin.form_admision_guardar');
Route::get('formato_preadmision/find/medicamento/alergia', 'SinLoginController@find_medicamento_alergia')->name('sinlogin.find_medicamento_alergia');
Route::post('formato_preadmision/guardar/alergia', 'SinLoginController@guardar_alergia')->name('sinlogin.guardar_alergia');

// reporte research

Route::post('bd/reporte/research', 'BigDataController@reporte_research')->name('bd.reporte_research');

// consolidado procedimientos bd

Route::post('bd/consolidado/consultas/proc', 'BdEstadisticosEndoscopyController@consolidado_consultas_proc')->name('bd.consolidado_consultas_proc');
Route::post('bd/newConsolidadoConsultasProc', 'BdEstadisticosEndoscopyController@newConsolidadoConsultasProc')->name('bd.newConsolidadoConsultasProc');
Route::post('bd/prueba_estadistico', 'BdEstadisticosEndoscopyController@prueba_estadistico')->name('bd.prueba_estadistico');
Route::post('bd/detalle_consultasProc', 'BdEstadisticosEndoscopyController@detalle_consultasProc')->name('bd.detalle_consultasProc');

// reporte rh bd
Route::post('bd/consulta/vacaciones', 'BdReportesRhController@consulta_vacaciones')->name('bd.consulta_vacaciones');

Route::post('bd/copago/labs', 'BdLabsController@copago_labs')->name('bd.copago_labs');

Route::post('bd/examenes/agrupados', 'LabsDoctorController@examenes_agrupados')->name('bd.examenes_agrupados');
Route::post('bd/fc/manuales', 'BdLabsController@fc_manuales')->name('bd.fc_manuales');

// recetas app ieced

Route::post('bd/listado/recetas', 'BdIecedController@listado_recetas')->name('bd.listado_recetas');
Route::get('bd/pdf/receta/{id}/{tipo}', 'BdIecedController@pdf_receta')->name('api.pdf_receta');
Route::get('bd/download/receta/{id}/{tipo}', 'BdIecedController@download_receta')->name('api.download_receta');
Route::get('bd/view_receta/{id}/{tipo}', 'BdIecedController@view_receta')->name('api.view_receta');
// biopsias app ieced
Route::post('bd/listado/biopsias', 'BdIecedController@listado_biopsias')->name('bd.listado_biopsias');
Route::get('bd/descarga_externo_biopsia/{name}', 'BdIecedController@descarga_externo_biopsia')->name('bd.descarga_externo_biopsia');
// funcionales app
Route::post('bd/listado/proc/funcionales', 'BdIecedController@listado_proc_funcionales')->name('bd.listado_proc_funcionales');
// comisiones codigo externo
Route::post('bd/ordenes/externas', 'BdLabsComisionesController@ordenes_externas')->name('bd.ordenes_externas');
Route::post('bd/lista/codigos/externos', 'BdLabsComisionesController@lista_codigos_externos')->name('bd.lista_codigos_externos');
// detalle consultas
Route::post('bd/reporte/det/consultas', 'BigDataController@reporte_det_consultas')->name('bd.reporte_det_consultas');

// ordenes externas de procedimientos
Route::post('ordenes/procedimientos/lista_proc', 'OrdenesDoctorController@lista_proc')->name('ordenesdoctor.lista_proc');
Route::post('ordenes/procedimientos/crear_orden_proc', 'OrdenesDoctorController@crear_orden_proc')->name('ordenesdoctor.crear_orden_proc');
Route::post('ordenes/procedimientos/crear_doctor', 'OrdenesDoctorController@crear_doctor')->name('ordenesdoctor.crear_doctor');
Route::post('ordenes/procedimientos/ordenes_creadas', 'OrdenesDoctorController@ordenes_creadas')->name('ordenesdoctor.ordenes_creadas');
Route::get('ordenes/procedimientos/imprimir_endoscopica_externa/{id}', 'OrdenesDoctorController@imprimir_endoscopica_externa')->name('ordenesdoctor.imprimir_endoscopica_externa');
Route::get('ordenes/procedimientos/imprimir_funcional_externa/{id}', 'OrdenesDoctorController@imprimir_funcional_externa')->name('ordenesdoctor.imprimir_funcional_externa');
Route::get('ordenes/procedimientos/imprimir_imagenes_externas/{id}', 'OrdenesDoctorController@imprimir_imagenes_externas')->name('ordenesdoctor.imprimir_imagenes_externas');
Route::post('ordenes/procedimientos/obtener_resultados', 'OrdenesDoctorController@obtener_resultados')->name('ordenesdoctor.obtener_resultados');
Route::get('ordenes/procedimientos/descarga_estudio_externo/{id}', 'OrdenesDoctorController@descarga_estudio_externo')->name('ordenesdoctor.descarga_estudio_externo');
Route::post('ordenes/procedimientos/comision_ordenes_mes', 'OrdenesDoctorController@comision_ordenes_mes')->name('ordenesdoctor.comision_ordenes_mes');
Route::post('ordenes/procedimientos/comision_mes_actual', 'OrdenesDoctorController@comision_mes_actual')->name('ordenesdoctor.comision_mes_actual');
Route::post('ordenes/procedimientos/crear_codigo_doctor', 'OrdenesDoctorController@crear_codigo_doctor')->name('ordenesdoctor.crear_codigo_doctor');
/* BD Productos */
Route::get('BdOpcionesProductos/', 'BdProductosController@opcionesProductos');
Route::match(['get', 'post'], 'BdOpcionesProductos/{opcion}', 'BdProductosController@opcionesProductos');

Route::match(['get', 'post'], 'nuevo/maquina/prueba', 'SinLoginController@nuevoMaquina1');
// detalle orden x seguro
Route::post('bd/detalle/ordenes/seguros', 'BigDataController@det_ordenes_seguros')->name('bd.det_ordenes_seguros');

// api landing
Route::post('api/landing/datos_usuario', 'BdConsultasProcController@datos_usuario')->name('bd.datos_usuario');
Route::post('api/landing/update_datos', 'BdConsultasProcController@update_datos')->name('bd.update_datos');

// info consultas y procedimientos
Route::post('bd/info_consulta_proc', 'BdConsultasProcController@info_consulta_proc')->name('bd.info_consulta_proc');

Route::get('calcula_porcentaje/{anio}/{mes}', 'BdLabsController@calcula_porcentaje')->name('calcula_porcentaje');
Route::post('bd/verificar_info_consultas', 'BdConsultasProcController@verificar_info_consultas')->name('verificar_info_consultas');
Route::post('bd/resumen_labs_paciente', 'BdConsultasProcController@resumen_labs_paciente')->name('resumen_labs_paciente');
// new bd
Route::post('bd/total_proc_consul', 'BdEstadisticosEndoscopyController@total_proc_consul')->name('total_proc_consul');
Route::post('bd/total_ordenes_labs', 'BdEstadisticosEndoscopyController@total_ordenes_labs')->name('total_ordenes_labs');
Route::post('bd/cons_procxseguro', 'BdEstadisticosEndoscopyController@cons_procxseguro')->name('cons_procxseguro');
Route::post('bd/labs_ordenesxseguro', 'BdEstadisticosEndoscopyController@labs_ordenesxseguro')->name('labs_ordenesxseguro');
Route::post('bd/consolidado_labs', 'BdLabsController@consolidado_labs')->name('consolidado_labs');
Route::match(['get', 'post'], 'maquina/electrolitos', 'SinLoginController@maquilaElectrolitos');
Route::post('bd/resumen_ordenes_proc', 'BdConsultasProcController@resumen_ordenes_proc')->name('resumen_ordenes_proc');
Route::post('bd/resumen_doc_externos', 'BdConsultasProcController@resumen_doc_externos')->name('resumen_doc_externos');
Route::get('pdf_doc_externo/{name}', 'BdConsultasProcController@pdf_doc_externo');

// sensores
Route::post('sensores/store', 'BdSensoresController@sensores_store')->name('bd.sensores_store');
Route::post('sensores/tabla', 'BdSensoresController@tabla')->name('bd.tabla');
Route::post('bd/facturado_cons_proc', 'BdEstadisticosEndoscopyController@facturado_cons_proc')->name('bd.facturado_cons_proc');

// boton pago
Route::match(['get', 'post'], 'bd/bpago', 'BdPagosController@bpIeced')->name('bd.bpIeced');
Route::match(['get', 'post'], 'bd/bpago/confirmar/pago', 'BdPagosController@confirmar_pago')->name('bd.confirmar_pago');

// facturado labs
Route::post('bd/newfacturado_labs', 'BdEstadisticosEndoscopyController@newfacturado_labs')->name('bd.newfacturado_labs');
Route::post('bd/newfacturado_endoscopy', 'BdEstadisticosEndoscopyController@newfacturado_endoscopy')->name('bd.newfacturado_endoscopy');
Route::post('bd/facturadoxseg', 'BdEstadisticosEndoscopyController@facturadoxseg')->name('bd.facturadoxseg');
Route::post('bd/reporte_camas', 'BdEstadisticosEndoscopyController@reporte_camas')->name('bd.reporte_camas');
Route::match(['get', 'post'], 'bd/InformacionPaciente/{id_paciente}', 'BdConsultasProcController@InformacionPaciente')->name('bd.InformacionPaciente');
Route::match(['get', 'post'], 'bd/infoTipo/{id_paciente}/{id_tipo}', 'BdConsultasProcController@infoTipo')->name('bd.infoTipo');
Route::post('bd/resumen_enfermedades', 'BdEstadisticosEndoscopyController@resumen_enfermedades')->name('bd.resumen_enfermedades');

Route::match(['get', 'post'], 'app/examenventas', 'EmisionDocumentosController@examenventas')->name('examenventas');

Route::post('bd/index_cobro', 'BdCobroController@index_cobro')->name('bdcobro.index_cobro');

// app sin internet consultas
Route::post('bd/loginApp', 'BdAppController@loginApp')->name('bdapp.loginApp');
Route::get('masivo/arregla/cab/movimiento/{id_empresa}', 'SinLoginController@arreglaCabMovimiento')->name('masivo_arregla_cab_movimiento');

// menu wt
Route::post('bd/menu_respuestas', 'BdRobotWtController@menu_respuestas')->name('bdrobot.menu_respuestas');
Route::get('bd/download/resultados/externos/{id}', 'PaginaLabsController@download_resultados_externos')->name('paginalabs.download_resultados_externos');
Route::post('bd/search_cie10', 'BigDataController@search_cie10')->name('bigdata.search_cie10');
Route::post('bd/reporte/det/consultas/excel', 'BigDataController@reporte_det_consultas_excel')->name('bd.reporte_det_consultas_excel');
Route::get('bd/reporte/research/excel', 'BigDataController@reporte_research_excel')->name('bd.reporte_research/excel');
Route::post('bd/preguntas/encuesta/app', 'BdRobotWtController@preguntas_encuesta_app')->name('bdrobot.preguntas_encuesta_app');
Route::post('bd/encuesta/ieced/guardar', 'BdRobotWtController@encuesta_ieced_guardar')->name('bdrobot.encuesta_ieced_guardar');
Route::get('bd/download/estudio/externo/{id}', 'BdRobotWtController@download_estudio_externo')->name('paginalabs.download_estudio_externo');
// Route::get('api/descargar_receta/pdf/{empresa}/{mes_plano}/{atencion}', 'nota_entrega\Nota_EntregaController@imprimir_pdf_nota_entrega')->name('nota_entrega.imprimir_pdf_nota_entrega');
// informes app
Route::post('bd/lista/informes', 'BdIecedController@lista_informes')->name('bdapp.lista_informes');
Route::post('bd/reporte/agendas/labs', 'BdLabsController@reporte_agendas_labs')->name('bdlabs.reporte_agendas_labs');
// app ieced
Route::post('bd/lista/doctor/app', 'BdIecedController@lista_doctor_app')->name('bdieced.lista_doctor_app');
Route::post('bd/horario/doctor/app', 'BdIecedController@horario_doctor_app')->name('bdieced.horario_doctor_app');
Route::post('bd/beneficios/membresia', 'BdIecedController@beneficios_membresia')->name('bdieced.beneficios_membresia');
Route::post('bd/envio_mail_agenda', 'BdIecedController@envio_mail_agenda')->name('bdieced.envio_mail_agenda');
Route::post('bd/crea/recibos', 'BdIecedController@crea_recibos')->name('bdieced.crea_recibos');
Route::post('bd/crea_agendaMem', 'BdIecedController@crea_agendaMem')->name('bdieced.crea_agendaMem');
Route::post('bd/login/ieced/app', 'BdIecedController@loginIecedApp')->name('bdieced.loginIecedApp');
Route::post('bd/registro/usuario', 'BdIecedController@registroUsuario')->name('bdieced.registroUsuario');
Route::post('bd/verificarUsuario', 'BdIecedController@verificarUsuario')->name('bdieced.verificarUsuario');
Route::post('bd/cambiarContrasena', 'BdIecedController@cambiarContrasena')->name('bdieced.cambiarContrasena');
Route::post('bd/updateUsuario', 'BdIecedController@updateUsuario')->name('bdieced.updateUsuario');

// registro de la transaccion de la pag web
Route::post('bd/registro/web', 'BdPagosController@registro_web')->name('bdpagos.registro_web');
// IMPRIME 012 DEL BOT
Route::get('bot_auditoria/orden012/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_012')->name('liquidador.descargar_012');
// app labs
Route::post('bd/horario/agendas', 'BdAppLabsController@horario_agendas')->name('applabs.horario_agendas');
Route::post('bd/lista/examenes', 'BdAppLabsController@listaExamenes')->name('applabs.listaExamenes');
Route::post('bd/new/crearOrdenLabs', 'BdAppLabsController@new_crearOrdenLabs')->name('applabs.new_crearOrdenLabs');
Route::post('bd/formas/pago', 'BdAppLabsController@formas_pago')->name('applabs.formas_pago');
Route::post('bd/detalle/orden', 'BdAppLabsController@detalle_orden')->name('applabs.detalle_orden');
Route::post('bd/pagoOrdenLabs', 'BdPagosController@pagoOrdenLabs')->name('applabs.pagoOrdenLabs');
Route::match(['get', 'post'], 'bd/confirmar/pago/orden', 'BdPagosController@confirmar_pago_orden')->name('applabs.confirmar_pago_orden');
Route::post('bd/verificarAgenda', 'BdAppLabsController@verificarAgenda')->name('applabs.verificarAgenda');
Route::post('bd/pago/toma/muestra', 'BdPagosController@pagoTomaMuestra')->name('applabs.pagoTomaMuestra');
Route::match(['get', 'post'], 'bd/confirmar/pago/tm', 'BdPagosController@confirmar_pagoTM')->name('applabs.confirmar_pagoTM');
Route::post('bd/editar/orden/tm', 'BdAppLabsController@editar_ordenTM')->name('applabs.editar_ordenTM');
Route::post('bd/transaccion/confirmada/tm', 'BdAppLabsController@transaccion_confirmadaTM')->name('applabs.transaccion_confirmadaTM');

Route::get('apilab/carga/parametros/resultado/{id}', 'SinLoginController@crear_resultados')->name('sinlogin.crear_resultados');
// reportes nuevos bd
Route::post('bd/reporte/producido/proc', 'BdEstadisticosEndoscopyController@producidoxproc')->name('bd.producidoxproc');
Route::post('bd/reporte/producido/forma/pago', 'BdEstadisticosEndoscopyController@producidoxformapago')->name('bd.producidoxformapago');
Route::post('bd/reporte/producido/seguro/mes', 'BdEstadisticosEndoscopyController@prodxmesxseg')->name('bd.prodxmesxseg');
Route::post('bd/lista/seguro', 'BdEstadisticosEndoscopyController@lista_seguro')->name('bdestadistico.lista_seguro');
Route::post('bd/reporte/informe/biopsia', 'BdEstadisticosEndoscopyController@reporte_informe_biopsia')->name('bdestadistico.reporte_informe_biopsia');

// reportes financieros bd
Route::post('bd/detalle/cxp', 'Apis\BdReportesFinancieros@detalle_cxp')->name('bdfinanciero.detalle_cxp');
Route::post('bd/search/provedor', 'Apis\BdReportesFinancieros@search_provedor')->name('bdfinanciero.search_provedor');
Route::post('bd/search/edad/cartera', 'Apis\BdReportesFinancieros@search_edad_cartera')->name('bdfinanciero.search_edad_cartera');
Route::post('bd/search/max/dias/prov', 'Apis\BdReportesFinancieros@search_max_dias_prov')->name('bdfinanciero.search_max_dias_prov');
Route::post('bd/search/client', 'Apis\BdReportesFinancieros@searchClient')->name('bdfinanciero.search_client');
Route::post('bd/detalle/cuentasxcobrar', 'Apis\BdReportesFinancieros@cuentasxcobrar')->name('bdfinanciero.cuentasxcobrar');
Route::post('bd/lista/seguros/cuentasxcobrar', 'Apis\BdReportesFinancieros@listaSegurosCuentasxcobrar')->name('bdfinanciero.lista_seguros_cuentasxcobrar');
Route::post('bd/QuitarPermisos', 'Apis\BdConfiguracionController@QuitarPermisos')->name('bdconfiguracion.QuitarPermisos');
Route::post('bd/detalle/cuentaspagadas', 'Apis\BdReportesFinancieros@cuentaspagadas')->name('bdfinanciero.cuentaspagadas');
Route::post('bd/detalle/flujocaja', 'Apis\BdReportesFinancieros@flujoCaja')->name('bdfinanciero.flujocaja');
Route::post('bd/detalle/inversiones', 'Apis\BdReportesFinancieros@inversiones')->name('bdfinanciero.inversiones');
Route::post('bd/detalle/amortizaciones', 'Apis\BdReportesFinancieros@amortizaciones')->name('bdfinanciero.amortizaciones');
Route::post('bd/detalle/prestamos', 'Apis\BdReportesFinancieros@prestamos')->name('bdfinanciero.prestamos');
Route::post('bd/detalle/cuentascobradas', 'Apis\BdReportesFinancieros@cuentascobradas')->name('bdfinanciero.cuentascobradas');

Route::post('bd/resumen/informe/biopsias', 'BdConsultasProcController@resumen_informebiopsias')->name('bd.resumen_informebiopsias');
Route::get('bd/pdf/informe/biopsia/externo/{id}', 'BdConsultasProcController@pdf_informe_biopsia_externo')->name('bd.pdf_informe_biopsia_externo');

Route::get('prueba/image/bmp', 'Apis\LiquidadorController@arrayToBMP');
Route::post('api/validar/login', 'Apis\LiquidadorController@validateLogin')->name('liquidador.validateLogin');
Route::post('api/guardar/log/descarga', 'Apis\LiquidadorController@guardarLogDescarga')->name('liquidador.guardarLogDescarga');
Route::post('api/empresa/planillaje', 'Apis\LiquidadorController@getEmpresaPlanillaje')->name('liquidador.getEmpresaPlanillaje');
Route::post('api/firmador/documentos', 'Apis\LiquidadorController@signPDFJar')->name('liquidador.firmador_documentos');
Route::get('api/cabecera/plano/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@getCabeceraPlano')->name('liquidador.getCabeceraPlano');
Route::get('api/imprimir/plantillas/xls/{id}/ptv', 'Apis\LiquidadorController@planilla_detalle_xls_ptv')->name('liquidador.planilla_detalle_xls_ptv');
Route::get('api/nombre/excel/planilla/{id}', 'Apis\LiquidadorController@getNameExcelPlanilla')->name('liquidador.getNameExcelPlanilla');
Route::post('api/new_guardar/log/descarga', 'Apis\NuevoLiquidadorController@guardarLogDescarga')->name('nuevoLiquidador.guardarLogDescarga');
Route::get('api_new/buscar/cabeceras/{empresa}/{mes_plano}/{atencion}/{id_seguro}', 'Apis\LiquidadorNewController@buscar_cabeceras')->name('liquidador_new.buscar_cabeceras');
Route::get('api_new/buscar/cabecera/plano/{id_plano}', 'Apis\LiquidadorNewController@getCabeceraPlano')->name('liquidador_new.getCabeceraPlano');
Route::get('api_new/buscar/orden/{id_plano}', 'Apis\LiquidadorNewController@buscar_orden')->name('liquidador_new.buscar_orden');
Route::get('api_new/buscar/agenda_as400/{id_plano}', 'Apis\LiquidadorNewController@buscar_agenda_as400')->name('liquidador_new.buscar_agenda_as400');
Route::get('api_new/buscar/estudio_espirometria/{id_plano}', 'Apis\LiquidadorNewController@buscar_estudio_espirometria')->name('liquidador_new.buscar_estudio_espirometria');
Route::get('api_new/buscar/estudio_funcional/{id_plano}', 'Apis\LiquidadorNewController@buscar_estudio_funcional')->name('liquidador_new.buscar_estudio_funcional');
Route::get('api_new/buscar/agenda_archivos/{id_plano}', 'Apis\LiquidadorNewController@buscar_agenda_archivos')->name('liquidador_new.buscar_agenda_archivos');
Route::get('api_new/buscar/adicionales/{id_plano}', 'Apis\LiquidadorNewController@buscar_adicionales')->name('liquidador_new.buscar_adicionales');
Route::get('api_new/descargar/planilla/{id_plano}', 'Apis\LiquidadorNewController@descargar_planilla')->name('liquidador_new.descargar_planilla');
Route::get('api_new/descargar/planilla_temp/{id_plano}', 'Apis\LiquidadorNewController@descargar_planilla_temp')->name('liquidador_new.descargar_planilla_temp');
Route::get('api_new/descargar/planilla_msp/{id_plano}', 'Apis\LiquidadorNewController@descargar_planilla_msp')->name('liquidador_new.descargar_planilla_msp');
Route::get('api_new/descargar/planilla_consolidada/{numero_paciente}/{id_empresa}/{mes_plano}/{id_seguro}', 'Apis\LiquidadorNewController@descargar_planilla_consolidada')->name('liquidador_new.descargar_planilla_consolidada');
Route::get('api_new/descargar/planilla_consolidada_temp/{numero_paciente}/{id_empresa}/{mes_plano}/{id_seguro}', 'Apis\LiquidadorNewController@descargar_planilla_consolidada_temp')->name('liquidador_new.descargar_planilla_consolidada_temp');
Route::get('api_new/descargar/factura/{id_plano}', 'Apis\LiquidadorNewController@descargar_factura')->name('liquidador_new.descargar_factura');
Route::get('api_new/descargar/acta_entrega/{id_plano}', 'Apis\LiquidadorNewController@descargar_acta_entrega')->name('liquidador_new.descargar_acta_entrega');
Route::get('api_new/descargar/epicrisis_nuevo/{id_plano}', 'Apis\LiquidadorNewController@descargar_epicrisis_nuevo')->name('liquidador_new.descargar_epicrisis_nuevo'); // actual
Route::get('api_new/descargar/epicrisis/{id_plano}', 'Apis\LiquidadorNewController@descargar_epicrisis')->name('liquidador_new.descargar_epicrisis'); // no usado
Route::get('api_new/descargar/epicrisis_antiguo/{id_plano}', 'Apis\LiquidadorNewController@descargar_epicrisis_antiguo')->name('liquidador_new.descargar_epicrisis_antiguo'); // antiguo
Route::get('api_new/descargar/interconsulta/{id_plano}', 'Apis\LiquidadorNewController@descargar_interconsulta')->name('liquidador_new.descargar_interconsulta'); // no usado
Route::get('api_new/descargar/interconsulta_unificada/{id_plano}', 'Apis\LiquidadorNewController@descargar_interconsulta_unificado')->name('liquidador_new.descargar_interconsulta_unificado'); // actual
Route::get('api_new/descargar/interconsulta_unificada/robot/{id_plano}', 'Apis\LiquidadorNewController@descargar_interconsulta_unificado_robot')->name('liquidador_new.descargar_interconsulta_unificado_robot'); // actual
Route::get('api_new/descargar/orden/{id_orden}', 'Apis\LiquidadorNewController@descargar_orden')->name('liquidador_new.descargar_orden');
Route::get('api_new/descargar/resultados/{id_orden}', 'Apis\LiquidadorNewController@descargar_resultado')->name('liquidador_new.descargar_resultado');
Route::get('api_new/descargar/orden_unificado/{id_orden}', 'Apis\LiquidadorNewController@dsecargar_orden_unificado')->name('liquidador_new.dsecargar_orden_unificado');
Route::get('api_new/descargar/imagenologiaA/{id_orden}', 'Apis\LiquidadorNewController@descargar_imageneologiaA')->name('liquidador_new.descargar_imageneologiaA');
Route::get('api_new/descargar/imagenologiaArobot/{id_plano}', 'Apis\LiquidadorNewController@descargar_imageneologiaArobot')->name('liquidador_new.descargar_imageneologiaArobot');
Route::get('api_new/descargar/imagenologiaB/{id_plano}', 'Apis\LiquidadorNewController@descargar_imageneologiaB')->name('liquidador_new.descargar_imageneologiaB');
Route::get('api_new/descargar/imagenologia_unificado/{id_plano}', 'Apis\LiquidadorNewController@descargar_imageneologia_unificado')->name('liquidador_new.descargar_imageneologia_unificado');
Route::get('api_new/descargar/protocolo_quirurgico/{id_plano}', 'Apis\LiquidadorNewController@descargar_protocolo_quirurgico')->name('liquidador_new.descargar_protocolo_quirurgico');
Route::get('api_new/descargar/protocolo_antiguo/{id_plano}', 'Apis\LiquidadorNewController@descargar_protocolo_antiguo')->name('liquidador_new.descargar_protocolo_antiguo'); // antiguo
Route::get('api_new/descargar/protocolo_nuevo/{id_plano}', 'Apis\LiquidadorNewController@descargar_protocolo_nuevo')->name('liquidador_new.descargar_protocolo_nuevo'); // actual
Route::get('api_new/descargar/cobertura_publica/{id_plano}', 'Apis\LiquidadorNewController@descargar_cobertura_publica')->name('liquidador_new.descargar_cobertura_publica');
Route::get('api_new/descargar/cobertura_privada/{id_plano}', 'Apis\LiquidadorNewController@descargar_cobertura_privada')->name('liquidador_new.descargar_cobertura_privada');
Route::get('api_new/descargar/anestesia/{id_plano}', 'Apis\LiquidadorNewController@descargar_anestesia')->name('liquidador_new.descargar_anestesia');
Route::get('api_new/descargar/pre_anestesico/{id_plano}', 'Apis\LiquidadorNewController@descargar_pre_anestesiologo')->name('liquidador_new.descargar_pre_anestesiologo');
Route::get('api_new/descargar/pre_anestesico/Horizontal/{id_plano}', 'Apis\LiquidadorNewController@descargar_pre_anestesiologo_horizontal')->name('liquidador_new.descargar_pre_anestesiologo_horizontal');
Route::get('api_new/descargar/pre_anestesico/vertical/{id_plano}', 'Apis\LiquidadorNewController@descargar_pre_anestesiologo_vertical')->name('liquidador_new.descargar_pre_anestesiologo_vertical');
Route::get('api_new/descargar/pre_anestesico_018/{id_plano}', 'Apis\LiquidadorNewController@descargar_pre_anestesico_018')->name('liquidador_new.descargar_pre_anestesico_018');
Route::get('api_new/descargar/post_anestesico/{id_plano}', 'Apis\LiquidadorNewController@descargar_post_anestesico')->name('liquidador_new.descargar_post_anestesico');
Route::get('api_new/descargar/post_anestesico/horizontal/{id_plano}', 'Apis\LiquidadorNewController@descargar_post_anestesico_horizontal')->name('liquidador_new.descargar_post_anestesico_horizontal');
Route::get('api_new/descargar/post_anestesico/vertical/{id_plano}', 'Apis\LiquidadorNewController@descargar_post_anestesico_vertical')->name('liquidador_new.descargar_post_anestesico_vertical');
Route::get('api_new/descargar/histopatologiaA/{id_plano}', 'Apis\LiquidadorNewController@descargar_histopatologiaA')->name('liquidador_new.descargar_histopatologiaA');
Route::get('api_new/descargar/histopatologiaB/{id_plano}', 'Apis\LiquidadorNewController@descargar_histopatologiaB')->name('liquidador_new.descargar_histopatologiaB');
Route::get('api_new/descargar/estudio/{id_plano}', 'Apis\LiquidadorNewController@descargar_estudio')->name('liquidador_new.descargar_estudio');
Route::get('api_new/descargar/estudio_robot/{id_plano}', 'Apis\LiquidadorNewController@descargar_estudio_robot')->name('liquidador_new.descargar_estudio_robot');
Route::get('api_new/descargar/bitacora/{id_plano}', 'Apis\LiquidadorNewController@descargar_bitacora')->name('liquidador_new.descargar_bitacora');
Route::get('api_new/descargar/anamenesis/{id_plano}', 'Apis\LiquidadorNewController@descargar_anamnesis')->name('liquidador_new.descargar_anamnesis'); // actual
Route::get('api_new/no_usar/anamenesis/{id_plano}', 'Apis\LiquidadorNewController@anamnesis_no_usar')->name('liquidador_new.anamnesis_no_usar'); // no usada
Route::get('api_new/descargar/hc_as400/{id_plano}', 'Apis\LiquidadorNewController@descargar_hc_as400')->name('liquidador_new.descargar_hc_as400');
Route::get('api_new/descargar/archivo_as400/{id}', 'Apis\LiquidadorNewController@descargar_archivo_as400')->name('liquidador_new.descargar_archivo_as400');
Route::get('api_new/descargar/cedula/{id_plano}', 'Apis\LiquidadorNewController@descargar_cedula')->name('liquidador_new.descargar_cedula');
Route::get('api_new/descargar/receta/{id_plano}', 'Apis\LiquidadorNewController@descargar_receta')->name('liquidador_new.descargar_receta');
Route::get('api_new/descargar/nota_entrega/pdf/{id_plano}', 'Apis\LiquidadorNewController@descargar_nota_entrega')->name('liquidador_new.descargar_nota_entrega');
Route::get('api_new/descargar/nota_entrega_subida/pdf/{id_plano}', 'Apis\LiquidadorNewController@descargar_nota_entrega_subida')->name('liquidador_new.descargar_nota_entrega_subida');
Route::get('api_new/descargar/scan/{id_plano}', 'Apis\LiquidadorNewController@descargar_scan')->name('liquidador_new.descargar_scan');
Route::get('api_new/descargar/validacion/{id_plano}', 'Apis\LiquidadorNewController@descargar_validacion')->name('liquidador_new.descargar_validacion');
Route::get('api_new/descargar/evolucion_nuevo/{id_plano}', 'Apis\LiquidadorNewController@descargar_evolucion_nuevo')->name('liquidador_new.descargar_evolucion_nuevo');
Route::get('api_new/descargar/informe_descargo/{id_plano}', 'Apis\LiquidadorNewController@descargar_informe_descargo')->name('liquidador_new.descargar_informe_descargo');
Route::get('api_new/descargar/informe_tecnico/{id_plano}', 'Apis\LiquidadorNewController@descargar_informe_tecnico')->name('liquidador_new.descargar_informe_tecnico');
Route::get('api_new/descargar/cedula_paciente/{id_plano}', 'Apis\LiquidadorNewController@descargar_cedula_paciente')->name('liquidador_new.descargar_cedula_paciente');
Route::get('api_new/descargar/formulario_externo/{cod_doc}/{id_plano}', 'Apis\LiquidadorNewController@descargar_formulario_externo')->name('liquidador_new.descargar_formulario_externo');
Route::get('api_new/descargar/espirometria/{id_plano}', 'Apis\LiquidadorNewController@descargar_espirometria')->name('liquidador_new.descargar_espirometria');
Route::get('api_new/descargar/estudio_espirometria/{id_archivo}', 'Apis\LiquidadorNewController@descargar_estudio_espirometria')->name('liquidador_new.descargar_estudio_espirometria');
Route::get('api_new/descargar/agenda_archivo/{id_archivo}', 'Apis\LiquidadorNewController@descargar_agenda_archivo')->name('liquidador_new.descargar_agenda_archivo');
Route::get('api_new/descargar/Sevoflurano/{id_plano}', 'Apis\LiquidadorNewController@descargar_sevoflurano')->name('liquidador_new.descargar_sevoflurano');
Route::get('api_new/descargar/adicionales/{id_archivo}', 'Apis\LiquidadorNewController@descargar_adicionales')->name('liquidador_new.descargar_adicionales');
Route::get('api_new/descargar/seguro_privado/{id_plano}', 'Apis\LiquidadorNewController@descargar_certificado_seg_priv')->name('liquidador_new.descargar_certificado_seg_priv');
Route::get('api_new/descargar/lista_insumos/{id_plano}', 'Apis\LiquidadorNewController@descargar_lista_insumos')->name('liquidador_new.descargar_lista_insumos');
Route::get('api_new/descargar/estudio_funcional/{id_archivo}', 'Apis\LiquidadorNewController@descargar_estudio_funcional')->name('liquidador_new.descargar_estudio_funcional');
Route::get('api_new/descargar/plantilla_xls/{id_plano}', 'Apis\LiquidadorNewController@planilla_individual_msp_excel')->name('liquidador_new.planilla_individual_msp_excel');
Route::get('api/descargar_plantillas/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@planilla_detalle_pdf')->name('liquidador.planilla_detalle_pdf');
Route::get('api/descargar_factura/mesplano/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@factura_descarga')->name('liquidador.factura_descarga');
Route::get('api/imprime/acta_entrega/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimirpdfActaEntrega')->name('liquidador.imprimirpdf.actaentrega');
Route::get('api/epicrisis_nuevo/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_epicrisis_nuevo')->name('liquidador.descargar_epicrisis_nuevo.imprimir'); // no usada
Route::get('api/nuevo/imprimir/epicrisis/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_nuevo_epicrisis')->name('liquidador.imprimir_nuevo_epicrisis'); // actual
Route::get('api/auditoria_imprimir/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_epicrisis')->name('liquidador.auditoria_epicrisis.imprimir'); // antiguo
Route::get('api/hc4_interconsulta/imprimir/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_interconsulta')->name('liquidador.consulta.imprimir'); // no usado
Route::get('api/interconsulta_unificada_descargar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_interconsulta_unificado')->name('liquidador.descargar_interconsulta_unificado'); // actual
Route::get('api/orden/buscar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@buscar_orden')->name('liquidador.buscar.orden');
Route::get('api/orden/imprimir/{id_orden}', 'Apis\LiquidadorController@descargar_orden_laboratorio')->name('liquidador.descargar.orden');
Route::get('api/orden/unificado/{id_orden}', 'Apis\LiquidadorController@formulario010_unificado')->name('liquidador.formulario010_unificado');
Route::get('api/resultados/imprimir/{id_orden}', 'Apis\LiquidadorController@imprimir_resultado')->name('liquidador.imprimir.resultado');
Route::get('api/imagenologia/imprimir/{id_orden}', 'Apis\LiquidadorController@imprimir_formulario012')->name('liquidador.imprimir.imagenologia');
Route::get('api/imagenologia_unificado/imprimir/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_formulario012_unificado')->name('liquidador.imagenologia_unificado');
Route::get('api/formulario12B/descartgar/{empresa}/{mes_plano}/{atencion}', 'Apis\NuevoLiquidadorController@formulario12B')->name('nuevoLiquidador.formulario12B.por_atencion');
Route::get('api/protocolo_quirurgico/imprimir/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_protocolo_quirurgico')->name('liquidador.descargar_protocolo_quirurgico');
Route::get('api/protocolo/imprimir/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_protocolo')->name('liquidador.imprimir.protocolo'); // antiguo
Route::get('api/nuevo_protocolo/descargar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_protocolo')->name('liquidador.descargar_nuevo_protocolo'); // actual
Route::get('api/descargar/cobertura/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_cobertura')->name('liquidador.descargar_cobertura');
Route::get('api/cobertura/privada/descargar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_cobertura_privada')->name('liquidador.descargar_cobertura_privada');
Route::get('api/anestesia/imprimir/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_anestesia')->name('liquidador.imprimir.anestesia');
Route::get('api/histopatologia/imprimir/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_histopatologia')->name('liquidador.imprimir.histopatologia');
Route::get('api/formulario013B/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@formulario013B')->name('liquidador.formulario013B');
Route::get('api/estudio/imprimir/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_estudio')->name('liquidador.imprimir.estudio');
Route::get('api/descargar/bitacora/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@bitacora')->name('liquidador.bitacora_pdf');
Route::get('api/descargar/anamenesis/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_anamnesis')->name('liquidador.descargar_anamnesis'); // no usada
Route::get('api/anamenesis/pdf/descargar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_anemnesis')->name('liquidador.imprimir_anemnesis'); // actual
Route::get('api/descargar/hc_as400/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_hc_as400')->name('liquidador.descargar_hc_as400');
Route::get('api/agenda_archivo/buscar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@buscar_agenda_as400')->name('liquidador.buscar_agenda_as400');
Route::get('api/agenda_archivo/imprimir/{id}', 'Apis\LiquidadorController@descargar_archivo_as400')->name('liquidador.descargar_archivo_as400');
Route::get('api/descargar/paciente/cedula/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_cedula')->name('liquidador.descargar_cedula');
Route::get('api/descargar_receta/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_receta')->name('liquidador.descargar_receta');
Route::get('api/descargar_nota_entrega/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@imprimir_nota_entrega')->name('liquidador.imprimir_nota_entrega');
Route::get('api/descargar/scan/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_scan')->name('liquidador.descargar_scan');
Route::get('api/descargar/validacion/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_validacion')->name('liquidador.descargar_validacion');
Route::get('api/pre_anestesico/descargar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@descargar_pre_anestesiologo')->name('liquidador.descargar_pre_anestesiologo');
Route::get('api/nuevo_evolucion05/descargar/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@nueva_evolucion_x_paciente')->name('liquidador.nueva_evolucion_x_paciente');
Route::get('api/obtener/nombre_paciente/{atencion}/{id_empresa}/{id_seguro}/{mes_plano}', 'Apis\LiquidadorController@getPacienteNombre')->name('liquidador.getPacienteNombre');
Route::get('api/descargar/informe_descargo/pdf/{empresa}/{mes_plano}/{atencion}', 'Apis\LiquidadorController@planilla_descargo_pdf')->name('liquidador.planilla_descargo_pdf');
Route::get('api/consultar/cabecera/plano/{id}', 'Apis\NuevoLiquidadorController@getCabeceraPlano')->name('nuevoLiquidador.getCabeceraPlano');
Route::get('api/descargar/buscar_paciente/{empresa}/{mes_plano}/{atencion}', 'Apis\NuevoLiquidadorController@buscar_planillas')->name('nuevoLiquidador.buscar_planillas');
Route::get('nuevo_planilla_individual/imprimir_plantillas/pdf/{numero_paciente}/{id_empresa}/{id_seguro}/{mes_plano}', 'Apis\NuevoLiquidadorController@nuevo_planilla_detalle_pdf')->name('nuevoLiquidador.nuevo_planilla_detalle_pdf');
Route::get('api/nuevo_planilla_individual/imprimir_plantillas/pdf/{numero_paciente}/{id_empresa}/{id_seguro}/{mes_plano}', 'Apis\NuevoLiquidadorController@nuevo_planilla_detalle_pdf')->name('nuevoLiquidador.nuevo_planilla_detalle_pdf');
Route::get('api/nuevo_planilla_individual/imprimir_plantillas/recupera_paciente/{numero_paciente}/{id_empresa}/{id_seguro}/{mes_plano}', 'Apis\NuevoLiquidadorController@recupera_paciente')->name('nuevoLiquidador.recupera_paciente');
Route::get('api/descargar/planilla/{id}', 'Apis\NuevoLiquidadorController@planilla_detalle_pdf')->name('nuevoLiquidador.planilla_detalle_pdf');
Route::get('api/descargar/factura/{id}', 'Apis\NuevoLiquidadorController@factura_descarga')->name('nuevoLiquidador.factura_descarga');
Route::get('api/descargar/acta_entrega/{id}', 'Apis\NuevoLiquidadorController@imprimirpdfActaEntrega')->name('nuevoLiquidador.imprimirpdf.actaentrega');
Route::get('api/descargar/epicrisis/{id}', 'Apis\NuevoLiquidadorController@imprimir_epicrisis')->name('nuevoLiquidador.auditoria_epicrisis.imprimir');
Route::get('api/descargar/epicrisis_nuevo/pdf/{id}', 'Apis\NuevoLiquidadorController@descargar_epicrisis_nuevo')->name('nuevoLiquidador.descargar_epicrisis_nuevo.imprimir');
Route::get('api/descargar/interconsulta/{id}', 'Apis\NuevoLiquidadorController@imprimir_interconsulta')->name('nuevoLiquidador.consulta.imprimir');
Route::get('api/descargar/interconsulta_unificada/{id}', 'Apis\NuevoLiquidadorController@descargar_interconsulta_unificado')->name('nuevoLiquidador.descargar_interconsulta_unificado');
Route::get('api/descargar/orden/buscar/{id}', 'Apis\NuevoLiquidadorController@buscar_orden')->name('nuevoLiquidador.buscar.orden');
Route::get('api/descargar/orden/{id_orden}', 'Apis\NuevoLiquidadorController@descargar_orden_laboratorio')->name('nuevoLiquidador.descargar.orden');
Route::get('api/descargar/resultados/{id_orden}', 'Apis\NuevoLiquidadorController@imprimir_resultado')->name('nuevoLiquidador.imprimir.resultado');
Route::get('api/descargar/imagenologia/{id_orden}', 'Apis\NuevoLiquidadorController@imprimir_formulario012')->name('nuevoLiquidador.imprimir.imagenologia');
Route::get('api/descargar/formulario12B/{id}', 'Apis\NuevoLiquidadorController@formulario12B')->name('nuevoLiquidador.formulario12B');
Route::get('api/descargar/imagenologia_unificado/{id}', 'Apis\NuevoLiquidadorController@imprimir_formulario012_unificado')->name('nuevoLiquidador.imagenologia_unificado');
Route::get('api/descargar/protocolo/{id}', 'Apis\NuevoLiquidadorController@imprimir_protocolo')->name('nuevoLiquidador.imprimir.protocolo');
Route::get('api/descargar/protocolo_quirurgico/{id}', 'Apis\NuevoLiquidadorController@descargar_protocolo_quirurgico')->name('nuevoLiquidador.descargar_protocolo_quirurgico');
Route::get('api/descargar/nuevo_protocolo/{id}', 'Apis\NuevoLiquidadorController@descargar_protocolo')->name('nuevoLiquidador.descargar_nuevo_protocolo');
Route::get('api/descargar/cobertura/{id}', 'Apis\NuevoLiquidadorController@descargar_cobertura')->name('nuevoLiquidador.descargar_cobertura');
Route::get('api/descargar/cobertura/privada/{id}', 'Apis\NuevoLiquidadorController@descargar_cobertura_privada')->name('nuevoLiquidador.descargar_cobertura_privada');
Route::get('api/descargar/anestesia/{id}', 'Apis\NuevoLiquidadorController@imprimir_anestesia')->name('nuevoLiquidador.imprimir.anestesia');
Route::get('api/descargar/histopatologia/{id}', 'Apis\NuevoLiquidadorController@imprimir_formulario013A')->name('nuevoLiquidador.imprimir.histopatologia');
Route::get('api/descargar/formulario013B/pdf/{id}', 'Apis\NuevoLiquidadorController@formulario013B')->name('nuevoLiquidador.formulario013B');
Route::get('api/descargar/estudio/{id}', 'Apis\NuevoLiquidadorController@imprimir_estudio')->name('nuevoLiquidador.imprimir.estudio');
Route::get('api/descargar/bitacora/{id}', 'Apis\NuevoLiquidadorController@bitacora')->name('nuevoLiquidador.bitacora_pdf');
Route::get('api/descargar/anamenesis/pdf/{id}', 'Apis\NuevoLiquidadorController@descargar_anamnesis')->name('nuevoLiquidador.descargar_anamnesis');
Route::get('api/descargar/nuevo_anamenesis/pdf/{id}', 'Apis\NuevoLiquidadorController@imprimir_anemnesis')->name('nuevoLiquidador.imprimir_anemnesis'); // actual
Route::get('api/descargar/orden_unificado/{id_orden}', 'Apis\NuevoLiquidadorController@formulario010_unificado')->name('nuevoLiquidador.formulario010_unificado');
Route::get('api/descargar/hc_as400/{id}', 'Apis\NuevoLiquidadorController@descargar_hc_as400')->name('nuevoLiquidador.descargar_hc_as400');
Route::get('api/agenda_archivo/buscar/{id}', 'Apis\NuevoLiquidadorController@buscar_agenda_as400')->name('nuevoLiquidador.buscar_agenda_as400');
Route::get('api/descargar/agenda_archivo/{id}', 'Apis\NuevoLiquidadorController@descargar_archivo_as400')->name('nuevoLiquidador.descargar_archivo_as400');
Route::get('api/descargar/cedula/{id}', 'Apis\NuevoLiquidadorController@descargar_cedula')->name('nuevoLiquidador.descargar_cedula');
Route::get('api/descargar/receta/pdf/{id}', 'Apis\NuevoLiquidadorController@descargar_receta')->name('nuevoLiquidador.descargar_receta');
Route::get('api/descargar/nota_entrega/pdf/{id}', 'Apis\NuevoLiquidadorController@imprimir_nota_entrega')->name('nuevoLiquidador.imprimir_nota_entrega');
Route::get('api/descargar/scan/{id}', 'Apis\NuevoLiquidadorController@descargar_scan')->name('nuevoLiquidador.descargar_scan');
Route::get('api/descargar/validacion/{id}', 'Apis\NuevoLiquidadorController@descargar_validacion')->name('nuevoLiquidador.descargar_validacion');
Route::get('api/descargar/pre_anestesico/{id}', 'Apis\NuevoLiquidadorController@descargar_pre_anestesiologo')->name('nuevoLiquidador.descargar_pre_anestesiologo');
Route::get('api/descargar/nuevo/evolucion05/{id}', 'Apis\NuevoLiquidadorController@nueva_evolucion_x_paciente')->name('nuevoLiquidador.nueva_evolucion_x_paciente');
// dr net
Route::post('bd/login/drnet', 'Apis\BdDrNetController@loginDrnet')->name('bddrnet.loginDrnet');
Route::post('bd/datos/usuario', 'Apis\BdDrNetController@datos_usuario')->name('bddrnet.datos_usuario');
Route::post('bd/verificar/usuario', 'Apis\BdDrNetController@verificar_usuario')->name('bddrnet.verificar_usuario');
Route::post('bd/cambiar/contrasena', 'Apis\BdDrNetController@cambiar_contrasena')->name('bddrnet.cambiar_contrasena');
Route::post('bd/crear/usuario/drnet', 'Apis\BdDrNetController@crearUserDrnet')->name('bddrnet.crearUserDrnet');
Route::post('bd/update/usuario/drnet', 'Apis\BdDrNetController@updateUsuarioDrnet')->name('bddrnet.updateUsuarioDrnet');
Route::post('bd/store/consulta', 'Apis\BdDrNetController@storeConsulta')->name('bddrnet.storeConsulta');
Route::post('bd/listado/especialidades', 'Apis\BdDrNetController@listadoEspecialidades')->name('bddrnet.listadoEspecialidades');
Route::post('bd/eliminar/user/drnet', 'Apis\BdDrNetController@eliminarUserDrnet')->name('bddrnet.eliminarUserDrnet');
Route::post('bd/upload/photo/perfil', 'Apis\BdDrNetController@uploadPhoto')->name('bddrnet.uploadPhoto');
Route::post('bd/lista/ciudades', 'Apis\BdDrNetController@listaCiudades')->name('bddrnet.listaCiudades');
Route::post('bd/registrar/info/general', 'Apis\BdDrNetController@registrarInfoGeneral')->name('bddrnet.registrarInfoGeneral');
Route::post('bd/info/general/doc', 'Apis\BdDrNetController@infoGeneralDoc')->name('bddrnet.infoGeneralDoc');
Route::post('bd/listado/consultorios', 'Apis\BdDrNetController@listado_consultorios')->name('bddrnet.listado_consultorios');
Route::post('bd/crear/consultorios', 'Apis\BdDrNetController@crearConsultorios')->name('bddrnet.crearConsultorios');
Route::post('bd/eliminar/consultorios', 'Apis\BdDrNetController@eliminarConsultorios')->name('bddrnet.eliminarConsultorios');
Route::post('bd/editar/consultorio', 'Apis\BdDrNetController@editarConsultorio')->name('bddrnet.editarConsultorio');
Route::post('bd/horario/doctor', 'Apis\BdDrNetController@HorarioxDoctor')->name('bddrnet.HorarioxDoctor');
Route::post('bd/crear/horario/doctor', 'Apis\BdDrNetController@crearHorariosDoctor')->name('bddrnet.crearHorariosDoctor');
Route::post('bd/listado/especialidad/doctor', 'Apis\BdDrNetController@listaEspexDoctor')->name('bddrnet.listaEspexDoctor');
Route::get('bd/foto/usuario/{id_usuario}', 'Apis\BdDrNetController@ver_foto_usuario')->name('bddrnet.ver_foto_usuario');
Route::match(['get', 'post'], 'bd/index_prueba', 'Apis\BdDrNetController@index_prueba')->name('bddrnet.index_prueba');
Route::post('bd/crear/especialidad/doctor', 'Apis\BdDrNetController@creaEspexDoctor')->name('bddrnet.creaEspexDoctor');
Route::post('bd/lista/doctores', 'Apis\BdDrNetController@lista_doctores')->name('bddrnet.lista_doctores');
Route::post('bd/subir/documentos/validacion', 'Apis\BdDrNetController@subirDocValidacion')->name('bddrnet.subirDocValidacion');
Route::post('bd/lista/documentos/validacion', 'Apis\BdDrNetController@listaDocValidacion')->name('bddrnet.listaDocValidacion');
Route::post('bd/crear/producto', 'Apis\BdDrNetController@crearProducto')->name('bddrnet.crearProducto');
Route::post('bd/listado/productos', 'Apis\BdDrNetController@listado_productos')->name('bddrnet.listado_productos');
Route::post('bd/verificar/estado/docs', 'Apis\BdDrNetController@verificarEstadoDocs')->name('bddrnet.verificarEstadoDocs');
Route::post('bd/asignar/valor/producto', 'Apis\BdDrNetController@asignarValorProd')->name('bddrnet.asignarValorProd');
Route::post('bd/listado/doctor/producto', 'Apis\BdDrNetController@listaProdxDoc')->name('bddrnet.listaProdxDoc');
Route::post('bd/disponibilidad/doctor', 'Apis\BdDrNetController@disponibilidadDoctor')->name('bddrnet.disponibilidadDoctor');
Route::post('bd/confirmacion/agenda/pago', 'Apis\BdDrNetController@ConfirmacionAgendaPayment')->name('bddrnet.ConfirmacionAgendaPayment');
Route::post('bd/confirmar/pago/consulta', 'Apis\BdDrNetController@confirmarPagoConsulta')->name('bddrnet.confirmarPagoConsulta');
Route::post('bd/pago/consulta', 'Apis\BdDrNetController@pagoConsulta')->name('bddrnet.pagoConsulta');

// Farmalink
Route::post('bd/auth/login', 'Apis\BdFarmalinkController@loginApp')->name('farmalink.loginApp');
Route::post('bd/codigo/contrasena', 'Apis\BdFarmalinkController@CreaCodigoContrasena')->name('farmalink.CreaCodigoContrasena');
Route::post('bd/verificar/codigo', 'Apis\BdFarmalinkController@ValCodContrasena')->name('farmalink.ValCodContrasena');
Route::post('bd/resetear/contrasena', 'Apis\BdFarmalinkController@cambiar_contraseña')->name('farmalink.cambiar_contraseña');
Route::post('bd/tipos/direcciones', 'Apis\BdFarmalinkController@tipos_direcciones')->name('farmalink.tipos_direcciones');
Route::post('bd/direccion/usuario', 'Apis\BdFarmalinkController@DireccionesxUsuario')->name('farmalink.DireccionesxUsuario');
Route::post('bd/crear/direccion', 'Apis\BdFarmalinkController@CrearDireccion')->name('farmalink.CrearDireccion');
Route::post('bd/actualizar/direccion', 'Apis\BdFarmalinkController@ActualizarDireccion')->name('farmalink.ActualizarDireccion');
Route::post('bd/auth/register', 'Apis\BdFarmalinkController@registerUser')->name('farmalink.registerUser');
Route::post('bd/auth/datos/usuario', 'Apis\BdFarmalinkController@datosUsuario')->name('farmalink.datosUsuario');
Route::post('bd/user/edit/profile', 'Apis\BdFarmalinkController@updateUser')->name('farmalink.updateUser');

Route::post('bd/storeCompra', 'BdProductosController@storeCompra')->name('bdproductos.storeCompra');
Route::post('bd/reporte_compras', 'BdEstadisticosEndoscopyController@reporte_compras')->name('bdestadisticos.reporte_compras');

Route::get('api/firmador/desencriptar_password/{password}', 'Apis\FirmadorController@decryp_password')->name('firmador.decryp_password');
Route::post('api/firmador/desencriptar_password', 'Apis\FirmadorController@decryp_password_post')->name('firmador.decryp_password_post');

Route::post('reporte/examenes/realizados', 'BdEstadisticosEndoscopyController@reporte_examenes_realizados')->name('bdestadisticos.reporte_examenes_realizados');
Route::post('bd/lista/examenes', 'BdEstadisticosEndoscopyController@lista_examenes')->name('bdestadisticos.lista_examenes');
