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

Route::get('contable/empresas', 'contable\FacturaController@empresas')->name('empresa.index');
Route::get('contable/empresas/editar/{id}', 'contable\FacturaController@empresa_editar')->name('empresa.editar');
Route::get('contable/empresas/facturacion/{id}', 'contable\FacturaController@facturas')->name('factura.index');
Route::get('contable/empresas/facturacion/crear/{id}/{id_cliente}/{id_empresa}/{id_suc}/{id_caj}/{id_factura}', 'contable\FacturaController@factura_crear')->name('factura.crear');
Route::post('contable/empresas/facturacion/crear/store', 'contable\FacturaController@factura_store')->name('factura.store_contable');

//Route::get('contable/paciente/crear/{id}', 'contable\FacturaController@crear');
//Route::get('contable/paciente/crear/', 'contable\FacturaController@crear')->name('paciente.crear');
Route::get('contable/paciente/crear/{id}/{id_cliente}/{id_empresa}/{id_suc}/{id_caj}/{id_factura}', 'contable\FacturaController@crear')->name('paciente.crear');

Route::get('contable/paciente_cliente/crear/{id}/{id_cliente}/{id_empresa}/{id_suc}/{id_caj}/', 'contable\FacturaController@crear')->name('paciente.crear');

//Buscador Factura
Route::post('contable/empresas/facturacion/busqueda', 'contable\FacturaController@factura_buscar')->name('factura.busqueda');

Route::get('contable/facturacion/agenda/{id}', 'contable\Factura_AgendaController@facturar')->name('factura.agenda');

Route::get('contable/facturacion/verificar/seguro/{id}', 'contable\Factura_AgendaController@verificar_seguro');
Route::get('contable/facturacion/verificar/pago/{id_seguro}/{id_doctor}', 'contable\Factura_AgendaController@verificar_pago');

//obtener el valor del facturero, con el seguro
Route::post('contable/facturacion/valores', 'contable\Factura_AgendaController@valores_seguro')->name('facturacion.valores_seguro');

Route::post('contable/facturacion/agenda/storenew', 'contable\Factura_AgendaController@store_new')->name('facturacion.store_new');
Route::post('contable/facturacion/agenda/updatenew', 'contable\Factura_AgendaController@update_new')->name('facturacion.update_new');
//Nueva Fucionalidad

//Obtener caja de la sucursal Seleccionada
Route::post('contable/facturacion/caja', 'contable\Factura_AgendaController@obtener_caja_sucursal')->name('caja.sucursal');

//Obtener Secuencia Numero de Factura
Route::get('contable/facturacion/consulta/num_factura', 'contable\Factura_AgendaController@obtener_numero_factura')->name('num_fact.consulta');

//Guardado de Factura
Route::post('contable/facturacion/guardado/consulta/', 'contable\Factura_AgendaController@guardar_factura')->name('facturacion.consulta_guardar');

Route::post('contable/orden/venta/guardado/consulta/', 'contable\Factura_AgendaController@guardar_orden')->name('facturacion.guardar_orden');
Route::post('contable/orden/venta/buscar/cliente/', 'contable\Factura_AgendaController@buscar_cliente')->name('facturacion.buscar_cliente');
Route::post('contable/orden/venta/cliente/', 'contable\Factura_AgendaController@cliente')->name('facturacion.cliente');

Route::get('comprobante/orden/venta/{id_orden}', 'contable\Factura_AgendaController@imprimir_ride')->name('facturacion.imprimir_ride');

//Nueva ruta
Route::get('contable/modal/recibo/cobro/{id_orden}', 'contable\Factura_AgendaController@obtener_modal')->name('facturacion.modal_recibo');

Route::post('contable/recibo/cobro/anular', 'contable\Factura_AgendaController@anular_recibo_cobro')->name('recibo_cobro.anular');

//Reporte Cierre Caja
Route::match(['get', 'post'], 'reporte/cierre_caja', 'contable\CierreCajaController@index_cierre')->name('reporte.index_cierre');

Route::post('reporte/exportar/cierre', 'contable\CierreCajaController@reporte')->name('cierrecaja.reporte');

Route::post('reporte/imprimir_excel', 'contable\CierreCajaController@imprimir_excel')->name('cierrecaja.imprimir_excel');

//editar comprobante pago
Route::get('contable/facturacion/agenda_a/editar/{id}/{valor}', 'contable\Factura_AgendaController@facturar_editar')->name('factura.editar_cp');
Route::get('contable/facturacion/agenda/editar/listado/{id}', 'contable\Factura_AgendaController@facturar_listado')->name('factura.listado');
Route::post('contable/facturacion/agenda/actualizar/', 'contable\Factura_AgendaController@facturar_actualizar')->name('factura.actualizar');

//changes by ac date 24 November 2020 factura convenios
Route::match(['get', 'post'], 'contable/factura/convenios/', 'contable\FacturaConveniosController@index')->name('factura_convenios.index');
Route::get('contable/factura/convenio/obtenerap', 'contable\FacturaConveniosController@obtener_ap')->name('factura_convenios.obtener_ap');
Route::post('contable/factura/convenios/store', 'contable\FacturaConveniosController@store')->name('factura_convenios.store');
Route::post('contable/factura/convenios/update/{id}', 'contable\FacturaConveniosController@update')->name('factura_convenios.update');
Route::get('contable/factura/convenios/crear', 'contable\FacturaConveniosController@create')->name('factura_convenios.create'); 
Route::get('contable/factura/convenios/edit/{id}', 'contable\FacturaConveniosController@edit')->name('factura_convenios.edit');

//Obtener Niveles Dependiendo del Seguro Recibo de Cobro Procedimiento
Route::post('contable/orden/venta/niveles/seguro', 'contable\Factura_AgendaController@obtener_nivel_seguro')->name('lista_nivel.seguro');

//Obtener Precio Producto Tarifario Buzqueda por Nombre
Route::post('contable/obtener/precio_producto/tarifario_nombre', 'contable\Factura_AgendaController@obtener_precio_prod_tarifario_nombre')->name('contable_precio_prod.tarifario_nombre');

//Verifica si el producto es un Paquete
Route::post('contable/verifica/producto/paquete', 'contable\Factura_AgendaController@verifica_producto_paquete')->name('contable_existe_prod.paquete');

//Obtener Precio Producto Tarifario Buzqueda por Codigo
Route::post('contable/obtener/precio_producto/tarifario_codigo', 'contable\Factura_AgendaController@obtener_precio_prod_tarifario_codigo')->name('contable_precio_prod.tarifario_codigo');

//Obtener Modal Orden Venta Detalle Paquete
Route::get('contable/facturacion/orden/detalle/paquete/{id}', 'contable\Factura_AgendaController@obtener_orden_detalle_paquete')->name('detalle_paquete.facturacion');

//Completar Proceso
Route::get('contable/facturacion/completa_proceso/cierre_caja/{id_prod}/{prec_prod}/{id_orden}', 'contable\CierreCajaController@completa_proceso_cierrecaja')->name('completa_proceso.cierrecaja');

//Completar Productos por Nombre
Route::post('contable/buscar/producto_paquete', 'contable\Factura_AgendaController@buscar_producto_paquete')->name('contable_producto_paquete');

//Completar Productos por Codigo
Route::post('contable/buscar/producto_codigo_paquete', 'contable\Factura_AgendaController@buscar_codigo_paquete')->name('contable_codigo_paquete');

//Obtener Precio del Producto Por Codigo
Route::post('contable/obtener/precio_producto/codigo', 'contable\Factura_AgendaController@obtener_precio_por_codigo')->name('contable_precio_prod.codigo');

//Obtener Precio del Producto Por Codigo Nuevo
Route::post('contable/obtener/precio_producto_nuevo/codigo', 'contable\Factura_AgendaController@obtener_precio')->name('contable_precio.prod');

//generar el comprobante para credito, debito, retenciones
Route::get('contable/documento/ride_pdf/{comprobante}/{id_empresa}/{tipo}/{documento}', 'ApiFacturacionController@comprobante_publico_general')->name("facturacion.comprobante_publico_general");
Route::match(['get', 'post'], 'contable/cierre_caja/index', 'contable\CierreCajaController@cierre_caja')->name("c_caja.index");
Route::get('contable/cierre_caja/recibo/{id}', 'contable\CierreCajaController@modalrecibo')->name("c_caja.modalrecibo");
Route::post('contable/cierre_caja/store', 'contable\CierreCajaController@store_cierre')->name('c_caja.store');
Route::post('contable/cierre_caja/storeLabs', 'contable\CierreCajaController@storeLabs')->name('c_caja.storeLabs');
Route::post('contable/cierre_caja/store_salida', 'contable\CierreCajaController@store_salida')->name('c_caja.store_salida');
Route::get('contable/facturacion/agendan/editar/{id}', 'contable\Factura_AgendaController@newEdit')->name('facturaagenda.editn');
Route::post('contable/facturacion/agendan/update/{id}', 'contable\Factura_AgendaController@newUpdate')->name('facturaagenda.nupdate');
Route::get('contable/facturacion/cierre_caja/obtener', 'contable\CierreCajaController@getData')->name('cierre_caja.getData');
Route::get('contable/facturacion/modal/cierre_caja/{id}', 'contable\CierreCajaController@modalforma')->name('cierre_caja.modalforma');
Route::get('contable/facturacion/modal/cierre/{id}/{fecha}', 'contable\CierreCajaController@cierre_caja_modal')->name('cierre_caja.modalcierre');
Route::get('contable/cierre_caja/orden/{id}/{s}', 'contable\CierreCajaController@redirecciona')->name('cierre_caja.redirecciona');
Route::post('contable/orden/facturacion', 'contable\CierreCajaController@storenew')->name('c.enviocopago');
Route::get('contable/facturacion/getUser', 'contable\CierreCajaController@getUserByCierre')->name('c.getUserByCierre');

Route::get('cierre_caja_usuarios', 'contable\CierreCajaController@buscar_usuarios')->name('cierrecaja.buscar_usuarios');
Route::match(['get', 'post'], 'contable/cierre_caja/observacion/{id}', 'contable\CierreCajaController@observacion')->name('cierre_caja.observacion');
//POR TIPO DE SE3GURO
Route::post('nrc/tipifica/seguros', 'contable\Factura_AgendaController@tipifica_seguros')->name('facturaagenda.tipifica_seguros');
Route::get('nrc/recibo_cobro/devolver/{id_orden}', 'contable\NuevoReciboCobroController@devolver')->name('nuevorecibocobro.devolver');
//Arqueo Caja
Route::match(['get', 'post'],'contable/arqueo_caja/crear','contable\Ct_Arqueo_CajaController@crear_arqueo')->name('ct_arqueo_caja.crear_arqueo');
Route::match(['get', 'post'],'contable/arqueo_caja/editar/{id}','contable\Ct_Arqueo_CajaController@editar_arqueo')->name('ct_arqueo_caja.editar_arqueo');
Route::match(['get', 'post'],'contable/arqueo_caja/crear_arqueo_usuario','contable\Ct_Arqueo_CajaController@crear_arqueo_user')->name('ct_arqueo_caja.crear_arqueo_user');

Route::post('contable/arqueo_caja/update_arqueo', 'contable\Ct_Arqueo_CajaController@update_arqueo')->name('ct_arqueo_caja.update_arqueo');
Route::post('contable/arqueo_caja/update/cabecera', 'contable\Ct_Arqueo_CajaController@update_cabecera')->name('ct_arqueo_caja.update_cabecera');
Route::match(['get','post'],'contable/arqueo_caja/index_arqueo', 'contable\Ct_Arqueo_CajaController@index_arqueo')->name('ct_arqueo_caja.index_arqueo');
Route::match(['get','post'],'contable/arqueo_caja/recalcular/valor_efectivo_arqueo/{id}', 'contable\Ct_Arqueo_CajaController@recalcular_valor_efectivo_arqueo')->name('ct_arqueo_caja.recalcular_valor_efectivo_arqueo');
//Route::match(['get', 'post'],'contable/arqueo_caja/pdf_arqueo', 'contable\Ct_Arqueo_CajaController@pdf_arqueo')->name('ct_arqueo_caja.pdf_arqueo');

//Route::match(['get','post'],'contable/arqueo_caja/index_arqueo', 'contable\Ct_Arqueo_CajaController@index_arqueo')->name('ct_arqueo_caja.index_arqueo');
Route::post('contable/arqueo_caja/edit/{id}', 'contable\Ct_Arqueo_CajaController@edit')->name('ct_arqueo_caja.edit');


Route::get('contable/comisiones/reporte', 'contable\ComisionController@index_comisiones')->name('comision.index');
Route::match(['get', 'post'], 'contable/comisiones/buscar', 'contable\ComisionController@buscar_comisiones')->name('buscar.comisiones');
Route::get('contable/comisiones/detalle', 'contable\ComisionController@detalle_examenes')->name('detalle_examenes.comisiones');
//subir excel
Route::post('contable/comisiones/subir/excel', 'contable\ComisionController@subir_excel')->name('subir_excel.index');
