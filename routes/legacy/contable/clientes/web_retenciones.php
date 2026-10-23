<?php
#MANTENIMIENTO RETENCIONES
Route::get('contable/clientes/retenciones', 'contable\ClientesRetencionesController@index')->name('retencion.cliente');
Route::match(['get', 'post'], 'contable/clientes/autocompletar_cliente', 'contable\ClientesRetencionesController@autocompletar_cliente')->name('retenciones.autocompletar.cliente');
Route::get('contable/clientes/getList', 'contable\ClientesRetencionesInformesController@getcliente')->name('get_sources.cliente');
Route::match(['get', 'post'], 'contable/clientes/retenciones/buscar', 'contable\ClientesRetencionesController@buscar')->name('retenciones.clientes.buscar');
Route::get('contable/clientes/comprobante/retenciones/pdf/{id}', 'contable\ClientesRetencionesController@imprimir_comprobante_retenciones')->name('pdf.comprobante.retenciones.clientes');

Route::get('contable/cliente/retenciones/crear', 'contable\ClientesRetencionesController@crear')->name('retenciones.clientes.crear');
Route::match(['get', 'post'], 'contable/cliente/retenciones/buscar/codigo', 'contable\ClientesRetencionesController@buscar_codigo')->name('retenciones.clientes.buscar.codigo');
Route::get('contable/clientes/retenciones/buscar/autocomplete', 'contable\ClientesRetencionesController@codigo')->name('retenciones.clientes.autocomplete');
Route::get('contable/clientes/retenciones/create2', 'contable\ClientesRetencionesController@create2')->name('retenciones.clientes2');
Route::post('contable/clientes/retenciones/store2', 'contable\ClientesRetencionesController@newstore')->name('retenciones.clientes.newstore');
Route::post('contable/clientes/retenciones/store', 'contable\ClientesRetencionesController@store')->name('retenciones.clientes.store');
Route::post('contable/clientes/retenciones/storeSRI', 'contable\ClientesRetencionesController@newstoreSRI')->name('retenciones.clientes.newstoreSRI');

#INFORMES
//REPORTE DE DEUDAS PENDIENTES
Route::match(['get', 'post'], 'contable/cliente/informes/deudas/pendientes', 'contable\ClientesRetencionesInformesController@deudasPendientes')->name('clientes.deudas.pendientes');
Route::match(['get', 'post'], 'contable/cliente/informes/deudas/pendientes/excel', 'contable\ClientesRetencionesInformesController@deudasPendientesExcel')->name('clientes.deudas.pendientes.excel');
//REPORTE DE SALDO DE CUENTAS POR COBRAR CLIENTES
Route::match(['get', 'post'], 'contable/cliente/informes/saldo/cuentas/cobrar', 'contable\ClientesRetencionesInformesController@saldocxc')->name('clientes.saldo.cxc');
Route::match(['get', 'post'], 'contable/cliente/informes/saldo/cuentas/excel', 'contable\ClientesRetencionesInformesController@saldosExcel')->name('clientes.saldo.cxc.excel');


//REPORTE DE CARTERAS POR PAGAR
Route::match(['get', 'post'], 'contable/cliente/informes/cartera/pagar/index', 'contable\ClientesRetencionesInformesController@clienteCarteraVencida')->name('clientes.cartera_vencida.index');


//REPORTE DE RETENCIONES
Route::match(['get', 'post'], 'contable/cliente/informe/retenciones', 'contable\ClientesRetencionesInformesController@informeRetenciones')->name('cliente.informe.retenciones');
Route::match(['get', 'post'], 'contable/cliente/informes/retenciones/excel', 'contable\ClientesRetencionesInformesController@retencionesExcel')->name('cliente.informe.retenciones.excel');

Route::get('contable/client/retenciones/anular_factura/{id}', 'contable\ClientesRetencionesController@anular')->name('retenciones.clientes.anular');
Route::get('contable/clientes/retenciones/edit/{id}', 'contable\ClientesRetencionesController@edit')->name('retenciones.clientes.edit');
// 05/03/2021
Route::get('contable/clientes/retenciones/actualizar', 'contable\ClientesRetencionesController@actualizar_fecha')->name('actualizar_fecha');

Route::get('contable/clientes/retenciones/selectsearch', 'contable\ClientesRetencionesController@buscaridUser')->name('retencion.buscaridUser');

Route::match(['get', 'post'], 'cliente/autocomplete', 'contable\NotaCreditoClienteController@autocomplete')->name('notacreditocliente.autocomplete');
Route::get('contable/retenciones/importar_porcentaje_retenciones','contable\RetencionesController@importar_porcentajes_retenciones')->name('retenciones.importar_porcentaje_retenciones');
//DEUDAS VENCIDAS
Route::match(['get', 'post'], 'contable/cliente/informes/deudas/pagar/index', 'contable\ClientesRetencionesInformesController@clienteDeudasVencidas')->name('clientes.deudas_vencidas.index');


Route::match(['get', 'post'], 'contable/cliente/informes/deudas/pagar/index_paga_vendedor', 'contable\ClientesRetencionesInformesController@clienteDeudasVencidas_vendedor')->name('clientes.deudas_vencidas.index_vendedor');
Route::match(['get', 'post'], 'contable/buscar/vendedor', 'contable\ClientesRetencionesInformesController@pagar_vendedor')->name('clientes.pagarvendedor');
