<?php

/*Route::match(['get', 'post'],'hospital/admin/gestionh/ buscar','hospital\HospitalAdminController@buscar')->name('hospital_admin.buscar');>
/*Route::match(['get', 'post'],'hospital/gestioncuartos','HospitalController@gcuartos')->name('hospital.gcuartos');
Route::match(['get', 'post'],'hospital/admcuarto','HospitalController@admcuarto')->name('hospital.admcuarto');
Route::match(['get', 'post'],'hospital/farmacia/agregarp','HospitalController@agregarp')->name('hospital.agregarp');
Route::match(['get', 'post'],'hospital/quirofano','HospitalController@quirofano')->name('hospital.quirofano');
Route::match(['get', 'post'],'hospital/modalq','HospitalController@modalq')->name('hospital.modalq');*/
//Route::get('administracion/procedimientos/', 'archivo_plano\ApProcedimientosController@index');
Route::match(['get', 'post'], 'administracion/procedimientos/lista', 'archivo_plano\ApProcedimientosController@lista');

Route::post('/plantillas/search', 'archivo_plano\ApPlantillaController@search')->name('plantillas.search');
Route::match(['get', 'post'], 'administracion/plantillas/editar/{id}', 'archivo_plano\ApPlantillaController@edit')->name('plantillas.edit');
Route::get('administracion/plantillas/items/{id}', 'archivo_plano\ApPlantillaController@veritem')->name('plantillas.items');
Route::post('administracion/plantillas/editar', 'archivo_plano\ApPlantillaController@update')->name('plantillas.update');
//ingreso procedimientos a cabecera de archivo plano
//Route::match(['get', 'post'],'archivo_plano/planilla/detalle/procedimiento/ingresar/{id}/{cabecera}/{fecha}', 'archivo_plano\Ap_PlanillaController@ingresa_procedimiento');
Route::match(['get', 'post'], 'archivo_plano/planilla/detalle/procedimiento/eliminar/{id}', 'archivo_plano\Ap_PlanillaController@elimino_procedimiento');

Route::get('archivo_plano/planilla/editar/{id}', 'archivo_plano\Ap_PlanillaController@edito_proce');
Route::post('archivo_plano/planilla/editar/{id}', 'archivo_plano\Ap_PlanillaController@edito_proce2');

Route::match(['get', 'post'], 'administracion/procedimientos/', 'archivo_plano\ApProcedimientosController@index');
Route::match(['get', 'post'], 'administracion/procedimientos/buscar', 'archivo_plano\ApProcedimientosController@buscar_procedimiento')->name('procedimientos.buscar');
Route::get('administracion/procedimientos/crear', 'archivo_plano\ApProcedimientosController@index2')->name('procedimientos.crear');
Route::post('administracion/procedimientos/frmcrear', 'archivo_plano\ApProcedimientosController@store')->name('procedimientos.store');
Route::get('administracion/procedimientos/editar/{id}', 'archivo_plano\ApProcedimientosController@editar')->name('procedimientos_editar');
Route::post('administracion/procedimientos/actualizar', 'archivo_plano\ApProcedimientosController@update')->name('procedimientos_editar.actualizar');
Route::post('administracion/procedimientos/inline/{id}', 'archivo_plano\ApProcedimientosController@guardarInline')->where('id', '[0-9]+')->name('procedimientos.inline.guardar');
Route::post('administracion/procedimientos/eliminar/{id}', 'archivo_plano\ApProcedimientosController@eliminarInline')->where('id', '[0-9]+')->name('procedimientos.inline.eliminar');

Route::get('administracion/procedimientos/asigno_nivel', 'archivo_plano\ApProcedimientosController@index4')->name('procedimientos.nivel');
Route::post('/procedimientosd/fetch', 'archivo_plano\ApProcedimientosController@fetch')->name('procedimientosd.fetch');
Route::post('administracion/procedimientos/frmasignonivel', 'archivo_plano\ApProcedimientosController@store2')->name('procedimientos.asigno_nivel');

Route::match(['get', 'post'], 'administracion/plantillas/', 'archivo_plano\ApPlantillaController@index')->name('applantilla.index');
Route::match(['get', 'post'], 'plantillas/buscar', 'archivo_plano\ApPlantillaController@buscar_plantillas')->name('plantillas.buscar');
Route::get('administracion/plantillas/crear', 'archivo_plano\ApPlantillaController@index2')->name('plantillas.crear');
//Route::post('administracion/plantillas/frmcrear', 'archivo_plano\ApPlantillaController@store');
Route::match(['get', 'post'], 'plantillas/insert', 'archivo_plano\ApPlantillaController@insert')->name('plantillas.insert');
//Route::resource('procedimientos', 'archivo_plano\ApProcedimientosController');
//Route::match(['get', 'post'],'archivo_plano/procedimientos','archivo_plano\ApProcedimientosController@index')->name('archivo_plano.procedimientos');
//Route::match(['get', 'post'],'archivo_plano/procedimientos', 'ApProcedimientosController@search')->name('archivo_plano.procedimientos');
Route::match(['get', 'post'], 'archivo_plano/planilla/', 'archivo_plano\Ap_PlanillaController@planilla')->name('archivo_plano.planilla');
Route::post('archivo_plano/guardar/', 'archivo_plano\Ap_PlanillaController@guardar')->name('planilla.guardar');
Route::get('archivo_plano/planilla_iess/autocomplete', 'archivo_plano\Ap_PlanillaController@auto')->name('planilla.auto');
Route::get('archivo_plano/planilla_iess/autocomplete2', 'archivo_plano\Ap_PlanillaController@auto2')->name('planilla.auto2');
//pantalla de planillas por historia clinica
Route::get('archivo_plano/planilla/{hcid}/{seguro}', 'archivo_plano\Ap_PlanillaController@planilla_hcid')->name('archivo_plano.planilla_hcid');
//Ingreso de Planilla Msp
Route::get('archivo_plano/planilla/msp/{hcid}/{seguro}', 'archivo_plano\Ap_PlanillaController@planilla_msp')->name('archivo_plano.planilla_msp');
//Guardar Planilla Msp
Route::post('archivo_plano/planilla/msp/guardar', 'archivo_plano\Ap_PlanillaController@guardar_planilla_msp')->name('planilla_msp.guardar');

//Item Iess
Route::get('archivo_plano/planilla/item/iess/crear/{idcabecera}', 'archivo_plano\ApPlantillaItemIessController@crear_item_iess')->name('planilla_item.iess');
Route::post('archivo_plano/planilla/item/iess/guardar', 'archivo_plano\ApPlantillaItemIessController@store_item_iess')->name('iess_store.item');

//Agrega Lista Item Iess
Route::get('archivo_plano/planilla/agrega/lista/item/iess/{idcab}', 'archivo_plano\ApPlantillaItemIessController@lista_item_iess')->name('lista_item_modal.iess');
Route::post('archivo_plano/planilla/procedimiento/search', 'archivo_plano\ApPlantillaItemIessController@buscar_plant_procedimiento')->name('buscar.procedimiento');

//Actualiza Item Iess
Route::get('archivo_plano/planilla/item/iess/crea/update/{id}/{indice}', 'archivo_plano\ApPlantillaItemIessController@crea_upd_item_iess')->name('update_item_modal.iess');
Route::post('archivo_plano/planilla/item/iess/store/update', 'archivo_plano\ApPlantillaItemIessController@store_upd_item_iess')->name('store_item_modal.iess');
Route::post('archivo_plano/elimina_todo/items/iess', 'archivo_plano\Ap_PlanillaController@delete_todo_items_iess')->name('delete.todo_items_iess');

//Autocompleta Descripcion  Iess
Route::match(['get', 'post'], 'archivo_plano/planilla/iess/buscardescripcion', 'archivo_plano\ApPlantillaItemIessController@buscar_descripcion')->name('item_iess.buscardescripcion');

//Autocomplete Codigo Iess
Route::match(['get', 'post'], 'archivo_plano/planilla/iess/buscarxcodigo', 'archivo_plano\ApPlantillaItemIessController@buscar_codigo')->name('item_iess.buscarxcodigo');

Route::match(['get', 'post'], 'vt_archivo_plano/planilla/iess/buscarxcodigo2', 'archivo_plano\ApPlantillaItemIessController@buscar_codigo_2')->name('item_iess.buscarxcodigo_2');
//Ingreso de Modal Procedimiento IESS Y MSP
Route::post('archivo_plano/planilla/procedimiento/lista/ingresar', 'archivo_plano\Ap_PlanillaController@ingresa_procedimiento_detalle')->name('ingreso_lista.procedimiento');

//Item MSP
Route::get('archivo_plano/planilla/item/msp/crear/{idcabecera}', 'archivo_plano\ApPlantillaItemMspController@crear_item_msp')->name('planilla_item.msp');
Route::post('archivo_plano/planilla/item/msp/guardar', 'archivo_plano\ApPlantillaItemMspController@store_item_msp')->name('msp_store.item');
Route::post('archivo_plano/elimina_todo/items/msp', 'archivo_plano\Ap_PlanillaController@delete_todo_items_msp')->name('delete.todo_items_msp');

//Agrega Lista Item Iess
Route::get('archivo_plano/planilla/agrega/lista/msp/{idcab}', 'archivo_plano\ApPlantillaItemMspController@lista_item_msp')->name('lista_item_modal.msp');

//Autocompleta Descripcion Msp
Route::match(['get', 'post'], 'archivo_plano/planilla/msp/buscardescripcion', 'archivo_plano\ApPlantillaItemMspController@buscar_descripcion_msp')->name('item_msp.buscardescripcion');

//Autocomplete Codigo Msp
Route::match(['get', 'post'], 'archivo_plano/planilla/msp/buscarxcodigo', 'archivo_plano\ApPlantillaItemMspController@buscar_codigo_msp')->name('item_msp.buscarxcodigo');

//Laboratorio
Route::get('archivo_plano/planilla/detalle/laboratorio/{cabecera}', 'archivo_plano\Ap_PlanillaController@busca_ordenes_labs')->name('planilla.busca_ordenes_labs');
Route::get('archivo_plano/planilla/detalle/insumos/{cabecera}', 'archivo_plano\Ap_PlanillaController@busca_insumos')->name('planilla.busca_insumos');
Route::post('archivo_plano/planilla/detalle/laboratorio/buscar', 'archivo_plano\Ap_PlanillaController@buscar_labs')->name('planilla.buscar_labs');
Route::get('ap_laboratorio/detalle/{id}', 'archivo_plano\Ap_PlanillaController@detalle_laboratorio')->name('planilla.detalle_laboratorio');
//excel planilla
Route::match(['get', 'post'], 'archivo_plano/planilla_individual/{hcid}', 'archivo_plano\Ap_PlanillaController@planilla_individual')->name('archivo_plano.planilla_individual');
Route::get('archivo_plano/planilla/detalle/laboratorio/ingresar/{orden}/{cabecera}', 'archivo_plano\Ap_PlanillaController@ingresa_ordenes_labs')->name('planilla.ingresa_ordenes_labs');
Route::get('archivo_plano/mostrar_detalle/{cabecera}/{id_seguro}', 'archivo_plano\Ap_PlanillaController@mostrar_detalle')->name('planilla.mostrar_detalle');

//Planilla Individual Msp
Route::match(['get', 'post'], 'archivo_plano/planilla_individual/msp/{hcid}', 'archivo_plano\Ap_PlanillaController@planilla_individual_msp')->name('archivo_plano_msp.planilla_individual');

//Planilla de Cargos Individual MSP
Route::match(['get', 'post'], 'archivo_plano/planilla_cargo_individual/msp/{idcab}', 'archivo_plano\Ap_PlanillaController@planilla_cargo_individual_msp')->name('archivo_plano_msp.planilla_cargo_individual');

//Planilla de Cargos Condolidado MSP
Route::match(['get', 'post'], 'archivo_plano/planilla_cargo_consolidado/msp/{hcid}', 'archivo_plano\Ap_PlanillaController@planilla_cargo_consolidado_msp')->name('archivo_plano_msp.planilla_cargo_consolidado');

//Generar Archivo Plano MSP
Route::match(['get', 'post'], 'archivo_plano/genera_ap_msp', 'archivo_plano\Ap_ArchivoMspController@crear_ap_msp')->name('genera_ap_msp.planilla');
Route::post('archivo_plano/planilla/search/mes_plano/empresa', 'archivo_plano\Ap_ArchivoMspController@search_mes_empresa')->name('buscar.mes_plano');
Route::post('archivo_plano/genera_ap_msp_excel', 'archivo_plano\Ap_ArchivoMspController@crear_ap_msp_excel')->name('genera_ap_msp_excel.planilla');
Route::post('archivo_plano/planilla/genera_reporte_consolidado', 'archivo_plano\Ap_ArchivoMspController@crear_reporte_consolidado')->name('genera_msp_rp_consol.planilla');

//archivo plano
Route::match(['get', 'post'], 'archivo_plano/genera_ap', 'archivo_plano\Ap_ArchivoController@genera_ap')->name('planilla.genera_ap');
Route::match(['get', 'post'], 'archivo_plano/genera_ap_excel', 'archivo_plano\Ap_ArchivoController@genera_ap_excel')->name('planilla.genera_ap_excel');

Route::match(['get', 'post'], 'nuevo_archivo_plano_092022/genera_ap_excel', 'archivo_plano\Ap_ArchivoController@genera_ap_excel_092022')->name('planilla.genera_ap_excel_092022');
//generar planillas
Route::match(['get', 'post'], 'archivo_plano/genera_planillas', 'archivo_plano\Ap_ArchivoController@genera_planillas')->name('planilla.genera_planillas');

Route::match(['get', 'post'], 'archivo_plano/paciente_nombre', 'archivo_plano\Ap_ArchivoController@paciente_nombre')->name('planilla.paciente_nombre');

Route::match(['get', 'post'], 'archivo_plano/paciente_nombre2', 'archivo_plano\Ap_ArchivoController@paciente_nombre2')->name('planilla.paciente_nombre2');

Route::match(['get', 'post'], 'archivo_plano/planillas_generadas', 'archivo_plano\Ap_ArchivoController@planillas_generadas')->name('planilla.planillas_generadas');

//Obtener Clasificador por Tipo
Route::get('archivo_plano/planilla/obtener/clasificador/{tip}', 'archivo_plano\Ap_PlanillaController@obtener_clasificador')->name('search.tipo_clasificador');

//Obtener precio Item
Route::get('archivo_plano/planilla/obtener/precio/item/{id_ap_pro}/{nivel_convenio}/{tip}', 'archivo_plano\Ap_PlanillaController@obtener_precio_item')->name('search.precio_item');

//Retorno
//Route::get('archivo_plano/retorna/idprocedimiento', 'archivo_plano\ApPlantillaItemIessController@obtener_id_proced')->name('obtener.proced');

//autocomplete procedimientos
Route::match(['get', 'post'], 'archivo_plano/procedimiento_plantilla', 'archivo_plano\Ap_PlanillaController@procedimiento_plantilla')->name('archivo_plano.procedimiento_plantilla');

//Carga de Datos Tabla Item
Route::post('archivo_plano/planilla/crear/detalle/items', 'archivo_plano\Ap_PlanillaController@crear_detalle_item')->name('obtener_detalle.items');

//Cardiologia
Route::match(['get', 'post'], 'archivo_planoc/planilla/cardiologia/{cabecera}', 'archivo_plano\Ap_PlanillaController@cardiologia')->name('planilla.cardiologia');

Route::match(['get', 'post'], 'archivo_planoc/planilla/busca_cardiologia', 'archivo_plano\Ap_PlanillaController@busca_cardiologia')->name('planilla.busca_cardiologia');

Route::get('archivo_planoc/planilla/ingresar_cardio/{hc_cardio}/{cabecera}/{seguro}/{empresa}/{agenda}', 'archivo_plano\Ap_PlanillaController@ingresar_cardio')->name('planilla.ingresar_cardio');

Route::post('archivo_plano/cargar_detalle/items', 'archivo_plano\Ap_PlanillaController@crear_item')->name('detalle.crear_item');

//Calculo de nuevos Valores Detalles ITEMS IESS
Route::get('archivo_plano/planilla/calcular_nuevos/valores/items/{idcabecera}/{idseguro}/{idempresa}', 'archivo_plano\ApPlantillaItemIessController@recalculo_valor_items')->name('planilla.iess_recalculo_valor');

//Autocompleta MesPlano
Route::match(['get', 'post'], 'archivo_plano/planilla/search/mes_plano', 'archivo_plano\Ap_ArchivoMspController@search_mes_plano')->name('search.mes_plano');

//Elimina Cabecera Planilla
Route::match(['get', 'post'], 'archivo_plano/planilla/cabecera_planilla/eliminar/{id}', 'archivo_plano\Ap_PlanillaController@elimina_cabecera_planilla');

//Crea Registro
Route::get('archivo_planosdd/planilla/completa/tabla', 'archivo_plano\Ap_PlanillaController@crear_registro')->name('completar_detalle.tabla');

//Generacion de Reporte
Route::match(['get', 'post'], 'archivo_plano/reportes', 'archivo_plano\Ap_ArchivoController@reportes')->name('planilla.reportes');
Route::match(['get', 'post'], 'archivo_plano/reportes_excel', 'archivo_plano\Ap_ArchivoController@reportes_excel')->name('planilla.reportes_excel');
Route::match(['get', 'post'], 'archivo_plano/reporte_agrupado', 'archivo_plano\Ap_ArchivoController@reporte_agrupado')->name('planilla.reporte_agrupado');
Route::match(['get', 'post'], 'archivo_plano/reporte/cuenta/iess', 'archivo_plano\Ap_ArchivoMspController@reporte_cuenta_iess')->name('planilla.reporte_cuenta_iess');
Route::match(['get', 'post'], 'archivo_plano/reporte/consolidado/iess', 'archivo_plano\Ap_ArchivoController@reporte_consolidado_iess')->name('planilla.reporte_consolidado_iess');
Route::match(['get', 'post'], 'archivo_plano/reporte/consolidado/campesino', 'archivo_plano\Ap_ArchivoController@reporte_consolidado_campesino')->name('planilla.reporte_consolidado_campesino');
Route::match(['get', 'post'], 'archivo_plano/reporte/seguros_privados', 'archivo_plano\Ap_PlanillaController@reporte_seguros_privados')->name('ap_planilla.reporte_seguros_privados');
Route::match(['get', 'post'], 'archivo_plano/reporte/cobertura_issfa', 'archivo_plano\Ap_ArchivoController@reporte_cobertura_issfa')->name('ap_planilla.reporte_cobertura_issfa');
Route::match(['get', 'post'], 'archivo_plano/reporte/cobertura_isspol', 'archivo_plano\Ap_ArchivoController@reporte_cobertura_isspol')->name('ap_planilla.reporte_cobertura_isspol');
Route::match(['get', 'post'], 'archivo_plano/reporte/honorario_cirujano', 'archivo_plano\Ap_ArchivoController@reporte_honorario_cirujano')->name('ap_planilla.reporte_honorario_cirujano');
Route::match(['get', 'post'], 'archivo_plano/reporte/honorario_medico', 'archivo_plano\Ap_PlanillaController@honorario_medico')->name('ap_planilla.honorario_medico');
//REPORTE DE BIOPSIAS
Route::match(['get', 'post'], 'archivo_plano/reporte/biopsias', 'archivo_plano\Ap_PlanillaController@reporte_biopsias')->name('ap_planilla.reporte_biopsias');

//Descarga Planilla Consolidada
//Route::match(['get', 'post'],'archivo_plano/genera_ap_excel','archivo_plano\Ap_ArchivoController@genera_ap_excel')->name('planilla.genera_ap_excel');

Route::match(['get', 'post'], 'archivo_plano/planillas_generadas/plan_consolidada', 'archivo_plano\Ap_ArchivoController@genera_plan_consolidad')->name('genera_plan.consolidada');

Route::match(['get', 'post'], 'archivo/agrupado', 'archivo_plano\Ap_ArchivoController@guardar_agrupado')->name('archivo.guardar_agrupado');
Route::match(['get', 'post'], 'archivo/agrupado_objetar', 'archivo_plano\Ap_ArchivoController@agrupado_objetar')->name('archivo.agrupado_objetar');

//masivo items cambio de estado
Route::match(['get', 'post'], 'archivo/items/cambio', 'archivo_plano\ApProcedimientosController@cambio_estado')->name('archivo.cambio_estado');
Route::match(['get', 'post'], 'archivo/codigo_proceso/{id}', 'archivo_plano\Ap_ArchivoController@codigo_proceso')->name('archivo.codigo_proceso');
Route::match(['get', 'post'], 'archivo/codigo_proceso2/{id}', 'archivo_plano\Ap_ArchivoController@codigo_proceso2')->name('archivo.codigo_proceso2');


//Ruta para Generar Planilla Cargo Consolidado Msp
Route::match(['get', 'post'], 'archivo_plano/planilla_cargo_consolidado/msp', 'archivo_plano\Ap_PlanillaController@obtener_consolidado_msp')->name('genera_msp.planilla_cargo_consolidado');

//ESTADISTICOS
Route::match(['get', 'post'], 'ap_estadisticos/honorarios', 'archivo_plano\Ap_EstadisticosController@honorarios')->name('ap_estadisticos.honorarios');

//MASIVO ORDENES DE LABORATORIO  PUBLICAS VALOR NIVEL2 -- MOVER A WEB_LABORATORIO
Route::get('laboratorio/masivo/proceso_nivel2', 'ImportarController@proceso_nivel2')->name('importar.proceso_nivel2');

//MASIVO CAMBIO VALOR IVA
Route::get('archivo_plano/modifica/valor_iva', 'archivo_plano\Ap_ArchivoController@modifica_valor_iva_total')->name('modifica_valor.iva');

//VALIDA EXCEL PLANILLA INDIVIDUAL IESS
Route::post('archivo_plano/planilla/verifica/planilla_individual', 'archivo_plano\Ap_PlanillaController@verifica_planilla_individual_iess')->name('verifica_planilla.individual');

//VALIDA EXCEL PLANILLA INDIVIDUAL MSP
Route::post('archivo_plano/planilla/verifica/planilla_cargo_individual_msp', 'archivo_plano\Ap_PlanillaController@verifica_planilla_individual_msp')->name('verifica_planilla_cargo.individualmsp');

//VALIDA EXCEL REPORTE AGRUPADO
Route::post('archivo_plano/planilla/verifica/reporte_agrupado', 'archivo_plano\Ap_ArchivoController@verifica_reporte_agrupado')->name('verifica_reporte.agrupado');

//VALIDA EXCEL REPORTE CONSOLIDADO GENERAL
Route::post('archivo_plano/planilla/verifica/reporte_consolidado_general', 'archivo_plano\Ap_ArchivoController@verifica_consolidado_general')->name('verifica_consolidado.general');


//VALIDA EXCEL REPORTE CONSOLIDADO CAMPESINO
Route::post('archivo_plano/planilla/verifica/reporte_consolidado_campesino', 'archivo_plano\Ap_ArchivoController@verifica_consolidado_campesino')->name('verifica_consolidado.campesino');

//VALIDA EXCEL REPORTE CONSOLIDADO ISSFA
Route::post('archivo_plano/planilla/verifica/reporte_consolidado_issfa', 'archivo_plano\Ap_ArchivoController@verifica_consolidado_issfa')->name('verifica_consolidado.issfa');

//VALIDA EXCEL REPORTE CONSOLIDADO ISSPOL
Route::post('archivo_plano/planilla/verifica/reporte_consolidado_isspol', 'archivo_plano\Ap_ArchivoController@verifica_consolidado_isspol')->name('verifica_consolidado.isspol');

//VALIDA EXCEL REPORTE SEGURO PRIVADO
Route::post('archivo_plano/planilla/verifica/reporte_seguro_privado', 'archivo_plano\Ap_ArchivoController@verifica_seguro_privado')->name('verifica_seguro.privado');

//VALIDA EXCEL REPORTE HONORARIO CIRUJANO
Route::post('archivo_plano/planilla/verifica/reporte_honorario_cirujano', 'archivo_plano\Ap_ArchivoController@verifica_honorario_cirujano')->name('verifica_honorario.cirujano');

//VALIDA EXCEL REPORTE HONORARIO ANESTESIOLOGO
Route::post('archivo_plano/planilla/verifica/reporte_honorario_anestesiologo', 'archivo_plano\Ap_ArchivoController@verifica_honorario_anestesiologo')->name('verifica_honorario.anestesiologo');

//VALIDA EXCEL REPORTE BIOPSIAS
Route::post('archivo_plano/planilla/verifica/reporte_biopsias', 'archivo_plano\Ap_ArchivoController@verifica_biopsias')->name('ap_archivo.verifica_biopsia');

//nivel x item
Route::match(['get', 'post'], 'archivo_plano/nivel/{id}/{nivel}', 'archivo_plano\ApProcedimientosController@item_nivel')->name('ap_procedimiento.item_nivel');

Route::get('archivo_plano/actualiza_nivel/{id}/{nivel}', 'archivo_plano\ApProcedimientosController@actualiza_nivel')->name('ap_procedimiento.actualiza_nivel');

Route::post('archivo_plano/nivel/update_nivel', 'archivo_plano\ApProcedimientosController@update_nivel')->name('ap_procedimiento.update_nivel');

Route::get('archivo_plano/item/nivel/{id}', 'archivo_plano\ApProcedimientosController@nivel')->name('ap_procedimiento.nivel');


//Actualiza Procedimiento
Route::get('archivo_plano/act_plantilla_item/idprocedplant', 'archivo_plano\ApPlantillaItemIessController@obtener_id_proced_codig')->name('actua_plantilla_item.proced');


//Creacion de Procedimiento Funcionales MANOMETRÍA ESOFAGICA
//Route::get('archivo_plano/crear_procedimiento/manometria_esofagica', 'archivo_plano\Ap_Crear_ProcedimientosController@crear_proced_mano_esofagica')->name('procedimiento.mano_esof');

Route::post('archivo_plano/crear_procedimiento/manometria_esofagica', 'archivo_plano\Ap_Crear_ProcedimientosController@crear_proced_mano_esofagica')->name('procedimiento.mano_esof');

//Creacion de Procedimiento Funcionales MANOMETRIA ANORECTAL
Route::post('archivo_plano/crear_procedimiento/manometria_anorectal', 'archivo_plano\Ap_Crear_ProcedimientosController@crear_proced_mano_anorectal')->name('procedimiento.mano_anor');


//Creacion de Procedimiento Funcionales PH-METRIA
Route::post('archivo_plano/crear_procedimiento/ph_metria', 'archivo_plano\Ap_Crear_ProcedimientosController@crear_proced_ph_metria')->name('procedimiento.ph_metria');


//GUARDA MANOMETRÍA ESOFAGICA
Route::post('archivo_plano/procedimiento/proc_funcional_mano_esofagica/store', 'archivo_plano\Ap_Crear_ProcedimientosController@store_procedimiento_funcional_mano_esofagica')->name('proc_fun.mano_esofagica');


//GUARDA MANOMETRIA ANORECTAL
Route::post('archivo_plano/procedimiento/proc_funcional_mano_anorectal/store', 'archivo_plano\Ap_Crear_ProcedimientosController@store_procedimiento_funcional_mano_anorect')->name('proc_fun.mano_anorectal');


//GUARDA PH-METRIA
Route::post('archivo_plano/procedimiento/proc_funcional_ph_metria/store', 'archivo_plano\Ap_Crear_ProcedimientosController@store_procedimiento_funcional_ph_metria')->name('proc_fun.ph_metria');


//Estadistico Plano
Route::get('apestadisticos/aniomes', 'EstadisticosPlanoController@apestadisticos')->name('estadisticosplano.apestadisticos');

Route::get('insumos2021', 'ImportarController@insumos2021')->name('importar.insumos2021');

Route::get('covid_marzo', 'ImportarController@covid_marzo')->name('importar.covid_marzo');

Route::get('masivo_carga_archivo_plano', 'EstadisticosPlanoController@masivo_carga_archivo_plano')->name('masivo.masivo_carga_archivo_plano');

Route::get('masivo_carga_archivo_plano/excel', 'ImportarController@masivo_carga_excel')->name('masivo.masivo_carga_excel');

Route::get('examenes/abril', 'ImportarController@importar_examenes_abril')->name('importar.examenes_abril');

Route::get('nuevo/seguro', 'ImportarController@nuevo_seguro')->name('importar.nuevo_seguro');

//PROCESO CARGA ESTADISTICO DE CONSULTAS IESS
Route::get('ap_reporte/consultas_seguro_iess', 'archivo_plano\Ap_ArchivoController@consulta_seguro_iess')->name('ap_archivo.consulta_seguro_iess');

//Mantenimiento medicamentos
Route::get('archivo/medicamentos/index', 'archivo_plano\CrudMedicamentosController@index') -> name('index.medicamentos');
Route::get('archivo/medicamentos/crear', 'archivo_plano\CrudMedicamentosController@crear') ->name('crear.medicamentos');
Route::get('archivo/medicamentos/editar/{id}', 'archivo_plano\CrudMedicamentosController@editar') ->name('editar.medicamentos');
Route::match(['get', 'post'], 'archivo/update/med/','archivo_plano\CrudMedicamentosController@update_med')->name('medicamentos.update_med');
Route::post('archivo/medicamentos/guardar', 'archivo_plano\CrudMedicamentosController@guardar') ->name('guardar.medicamentos');
Route::match(['get', 'post'], 'archivo/bucar/med/','archivo_plano\CrudMedicamentosController@buscar')->name('buscar.medicamentos');


//Mantenimeitno Insumos
Route::get('archivo/insumos/index', 'archivo_plano\CrudInsumosController@index') -> name ('index_insumos');
Route::get('archivo/insumos/crear', 'archivo_plano\CrudInsumosController@crear') -> name ('crear_insumos');
Route::get('archivo/insumos/editar/{id}', 'archivo_plano\CrudInsumosController@editar') -> name ('editar_insumos');
Route::match(['get', 'post'],'archivo/update/ins', 'archivo_plano\CrudInsumosController@update_ins') -> name ('insumos.update_ins');
Route::post('archivo/insumos/guardar','archivo_plano\CrudInsumosController@guardar') -> name ('guardar_insumos');
Route::match(['get', 'post'], 'archivo/bucar/insumos','archivo_plano\CrudInsumosController@buscar')->name('buscar.insumos');

//Importar planillas
Route::get('subir_plantillas_ap', 'ImportarController@subir_plantillas_ap')-> name ('importar.subir_plantillas_ap');
Route::get('ap/imprimir_plantillas/pdf/{id_procedimiento}', 'archivo_plano\Ap_PlanillaDetalleController@planilla_detalle_pdf')->name('ap_planilladetalle.planilla_detalle_pdf');
Route::get('ap/imprimir_informe_descargo/pdf/{id_cabecera}', 'archivo_plano\Ap_PlanillaDetalleController@planilla_descargo_pdf')->name('ap_planilladetalle.planilla_descargo_pdf');
Route::get('ap/imprimir_plantillas/pdf/contab/{id_procedimiento}','archivo_plano\Ap_PlanillaDetalleController@planilla_detalle_contab_pdf')->name('ap_planilladetalle.planilla_detalle_contab_pdf');
Route::get('ap/imprimir_plantillas/pdf/contab/vs/{id}/{id_hc_procedimiento}','Insumos\PlantillaController@planilla_detalle_contab_pdf_vs')->name('ap_planilladetalle.planilla_detalle_contab_pdf_vs');

//12/1/2020
Route::match(['get', 'post'], 'guardar/plantillas_iess','archivo_plano\Ap_PlanillaController@guardar_plantilla_iess')->name('archivo_plano.guardar_plantilla_iess');

Route::get('masivo_corregir_subtotal', 'archivo_plano\Ap_ArchivoController@masivo_corregir_subtotal') -> name ('aparchivo.masivo_corregir_subtotal');

Route::get('plano_contable/ingresar/{aniomes}/{tipo}/{seg}/{cobertura}/{empresa}', 'archivo_plano\Ap_ArchivoController@plano_contable_ingresar')->name('aparchivo.plano_contable_ingresar');

Route::post('plano_contable/guardar', 'archivo_plano\Ap_ArchivoController@guardar_agrupado_vt')->name('archivo.guardar_agrupado_vt');
Route::get('plano_contable/ingresar/{id}', 'archivo_plano\Ap_ArchivoController@plano_contable_editar')->name('aparchivo.plano_contable_editar');
Route::get('plano_contable/eliminar/registro/{id}', 'archivo_plano\Ap_ArchivoController@plano_contable_eliminar')->name('aparchivo.plano_contable_eliminar');

Route::match(['get', 'post'],'total_agrupado/', 'archivo_plano\Ap_ArchivoController@total_agrupado')->name('aparchivo.total_agrupado');
Route::get('total_agrupado/crear', 'archivo_plano\Ap_ArchivoController@total_agrupado_crear')->name('aparchivo.total_agrupado_crear');
Route::get('total_agrupado/editar/{id}', 'archivo_plano\Ap_ArchivoController@total_agrupado_editar')->name('aparchivo.total_agrupado_editar');
Route::post('total_agrupado/store', 'archivo_plano\Ap_ArchivoController@total_agrupado_store')->name('aparchivo.total_agrupado_store');
Route::post('total_agrupado/update', 'archivo_plano\Ap_ArchivoController@total_agrupado_update')->name('aparchivo.total_agrupado_update');

Route::post('plano_agrupado_contable/guardar', 'archivo_plano\Ap_ArchivoController@guardar_agrupado_contable')->name('archivo.guardar_agrupado_contable');
Route::get('plano_agrupado_contable/ingresar/{id}', 'archivo_plano\Ap_ArchivoController@plano_contable_editar')->name('aparchivo.plano_agrupado_contable_editar');
Route::get('plano_agrupado_contable/eliminar/registro/{id}', 'archivo_plano\Ap_ArchivoController@plano_contable_eliminar')->name('aparchivo.plano_agrupado_contable_eliminar');

Route::match(['get', 'post'],'total_agrupado_contable/', 'archivo_plano\Ap_ArchivoController@total_agrupado_contable')->name('aparchivo.total_agrupado_contable');
Route::get('total_agrupado_contable/crear', 'archivo_plano\Ap_ArchivoController@total_agrupado_contable_crear')->name('aparchivo.total_agrupado_contable_crear');
Route::get('total_agrupado_contable/editar/{id}', 'archivo_plano\Ap_ArchivoController@total_agrupado_contable_editar')->name('aparchivo.total_agrupado_contable_editar');
Route::post('total_agrupado_contable/store', 'archivo_plano\Ap_ArchivoController@total_agrupado_contable_store')->name('aparchivo.total_agrupado_contable_store');
Route::post('total_agrupado_contable/update', 'archivo_plano\Ap_ArchivoController@total_agrupado_contable_update')->name('aparchivo.total_agrupado_contable_update');

//MSP
Route::get('plano_contable_msp/ingresar/{aniomes}/{tipo}/{seg}/{cobertura}/{empresa}', 'archivo_plano\Ap_ArchivoController@plano_contable_ingresar_msp')->name('aparchivo.plano_contable_ingresar_msp');

Route::match(['get','post'],'plano_contable_msp/guardar', 'archivo_plano\Ap_ArchivoController@guardar_agrupado_msp')->name('archivo.guardar_agrupado_msp');
Route::match(['get' ,'post'],'plano_contable_msp/ingresar/{id}', 'archivo_plano\Ap_ArchivoController@plano_contable_editar_msp')->name('aparchivo.plano_contable_editar_msp');
Route::get('plano_contable_msp/eliminar/registro/{id}', 'archivo_plano\Ap_ArchivoController@plano_contable_eliminar_msp')->name('aparchivo.plano_contable_eliminar_msp');

Route::get('prefacturar_factura_publica/{id}', 'archivo_plano\Ap_ArchivoController@ingresar_prefactura')->name('aparchivo.ingresar_prefactura');
Route::match(['get', 'post'],'contable/prefactura_delete/{id}', 'archivo_plano\Ap_ArchivoController@prefactura_delete')->name('aparchivo.prefactura_delete');
Route::get ('contable/visualizar_prefactura', 'archivo_plano\Ap_ArchivoController@visualizar_prefactura')->name('aparchivo.visualizar_prefactura');
Route::match(['get','post'],'contable/prefactura_ap_orden_venta', 'archivo_plano\Ap_ArchivoController@detalle_prefactura')->name('aparchivo.detalle_prefactura');
Route::match(['get','post'],'contable/enviar_prefactura', 'archivo_plano\Ap_ArchivoController@enviar_prefactura')-> name('aparchivo.enviar_prefactura');



//Leer excel 
Route::get('actualizar_valores_publicos_2022','ImportarController@actualizar_valores_publicos_2022')->name('importar.actualizar_valores_publicos_2022');
Route::match(['get', 'post'], 'nuevo_archivo_plano_campesino_092022/genera_ap_excel_campesino', 'archivo_plano\Ap_ArchivoController@genera_ap_excel_campesino_092022')->name('planilla.genera_ap_excel_campesino_092022');
//NUEVOARCHIVOPLANO
Route::match(['get', 'post'], 'solic_oct/archivo_plano/genera_ap/excel_general', 'archivo_plano\NuevoArchivoPlanoController@genera_ap_excel_092022')->name('nuevoArchivoPlano.genera_ap_excel_092022');
Route::match(['get', 'post'], 'solic_oct/archivo_plano/genera_ap/excel_jubilado_ssc', 'archivo_plano\NuevoArchivoPlanoController@genera_ap_excel_jubilado_ssc')->name('nuevoArchivoPlano.genera_ap_excel_jubilado_ssc');
Route::match(['get', 'post'], 'solic_oct/archivo_plano/planillas_generadas/plan_consolidada_pdf','archivo_plano\NuevoArchivoPlanoController@genera_plan_consolidada_pdf')->name('nuevoArchivoPlano.consolidada_pdf');
Route::match(['get', 'post'], 'archivo_plano/planilla_individual_pdf_codigo/msp/{idcab}', 'archivo_plano\NuevoArchivoPlanoController@genera_plan_consolidada_codigo_pdf')->name('nuevoArchivoPlano.consolidada_codigo_pdf');
Route::match(['get', 'post'], 'archivo_plano/nuevo_reporte/consolidado/general', 'archivo_plano\NuevoArchivoPlanoController@nuevo_reporte_consolidado_general')->name('nuevoArchivoPlano.nuevo_reporte_consolidado_general');
Route::match(['get', 'post'], 'archivo_plano/iva_10porc_nuevo_reporte/consolidado/general', 'archivo_plano\NuevoArchivoPlanoController@iva_10porc_nuevo_reporte_consolidado_general')->name('nuevoArchivoPlano.iva_10porc_nuevo_reporte_consolidado_general');
Route::match(['get', 'post'], 'archivo_plano/nuevo_reporte/consolidado/campesino', 'archivo_plano\NuevoArchivoPlanoController@nuevo_reporte_consolidado_campesino')->name('nuevoArchivoPlano.nuevo_reporte_consolidado_campesino');
Route::match(['get', 'post'], 'archivo_plano/genera_planillas/consultas', 'archivo_plano\Ap_ArchivoController@genera_planillas_consultas')->name('planilla.genera_planillas_consultas');
Route::match(['get', 'post'], 'archivo_plano/planilla/create/{hcid}/{id_seguro}', 'archivo_plano\Ap_PlanillaController@planilla_hcid_create')->name('archivo_plano.planilla_hcid_create');
Route::get('ap_nuevo_edit/{id}', 'archivo_plano\Ap_PlanillaController@ap_nuevo_edit')->name('applanilla.ap_nuevo_edit');
Route::match(['get', 'post'], 'medicina_publica/index', 'archivo_plano\MedicinaPublicaController@index')->name('procedimientos.index');
Route::get('medicina_publica/crear', 'archivo_plano\MedicinaPublicaController@crear')->name('procedimientos.crear');
Route::post('medicina_publica/store', 'archivo_plano\MedicinaPublicaController@store')->name('procedimientos.store');
Route::get('medicina_publica/editar/{id}', 'archivo_plano\MedicinaPublicaController@editar')->name('procedimientos.editar');
Route::post('medicina_publica/update', 'archivo_plano\MedicinaPublicaController@update')->name('procedimientos.update');
Route::get('medicina_publica/delete/{id}', 'archivo_plano\MedicinaPublicaController@delete')->name('procedimientos.delete');
Route::get('ap_masivo/medicinas', 'ImportarController@masivo_medicinas')->name('importar.masivo_medicinas');
Route::match(['get', 'post'], 'ap_estad/archivo_plano/resumen_planillas', 'archivo_plano\Ap_ArchivoController@resumen_planillas')->name('aparchivo.resumen_planillas');
Route::match(['get', 'post'], 'ap_estad/archivo_plano/resumen_agrupado/xls', 'archivo_plano\Ap_ArchivoController@resumen_agrupado')->name('aparchivo.resumen_agrupado');
Route::match(['get', 'post'], 'ap_masivo_msp/msp', 'archivo_plano\Ap_ArchivoController@ap_masivo_msp')->name('aparchivo.ap_masivo_msp');

Route::get('vt_subir_insumos_2023', 'archivo_plano\NuevoArchivoPlanoController@subir_insumos_2023')->name('importar.subir_insumos_2023');

Route::match(['get', 'post'], 'medicina_publica/buscar', 'archivo_plano\MedicinaPublicaController@buscar')->name('procedimientos.buscar');
Route::match(['get', 'post'], 'archivo_plano/excel/formulario013/{id_hc_procedimiento}', 'archivo_plano\NuevoArchivoPlanoController@formulario_013')->name('nuevoArchivoPlano.formulario_013');
Route::get('vt_actualiza_plano_2023', 'archivo_plano\NuevoArchivoPlanoController@actualiza_plano_2023')->name('importar.actualiza_plano_2023');

//Recalcular Valor AP CABECERA
Route::match(['get', 'post'], 'archivo_plano/recalcular_valor_cabecera', 'contable\ReporteCostosVentasController@recalcular_valor_ap_cab')->name('reportecostosventas.recalcular_valor_ap_cab');
 
//NUEVA VERSION DE ARCHIVO PLANO  2023
Route::match(['get', 'post'], 'solic_oct/archivo_plano/genera_ap/excel_general/v2023', 'archivo_plano\NuevoArchivoPlanoController@genera_ap_excel_v2023')->name('nuevoArchivoPlano.genera_ap_excel_v2023');

Route::match(['get', 'post'], 'archivo_plano/nuevo_reporte/consolidado/general/nuevo_v2023', 'archivo_plano\NuevoArchivoPlanoController@reporte_consolidado_general_v2023')->name('nuevoArchivoPlano.reporte_consolidado_general_v2023');
 
Route::get('medicina_publica/nuevo_codigo/actualizar', 'archivo_plano\NuevoArchivoPlanoController@actualizar_nuevo_codigo')->name('nuevoArchivoPlano.actualizar_nuevo_codigo');

Route::get('vt_nuevo_plano/crear/planilla/{hc_id_procedimiento}/{seguro}', 'archivo_plano\NuevoArchivoPlanoController@crear_planilla')->name('nuevoArchivoPlano.crear_planilla');

Route::get('medicina_publica/covid/eliminar/insumos', 'archivo_plano\NuevoArchivoPlanoController@quitar_insumos_covid_2023')->name('nuevoArchivoPlano.quitar_insumos_covid_2023');

Route::get('derivacion_publica/importar', 'archivo_plano\NuevoArchivoPlanoController@cargar_derivacion')->name('nuevoArchivoPlano.cargar_derivacion');

Route::get('importar_valores_plano/{empresa}', 'archivo_plano\NuevoArchivoPlanoController@importar_valores_plano')->name('nuevoarchivoplano.importar_valores_plano');
Route::get('gye_codigos/derivacion/plano', 'archivo_plano\NuevoArchivoPlanoController@importar_codigos')->name('nuevoArchivoPlano.importar_codigos');

//masivo actualiuza codigo de medicina por ap 
Route::get('medicina_publica/detalle/por_medicina/{codigo}', 'archivo_plano\NuevoArchivoPlanoController@actualizar_detalle_x_medicina')->name('nuevoArchivoPlano.actualizar_detalle_x_medicina');

//Route::get('vt_nuevo_plano/crear/planilla/{hc_id_procedimiento}/{seguro}', 'archivo_plano\NuevoArchivoPlanoController@crear_planilla')->name('nuevoArchivoPlano.crear_planilla');
Route::get('archivo_plano/factura/mesplano/index', 'archivo_plano\NuevoArchivoPlanoController@index_factura_mesplano')->name('nuevoArchivoPlano.index_archivo_factura');
Route::get('archivo_plano/factura/create', 'archivo_plano\NuevoArchivoPlanoController@create')->name('nuevoArchivoPlano.create');
Route::post('archivo_plano/factura/store', 'archivo_plano\NuevoArchivoPlanoController@store_archivo')->name('nuevoArchivoPlano.store_archivo');
Route::get('archivo_plano/factura/eliminar_archivo/{id}', 'archivo_plano\NuevoArchivoPlanoController@eliminar_archivo')->name('nuevoArchivoPlano.eliminar_archivo');
Route::match(['get', 'post'],'archivo_plano/factura/mostrar/pdf', 'archivo_plano\NuevoArchivoPlanoController@mostrar_pdf')->name('nuevoArchivoPlano.mostrar_pdf');
Route::match(['get', 'post'],'archivo_plano/factura/buscar', 'archivo_plano\NuevoArchivoPlanoController@search')->name('nuevoArchivoPlano.search_archivo_factura');
Route::get('archivo_plano/formulario013/evolucion/{id}', 'hc_admision\ApBitacoraController@cargar')->name('nuevoArcivoPlano.formulario013.evolucion.cargar');
Route::get('archivo_plano/formulario013/biopsia/store/{id}', 'hc_admision\ApBitacoraController@storeAuditoriaBiopsias')->name('nuevoArcivoPlano.formulario013.store_biopsia');
Route::get('archivo_plano/formulario013/biopsia/delete/{id}', 'hc_admision\ApBitacoraController@deleteAuditoriaBiopsias')->name('nuevoArcivoPlano.formulario013.delete_biopsia');
Route::match(['get', 'post'], 'archivo_plano/pdf/formulario013/{id_hc_procedimiento}', 'archivo_plano\NuevoArchivoPlanoController@formulario_013_pdf')->name('nuevoArchivoPlano.formulario_013_pdf');

//Planillar desde insumos aprobados 
Route::post('vt_nuevo_plano/guardarItem', 'archivo_plano\NuevoArchivoPlanoController@guardarItem')->name('nuevoArchivoPlano.guardarItem');
Route::get('archivo_plano/nuevo/planilla/insumos/modal/{cabecera}', 'archivo_plano\ApNuevoPlanillaController@cargarInsumosPlanillaModal')->name('nueva_planilla.cargarInsumosPlanillaModal');
Route::match(['get', 'post'], 'archivo_plano/nuevo/planilla/insumos/{cabecera}', 'archivo_plano\ApNuevoPlanillaController@cargarInsumosPlanillaBuscar')->name('nueva_planilla.cargarInsumosPlanillaBuscar');
Route::match(['get', 'post'], 'archivo_plano/nuevo/planilla/medicinas/{cabecera}', 'archivo_plano\ApNuevoPlanillaController@cargarMedicinasPlanilla')->name('nueva_planilla.medicinas');
Route::match(['get', 'post'], 'archivo_plano/nuevo/planilla/medicinas/div/{cabecera}', 'archivo_plano\ApNuevoPlanillaController@cargarMedicinasPlanilla_div')->name('nueva_planilla.cargarMedicinasPlanilla_div');
Route::post('archivo_plano/nuevo/planilla/agregar/insumo', 'archivo_plano\ApNuevoPlanillaController@agregarInsumo')->name('nueva_planilla.agregar_insumo');
Route::post('archivo_plano/nuevo/planilla/agregar/medicina', 'archivo_plano\ApNuevoPlanillaController@agregarMedicina')->name('nueva_planilla.agregar_medicina');
Route::get('archivo_plano/nuevo/planilla/add/all/insumos/{cabecera}', 'archivo_plano\ApNuevoPlanillaController@add_all_insumos')->name('nueva_planilla.all_insumos');
Route::get('archivo_plano/nuevo/planilla/add/all/medicinas/{cabecera}', 'archivo_plano\ApNuevoPlanillaController@add_all_medicinas')->name('nueva_planilla.all_medicinas');
Route::post('archivo_plano/boton_masivo/agrega_planilla/insumos/{cabecera}', 'archivo_plano\ApNuevoPlanillaController@agregaMasivoInsumosLaboratorio')->name('nueva_planilla.masivo_insumos_laboratorio');

Route::get('archivo_plano/act_valores/ap_nivel_act_2023', 'ImportarController@actualiza_valores_ap_nivel_2023')->name('importar.actualiza_valores_ap_nivel');
Route::get('archivo_plano/act_valores/ap_nivel_exAm_act_2023', 'ImportarController@actualiza_valores_exam_nivel_2023')->name('importar.actualiza_valores_exam_nivel');
Route::get('4decimal_masivo/plano/{id}', 'archivo_plano\NuevoArchivoPlanoController@actualiza_4decimal')->name('nuevoArchivo.actualiza_4decimal');
Route::get('4decimal_masivo/plano/empresa/{id}', 'archivo_plano\NuevoArchivoPlanoController@actualiza_4decimal_empresa')->name('nuevoArchivo.actualiza_4decimal_empresa');

Route::get('correccion_medicinas_5_dic/maestra', 'archivo_plano\NuevoArchivoPlanoController@actualiza_maestra_ap')->name('nuevoArchivo.actualiza_maestra_ap');
Route::get('correccion_medicinas_5_dic/plano', 'archivo_plano\NuevoArchivoPlanoController@actualiza_ames_plano')->name('nuevoArchivo.actualiza_ames_plano');

Route::get('correccion_medicinas_5_dic/plano/cabecera/{id}', 'archivo_plano\NuevoArchivoPlanoController@corrige_medicinas_12_05')->name('nuevoArchivo.corrige_medicinas_12_05');
//BITACORA
Route::get('biatcora/pdf/{id}', 'archivo_plano\NuevoArchivoPlanoController@bitacora_pdf')->name('nuevoarchivoplano.bitacora');

//NUEVA VERSION DE ARCHIVO PLANO  2023 DIC 
Route::match(['get', 'post'], 'solic_dic/archivo_plano/genera_ap/excel_general/v2023/total', 'archivo_plano\NuevoArchivoPlanoController@genera_ap_excel_v2023_Total')->name('nuevoArchivoPlano.genera_ap_excel_v2023_Total');

//LOG DE AUDITORIA
Route::match(['get', 'post'], 'archivo_plano/log_auditoria_publica/{cabecera}', 'archivo_plano\NuevoArchivoPlanoController@index_log_auditoria')->name('nuevoArchivoPlano.index_log_auditoria');

Route::get('mas_esp/agrega_insumos_cabecera/{id}/{tipo}', 'archivo_plano\NuevoArchivoPlanoController@agrega_insumos_cabecera')->name('nuevoarchivoplano.agrega_insumos_cabecera');
Route::get('mas_esp/lee_cabeceras_plano', 'archivo_plano\NuevoArchivoPlanoController@lee_cabeceras_plano')->name('nuevoarchivoplano.lee_cabeceras_plano');

Route::match(['get', 'post'],'cerrar_liquidacion/{id}', 'archivo_plano\NuevoArchivoPlanoController@cerrar_liquidacion')->name('nuevoarchivoplano.cerrar_liquidacion');

Route::match(['get', 'post'],'archivo_plano/reporte/valores_detalle_por_empresa', 'archivo_plano\NuevoArchivoPlanoController@reporte_valores_por_empresa')->name('nuevoarchivoplano.reporte_valores_por_empresa');

Route::match(['get', 'post'],'cargar_ap_procedimiento_crm', 'archivo_plano\ApNuevoPlanillaController@cargar_ap_procedimiento')->name('apnuevoplantilla.cargar_ap_procedimiento');


Route::get('agrega_insumos_fungibles/{id}/{tipo}', 'archivo_plano\NuevoArchivoPlanoController@agrega_insumos_fungibles')->name('nuevoArchivo.agrega_insumos_fungibles');

Route::get('corrige_iva_unitario/{id_detalle}', 'archivo_plano\NuevoArchivoPlanoController@corrige_iva_unitario')->name('nuevoArchivo.corrige_iva_unitario');

Route::get('corrige_insumos_plano/{mes_plano}', 'archivo_plano\NuevoArchivoPlanoController@corrige_insumos_plano')->name('nuevoArchivo.corrige_insumos_plano');

Route::get('plano_estadistico/carga/{mes_plano}/{id_seguro}', 'archivo_plano\NuevoArchivoPlanoController@carga_estadisticos')->name('nuevoArchivo.carga_estadisticos');

Route::get('nuevo_plantillas_iess/carga_masiva', 'archivo_plano\ApNuevoPlanillaController@cargar_plantilla')->name('nueva_planilla.cargar_plantilla');
Route::get('vt_nuevo_archivo/medicina/detalle/{id}', 'archivo_plano\ApNuevoPlanillaController@detalle_receta')->name('nueva_planilla.detalle_receta');

Route::get('masivo_correccion_nivel_msp', 'archivo_plano\Ap_ArchivoMspController@masivo_correccion_msp')->name('ap_archivo_msp.masivo_correccion_msp');


//Actualiza solo el codigo de la medicina 
Route::get('medicina_publica/nuevo_codigo/actualizar/cod_medicina', 'archivo_plano\NuevoArchivoPlanoController@actualizar_nuevo_codigo_med')->name('nuevoArchivoPlano.actualizar_nuevo_codigo_med');

Route::get('nuevo_planilla_individual/imprimir_plantillas/pdf/{numero_paciente}/{id_empresa}/{id_seguro}/{mes_plano}', 'archivo_plano\Ap_PlanillaDetalleController@nuevo_planilla_detalle_pdf')->name('ap_planilladetalle.nuevo_planilla_detalle_pdf');
Route::get('iva10porc_nuevo_planilla_individual/imprimir_pdf/{numero_paciente}/{id_empresa}/{id_seguro}/{mes_plano}', 'archivo_plano\Ap_PlanillaDetalleController@iva10porcen_nuevo_planilla_detalle_pdf')->name('ap_planilladetalle.iva10porcen_nuevo_planilla_detalle_pdf');
Route::post('auditoria/msp/subida/plantilla', 'archivo_plano\Ap_ArchivoController@subida_plantilla_msp')->name('subida_plantilla_msp');
