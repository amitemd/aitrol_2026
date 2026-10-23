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
// Route::match(['get', 'post'], 'contable/Banco/notadebito', 'contable\NotaDebitoController@index')->name('notadebito.index');

// Route::get('contable/Banco/notadebito/crear/', 'contable\NotaDebitoController@crear')->name('notadebito.crear');
Route::get('reporte/liquidacion/comisiones/{id}', 'contable\VentasController@pdf_liquidacion_comision')->name('ventas.pdf_liquidacion_comision');
Route::get('reporte/liquidacion/comisiones/eliminar/{id}', 'contable\VentasController@eliminar_liquidacion_comision')->name('ventas.eliminar_liquidacion_comision');
Route::match(['get', 'post'], 'reporte_liquidacion_comisiones/generar_comision', 'contable\VentasController@guardar_comision')->name('ventas.guardar_comision');
Route::get('contable/cliente/buscar/productos', 'contable\VentasController@buscar_producto_codigo')->name('venta.buscar_producto_codigo');
Route::get('reporte/liquidacion/comisiones/buscar_precio/{id}', 'contable\VentasController@buscar_precio')->name('ventas.buscar_precio');

Route::get('contable/ventas', 'contable\VentasController@index')->name('venta_index');
Route::match(['get', 'post'], 'contable/ventas2', 'contable\VentasController@index2')->name('venta_index2');

Route::match(['get', 'post'], 'contable/ventas2/buscar', 'contable\VentasController@index2_buscar')->name('venta_index2_buscar');

Route::get('contable/ventas/crear', 'contable\VentasController@crear')->name('ventas_crear.formulario');
Route::get('contable/ventas/crear_factura', 'contable\VentasController@crear_factura')->name('ventas_crear');
Route::post('contable/ventas/store', 'contable\VentasController@store')->name('ventas_store');
Route::match(['get', 'post'], 'contable/ventas/buscar', 'contable\VentasController@search')->name('ventas_search');
Route::post('contable/ventas/precio_producto', 'contable\VentasController@precios')->name('precios');

Route::get('contable/ventas/crear_orden', 'contable\VentasController@crear_orden')->name('orden_crear');
Route::post('contable/ventas/actualiza_orden', 'contable\VentasController@updateorden')->name('orden_update');
Route::post('contable/ventas/updatev/{id}', 'contable\VentasController@update')->name('ventas_update');
Route::post('contable/ventas/storeConsolidado', 'contable\VentasController@store_varios')->name('ventas_storeVarios');

Route::match(['get', 'post'], 'contable/ventas/buscarCliente', 'contable\VentasController@buscarCliente')->name('ventas.buscarcliente');
Route::match(['get', 'post'], 'contable/ventas/buscarClienteXId', 'contable\VentasController@buscarClientexId')->name('ventas.buscarclientexid');

Route::match(['get', 'post'], 'contable/ventas/buscarPaciente', 'contable\VentasController@buscarPaciente')->name('ventas.buscarpaciente');

Route::match(['get', 'post'], 'contable/ventas/buscarPaciente_Nombre', 'contable\VentasController@buscarPaciente_nombre')->name('ventas.buscarpaciente_nombre');

Route::match(['get', 'post'], 'contable/ventas/reporte_caja', 'contable\VentasController@index_cierre')->name('ventas.index_cierre');
Route::get('contable/venta/orden/{id_orden}', 'contable\VentasController@factura_caja')->name('venta.factura_orden');

Route::match(['get', 'post'], 'contable/ventas/facturas_omni', 'contable\VentasController@facturas_omni')->name('ventas.omni');
Route::post('contable/ventas/viewOmni', 'contable\VentasController@view_omni')->name('ventas_viewOmni');
Route::post('contable/ventas/store_omni', 'contable\VentasController@store_omni')->name('ventas_storeOmni');

Route::get('contable/venta/excel/{id}', 'contable\VentasController@excel')->name('venta.excel');
Route::get('contable/factura/convenios/sell', 'contable\FacturaConveniosController@tabla')->name('f_convenios.tabla');
Route::get('contable/venta/previewExcell/{fecha}/{fechafin}/{seguro}/{tipo}/{id_empresa}', 'contable\VentasController@excelPreview')->name('venta.previewx');
Route::post('contable/venta/previewExcel/', 'contable\VentasController@previewData')->name('venta.preview');

// orden de venta
Route::match(['get', 'post'], 'contable/ventas/ordenes', 'contable\VentasController@ordenes')->name('orden_venta');
Route::match(['get', 'post'], 'contable/ventas/ordenes_crear_nuevo', 'contable\VentasController@ordenes_crear_nuevo')->name('orden_venta_crear_nuevo');
Route::match(['get', 'post'], 'contable/ventas/ordenes/search', 'contable\VentasController@searchOrdenes')->name('ventas.searchOrdenes');
Route::get('contable/ventas/ordenes/editar/{id}', 'contable\VentasController@crear_facturaOrden')->name('orden_editar');
Route::match(['get', 'post'], 'contable/update/estado/pago', 'contable\CajaController@updateEstadoPago')->name('caja.update_estado_pago');
Route::get('contable/ventas/ordenes/crear', 'contable\VentasController@crear_ordenes')->name('ventas.crear_ordenes');
Route::post('contable/ventas/ordenes/store_ordenes', 'contable\VentasController@store_ordenes')->name('ventas.store_ordenes');
Route::get('contable/ventas/ordenes/eliminar/{id}', 'contable\VentasController@eliminar')->name('venorden.eliminar');

// facturas de insumos
Route::match(['get', 'post'], 'contable/ventas/insumos', 'contable\VentasController@insumos')->name('insumos');
Route::match(['get', 'post'], 'contable/ventas/insumos/search', 'contable\VentasController@searchInsumos')->name('insumos_search');

Route::match(['get', 'post'], 'contable/ventas/modal/examenes/detalle/{id}', 'contable\VentasController@modalDetalle')->name('ventas.modalDetalleInsumos');

// Productos
// Route ::get('contable/productos', 'contable\VentasController@index')->name('venta_index');

Route::match(['get', 'post'], 'contable/fact/verificar/stock', 'contable\Fact_ContableController@verificarStock')->name('fact_contable.verificarStock');

Route::match(['get', 'post'], 'contable/ventas/verificar/stock/kardex/nuevo', 'contable\NuevoKardexContableController@verificarKardex')->name('nuevokardex.verificarKardex');

// stock
Route::post('contable/ventas/consulta/stock', 'contable\VentasController@validarStock')->name('ventas.stock');
Route::post('contable/ventas/consulta/inventariable', 'contable\VentasController@esInventariable')->name('ventas.inventariable');
Route::match(['get', 'post'], 'contable/venta/previewInforme/index', 'contable\VentasController@informe_ventas')->name('venta.informe_ventas');
Route::match(['get', 'post'], 'contable/venta/previewInforme_mensual/index', 'contable\VentasController@informe_ventas_mensual')->name('venta.informe_ventas_mensual');
Route::match(['get', 'post'], 'contable/venta/previewInforme_mensual/reporte_excel', 'contable\VentasController@exportar_informe_ventas_mensual')->name('venta.exportar_informe_ventas_mensual');
Route::match(['get', 'post'], 'contable/venta/inf_orden_pendientes/index', 'contable\VentasController@informe_ordenes_pendientes')->name('venta.informe_ordenes_pendientes');
Route::match(['get', 'post'], 'contable/venta/inf_liquidaciones_comisiones', 'contable\VentasController@informe_liquidaciones_comisiones')->name('venta.informe_liquidaciones_comisiones');
Route::post('contable/ventas/previewExcelx', 'contable\VentasController@excel_ventas')->name('venta.excel_ventas');
Route::get('contable/ventas/subir/excel/importar', 'contable\VentasController@masivo_ventas');

Route::match(['get', 'post'], 'contable/ventas/pdf_nuevo', 'contable\VentasController@pdf_nuevo')->name('ventas.pdf_nuevo');
Route::match(['get', 'post'], 'contable/ventas/pdf_nuevo2', 'contable\VentasController@pdf_nuevo2')->name('ventas.pdf_nuevo2');
Route::match(['get', 'post'], 'contable/ventas/modalPreviws/s', 'contable\VentasController@modal_preview')->name('ventas.modal_preview');
Route::match(['get', 'post'], 'contable/ventas/modalPreviews/conglemerada', 'contable\VentasController@modal_preview_c')->name('ventasConglomerada.modal_preview');
Route::get('contable/ventas/previewPdf/em/{id}', 'contable\VentasController@previewPdf')->name('ventas.previewPdf');
Route::get('contable/ventas/previewPdfV/ventas/{id}', 'contable\VentasController@pdf_omni')->name('ventas.pdf_omni');

Route::get('contable/ventas/previewConglomerada/pdfventas/{id}', 'contable\VentasController@pdf_conglomerada')->name('ventas.pdf_conglomerada');
Route::get('contable/ventas/ride_pdf/{comprobante}/{id_empresa}/{tipo}', 'ApiFacturacionController@comprobante_publico')->name('ventas.comprobante_publico');

Route::get('contable/ventas/getPrice/withClient', 'contable\VentasController@getPrices')->name('ventas.getPrices');

Route::match(['get', 'post'], 'contable/ventas/getUses/pdf', 'contable\VentasController@getReportUses')->name('ventas.getReportUses');
Route::match(['get', 'post'], 'contable/ventas/getReportUsesExcel/excel', 'contable\VentasController@getReportUsesExcel')->name('ventas.getReportUsesExcel');
// post get put match
// Cruce de cuentas con factura
Route::get('contable/cruce_cuentas_clientes/valores', 'contable\CruceClientesController@cruce_cuentas')->name('cr.cruce_cuentas');
Route::get('contable/cruce_cuentas_clientes/traervalores', 'contable\CruceClientesController@traervalores')->name('cr.traervalores');
Route::get('contable/cruce_cuentas_clientes/valores/edit/{id}', 'contable\CruceClientesController@cruce_cuentas_edit')->name('cr.cruce_cuentas_edit');
Route::get('contable/cruce_cuentas_clientes/crear', 'contable\CruceClientesController@cruce_cuentas_create')->name('cr.cruce_cuentas_create');
Route::match(['get', 'post'], 'contable/cruce_cuentas_clientes/valores/buscar', 'contable\CruceClientesController@buscar_cruce')->name('cr.cruce_cuentas_search');
Route::post('contable/cruce_cuentas_clientes/store', 'contable\CruceClientesController@cruce_cuentas_store')->name('cr.cruce_cuentas_store');
Route::match(['get', 'post'], 'contable/cruce_cuentas_clientes/anular/comprobante/{id}', 'contable\CruceClientesController@anular_cruce_cuentas')->name('cr.anular_cruce_cuentas');

Route::get('contable/uso_equipo/Insumos', 'contable\UsosController@getUses')->name('u.getUses');
Route::get('contable/ventas/estado/factura/{id}', 'ApiFacturacionController@mod_venta');

Route::post('contable/ventas/retenciones/orden/{id}', 'contable\ClientesRetencionesController@update')->name('clienter.update');
// 08/03/2021
Route::match(['get', 'post'], 'contable/venta/infome_labs/index', 'contable\TipoTarjetaController@infome_labs')->name('venta.infome_labs');
Route::match(['get', 'post'], 'contable/venta/infome_labs/excel', 'contable\TipoTarjetaController@excel_labs')->name('venta.infome_excel');
// 24/03/2021
Route::match(['get', 'post'], 'contable/informe_nca/index', 'contable\VentasController@informe_nca')->name('venta.informe_nca');
Route::post('contable/ventas/informe_nca/excel_ventas_nca', 'contable\VentasController@excel_ventas_nca')->name('venta.excel_ventas_nca.generar');

Route::get('contable/ventas/informes/excel_ventas_nca', 'contable\VentasController@excel_ventas_nca')->name('venta.excel_ventas_nca');
Route::match(['get', 'post'], 'contable/ventas/informe/estadistico', 'contable\VentasController@estadistico')->name('venta.estadisticos');
Route::match(['get', 'post'], 'contable/ventas/informe/estadisticohc4', 'contable\VentasController@estadisticoshc4')->name('venta.estadisticoshc4');
Route::match(['get', 'post'], 'contable/ventas/informe/estadisticohc4/complement', 'contable\VentasController@graphics')->name('venta.graphics');

Route::get('contable/vf/selectsearch', 'contable\VentasController@selectsearch')->name('venta.selectsearch');
Route::get('contable/cliente/selectsearch', 'contable\VentasController@clientesearch')->name('venta.clientesearch');
Route::get('contable/cliente/select/cedula', 'contable\VentasController@cedulasearch')->name('venta.cedulasearch');
Route::get('contable/cliente/select/producto', 'contable\VentasController@productosearch')->name('venta.productosearch');
Route::post('contable/ventas/create_recibo/store', 'contable\VentasController@store_recibo')->name('ventas_store_recibo');
Route::get('contable/ventas/create_recibo/generar', 'contable\VentasController@create_recibo')->name('venta.create_recibo');
Route::match(['get', 'post'], 'contable/ventas/create_recibo/index', 'contable\VentasController@index_recibo')->name('venta.index_recibo');
Route::get('contable/ventas/create_recibo/cargar', 'contable\VentasController@htmlrecibo')->name('venta.cargar_recibo');
Route::get('contable/masivo/asientos/emigar', 'contable\VentasController@masivCambioAsientos')->name('venta.masivoAsientos');
// PLANILLA
Route::get('contable/ventas/planilla/detalle/pdf/{id_venta}/{id_hc_procedimiento}', 'contable\VentasController@imprimirPlanillaDetalle')->name('venta.planilla.detalle.pdf');
Route::get('contable/ventas/planilla/detalle/{id}', 'contable\VentasController@obtenerPlanillasAgenda')->name('venta.planilla.detalle');
Route::get('contable/ventas/pdf/ieced/{id}', 'contable\VentasController@pdf_ieced')->name('venta.pdf_ieced');
Route::get('contable/ventas/pdf/envio/ieced/{id}', 'contable\VentasController@envio_correo')->name('ventas.envio_correo');
Route::get('contable/ventas/pdf/visualizador/html/{id}', 'contable\VentasController@visualizador_pdf')->name('venta.visualizador_pdf_html');

Route::get('contable/ventas/reproceso/venta/{id}', 'contable\VentasController@getSRI')->name('venta.reprocesar.getSri');
/* General */
Route::get('contable/ventas/{opcion}', 'contable\VentasController@index');
Route::match(['get', 'post'], 'ventas/{opcion}', 'contable\VentasController@index');
Route::match(['get', 'post'], 'factura_caja/buscador_paciente', 'contable\VentasController@buscador_paciente')->name('ventas.buscador_paciente');

Route::match(['get', 'post'], 'contable/ventas/informe/producto_cliente', 'contable\DeclaracionTributacionController@informe_por_producto')->name('ventas.informexproducto');

Route::match(['get', 'post'], 'contable/excel/conglomerada/{id_venta}', 'contable\VentasController@excelConglomerada')->name('ventas.conglomerada');
Route::match(['get', 'post'], 'contable/ventas/asignar/egreso/{id_ventas}', 'contable\VentasController@asignar_egreso')->name('ventas.asignar_egreso');
Route::match(['get', 'post'], 'contable/ventas/guardar/asignar/egreso', 'contable\VentasController@guardar_asignar_egreso')->name('ventas.guardar_asignar_egreso');

// nuevo estadistico
Route::match(['get', 'post'], 'contable/ventas/informe/nuevo_estadistico', 'contable\estadisticonuevoController@nuevo_estadistico')->name('venta.nuevo_estadistico');
Route::match(['get', 'post'], 'contable/ventas/informe/estadisticohc4/complement2', 'contable\VentasController@graphics2')->name('venta.graphics2');
Route::match(['get', 'post'], 'contable/reporte/indexFcVendedor', 'contable\VentasController@indexFcVendedor')->name('ventas.indexFcVendedor');

Route::match(['get', 'post'], 'contable/reporte/indexFcVendedor/pdf', 'contable\VentasController@indexFcVendedor_pdf')->name('ventas.indexFcVendedor_pdf');

// Rutas para verificar y asignar egresos masivamente
Route::match(['get', 'post'], 'contable/ventas_consulta/sin_egreso', 'contable\VentasController@ventas_sin_egreso')->name('ventas.sin_egreso');
Route::post('contable/ventas_consulta/asignar_egresos_masivo', 'contable\VentasController@asignar_egresos_masivo')->name('ventas.asignar_egresos_masivo');
