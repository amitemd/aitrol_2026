<?php


Route::get('contable/cliente/comprobante/ingreso', 'contable\ComprobanteIngresoController@index')->name('comprobante_ingreso.index');
Route::get('contable/cliente/comprobante/ingresos/{id}', 'contable\ComprobanteIngresoController@edit')->name('comprobante_ingreso.edit');
Route::get('contable/cliente/comprobante/ingreso/create', 'contable\ComprobanteIngresoController@crear')->name('comprobante_ingreso.create');
Route::match(['get','post'],'contable/cliente/obtener/id', 'contable\ComprobanteIngresoController@id_cliente')->name('clientes.id_cliente');
Route::match(['get','post'],'contable/cliente/obtener/nombre', 'contable\ComprobanteIngresoController@nombre_cliente')->name('clientes.nombre_clientes');
Route::match(['get','post'],'contable/cliente/set/nombre', 'contable\ComprobanteIngresoController@datos_cliente')->name('clientes.datos_cliente');
Route::match(['get','post'],'contable/cliente/set/id', 'contable\ComprobanteIngresoController@datos_cliente2')->name('clientes.datos_cliente2');
Route::match(['get','post'],'contable/cliente/deudas', 'contable\ComprobanteIngresoController@deudas')->name('clientes.deudas');
Route::post('contable/cliente/comprobante/ingreso/store', 'contable\ComprobanteIngresoController@store')->name('comprobante_ingreso.store');
Route::post('contable/cliente/comprobante/ingreso/superavit', 'contable\ComprobanteIngresoController@superavit')->name('comprobante_ingreso.superavit');
Route::get('contable/cliente/comprobante/ingreso/pdf/{id}', 'contable\ComprobanteIngresoController@pdf_comprobante')->name('comprobante_ingreso.pdf');
Route::post('contable/cliente/comprobante/ingreso/update/{id}', 'contable\ComprobanteIngresoController@update')->name('comprobante_ingreso.update');
Route::match(['get','post'],'contable/comprobante/ingreso/anular/{id}', 'contable\ComprobanteIngresoController@anular')->name('comprobante_ingreso.anular');
Route::match(['get','post'],'contable/comprobante/ingreso/buscar', 'contable\ComprobanteIngresoController@search')->name('comp_ingreso.buscar');
Route::get('contable/comprobante/ingreso/buscar_paciente_codigo', 'contable\ComprobanteIngresoController@buscar_nombre_codigo')->name('comp_ingreso.buscar_nombre_codigo');
Route::match(['get','post'],'contable/comprobante/ingreso/banco/buscar', 'contable\ComprobanteIngresoController@cambio_tarjeta')->name('comp_ingreso.tarjeta');
#comp_ingreso varios
Route::get('contable/cliente/comprobante/ingreso_varios', 'contable\ComprobanteIngresoVariosController@index')->name('comprobante_ingreso_v.index');
Route::get('contable/cliente/comprobante/ingreso_varios/edit/{id}', 'contable\ComprobanteIngresoVariosController@edit')->name('comprobante_ingreso_v.edit');
Route::get('contable/cliente/comprobante/ingreso_varios/anular/{id}', 'contable\ComprobanteIngresoVariosController@anular')->name('comprobante_ingreso_v.anular');
Route::get('contable/cliente/comprobante/ingreso_varios/create', 'contable\ComprobanteIngresoVariosController@crear')->name('comprobante_ingreso_v.create');
Route::post('contable/cliente/comprobante/ingreso_varios/store', 'contable\ComprobanteIngresoVariosController@store')->name('comprobante_ingreso_v.store');
Route::get('contable/cliente/comprobante/ingreso/varios/pdf/{id}', 'contable\ComprobanteIngresoVariosController@pdf_comprobante')->name('comprobante_ingreso_v.pdf');
Route::match(['get','post'],'contable/comprobante/ingreso_varios/buscar', 'contable\ComprobanteIngresoVariosController@search')->name('comprobante_ingreso_v.buscar');
Route::match(['get','post'],'contable/comprobante/ingreso_varios/bancos/buscar', 'contable\ComprobanteIngresoVariosController@banco')->name('comprobante_ingreso_v.banco');

#asiento
Route::get('contable/buscar/asiento', 'contable\LibroDiarioController@buscar_asiento')->name('buscar_asiento.diario');

#nota de debito clientes
Route::get('contable/cliente/nota_debito', 'contable\NotaDebitoClienteController@index')->name('nota_cliente_debito.index');
Route::get('contable/cliente/nota_debito/buscar/cliente', 'contable\NotaDebitoClienteController@obtener_cliente')->name('nota_cliente_debito.buscar_cliente');
Route::get('contable/cliente/nota_debito/editar/{id}', 'contable\NotaDebitoClienteController@edit')->name('nota_cliente_debito.edit');
Route::get('contable/cliente/nota_debito/anular/{id}', 'contable\NotaDebitoClienteController@anular')->name('nota_cliente_debito.anular');
Route::match(['get','post'],'contable/cliente/nota_debito/buscar', 'contable\NotaDebitoClienteController@search')->name('nota_cliente_debito.search');
Route::post('contable/cliente/nota_debito/store', 'contable\NotaDebitoClienteController@store')->name('nota_cliente_debito.store');
Route::post('contable/cliente/nota_debito/newstore', 'contable\NotaCreditoClienteController@newstore')->name('nota_cliente_debito.newstore');
Route::get('contable/cliente/nota_debito/pdf/{id}', 'contable\NotaDebitoClienteController@pdf_nota')->name('nota_cliente_debito.pdf'); 
Route::get('contable/cliente/nota_debito/crear', 'contable\NotaDebitoClienteController@create')->name('nota_cliente_debito.create');

Route::post('contable/cliente/nota_debito/newGetSriParcial', 'contable\NotaCreditoClienteController@newGetSriParcial')->name('nota_cliente_debito.newGetSriParcial');
Route::match(['get','post'],'contable/cliente/nota_credito/newGetSriParcial/{id}', 'contable\NotaCreditoClienteController@newGetSriParcial')->name('nota_cliente_debito.newGetSriParcial2');

Route::post('contable/cliente/nota_credito/newGetSriParcial/{id}', 'contable\NotaCreditoClienteController@newGetSriParcial')->name('nota_cliente_debito.newGetSriParcial2');

#rubros clientes
#RUBROS ACREEDORES
Route::get('contable/cliente/mantenimiento/rubros', 'contable\RubrosClienteController@index')->name('rubros_cliente.index');
Route::get('contable/cliente/mantenimiento/rubros/crear', 'contable\RubrosClienteController@create')->name('rubros_cliente.create');
Route::post('contable/cliente/mantenimiento/rubros/store', 'contable\RubrosClienteController@store')->name('rubros_cliente.store');
Route::post('contable/cliente/mantenimiento/rubros/buscar', 'contable\RubrosClienteController@search')->name('rubros_cliente.search');
Route::get('contable/cliente/mantenimiento/rubros/editar/{codigo}', 'contable\RubrosClienteController@editar')->name('rubros_cliente.editar');
Route::post('contable/cliente/mantenimiento/rubros/actualizar/{codigo}', 'contable\RubrosClienteController@update')->name('rubros_cliente.update');
Route::post('contable/cliente/mantenimiento/rubros/buscarcodigo', 'contable\RubrosClienteController@nombre')->name('rubros_cliente.nombres');
Route::match(['get', 'post'], 'contable/cliente/mantenimiento/rubrosa/autocomplete/codigo', 'contable\RubrosClienteController@autocomplete')->name('rubros_cliente.searchcode');
Route::get('contable/cliente/rubros/codigo', 'contable\RubrosClienteController@codigo')->name('rubros_cliente.codigo');
Route::post('contable/cliente/rubros/nombre', 'contable\RubrosClienteController@nombre')->name('rubros_cliente.nombre');
Route::post('contable/cliente/rubros/nombre/buscar', 'contable\RubrosClienteController@nombre2')->name('rubros_cliente.nombre2');
Route::post('contable/cliente/rubros/codigo/nombre', 'contable\RubrosClienteController@codigo2')->name('rubros_cliente.codigo2');

#nota de credito clientes
Route::match(['get', 'post'],'contable/cliente/nota_credito/buscarClienteXId', 'contable\NotaCreditoClienteController@buscarClientexId')->name('notacredito.buscarclientexid');
Route::match(['get', 'post'],'contable/cliente/nota_credito/buscarCliente', 'contable\NotaCreditoClienteController@buscarCliente')->name('notacredito.buscarcliente');
Route::get('contable/cliente/nota_credito', 'contable\NotaCreditoClienteController@index')->name('nota_credito_cliente.index');
Route::get('contable/cliente/nota_credito/crear', 'contable\NotaCreditoClienteController@create')->name('nota_credito_cliente.create');
Route::match(['get', 'post'],'contable/cliente/nota_credito/store', 'contable\NotaCreditoClienteController@store_nota_credito')->name('nota_credito_cliente.store');
Route::post('contable/cliente/nota_credito/buscar_parametros', 'contable\NotaCreditoClienteController@buscar_parametros')->name('nota_credito_cliente.buscarparametros');
Route::post('contable/nota_credito/deudas/cliente', 'contable\NotaCreditoClienteController@obtener_deudas_cliente')->name('nota_credito_cliente.deudas');
Route::get('contable/cliente/nota_credito/editar/{id}', 'contable\NotaCreditoClienteController@edit_nota_credito')->name('nota_credito_cliente.edit');

Route::match(['get','post'],'contable/cliente/nota_credito/buscar', 'contable\NotaCreditoClienteController@search')->name('nota_credito_cliente.search');
Route::get('contable/cliente/nota_credito/exportar_excel', 'contable\NotaCreditoClienteController@exportar_excel')->name('nota_credito_cliente.exportar_excel');
Route::get('contable/cliente/nota_credito/anular/{id}', 'contable\NotaCreditoClienteController@anular')->name('nota_credito_cliente.anular');
Route::post('contable/nota_credito/obtener/total/deuda', 'contable\NotaCreditoClienteController@obtener_total_deudas')->name('obtener_total_deudas.clientes');
Route::match(['get', 'post'],'contable/cliente/nota_credito/obtener_num_fact', 'contable\NotaCreditoClienteController@obtener_num_fact')->name('notacredito.obtener_num_fact');
Route::get('contable/cliente/nota_credito/pdf/{id}', 'contable\NotaCreditoClienteController@crear_pdf_nota_credito')->name('notacredito.crear_pdf');
Route::post('contable/nota_credito/buscar/deudas/cliente', 'contable\NotaCreditoClienteController@buscar_deudas_cliente')->name('nota_cred_deudas.buscar');
Route::post('contable/nota_credito/total/deuda/cliente', 'contable\NotaCreditoClienteController@suma_deudas_clientes')->name('suma_deudas.clientes'); 
Route::match(['get', 'post'], 'contable/nota_credito_cliente/previewInforme/index', 'contable\NotaCreditoClienteController@informe_notacrecliente')->name('nota_credito_cliente.informe_notacreditoclientes');
Route::post('contable/nota_credito_cliente/previewExcelx', 'contable\NotaCreditoClienteController@excel_ncc')->name('nota_credito_cliente.excel_ncc');
Route::get('contable/nota_credito_cliente/create2', 'contable\NotaCreditoClienteController@create2')->name('nota_credito_cliente.create2');
Route::get('contable/nota_credito_cliente/inventario/crear', 'contable\NotaCreditoClienteInventarioController@create')->name('nota_credito_cliente.inventario.create');
Route::get('contable/nota_credito_cliente/inventario/facturas', 'contable\NotaCreditoClienteInventarioController@facturas')->name('nota_credito_cliente.inventario.facturas');
Route::get('contable/nota_credito_cliente/inventario/factura/{id}', 'contable\NotaCreditoClienteInventarioController@detalleFactura')->name('nota_credito_cliente.inventario.detalle');
Route::post('contable/nota_credito_cliente/inventario/guardar', 'contable\NotaCreditoClienteInventarioController@store')->name('nota_credito_cliente.inventario.store');
Route::get('contable/nota_credito_cliente/searchs', 'contable\NotaCreditoClienteController@getcomprobante')->name('nota_credito_cliente.getcomprobante');
Route::match(['get', 'post'], 'contable/nota_credito/newData', 'contable\NotaCreditoClienteController@newData')->name('nota_credito_cliente.newData2');
Route::get('contable/cliente/nota_credito/editar2/{id}', 'contable\NotaCreditoClienteController@edit_nota_credito')->name('nota_credito_cliente.edit2');

Route::match(['get', 'post'], 'contable/nota_credito_cliente/validate/factura', 'contable\NotaCreditoClienteController@validarFactura')->name('clientes.notacredit.validarFactura');


Route::match(['get', 'post'], 'contable/nota_credito_cliente/newGetSriParcial/{id_credito}', 'contable\NotaCreditoClienteController@newGetSriParcial')->name('nota_credito_cliente.newGetSriParcial');

//Route::post('contable/cliente/nota_credito/rubro/codigo', 'contable\NotaCreditoClienteController@search_rubro_codigo')->name('search_rubro.cliente');

#cruce de valores a favor
Route::get('contable/cliente/cruce_valores', 'contable\CruceClientesController@index')->name('cruce_clientes.index');
Route::get('contable/cliente/cruce_valores/edit/{id}', 'contable\CruceClientesController@edit')->name('cruce_clientes.edit');
Route::post('contable/cliente/cruce_valores/store', 'contable\CruceClientesController@store')->name('cruce_clientes.store');
Route::match(['get', 'post'], 'contable/cliente/cruce_valores/buscar', 'contable\CruceClientesController@buscar')->name('cruce_clientes.search');
Route::post('contable/cliente/cruce_valores/anticipos', 'contable\CruceClientesController@obtener_anticipos')->name('cruce_clientes.obtener_anticipos');
Route::get('contable/cliente/cruce_valores/crear', 'contable\CruceClientesController@create')->name('cruce_clientes.create');
Route::get('contable/cliente/cruce_valores/anular/{id}', 'contable\CruceClientesController@anular')->name('cruce_clientes.anular');

#bancos

Route::get('contable/cliente/bancos', 'contable\BancoClientesController@index')->name('banco_clientes.index');
Route::match(['get', 'post'], 'contable/cliente/cliente/bancos/buscar', 'contable\BancoClientesController@search')->name('banco_clientes.search');
Route::get('contable/cliente/bancos/edit/{id}', 'contable\BancoClientesController@edit')->name('banco_clientes.edit');
Route::post('contable/cliente/bancos/update/{id}', 'contable\BancoClientesController@update')->name('banco_clientes.update');
Route::post('contable/cliente/bancos/store', 'contable\BancoClientesController@store')->name('banco_clientes.store');
Route::get('contable/cliente/bancos/crear', 'contable\BancoClientesController@create')->name('banco_clientes.create');

#chequespostfechado
Route::get('contable/cliente/cheque/postfechados', 'contable\ChequesPostController@index')->name('chequespost.index');
Route::match(['get', 'post'], 'contable/cliente/cheques/postfechados/anular/{id}', 'contable\ChequesPostController@anular')->name('chequespost.anular');
Route::get('contable/cliente/cheque/postfechados/edit/{id}', 'contable\ChequesPostController@edit')->name('chequespost.edit');
Route::post('contable/cliente/cheque/postfechados/store', 'contable\ChequesPostController@store')->name('chequespost.store');
Route::get('contable/cliente/cheque/postfechados/create', 'contable\ChequesPostController@crear')->name('chequespost.create');
Route::match(['get', 'post'], 'contable/cliente/cheque/postfechados/buscar', 'contable\ChequesPostController@search')->name('chequespost.search');
Route::get('contable/cliente/cheque/postfechados/pdf/{id}', 'contable\ChequesPostController@pdf_comprobante')->name('chequespost.pdf');

#buscador por fecha
#clientes deudas vs pagos
Route::match(['get', 'post'], 'contable/cliente/deudasvspagos/index', 'contable\DeudasClienteController@index')->name('deudasvspagos.cliente');
Route::match(['get', 'post'], 'contable/cliente/deudasvspagos/cortefecha/{fecha_desde}/{fecha_hasta}', 'contable\DeudasClienteController@corteafecha');
Route::post('contable/cliente/deudasvspagos/cortefecha_', 'contable\DeudasClienteController@corteafecha')->name('cortefecha_');
Route::match(['get', 'post'], 'contable/cliente/deudasvspagos/excel', 'contable\DeudasClienteController@excel_deudas')->name('deudasvspagosc.excel');

#Saldos Iniciales Clientes
Route::get('contable/saldos_iniciales/clientes','contable\ClientesController@saldos_iniciales_cliente')->name('saldosinicialesclientes.index');
Route::get('contable/saldos_iniciales/clientes/index','contable\ClientesController@saldos_iniciales_cliente2')->name('saldosinicialesclientes.index2');
Route::match(['get', 'post'], 'contable/saldos_iniciales/clientes/buscar', 'contable\ClientesController@search_cliente')->name('saldosinicialesclientes.search_cliente');
Route::match(['get', 'post'], 'contable/saldos_iniciales/cliente/{id}', 'contable\ClientesController@anular_saldo')->name('saldosinicialesclientes.anular_saldo');
Route::post('contable/saldos_iniciales/clientes/store','contable\ClientesController@guardar_saldo_inicial_cliente')->name('saldosinicialcliente.store');
Route::match(['get', 'post'], 'contable/saldos_iniciales/clientes/autocomplete/codigo', 'contable\ClientesController@autocomplete_rub_cliente')->name('rubrocliente.searchcode');

#verificar
Route::get('contable/vent/verificar','contable\ComprobanteIngresoController@verificar')->name('ventas.verificar');

Route::match(['get','post'],'contable/anticipo/anular_anticipo_cliente/{id}', 'contable\CruceClientesController@anular_anticipo_cliente')->name('anticipo.anular_anticipo_cliente');

Route::get('contable/credito_clientes/reenviar', 'contable\NotaCreditoClienteController@getSRIParcialReenviar')->name('nota_credito_reenviar');


Route::get('contable/usuario/proceso/index', 'contable\ComprobanteIngresoController@index_proceso')->name('compraspedidos.index_proceso');
Route::match(['get', 'post'],'contable/usuario/proceso/crear', 'contable\ComprobanteIngresoController@crear_proceso')->name('compraspedidos.create');
Route::match(['get', 'post'],'contable/usuario/proceso/guardar', 'contable\ComprobanteIngresoController@guardar')->name('compraspedidos.guardar');
Route::match(['get', 'post'],'contable/usuario/proceso/editar/{id}', 'contable\ComprobanteIngresoController@editar_proceso')->name('compraspedidos.editar');
Route::match(['get', 'post'],'contable/usuario/proceso/actualizar', 'contable\ComprobanteIngresoController@actualizar_proceso')->name('compraspedidos.actualizar');

Route::post('contable/cliente/cheque/actualizar/precio', 'contable\NotaCreditoClienteController@actualizar_precio')->name('precio.update');
//15/11/2022
Route::get('contable/buscar/factura', 'contable\NotaCreditoClienteController@buscar_comprobante')->name('buscar_comprobante.nota_credito');

Route::get('notacredito/{opcion}', 'contable\NotaCreditoClienteController@index');
Route::post('notacredito/{opcion}', 'contable\NotaCreditoClienteController@index');
Route::get('contable/cliente/notaCredito/editar/cliente', 'contable\NotaCreditoClienteController@editar_cliente')->name('notaCredito.editarCliente');
Route::get('contable/nota_credito/verificar_precio', 'contable\NotaCreditoClienteController@verificar_precio')->name('notacredito.verificar_precio');
Route::match(['get','post'],'contable/cliente/notaCredito/buscador_cliente', 'contable\NotaCreditoClienteController@buscador_cliente')->name('notaCredito.buscador_cliente');



Route::get('contable/clientes/buscar/clientes', 'contable\ComprobanteIngresoController@clientessearch')->name('clientes.clientes_search');
