<?php
//MODULO DE CUENTAQS POR PAGAR
Route::get('contable/app-template', 'contable\AnticipoProveedorController@template')->name('contable.app-template');

Route::post('contable/bancos/obtener', 'contable\AnticipoProveedorController@bancos')->name('anticipo.bancos');
Route::get('contable/anticipo/comprobante/{id}', 'contable\AnticipoProveedorController@pdf_comprobante')->name('pdf_comprobante_anticipo');
Route::get('contable/cuentas_pagar/{id}/{tipo}', 'contable\CuentasPagarController@index')->name('cuentas_pagar.index');
Route::get('contable/cuentas/factura/gastos', 'contable\CuentasPagarController@tap_factura_contable')->name('cuentas_pagar.fact_contable');
Route::match(['get', 'post'], 'contable/acreedores/documentos/cuentas/egreso/varios', 'contable\EgresoAcreedorController@egresosv')->name('egresosv.index');
Route::get('contable/acreedores/documentos/cuentas/egreso/varios/anular/{id}', 'contable\EgresoAcreedorController@anular_egreso_v')->name('egresosv.anular');
Route::get('contable/acreedores/documentos/cuentas/egreso/masivo/anular/{id}', 'contable\Egreso_MasivoController@anular')->name('egresom.anular');
Route::get('contable/acreedores/documentos/cuentas/egreso/varios/edit/{id}', 'contable\EgresoAcreedorController@egresosvedit')->name('egresosv.edit');
Route::get('contable/acreedores/documentos/cuentas/egreso/varios/crear', 'contable\EgresoAcreedorController@egresov_create')->name('egresov.create');
Route::get('contable/acreedores/documentos/cuentas/egreso/varios/buscar/banco/{id}', 'contable\EgresoAcreedorController@buscar_banco')->name('egresoacreedor.buscar_banco');
Route::post('contable/acreedores/documentos/cuentas/egreso/varios/store', 'contable\EgresoAcreedorController@egresov_store')->name('egresov.store');
Route::post('contable/acreedores/documentos/cuentas/egreso/varios/update/{id}', 'contable\EgresoAcreedorController@egresov_update')->name('egresov.update');
Route::post('contable/acreedores/documentos/cuentas/egreso/varios/actualizar/{id}', 'contable\EgresoAcreedorController@egresov_update_observacion')->name('egresoacreedor.egresov_update_observacion');
Route::match(['get', 'post'], 'contable/compras/buscar', 'contable\ComprasController@buscar')->name('compra.buscar');
Route::match(['get', 'post'], 'contable/acreedores/egresos/compegreso/{id}/{tipo}', 'contable\EgresoAcreedorController@reporte_compegreso')->name('reporte_datos.compegreso');
Route::match(['get', 'post'], 'contable/acreedores/egresos/compegresom/{id}/{tipo}', 'contable\Egreso_MasivoController@reporte_compegresoma')->name('reporte_datos.compegresom');

Route::match(['get', 'post'], 'contable/comp_egreso/buscar', 'contable\EgresoAcreedorFontroller@buscar')->name('comp_egreso.buscar');

Route::match(['get', 'post'], 'contable/acreedores/egreso/masivo/reparar/{id}', 'contable\Egreso_MasivoController@repararEgresoMasivo')->name('egresom.anular');
//MANTENIMIENTO COMPRAS
#febrero 11 anticipo a proveedores
//FACTURA DE COMPRAS
Route::match(['get', 'post'], 'compras/{opcion}', 'contable\ComprasController@index');
Route::get('compras', 'contable\ComprasController@index');
Route::get('contable/compras', 'contable\ComprasController@index')->name('compras_index');
Route::get('contable/comprassri', 'contable\ComprasController@verificar_documento_sri')->name('verificar_documento_sri');
Route::get('tributacion/estadoDocumentos', 'tributacion\TributacionController@estado_documento_sri')->name('estado_documento_sri');
Route::get('contable/compras/buscar/proveedor', 'contable\ComprasController@proveedorsearch')->name('compras.proveedorsearch');
Route::post('contable/trearcomprobante', 'contable\ComprasController@traercomprobante')->name('traercomprobante_sri');
Route::match(['get', 'post'], 'contable/comprasri_store', 'contable\ComprasController@comprasri_store')->name('comprasri_store');
Route::match(['get', 'post'], 'contable/compras/buscarProducto', 'contable\ComprasController@buscarProducto')->name('compras.buscarProducto');
Route::match(['get', 'post'], 'contable/compras/buscar', 'contable\ComprasController@buscar')->name('compras.buscar');
Route::get('contable/compras/crear', 'contable\ComprasController@crear')->name('compra_crear');
Route::post('contable/compras/store', 'contable\ComprasController@store')->name('compra_store');
Route::get('contable/compras/devolucion/{id}', 'contable\ComprasController@devolucion')->name('compra_devolucion');
Route::get('contable/compras/editar/{id}', 'contable\ComprasController@editar')->name('compra_editar');
Route::post('contable/buscar/nombre', 'contable\ComprasController@nombre')->name('compra_nombre');
Route::post('contable/buscador/nombre/autocomplete', 'contable\ComprasController@nombre2')->name('compra_nombre2');
Route::post('contable/compras/actualizar/retenciones', 'contable\ComprasController@retenciones')->name('compra_retenciones');
Route::post('contable/compras/buscar/rubros', 'contable\ComprasController@buscar_rubros')->name('compra.buscar_rubros');
Route::get('contable/producto/codigo', 'contable\ComprasController@codigo')->name('compra_codigo');

//subir pdf 
Route::get('contable/compras/subirpdf/{id}/{parametro}', 'contable\ComprasController@subirpdf')->name('compras.subirpdf');
Route::post('contable/compras/gurdarpdf', 'contable\ComprasController@guardarpdf')->name('compras_guardarpdf');
//Route::get('contable/compras/pdf_visualizar/{id}', 'contable\ComprasController@pdf_visualizar')->name('compras.visualizar');
Route::get('contable/compras/pdf_visualizar', 'contable\ComprasController@pdf_visualizar_nuevo')->name('compras.visualizar');
//retenciones acreedores
Route::match(['get', 'post'], 'contable/acreedores/documentos/retenciones/buscar', 'contable\RetencionesController@buscar')->name('retenciones.buscar');
Route::get('contable/retenciones/nombre_proveedor', 'contable\RetencionesController@nombre_proveedor')->name('retenciones.nombre_proveedor');
//MODULO COMPRAS | PEDIDOS REALIZADOS
//Route::match(['get', 'post'], 'codigo/barras/', 'Insumos\ProductoController@codigo')->name('codigo.barra');
Route::match(['get', 'post'], 'contable/compras/pedidos', 'contable\CompPedidosRealizadosController@index')->name('compras.pedidos');
Route::match(['get', 'post'], 'contable/compras/pedidos/{id}', 'contable\CompPedidosRealizadosController@pedido')->name('pedido.seguimiento');
Route::match(['get', 'post'], 'contable/compras/modal', 'contable\CompPedidosRealizadosController@modalcompras')->name('pedido.modal');
Route::match(['get', 'post'], 'contable/compras/modal/fetch_data', 'contable\CompPedidosRealizadosController@fetch_data')->name('pedido.paginate');


Route::get('contable/producto/ingreso/codigo', 'contable\CompPedidosRealizadosController@crear_bodega_producto')->name('compra.crear_bodega_producto');

Route::post('contable/ingresar/producto/compra', 'contable\CompPedidosRealizadosController@guardar_pedido_compra')->name('ingreso.guardar_pedido_compra');

//Route::get('contable/compras/pedidos', 'contable\CompPedidosRealizadosController@index')->name('compras.pedidos');
// 05/03/2021
Route::get('contable/clientes/retenciones/actualizar', 'contable\ClientesRetencionesController@actualizar_fecha')->name('actualizar_fecha');

//MANTENIMIENTO ACREEDORES
Route::get('contable/acreedores/mantenimiento/acreedor', 'contable\AcreedoresController@index')->name('acreedores_index');

Route::get('contable/acreedores/mantenimiento/acreedor', 'contable\AcreedoresController@index')->name('acreedores_index');

Route::get('contable/acreedores/mantenimiento/acreedor/crear', 'contable\AcreedoresController@crear')->name('acreedores_crear');
Route::post('contable/acreedores/mantenimiento/acreedor/store', 'contable\AcreedoresController@store')->name('acreedores_store');
Route::post('contable/acreedores/mantenimiento/subir/logo', 'contable\AcreedoresController@subir_logo')->name('acreedores_subir_logo');
Route::match(['get', 'post'], 'contable/acreedores/mantenimiento/acreedor/buscar', 'contable\AcreedoresController@search')->name('acreedores_search');
Route::get('contable/acreedores/mantenimiento/acreedor/editar/{id_acreedor}', 'contable\AcreedoresController@editar')->name('acreedores_editar');
Route::match(['get', 'post'], 'contable/acreedores/mantenimiento/acreedor/actualizar/{id_acreedor}', 'contable\AcreedoresController@update')->name('acreedores.update');

Route::get('contable/acreedores/mantenimiento/acreedor/tipo', 'contable\TipoAcreedoresController@index')->name('tipoacreedor.index');
Route::get('contable/acreedores/mantenimiento/acreedor/tipo/crear', 'contable\TipoAcreedoresController@create')->name('tipoacreedor.create');
Route::match(['get', 'post'], 'contable/acreedores/mantenimiento/acreedor/tipo/buscar', 'contable\TipoAcreedoresController@search')->name('tipoacreedor.search');
Route::post('contable/acreedores/mantenimiento/acreedor/tipo/store', 'contable\TipoAcreedoresController@store')->name('tipoacreedor.store');
Route::get('contable/acreedores/mantenimiento/acreedor/tipo/editar/{id_acreedor}', 'contable\TipoAcreedoresController@edit')->name('tipoacreedor.edit');
Route::match(['get', 'post'], 'contable/acreedores/mantenimiento/acreedor/tipo/actualizar/{id_acreedor}', 'contable\TipoAcreedoresController@update')->name('tipoacreedor.update');

Route::match(['get', 'post'], 'contable/acreedores/documentos/comprobante/egreso/comprobante/buscar', 'contable\EgresoAcreedorController@buscar')->name('comp_egreso.buscar');
Route::match(['get', 'post'], 'contable/acreedores/documentos/egreso/comprobante/varios/buscar', 'contable\EgresoAcreedorController@buscar_varios')->name('comp_egresov.buscar');

Route::get('contable/acreedores/nota/egreso', 'contable\EgresoAcreedorController@index')->name('egresoa_index');
Route::get('contable/acreedores/nota/egreso/crear', 'contable\EgresoAcreedorController@create')->name('egresoa_create');
Route::post('contable/acreedores/nota/egreso/store', 'contable\EgresoAcreedorController@store')->name('egresoa_store');
Route::get('contable/acreedores/nota/egresos/edit/{id}', 'contable\EgresoAcreedorController@comprobante_edit')->name('egresoa_edit');

Route::match(['get', 'post'], 'contable/acreedores/egresos/asumida/', 'contable\EgresoAcreedorController@update_rt_asumidas')->name('acreedores_cegreso.asumidas');


Route::post('contable/acreedores/nota/egresos/update/{id}', 'contable\EgresoAcreedorController@update_comprobante')->name('egresoacreedor_update');
Route::post('contable/acreedores/nota/egresos/actualizar/{id}', 'contable\EgresoAcreedorController@update_comprobante_observacion')->name('egresoacreedor.update_comprobante_observacion');
Route::post('contable/acreedores/nota/egreso/update', 'contable\EgresoAcreedorController@update')->name('egresoa_update');
Route::match(['get', 'post'], 'contable/acreedores/documentos/comprobante/egreso/comprobante', 'contable\EgresoAcreedorController@comprobante_index')->name('acreedores_cegreso');
Route::get('contable/acreedores/documentos/comprobante/egreso/create', 'contable\EgresoAcreedorController@comprobante_create')->name('acreedores_ccreate');
Route::get('contable/acreedores/documentos/nota/comprobante/egreso/anular/{id}', 'contable\EgresoAcreedorController@anular_egreso')->name('acreedores_anular');
Route::get('contable/acreedores/documentos/nota/comprobante/egreso/envio_correo/{id}', 'contable\EgresoAcreedorController@envioCorreo')->name('egreso_enviar_email');
Route::post('contable/acreedores/documentos/comprobante/egreso/comprobante/store', 'contable\EgresoAcreedorController@comprobante_store')->name('acreedores_cstore');
Route::get('contable/acreedores/documentos/comprobante/egreso/edit', 'contable\EgresoAcreedorController@comprobante_edit')->name('acreedores_cedit');
Route::post('contable/acreedores/documentos/comprobante/egreso/update', 'contable\EgresoAcreedorController@comprobante_update')->name('acreedores_cupdate');
Route::post('contable/acreedores/documentos/comprobante/egreso/superavit', 'contable\EgresoAcreedorController@superavit')->name('acreedores_superavit');
Route::post('contable/acreedores/documentos/comprobante/egreso/buscarcodigo', 'contable\EgresoAcreedorController@buscar_codigo')->name('acreedores_buscar_codigo');
Route::post('contable/acreedores/egreso/buscarproveedor', 'contable\EgresoAcreedorController@buscarproveedor')->name('acreedores_buscarproveedor');
Route::post('contable/acreedores/factura/anticipo', 'contable\ComprasController@anticipo_proveedor')->name('acreedores_anticipo_proveedor');
Route::post('contable/acreedores/factura/anticipo/guardar', 'contable\EgresoAcreedorController@guardar_anticipos')->name('acreedores_guardar_anticipos');
Route::post('contable/acreedores/factura/pagofactura', 'contable\EgresoAcreedorController@crear_pago')->name('acreedores_crear_pago');


////////////MOSTRAR LOS ANTICIPOS ANTERIORES ////////////////////////
Route::match(['get', 'post'], 'contable/acreedores/egreso/buscar/anticipos', 'contable\EgresoAcreedorController@buscarAntAnticipos')->name('compra.egreso.buscarAntAnticipos');
////////////--------------------------------------------------------/////////

Route::match(['get', 'post'], 'contable/acreedores/comprobante/egreso/validar/retencion', 'contable\EgresoAcreedorController@validar_retencion')->name('compra.validar_retencion');

//MODAL PARA COMPROBANTES DE EGRESO
Route::get('contable/acreedores/comprobante/egreso/modal/{valor}/{fact_numero}', 'contable\EgresoAcreedorController@anticipomodal')->name('acreedores_anticipomodal');

#FUNCION QUE VALIDA LA SECUENCIA FACTURA
Route::get('contable/compra/validation', 'contable\ComprasController@validar_secuencia')->name('compras_validar_secuencia');
#SUBIDA DE XML PARA FACTURAS
/*
Route::post('contable/subida/xml','contable\ComprasController@xml')->name('compras_xml');*/
#pdf para comprobante de egreso
Route::get('contable/compra/comprobante/pdf/egreso/{id}', 'contable\EgresoAcreedorController@pdfcomprobante')->name('pdf_comprobante');
//MANTENIMIENTO PRODUCTOS Y SERVICIOS

//pdf comprobante de egreso noraml
Route::get('contable/compra/comprobante/egreso/pdf/{id}', 'contable\EgresoAcreedorController@pdfcomprobante')->name('pdf_comprobante_egreso');
Route::get('contable/compra/comprobante/egresovarios/pdf/{id}', 'contable\EgresoAcreedorController@pdfegresovarios')->name('pdf_egresovarios');

Route::get('contable/compra/comprobante/egreso/anulado', 'contable\EgresoAcreedorController@egreso_anulado')->name('compra.egreso_anulado');

Route::match(['get', 'post'], 'contable/compra/comprobante/egreso/anulado/store', 'contable\EgresoAcreedorController@egreso_anulado_store')->name('compra.egreso_anulado_store');

Route::match(['get', 'post'], 'contable/cruce/valores', 'contable\AnticipoProveedorController@index')->name('cruce.index');
Route::get('contable/cruce/valores/edit/{id}', 'contable\AnticipoProveedorController@edit')->name('cruce.edit');
Route::get('contable/cruce/crear', 'contable\AnticipoProveedorController@create')->name('cruce.create');
Route::match(['get', 'post'], 'contable/cruce/valores/buscar', 'contable\AnticipoProveedorController@search')->name('cruce.search');
Route::post('contable/cruce/store', 'contable\AnticipoProveedorController@store')->name('cruce.store');
Route::get('contable/cruce/pdf/{id}', 'contable\AnticipoProveedorController@pdf_comprobante')->name('cruce.pdf');
Route::post('contable/cruce/anticipos', 'contable\AnticipoProveedorController@obtener_anticipos')->name('cruce.anticipos');
#AHORA NOTA DE DEBITO ACREEDORES
Route::get('contable/acreedores/nota/credito', 'contable\NotaCreditoAcreedoresController@index')->name('creditoacreedores.index');
Route::get('contable/acreedores/nota/credito/editar/{id}', 'contable\NotaCreditoAcreedoresController@edit')->name('creditoacreedores.edit');
Route::get('contable/acreedores/nota/credito/anular/{id}', 'contable\NotaCreditoAcreedoresController@anular')->name('creditoacreedores.anular');
Route::match(['get', 'post'], 'contable/acreedores/nota/credito/anular', 'contable\NotaCreditoAcreedoresController@anular')->name('creditoacreedores.anular');
Route::match(['get', 'post'], 'contable/acreedores/nota/credito/buscar', 'contable\NotaCreditoAcreedoresController@search')->name('creditoacreedores.search');
Route::get('contable/acreedores/nota/credito/create', 'contable\NotaCreditoAcreedoresController@create')->name('creditoacreedores.create');
Route::post('contable/acreedores/nota/credito/store', 'contable\NotaCreditoAcreedoresController@store')->name('creditoacreedores.store');
Route::get('contable/acreedores/nota/debito', 'contable\NotaDebitoAcreedoresController@index')->name('debitoacreedores.index');
Route::get('contable/acreedores/nota/debito/edit/{id}', 'contable\NotaDebitoAcreedoresController@edit')->name('debitoacreedores.edit');
Route::get('contable/acreedores/nota/debito/anular/{id}', 'contable\NotaDebitoAcreedoresController@anular')->name('debitoacreedores.anular');
Route::match(['get', 'post'], 'contable/acreedores/nota/debito/buscar', 'contable\NotaDebitoAcreedoresController@search')->name('debitoacreedores.search');
Route::get('contable/acreedores/nota/debito/create', 'contable\NotaDebitoAcreedoresController@create')->name('debitoacreedores.create');
Route::post('contable/acreedores/nota/debito/store', 'contable\NotaDebitoAcreedoresController@store')->name('debitoacreedores.store');
Route::get('contable/debito_acreedores/pdf_debito_acreedores/{id}', 'contable\NotaDebitoAcreedoresController@pdf_debito_acreedores')->name('pdf_debito_acreedores.pdf');
Route::get('contable/acreedores/nota/credito/editar2/{id}', 'contable\NotaCreditoAcreedoresController@edit2')->name('creditoacreedores.edit2');
/************************NUEVA NOTA DE CREDITO ************************************/
Route::get('contable/acreedores/new/nota/credito', 'contable\NotaCreditoAcreedoresController@newNotaCredito')->name('compra.notacredito.newNotaCredito');
Route::match(['get', 'post'], 'contable/acreedores/new/buscar/facturas', 'contable\NotaCreditoAcreedoresController@buscarFacturas')->name('compra.notacredito.buscarFacturas');
Route::match(['get', 'post'], 'contable/acreedores/new/buscar/facturas/detalle', 'contable\NotaCreditoAcreedoresController@buscarDetalleFacturas')->name('compra.notacredito.buscarDetalleFacturas');
Route::post('contable/acreedores/nota/credito/new/credito/store', 'contable\NotaCreditoAcreedoresController@newCreditoStore')->name('compra.notaCreditoAcreedores.newStore');
Route::match(['get', 'post'], 'contable/acreedores/new/buscar/facturas/detalle', 'contable\NotaCreditoAcreedoresController@buscarDetalleFacturas')->name('compra.notacredito.buscarDetalleFacturas');

#RUBROS ACREEDORES
Route::resource('contable/acreedores/mantenimiento/rubrosa', 'contable\RubrosAcreedorController');
Route::post('contable/acreedores/mantenimiento/rubrosa/store', 'contable\RubrosAcreedorController@store')->name('rubrosa.store');
Route::post('contable/acreedores/mantenimiento/rubrosa/buscar', 'contable\RubrosAcreedorController@search')->name('rubrosa.search');
Route::get('contable/acreedores/mantenimiento/rubrosa/editar/{codigo}', 'contable\RubrosAcreedorController@editar')->name('rubrosa.editar');
Route::post('contable/acreedores/mantenimiento/rubrosa/actualizar/{codigo}', 'contable\RubrosAcreedorController@update')->name('rubrosa.update');
Route::post('contable/acreedores/mantenimiento/rubrosa/buscarcodigo', 'contable\RubrosAcreedorController@nombre')->name('rubrosa.nombre');
Route::match(['get', 'post'], 'contable/acreedores/mantenimiento/rubrosa/autocomplete/codigo', 'contable\RubrosAcreedorController@autocomplete')->name('rubrosa.searchcode');

#informes
Route::match(['get', 'post'], 'contable/acreedores/informes/cartera/pagar', 'contable\InformesAcreedorController@index_cartera')->name('carterap.index');
Route::post('contable/carterapagar/get', 'contable\InformesAcreedorController@cartera')->name('compras.cartera_pagar');
Route::match(['get', 'post'], 'contable/acreedores/informe/retenciones', 'contable\InformesAcreedorController@index_retenciones')->name('informe_retenciones.index');
Route::match(['get', 'post'], 'contable/acreedores/informes/informe/saldos', 'contable\InformesAcreedorController@index_saldos')->name('saldos_acreedor.index');
Route::match(['get', 'post'], 'contable/acreedores/informe/cheques', 'contable\InformesAcreedorController@index_cheques')->name('chequesa.index');
Route::match(['get', 'post'], 'contable/acreedores/informes/saldos/excel', 'contable\InformesAcreedorController@excel_saldos')->name('saldos_informe.excel');
Route::match(['get', 'post'], 'contable/acreedores/informes/cartera/excel', 'contable\InformesAcreedorController@excel_saldos2')->name('saldos2_informe.excel');
Route::match(['get', 'post'], 'contable/acreedores/informes/cheques/excel', 'contable\InformesAcreedorController@excel_cheque')->name('chequesa.excel');
Route::match(['get', 'post'], 'contable/acreedores/informe/retenciones/excel', 'contable\InformesAcreedorController@excel_retenciones')->name('retenciones.excel');
Route::match(['get', 'post'], 'contable/acreedores/informes/deudas/pagos', 'contable\InformesAcreedorController@index_deudas_pagos')->name('deudasvspagos.index');
Route::match(['get', 'post'], 'contable/acreedores/informes/deudas/excel', 'contable\InformesAcreedorController@excel_deudas')->name('deudasvspagos.excel');

Route::match(['get', 'post'], 'contable/compras/informes', 'contable\InformesAcreedorController@informe_compras')->name('compras.informe');
Route::match(['get', 'post'], 'contable/compras/informes/excel', 'contable\InformesAcreedorController@excel_informe_compras')->name('compras.excel');
Route::match(['get', 'post'], 'contable/acreedores/deudas/pendientes', 'contable\InformesAcreedorController@index_deudas_pendientes')->name('deudas_pendientes.index');
Route::match(['get', 'post'], 'contable/acreedores/deudas/pendientes/excel', 'contable\InformesAcreedorController@excel_deudas_pendientes')->name('deudas_pendientes.excel');

#anulaciones
Route::match(['get', 'post'], 'contable/retenciones/acreedores/anular/{id}', 'contable\RetencionesController@anular')->name('retencionesa.anular');
Route::match(['get', 'post'], 'contable/nota/credito/acreedores/anular/{id}', 'contable\NotaCreditoAcreedoresController@anular')->name('notacreedorescredito.anular');
// 05/03/2021
Route::get('contable/retenciones/actualizar_fecha', 'contable\RetencionesController@actualizar_fecha')->name('actualizar_fecha_nueva');

#MANTENIMIENTO RETENCIONES
Route::get('contable/acreedores/documentos/newretenciones', 'contable\RetencionesController@newcreate')->name('r.crear_retencion');
Route::get('contable/acreedores/documentos/retenciones', 'contable\RetencionesController@index')->name('retenciones_index');
Route::get('contable/acreedores/create/retenciones/anuladas', 'contable\RetencionesController@create_anuladas')->name('retenciones_anuladas');
Route::get('contable/acreedores/documentos/retenciones/reenviar/{id}', 'contable\RetencionesController@reenviar')->name('retenciones_reenviar');
Route::match(['get', 'post'], 'contable/retenciones/acreedores/buscadorf', 'contable\RetencionesController@buscarpro')->name('retencionesa.buscarpro');
Route::get('contable/acreedores/documentos/retenciones/editar/{id}', 'contable\RetencionesController@edit')->name('retenciones_edit');
Route::post('contable/acreedores/documentos/retenciones/update/{id}', 'contable\RetencionesController@update')->name('retenciones_update');
Route::get('contable/acreedores/documentos/retenciones/crear', 'contable\RetencionesController@crear')->name('retenciones_crear');
Route::match(['get', 'post'], 'contable/acreedores/documentos/retenciones/buscar/codigo', 'contable\RetencionesController@buscar_codigo')->name('retenciones_buscar_codigo');
Route::get('contable/acreedores/documentos/retenciones/buscar/codigo/autocomplete', 'contable\RetencionesController@codigo')->name('retenciones_codigo');
Route::post('contable/acreedores/documentos/retenciones/store', 'contable\RetencionesController@store')->name('retenciones_store');
Route::get('contable/acreedores/documentos/retenciones/modal/acreedores/documentos/retenciones', 'contable\RetencionesController@modal_retenciones')->name('retenciones_modal_retenciones');
Route::post('contable/acreedores/documentos/retenciones/buscar/query', 'contable\RetencionesController@query_cuentas')->name('retenciones_query');
Route::post('contable/acreedores/documentos/retenciones/buscar/query2', 'contable\RetencionesController@query_cuentas2')->name('retenciones_query2');

Route::get('contable/actualizate/cs/prouctos', 'contable\AnticipoProveedorController@relacionar_campos')->name('actualizsss.relacionar_campos');

//nota de ingreso inventario
Route::get('contable/anularpdf/compra/{id}/{visualizar}', 'contable\ComprasController@anularpdf')->name('anularpdf.compras');
Route::get('contable/productos/documentos/nota/inventarios', 'contable\NotaInventarioController@index')->name('notainventario.index');
Route::match(['get', 'post'], 'contable/productos/documentos/inventarios', 'contable\NotaInventarioController@search')->name('notainventario.search');
Route::get('contable/productos/documentos/nota/inventarios/anular/{id}', 'contable\NotaInventarioController@anular')->name('notainventario.anular');
Route::get('contable/productos/documentos/nota/inventarios/edit/{id}', 'contable\NotaInventarioController@edit')->name('notainventario.edit');
Route::get('contable/productos/documentos/nota/inventarios/crear', 'contable\NotaInventarioController@create')->name('notainventario.create');
Route::post('contable/productos/documentos/nota/inventarios/store', 'contable\NotaInventarioController@store')->name('notainventario.store');
Route::get('contable/saldos_iniciales/proveedores', 'contable\AcreedoresController@saldos_iniciales')->name('saldosinicialesp.index');
Route::get('contable/saldos_iniciales/proveedor/edit/{id}', 'contable\AcreedoresController@saldos_iniciales_edit')->name('saldos_inicialesp.edit');
Route::get('contable/saldos_iniciales/proveedores/index', 'contable\AcreedoresController@saldos_iniciales_index')->name('saldosinicialesp.index2');
Route::match(['get', 'post'], 'contable/saldos_iniciales/proveedor/buscar', 'contable\AcreedoresController@searchsaldos')->name('saldosinicialesp.search');
Route::match(['get', 'post'], 'contable/saldos_iniciales/proveedorz/anular/{id}', 'contable\AcreedoresController@anular_saldo')->name('saldosinicialesp.anular');
Route::post('contable/saldos_iniciales/store', 'contable\AcreedoresController@guardar_iniciales')->name('saldosinicialesp.store');

Route::get('contable/compras/masivo/actualizar', 'contable\ComprasController@subir_masivo')->name('compras.subir_masivo');

Route::match(['get', 'post'], 'contable/anticipo/anular_anticipo/{id}', 'contable\AnticipoProveedorController@anular_anticipo')->name('anticipo.anular_anticipo');

Route::get('contable/compras/verificar/anulacion', 'contable\ComprasController@verificar_anulacion')->name('compras.verificar_anulacion');
//EGRESOS_MASIVOS
Route::match(['get', 'post'], 'contable/comp_egreso_masivo/index', 'contable\Egreso_MasivoController@index')->name('comp_egreso_masivo.index');
Route::match(['get', 'post'], 'contable/comp_egreso_masivo/create', 'contable\Egreso_MasivoController@create')->name('comp_egreso_masivo.create');
Route::post('contable/comp_egreso_masivo/store', 'contable\Egreso_MasivoController@store')->name('comp_egreso_masivo.store');
Route::post('contable/comp_egreso_masivo/store2', 'contable\Egreso_MasivoController@store2')->name('comp_egreso_masivo.store2');
Route::get('contable/comp_egreso_masivo/edit/{id}', 'contable\Egreso_MasivoController@edit')->name('comp_egreso_masivo.edit');
Route::match(['get', 'post'], 'contable/comp_egreso_masivo/buscar', 'contable\Egreso_MasivoController@buscar')->name('comp_egreso_masivo.buscar');
Route::get('contable/comp_egreso_masivo/pdf_egreso_masivo/{id}', 'contable\Egreso_MasivoController@pdf_egreso')->name('pdf_egreso_masivo.pdf');

//Obtenemos las Facturas de Compra
Route::match(['get', 'post'], 'contable/acreedores/nota/credito/obtener_num_fact', 'contable\NotaCreditoAcreedoresController@obtener_num_fact')->name('notacreditocreedores.obtener_num_fact');




Route::match(['get', 'post'], 'contable/comp_egreso_masivo/newCreate2', 'contable\Egreso_MasivoController@newCreate2')->name('comp_egreso_masivo.newCreate2');
Route::match(['get', 'post'], 'contable/comp_egreso_masivo/buscador/detalle', 'contable\Egreso_MasivoController@buscadorDetalles')->name('comp_egreso_masivo.buscadorDetalles');
Route::post('contable/comp_egreso_masivo/store2/new', 'contable\Egreso_MasivoController@newStore2')->name('compra.egresoMasivo.newStore2');




#Cruce de cuentas con factura
Route::get('contable/cruce_cuentas/valores', 'contable\AnticipoProveedorController@cruce_cuentas')->name('pr.cruce_cuentas');
Route::get('contable/cruce_cuentas/traervalores', 'contable\AnticipoProveedorController@traervalores')->name('pr.traervalores');
Route::get('contable/cruce_cuentas/valores/edit/{id}', 'contable\AnticipoProveedorController@cruce_cuentas_edit')->name('pr.cruce_cuentas_edit');
Route::get('contable/cruce_cuentas/crear', 'contable\AnticipoProveedorController@cruce_cuentas_create')->name('pr.cruce_cuentas_create');
Route::match(['get', 'post'], 'contable/cruce_cuentas/valores/buscar', 'contable\AnticipoProveedorController@buscar_cruce')->name('pr.cruce_cuentas_search');
Route::get('contable/cruce_cuentas/valores/buscar/proveedor', 'contable\AnticipoProveedorController@proveedorsearch')->name('anticipoproveedor.proveedorsearch');
Route::post('contable/cruce_cuentas/store', 'contable\AnticipoProveedorController@cruce_cuentas_store')->name('pr.cruce_cuentas_store');
Route::match(['get', 'post'], 'contable/cruce_cuentas/anular/comprobante/{id}', 'contable\AnticipoProveedorController@anular_cruce_cuentas')->name('pr.anular_cruce_cuentas');
Route::get('contable/cruce_cuentas/pdf_cruce_cuentas/{id}', 'contable\AnticipoProveedorController@pdf_cruce_cuentas')->name('pdf_cruce_cuentas.pdf');


//Leer Xml
Route::get('contable/compras/leer_xml', 'contable\ComprasController@leer_xml')->name('compra_leer_xml');
Route::get('contable/rete/verificar/secuencia', 'contable\RetencionesController@secuencia')->name('verificar_secuencia.contable');

##lo puse porque nadaie me dio rutas
Route::match(['get', 'post'], 'planos/estadisticos', 'EstadisticosPlanoController@index')->name('estaditicos_plano.index');
Route::match(['get', 'post'], 'contable/facturacion/estadisticos', 'EstadisticosPlanoController@orden')->name('estaditicos_plano.orden');
Route::match(['get', 'post'], 'contable/facturacion/estadisticoshc4', 'EstadisticosPlanoController@hc4')->name('estadisticos_hc4_s.privados');

//6/01/2020 gracias amigo
Route::get('contable/cruces/pdf_cruces/{id}', 'contable\AnticipoProveedorController@pdf_cruces')->name('pdf_cruces.contable');

//Vista 17/02/2021
Route::match(['get', 'post'], 'contable/acreedores/credito/informe', 'contable\ComprasController@nota_credito')->name('acreedores.informenc');
Route::get('contable/acreedores/vista/carga_logo', 'contable\ComprasController@carga_logo')->name('cargar.parteinicial');

Route::get('/fastlimpiar', function () {
    //$exitCode = Artisan::call('cache:clear');
    //$exitCode = Artisan::call('config:clear');
    //$exitCode = Artisan::call('cache:config');
    //$exitCode = Artisan::call('views:clear');
});

//rutas crear bodega
Route::match(['get', 'post'], 'contable/compra/bodega/search', 'contable\BodegaController@search')->name('contable.bodega.search');
Route::resource('contable/compra/bodega', 'contable\BodegaController', ['names' => [
    'create' => 'contable.bodega.create',
    'index'  => 'contable.bodega.index',
    'store'  => 'contable.bodega.store',
    'show'   => 'contable.bodega.show',
    'edit'   => 'contable.bodega.edit',
    'update' => 'contable.bodega.update',
    'destroy' => 'contable.bodega.destroy',
]]);

//rutas nueva forma de pedido
Route::get('contable/compras/pedido', 'contable\PedidoController@index')->name('contable.pedido.index');
Route::get('contable/compras/pedido/generar/{id}', 'contable\CompraPedidoController@generar_factura')->name('contable.pedido.generar');
Route::get('contable/compras/pedido/aprobar', 'contable\CompraPedidoController@aprobar_pedido')->name('contable.pedido.aprobar');
Route::get('contable/compras/pedido/aprobarpago', 'contable\CompraPedidoController@aprobar_factura')->name('contable.pedido.aprobar_factura');
//compras pedido 13/08/2021
Route::match(['get', 'post'], 'contable/pedidos/compra', 'contable\CompraPedidoController@index')->name('contable.compraspedidos.index');
Route::get('contable/pedidos/compra/buscar/proveedor', 'contable\CompraPedidoController@proveedorsearch')->name('comprapedido.proveedorsearch');
Route::get('contable/timeline/{id}', 'contable\CompraPedidoController@timeline')->name('contable.timeline');
Route::get('contable/pedidos/create', 'contable\CompraPedidoController@create')->name('contable.compra_pedidos.create');
Route::match(['get', 'post'], 'contable/pedidos/store', 'contable\CompraPedidoController@store')->name('contable.compra_pedido_store.create');
Route::get('contable/pedidos/edit/{id}', 'contable\CompraPedidoController@edit')->name('contable.compra_pedidos.edit');
Route::match(['get', 'post'], 'contable/pedidos/update/{id}', 'contable\CompraPedidoController@update')->name('contable.pedidos_update.create');
Route::match(['get', 'post'], 'contable/pedidos/buscar', 'contable\CompraPedidoController@buscar')->name('compras.pedidos.buscar');
Route::post('contable/pedidos/cambio_estado', 'contable\CompraPedidoController@cambio_estado')->name('contable.cambio_estado.edit');
Route::post('contable/pedido/paso/compra', 'contable\CompraPedidoController@paso_factura')->name('contable.pedido.paso_factura');
Route::post('contable/pedido/paso/retencion', 'contable\CompraPedidoController@retencion')->name('contable.pedido.paso_retencion');

Route::get('contable/pedidos/confirmar/check/{id}', 'contable\CompraPedidoController@confirmar_Check')->name('contable.compra_pedidos.check');

Route::match(['get', 'post'], 'contable/pedidos/confirmar/check/update/datos', 'contable\CompraPedidoController@update_Check')->name('contable.compra_pedidos.check_update');

Route::match(['get', 'post'], 'contable/pedidos/store/invBodega', 'contable\CompraPedidoController@storeInvBodega')->name('contable.compra_pedidos.storeInvBodega');

Route::get('contable/informe/contabilidad', 'contable\InformeContableController@index')->name('contable.infome_uso');

Route::match(['get', 'post'], 'contable/contabilidad/tiempos', 'contable\InformesAcreedorController@reporte_tiempo')->name('contable.reporte_tiempo');
Route::get('contable/masivo/subtotales', 'contable\ComprasController@masivo_subtotal')->name('contable.masivo_nota_credito');

Route::get('contable/getmodal/type/{id}', 'contable\LibroDiarioController@modal')->name('contable.getmoduleType');

Route::match(['get', 'post'], 'contable/anticipo/proveedores', 'contable\EgresoAcreedorController@anticipo_proveedores')->name('contable.anticipo_proveedores');
Route::post('contable/retenciones/paso/storenew', 'contable\RetencionesController@newstore')->name('contable.retenciones.newstore');

//mantenimiento usuario proceso

/*Route::get('contable/usuario/proceso/index', 'contable\CompraPedidoController @index_proceso')->name('compraspedidos.index_proceso');
Route::match(['get', 'post'],'contable/usuario/proceso/crear', 'contable\CompraPedidoController @crear_proceso')->name('compraspedidos.create');
Route::match(['get', 'post'],'contable/usuario/proceso/guardar', 'contable\CompraPedidoController @guardar')->name('compraspedidos.guardar');*/
Route::match(['get', 'post'], 'contable/producto/saldos_iniciales/index', 'contable\CompraPedidoController@indexInicales')->name('contable.compraspedido.indexInicial');
Route::match(['get', 'post'], 'contable/producto/saldos_iniciales', 'contable\CompraPedidoController@createInicial')->name('contable.compraspedido.createInicial');
Route::match(['get', 'post'], 'contable/producto/saldos_iniciales/store', 'contable\CompraPedidoController@storeInicial')->name('contable.storeProdInicial');
Route::match(['get', 'post'], 'contable/producto/saldos_iniciales/editar/{id}', 'contable\CompraPedidoController@editarInicial')->name('contable.editarProdInicial');
Route::match(['get', 'post'], 'contable/producto/saldos_iniciales/delete', 'contable\CompraPedidoController@deleteInicial')->name('contable.deleteProdInicial');

Route::match(['get', 'post'], 'contable/producto/saldos_iniciales/excel', 'contable\CompraPedidoController@excelInicial')->name('contable.excelProdInicial');
Route::match(['get', 'post'], 'contable/producto/saldos_iniciales/excel/descargar', 'contable\CompraPedidoController@descargarExcel')->name('contable.excelProdInicialdescargar');
Route::match(['get', 'post'], 'contable/producto/saldos_iniciales/excel/create', 'contable\CompraPedidoController@creareExcelInicial')->name('contable.excelProdInicialCreate');
Route::get('contable/saldos/iniciales/acreedores', 'contable\AcreedoresController@buscar_proveedor')->name('acreedores.buscar_proveedor');

//EXCEL Y PDF Deuda vs Pagos
Route::match(['get', 'post'], 'deudasvspagos/informe_pdf', 'contable\InformesAcreedorController@deudasvspagos_pdf')->name('deudasvspagos.informe_pdf');
//ENVIO DE correo
Route::get('contable/acreedores/egresos/envio/correo', 'contable\Egreso_MasivoController@envio_correo_pro')->name('envio_correo_pro.acreedores');
Route::post('contable/acreedores/documentos/retenciones/send/information', 'contable\RetencionesController@send_information')->name('retenciones.send_information');
Route::get('contable/liquidacion', 'contable\LiquidacionCompraController@index');
Route::get('contable/liquidacion/{submodulo}', 'contable\LiquidacionCompraController@index');
Route::post('contable/liquidacion/{submodulo}', 'contable\LiquidacionCompraController@index');
Route::post('contable/compras/buscador/prooveedor', 'contable\ComprasController@buscador_master_tipos')->name('compras.buscador_master_tipos');
Route::match(['get', 'post'], 'compras/editarCabeceraCompras/{id}', 'contable\ComprasController@editar_proveedor')->name('compras.editar_proveedor');
Route::get('contable/proveedor/compras/buscador_proveedor', 'contable\ComprasController@buscador_proveedor')->name('compras.buscador_proveedor');
Route::match(['get', 'post'], 'contable/compras/masivoAsientoDetalle/{anio}/{mes}/{dia}', 'contable\ComprasController@masivoAsientoDetalle')->name('compras.masivo_asientos_detalle_anio_mes_dia');
Route::match(['get', 'post'], 'contable/compras/masivoAsientoDetalle/{anio}/{mes}', 'contable\ComprasController@masivoAsientoDetalle')->name('compras.masivo_asientos_detalle_anio_mes');
Route::match(['get', 'post'], 'contable/compras/masivoAsientoDetalle/{anio}', 'contable\ComprasController@masivoAsientoDetalle')->name('compras.masivo_asientos_detalle_anio');

Route::match(['get', 'post'], 'contable/acreedores/informe/cheques/emitidos', 'contable\InformesAcreedorController@index_cheques_emitidos')->name('compra.informe.index_cheques_emitidos');
Route::match(['get', 'post'], 'contable/acreedores/informe/excel/cheques/emitidos', 'contable\InformesAcreedorController@excel_cheques_emitidos')->name('compra.informe.excel_cheques_emitidos');
Route::match(['get', 'post'], 'contable/acreedores/informe/cheques/update/check', 'contable\InformesAcreedorController@updateCheck')->name('compra.informe.updateCheck');

Route::post('contable/comprasri_store', 'contable\ComprasController@comprasri_store')->name('comprasri_store');
Route::match(['get', 'post'], 'contable/compras/buscarProducto', 'contable\ComprasController@buscarProducto')->name('compras.buscarProducto');

Route::match(['get', 'post'], 'contable/compra_venta/declaracion/tributacion', 'contable\DeclaracionTributacionController@index')->name('compra.declaracionTributacion.index');
Route::match(['get', 'post'], 'contable/compra_venta/declaracion/tributacion/buscador', 'contable\DeclaracionTributacionController@busqueda')->name('compra.declaracionTributacion.busqueda');
Route::match(['get', 'post'], 'contable/compra_venta/declaracion/tributacion/descargarExcel', 'contable\DeclaracionTributacionController@descargarExcel')->name('compra.declaracionTributacion.descargarExcel');
Route::get('contable/asiento/modulo/{id}', 'contable\DeclaracionTributacionController@asiento_modulo')->name('compra.asiento_modulo');


Route::get('bodega/ligue/bodegas/gye', 'Insumos\BodegaController@ligueGye')->name('bodega.ligueGye');
Route::post('bodega/ligue/bodegas/gye/store', 'Insumos\BodegaController@ligueGyeStore')->name('bodega.ligueGyeStore');

// anticipos clientes

Route::match(['get', 'post'], 'contable/cliente/anticipos/reporte', 'contable\AnticipoClienteController@index')->name("anticipos_clientes.reporte");


Route::match(['get', 'post'], 'contable/ventas/informe/reporte/ventas/utilidad', 'contable\InformeVariosController@ventasUtilidad')->name('ventas.reporte_varios.ventasUtilidad');
Route::match(['get', 'post'], 'contable/ventas/informe/reporte/ventas/busquedaFact', 'contable\InformeVariosController@busquedaFact')->name('ventas.reporte_varios.busquedaFact');
Route::match(['get', 'post'], 'contable/ventas/informe/reporte/ventas/masivoPrecio', 'contable\InformeVariosController@masivoPrecio')->name('ventas.reporte_varios.masivoPrecio');
//victor pilozo 
Route::get('contable/acreedores/documentos/comprobante/egreso/solicitud/create', 'contable\EgresoAcreedorController@comprobante_create_solicitud')->name('acreedores_ccreate_solicitud');
Route::post('contable/acreedores/documentos/comprobante/egreso/comprobante/store/solicitud', 'contable\EgresoAcreedorController@comprobante_store_solicitud')->name('acreedores_cstore_solicitud');

Route::get('contable/compra/comprobante/egreso/solicitud/pdf/{id}', 'contable\EgresoAcreedorController@pdfcomprobante_solicitud')->name('pdf_comprobante_egreso_solicitud');

Route::match(['get', 'post'], 'contable/acreedores/documentos/comprobante/egreso/solicitud', 'contable\EgresoAcreedorController@comprobante_index_solicitud')->name('acreedores_cegreso_solicitud');

Route::match(['get', 'post'], 'contable/acreedores/documentos/comprobante/egreso/comprobante/buscar/solicitud', 'contable\EgresoAcreedorController@buscar_solicitud')->name('comp_egreso.buscar_solicitud');

Route::get('contable/acreedores/nota/egresos/edit/solicitud/{id}', 'contable\EgresoAcreedorController@comprobante_edit_solicitud')->name('egresoa_edit_solicitud');

Route::get('contable/acreedores/documentos/nota/comprobante/egreso/anular/solicitud/{id}', 'contable\EgresoAcreedorController@anular_egreso_solicitud')->name('acreedores_anular_solicitud');
//nuevo 
Route::match(['get', 'post'], 'contable/acreedores/documentos/comprobante/egreso/anticipos', 'contable\EgresoAcreedorController@comprobante_index_anticipo')->name('acreedores_cegreso_anticipo');
Route::match(['get', 'post'], 'contable/acreedores/documentos/comprobante/egreso/comprobante/buscar/anticipo', 'contable\EgresoAcreedorController@buscar_anticipo')->name('comp_egreso.buscar_anticipo');
//reporte edad de cartera bd
Route::match(['get', 'post'], 'contable/reporte/edad/cartera', 'contable\InformesAcreedorController@reporte_edad_cartera')->name('contable.reporte_edad_cartera');
