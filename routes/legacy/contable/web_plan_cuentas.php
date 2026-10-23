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

// consulta máster
Route::get('contable/empresa/seleccion', 'contable\Plan_CuentasController@seleccion_empresa')->name('plan_cuentas.seleccion_empresa');
Route::match(['get', 'post'], 'contable/empresa/guardar/inventario', 'contable\Plan_CuentasController@consultar_inventario')->name('plan_cuentas.consultar_inventario');
Route::post('contable/plan/cuentas/guardar/empresa', 'contable\Plan_CuentasController@guardar_empresa')->name('plan_cuentas.guardar_empresa');

Route::match(['get', 'post'], 'contable/empresa/egreso/kardex', 'contable\Plan_CuentasController@egresoKardex')->name('plan_cuentas.egresoKardex');

Route::resource('contable/contabilidad/plan_cuentas', 'contable\Plan_CuentasController');

Route::match(['get', 'post'], 'contable/productos_servicios', 'contable\Productos_ServiciosController@index')->name('productos_servicios_index');

// Crea Tarifario
Route::match(['get', 'post'], 'contable/productos_servicios/crear_tarifario/{codigo}', 'contable\Productos_ServiciosController@crear_tarifario')->name('crear_tarifario.productos');
Route::match(['get', 'post'], 'contable/productos/no_ligados', 'contable\Productos_ServiciosController@productoNoLigados')->name('productoNoLigados');

// Actualiza Productos Tarifario
Route::match(['get', 'post'], 'contable/productos/actualiza_tarifario/{id_producto}/{id_seguro}/{id_nivel}', 'contable\Productos_ServiciosController@edit_producto_tarifario')->name('actualiza_producto.tarifario');

// Actualiza Productos Tarifario Paquete
Route::match(['get', 'post'], 'contable/productos/actualiza_tarifario_paquete/{id_prod_tar_paq}/{id_prod}/{id_seguro}/{id_nivel}', 'contable\Productos_ServiciosController@edit_producto_tarifario_paquete')->name('actualiza_producto_tarifario.paquete');

Route::match(['get', 'post'], 'contable/update/producto_tarifario_paquete/{id_prod_tar_paq}', 'contable\Productos_ServiciosController@update_producto_tarifario_paquete')->name('producto_tarifario_paquete.update');

// Modal Crear Tarifario Producto
Route::match(['get', 'post'], 'contable/productos_servicios/modal_crear_tarifario/{id_prod_paq}/{codigo}/{id_paquete}/{cantidad}', 'contable\Productos_ServiciosController@modal_crear_tarifario')->name('modal_crear_tarifario.productos');

Route::match(['get', 'post'], 'contable/productos/edit_tarifario_uno/{id_producto}', 'contable\Productos_ServiciosController@edit_tarifario_uno')->name('actualiza_product_uno.tarifario');

Route::get('contable/productos_servicios/crear', 'contable\Productos_ServiciosController@crear')->name('productos_servicios_crear');
Route::match(['get', 'post'], 'contable/productos_servicios/store', 'contable\Productos_ServiciosController@store')->name('productos_servicios_store');
Route::match(['get', 'post'], 'contable/productos_servicios/buscar', 'contable\Productos_ServiciosController@search')->name('productos_servicios_search');
Route::get('contable/productos_servicios/editar/{codigo}', 'contable\Productos_ServiciosController@editar')->name('productos_servicios_editar');

// Crea el Tarifario del Producto al Paquete
Route::get('contable/productos_servicios/crea_producto_tarifario/paquete/{id_prod_paq}/{id_producto}/{id_paquete}', 'contable\Productos_ServiciosController@crea_producto_tarifario_paquete')->name('crea_producto_tarifario.paquete');

// Guarda el Tarifario del Producto al Paquete
Route::match(['get', 'post'], 'contable/store_producto_tarifario/paquete', 'contable\Productos_ServiciosController@store_producto_tarifario_paquete')->name('store_producto_tarifario.paquete');

// Elimina Producto Tarifario
Route::get('contable/anula_producto/tarifario', 'contable\Productos_ServiciosController@anula_producto_tarifario')->name('anula_producto.tarifario');

// Elimina Producto Tarifario Paquete
Route::get('contable/anula_producto_tarifario/paquete', 'contable\Productos_ServiciosController@anula_producto_tarifario_paquete')->name('anula_producto_tarifario.paquete');

// Carga Tabla Producto Tarifario Paquete
/* Route::get('contable/carga/tarifario_paquete/{id_prod}', 'contable\Productos_ServiciosController@recarga_prod_tarifario')->name('recarga_prod_tarifario.index'); */

Route::get('contable/carga/tarifario_paquete/{id_prod}/{id_paq}', 'contable\Productos_ServiciosController@recarga_prod_tarifario');

Route::get('contable/carga/tarifario_paquete', 'contable\Productos_ServiciosController@recarga_prod_tarifario')->name('recarga_prod_tarifario.index');
Route::post('contable/insumo/buscar/precio_producto', 'contable\Productos_ServiciosController@buscar_precio')->name('productos_servicios.buscar_precio');
Route::post('contable/productos_servicios/actualizar/{codigo}', 'contable\Productos_ServiciosController@update')->name('productos_servicios.update');
Route::post('contable/insumo/nombre', 'contable\Productos_ServiciosController@buscar_insumo')->name('contable_find_insumo');
Route::post('contable/insumo/equipo', 'contable\Productos_ServiciosController@buscar_producto')->name('contable_find_producto');
Route::post('contable/insumo/procedimientos', 'contable\Productos_ServiciosController@buscar_procedimientos')->name('contable_find_procedimientos');
Route::post('contable/insumo/id', 'contable\Productos_ServiciosController@agregar_id')->name('contable_find_id');
Route::post('contable/insumo/id_equipo', 'contable\Productos_ServiciosController@agregar_id_equipo')->name('contable_find_id_equipo');
Route::post('contable/insumo/id_procedimiento', 'contable\Productos_ServiciosController@agregar_id_procedimiento')->name('contable_find_id_procedimiento');
Route::post('contable/insumo/paquete', 'contable\Productos_ServiciosController@buscar_paquete')->name('contable_find_paquete');
Route::post('contable/insumo/id_paquete', 'contable\Productos_ServiciosController@agregar_id_paquete')->name('contable_find_id_paquete');
// Route::post('contable/insumo/editar_producto', 'contable\Productos_ServiciosController@editar_producto')->name('editar_producto');
// MANTENIMIENTO CLIENTE
// Route::resource('contable/clientes', 'contable\ClientesController');
Route::match(['get', 'post'], 'contable/clientes', 'contable\ClientesController@index')->name('clientes.index');
Route::get('contable/clientes/crear', 'contable\ClientesController@crear')->name('clientes.crear');
Route::post('contable/clientes/store', 'contable\ClientesController@store')->name('clientes.store');
Route::get('contable/clientes/editar/{id_cliente}', 'contable\ClientesController@editar')->name('clientes.editar');
Route::post('contable/clientes/actualizar/{id_cliente}', 'contable\ClientesController@update')->name('clientes.update');
Route::match(['get', 'post'], 'contable/clientes/buscar', 'contable\ClientesController@search')->name('clientes_search');

// MANTENIMIENTO EMPLEADO
Route::get('contable/empleados/cta_comisionsearch', 'contable\EmpleadosController@find_cta_comision')->name('empleados.find_cta_comision');
Route::resource('contable/empleados', 'contable\EmpleadosController');
Route::post('contable/empleados/store', 'contable\EmpleadosController@store')->name('empleados_store');
Route::post('contable/empleados/buscar', 'contable\EmpleadosController@search')->name('empleados_search');
Route::get('contable/empleados/editar/{id}', 'contable\EmpleadosController@editar')->name('empleados_editar');
Route::post('contable/empleados/actualizar/{id}', 'contable\EmpleadosController@update')->name('empleados_update');
// Route::get('contable/clientes/editar/{id_cliente}', 'contable\ClientesController@editar')->name('cliente_editar');
// Route::post('contable/clientes/actualizar/{id_cliente}', 'contable\ClientesController@update')->name('cliente_update');

// MANTENIMIENTO RUBROS
Route::resource('contable/rubros', 'contable\RubrosController');
Route::post('contable/rubros/store', 'contable\RubrosController@store')->name('rubros_store');
Route::post('contable/rubros/buscar', 'contable\RubrosController@search')->name('rubros_search');
Route::get('contable/rubros/editar/{codigo}', 'contable\RubrosController@editar')->name('rubros_editar');
Route::post('contable/rubros/actualizar/{codigo}', 'contable\RubrosController@update')->name('rubros_update');

// MANTENIMIENTO VENTAS
// FACTURA DE VENTAS
Route::get('contable/ventas/editar/{id}', 'contable\VentasController@editar')->name('ventas_editar');
// 4/5/2021  Subir Pdf
Route::get('contable/ventas/modalsubir', 'contable\PlantillasNominaController@modalsubir')->name('modalsubir_ventas');
Route::post('contable/ventas/subirpdf', 'contable\PlantillasNominaController@subir_pdf')->name('ventas_subirpdf');
Route::get('contable/ventas/pdf_visualizar/{id}', 'contable\PlantillasNominaController@pdf_visualizar')->name('pdf.visualizar');

Route::post('contable/ventas/buscar/cedula_ruc', 'contable\VentasController@buscar_identificacion')->name('ventas_buscar_identificacion');

Route::post('contable/ventas/buscar/cedula_vendedor', 'contable\VentasController@buscar_identificacion_vendedor')->name('vendedor.identificacion');

Route::post('contable/ventas/buscar/cedula_recaudador', 'contable\VentasController@buscar_identificacion_recaudador')->name('recaudador.identificacion');

Route::post('contable/ventas/actualiza/direccion_client', 'contable\VentasController@update_direccion')->name('update_direccion_client');
// Route::post('contable/ventas/nombre', 'contable\VentasController@buscar_nombre')->name('ventas_buscar_nombre');
Route::match(['get', 'post'], 'contable/ventas/nombre', 'contable\VentasController@buscar_nombre')->name('ventas_buscar_nombre');

Route::post('contable/ventas/buscar/codigo', 'contable\VentasController@buscar_codigo')->name('ventas_buscar_codigo');
// Route::match(['get', 'post'], 'contable/ventas/buscar/codigo', 'contable\VentasController@buscar_codigo')->name('ventas_buscar_codigo');

Route::match(['get', 'post'], 'contable/ventas/codigo', 'contable\VentasController@buscar_codigo2')->name('ventas_completa_codigo');

Route::post('contable/ventas/buscar/nombre', 'contable\VentasController@buscar_nombre2')->name('ventas_buscar_nombre2');
// Route::match(['get', 'post'], 'contable/ventas/buscar/nombre', 'contable\VentasController@buscar_nombre2')->name('ventas_buscar_nombre2');

// Obtener Secuencia Numero de Factura
Route::get('contable/ventas/num_factura', 'contable\VentasController@obtener_numero_factura')->name('numero_factura');

// Anular Factura
Route::get('contable/ventas/factura/{id}', 'contable\VentasController@anular_factura')->name('anulacion_factura');

// Permite completar la cedula del paciente
Route::match(['get', 'post'], 'contable/ventas/completa', 'contable\VentasController@completa_cedula_pacie')->name('autocomple_paciente_cedula');

// Permite Obtener Informacion del Paciente
Route::post('contable/ventas/buscar/paciente', 'contable\VentasController@buscar_paciente')->name('obtener_info_paciente');

// Buscamos los productos movimiento paciente
Route::post('contable/buscador/movimiento/paciente', 'contable\VentasController@buscar_insumos_mov')->name('ventas_buscar_insumos');

// PDF RETENCIONES
Route::get('contable/porcentaje_retencion/retenciones_cuentas_find', 'contable\PorcentajeRetencionController@find_cta_retencion')->name('porcentajeretencion.find_cta_retencion');
Route::get('contable/compra/comprobante/retenciones/{id}', 'contable\RetencionesController@imprimir_comprobante_retenciones')->name('pdf_comprobante_retenciones');
Route::post('contable/producto/buscadorcodigo', 'contable\ComprasController@codigo2')->name('compra_codigo2');
Route::post('contable/buscador/pedido/detalle', 'contable\ComprasController@buscar_pedido')->name('compra_buscar_pedido');
Route::post('contable/buscador/factura/detalle', 'contable\ComprasController@buscar_factura')->name('compra_buscar_factura');
Route::get('contable/compras/factura/{id}', 'contable\ComprasController@anular_factura_compras')->name('anulacion_factura_compras');
Route::get('contable/proveedor/codigo', 'contable\ComprasController@identificacion')->name('compra_identificacion');
Route::post('contable/proveedor/buscadorcodigo', 'contable\ComprasController@buscar_proveedor')->name('compra_buscar_proveedor');
Route::get('contable/proveedor/nombre', 'contable\ComprasController@nombre_proveedor')->name('compra_buscar_nombreproveedor');
Route::post('contable/proveedor/buscadorproveedor', 'contable\ComprasController@buscar_nombreproveedor')->name('compra_buscar_proveedornombre');
// FACTURA CONTABLE
Route::get('contable/fact_contable', 'contable\Fact_ContableController@index')->name('fact_contable_index');
Route::get('contable/fact_contable/crear', 'contable\Fact_ContableController@crear')->name('fact_contable_crear');
Route::get('contable/fact_contable/nombre', 'contable\Fact_ContableController@nombre')->name('fact_contable_nombre');
Route::get('contable/fact_contable/nombre/autocomplete', 'contable\Fact_ContableController@nombre2')->name('fact_contable_nombre2');
Route::get('contable/fact_contable/codigo', 'contable\Fact_ContableController@codigo')->name('fact_contable_codigo');
Route::post('contable/fact_contable/buscadorcodigo', 'contable\Fact_ContableController@codigo2')->name('fact_contable_codigo2');
Route::post('contable/fact_contable/store', 'contable\Fact_ContableController@store')->name('fact_contable_store');
Route::post('contable/fact_contable/storeSRI', 'contable\Fact_ContableController@storeSRI')->name('compra_store_sri');
Route::get('contable/fact_contable/editar/{id}', 'contable\Fact_ContableController@editar')->name('fact_contable_editar');
Route::get('contable/fact_contable/factura/{id}', 'contable\Fact_ContableController@anular_factura_compras')->name('anulacion_fact_contable');
Route::match(['get', 'post'], 'contable/fact_contable/buscar', 'contable\Fact_ContableController@search')->name('fact_contable_search');
Route::get('contable/fact_contable/nombre_proveedor', 'contable\Fact_ContableController@nombre_proveedor')->name('fact_contable_nombre_proveedor');
Route::match(['get', 'post'], 'contable/fact_contable/buscar/proveedor', 'contable\Fact_ContableController@buscar_proveedor')->name('fact_contable.buscar_proveedor');

// RETENCIONES MANTENIMIENTO
Route::resource('contable/porcentaje_retencion', 'contable\PorcentajeRetencionController');
Route::post('contable/porcentaje_retencion/{id}', 'contable\PorcentajeRetencionController@update')->name('porcentaje_update');
Route::match(['get', 'post'], 'contable/porcentaje/retencion/buscar', 'contable\PorcentajeRetencionController@buscar')->name('porcentaje_retencion.buscar');
// RUTA PARA TABLA DE BUSCADOR RETENCIONES
Route::match(['get', 'post'], 'contable/compras/buscar', 'contable\ComprasController@search')->name('compra_search');

// RETENCIONES BUSCADOR
Route::get('contable/buscador/proveedor/retenciones', 'contable\RetencionesController@buscar_proveedor')->name('retenciones.buscar_proveedor');

// MANTENIMIENTO ESTABLECIMIENTOS
Route::match(['get', 'post'], 'contable/establecimiento', 'contable\SucursalesController@index')->name('establecimiento.index');
Route::get('contable/establecimiento/crear', 'contable\SucursalesController@crear')->name('establecimiento.crear');
Route::post('contable/establecimiento/guardar', 'contable\SucursalesController@store')->name('establecimiento.store');
Route::get('contable/establecimiento/editar/{id}/{id_empresa}', 'contable\SucursalesController@editar')->name('establecimiento.editar');
Route::post('contable/establecimiento/update', 'contable\SucursalesController@update')->name('establecimiento.update');
Route::match(['get', 'post'], 'contable/establecimiento/buscar', 'contable\SucursalesController@buscar')->name('establecimiento.buscar');
// MANTENIMIENTO PUNTO EMISION
Route::match(['get', 'post'], 'contable/punto/emision', 'contable\CajaController@index')->name('punto_emision.index');
Route::get('contable/punto/emision/crear', 'contable\CajaController@crear')->name('puntoemision.crear');
Route::post('contable/punto/emision/guardar', 'contable\CajaController@store')->name('puntoemision.store');
Route::get('contable/punto/emision/editar/{id}/{id_empresa}', 'contable\CajaController@editar')->name('puntoemision.editar');
Route::post('contable/punto/emision/update', 'contable\CajaController@update')->name('puntoemision.update');
Route::match(['get', 'post'], 'contable/punto/emision/buscar', 'contable\CajaController@buscar')->name('puntoemision.buscar');

// MANTENIMIENTO CAJA Y BANCO
Route::match(['get', 'post'], 'contable/caja/banco', 'contable\CajaBancoController@index')->name('caja_banco.index');
Route::get('contable/caja/banco/crear', 'contable\CajaBancoController@crear')->name('caja_banco.crear');
Route::post('contable/caja/banco/detalle/grupo', 'contable\CajaBancoController@obtener_detalle_grupo')->name('caja_banco.detallegrupo');
Route::post('contable/caja/banco/guardar', 'contable\CajaBancoController@store')->name('caja_banco.store');
Route::get('contable/caja/banco/editar/{id}/{id_empresa}', 'contable\CajaBancoController@editar')->name('caja_banco.editar');
Route::post('contable/caja/banco/update', 'contable\CajaBancoController@update')->name('caja_banco.update');
Route::match(['get', 'post'], 'contable/caja/banco/buscar', 'contable\CajaBancoController@buscar')->name('caja_banco.buscar');

// RETENCIONES
Route::match(['get', 'post'], 'contable/porcentaje/retencion', 'contable\PorcentajeRetencionController@index')->name('retenciones.index');
// DIVISAS
Route::match(['get', 'post'], 'contable/divisas', 'contable\DivisasController@index')->name('divisas.index');
Route::match(['get', 'post'], 'contable/divisas/buscar', 'contable\DivisasController@buscar')->name('divisas.buscar');
Route::get('contable/divisas/crear', 'contable\DivisasController@crear')->name('divisas.crear');
Route::post('contable/divisas/guardar', 'contable\DivisasController@store')->name('divisas.store');
Route::get('contable/divisas/editar/{id}', 'contable\DivisasController@editar')->name('divisas.editar');
Route::post('contable/divisas/update', 'contable\DivisasController@update')->name('divisas.update');

// MANTENIMIENTO TIPO EMISION
Route::match(['get', 'post'], 'contable/tipo/emision', 'contable\TipoEmisionController@index')->name('tipo_emision.index');
Route::get('contable/tipo/emision/crear', 'contable\TipoEmisionController@crear')->name('tipo_emision.crear');
Route::post('contable/tipo/emision/guardar', 'contable\TipoEmisionController@store')->name('tipo_emision.store');
Route::get('contable/tipo/emision/editar/{id}', 'contable\TipoEmisionController@editar')->name('tipo_emision.editar');
Route::post('contable/tipo/emision/update', 'contable\TipoEmisionController@update')->name('tipo_emision.update');
Route::match(['get', 'post'], 'contable/tipo/emision/buscar', 'contable\TipoEmisionController@buscar')->name('tipo_emision.buscar');

// MANTENIMIENTO TIPO COMPROBANTE
Route::match(['get', 'post'], 'contable/tipo/comprobante', 'contable\TipoComprobanteController@index')->name('tipo_comprobante.index');
Route::get('contable/tipo/comprobante/crear', 'contable\TipoComprobanteController@crear')->name('tipo_comprobante.crear');
Route::post('contable/tipo/comprobante/guardar', 'contable\TipoComprobanteController@store')->name('tipo_comprobante.store');
Route::get('contable/tipo/comprobante/editar/{id}', 'contable\TipoComprobanteController@editar')->name('tipo_comprobante.editar');
Route::post('contable/tipo/comprobante/update', 'contable\TipoComprobanteController@update')->name('tipo_comprobante.update');
Route::match(['get', 'post'], 'contable/tipo/comprobante/buscar', 'contable\TipoComprobanteController@buscar')->name('tipo_comprobante.buscar');

// MANTENIMIENTO TIPO AMBIENTE
Route::match(['get', 'post'], 'contable/tipo/ambiente', 'contable\TipoAmbienteController@index')->name('tipo_ambiente.index');
Route::get('contable/tipo/ambiente/crear', 'contable\TipoAmbienteController@crear')->name('tipo_ambiente.crear');
Route::post('contable/tipo/ambiente/guardar', 'contable\TipoAmbienteController@store')->name('tipo_ambiente.store');
Route::get('contable/tipo/ambiente/editar/{id}', 'contable\TipoAmbienteController@editar')->name('tipo_ambiente.editar');
Route::post('contable/tipo/ambiente/update', 'contable\TipoAmbienteController@update')->name('tipo_ambiente.update');
Route::match(['get', 'post'], 'contable/tipo/ambiente/buscar', 'contable\TipoAmbienteController@buscar')->name('tipo_ambiente.buscar');

// MANTENIMIENTO TIPO DE PAGO
Route::match(['get', 'post'], 'contable/tipo_pago', 'contable\TipoPagoController@index')->name('tipo_pago.index');
Route::match(['get', 'post'], 'contable/tipo_pago/buscar', 'contable\TipoPagoController@buscar')->name('tipo_pago.search');
Route::get('contable/tipo_pago/editar/{id}', 'contable\TipoPagoController@editar')->name('tipo_pago.editar');
Route::post('contable/tipo_pago/actualizar', 'contable\TipoPagoController@actualizar')->name('tipo_pago.actualizar');
Route::get('contable/tipo_pago/crear', 'contable\TipoPagoController@crear')->name('tipo_pago.crear');
Route::post('contable/tipo_pago/guardar', 'contable\TipoPagoController@store')->name('tipo_pago.store');

// MANTENIMIENTO TIPO TARJETA
Route::match(['get', 'post'], 'contable/tipo_tarjeta', 'contable\TipoTarjetaController@index')->name('tipo_tarjeta.index');
Route::match(['get', 'post'], 'contable/tipo_tarjeta/buscar', 'contable\TipoTarjetaController@buscar')->name('tipo_tarjeta.search');
Route::get('contable/tipo_tarjeta/editar/{id}', 'contable\TipoTarjetaController@editar')->name('tipo_tarjeta.editar');
Route::post('contable/tipo_tarjeta/actualizar', 'contable\TipoTarjetaController@update')->name('tipo_tarjeta.actualizar');
Route::get('contable/tipo_tarjeta/crear', 'contable\TipoTarjetaController@crear')->name('tipo_tarjeta.crear');
Route::post('contable/tipo_tarjeta/guardar', 'contable\TipoTarjetaController@store')->name('tipo_tarjeta.store');

// MANTENIMIENTO PORCENTAJE IR
Route::match(['get', 'post'], 'contable/tipo/porcentaje_imp_renta', 'contable\Porcentaje_Impuesto_RentaController@index')->name('porcentaje_imp_renta.index');
Route::get('contable/tipo/porcentaje_imp_renta/crear', 'contable\Porcentaje_Impuesto_RentaController@crear')->name('porcentaje_imp_renta.crear');
Route::post('contable/tipo/porcentaje_imp_renta/guardar', 'contable\Porcentaje_Impuesto_RentaController@store')->name('porcentaje_imp_renta.store');
Route::get('contable/tipo/porcentaje_imp_renta/editar/{id}', 'contable\Porcentaje_Impuesto_RentaController@editar')->name('porcentaje_imp_renta.editar');
Route::post('contable/tipo/porcentaje_imp_renta/update', 'contable\Porcentaje_Impuesto_RentaController@update')->name('porcentaje_imp_renta.update');
Route::match(['get', 'post'], 'contable/tipo/porcentaje_imp_renta/buscar', 'contable\Porcentaje_Impuesto_RentaController@buscar')->name('porcentaje_imp_renta.buscar');

// MANTENIMIENTO LISTA DE BANCOS

// MANTENIMIENTO LISTA DE CIUDADES

// Route::resource('contable/caja_banco', 'contable\Caja_BancoController');
// Route::get('contable/banco/caja', 'contable\Caja_BancoController@index')->name('banco.index');
// Route::get('contable/banco/create', 'contable\Caja_BancoController@crear')->name('banco.create');
// Route::get('contable/banco/edit/{id}', 'contable\Caja_BancoController@edit')->name('banco.edit');
// Route::post('contable/banco/update/{id}', 'contable\Caja_BancoController@update')->name('banco.update');
// Route::post('contable/banco/guardar_caja_banco', 'contable\Caja_BancoController@guardar_caja_banco')->name('banco.guardar_caja_banco');

Route::get('contable/index', 'PaginaController@contable_index')->name('contable.index');

// AGREGAR USUARIO PUNTO EMISION
Route::match(['get', 'post'], 'contable/usuario/punto/emision', 'contable\UsuarioCajaController@index')->name('usuario_caja.index');

// RUTA CREADA PARA CAMBIO EN RETENCIONES 5 DE FEBRERO
Route::match(['get', 'post'], '/contable/retencion/buscador', 'contable\RetencionesController@buscar_tipo')->name('retenciones.buscartipo');

// / PRUEBAS....CHRISTIAN...
Route::match(['get', 'post'], '/contable/retencion/pruebas', 'contable\RetencionesController@autcom_fc')->name('retenciones.autcom_fc');

// MANTENIMIENTO PUNTO DE EMISION
Route::resource('contable/caja', 'contable\CajaController');

// BALANCE GENERALs
Route::match(['get', 'post'], 'contable/balance/general', 'contable\BalanceGeneralController@index')->name('balancegeneral.index');

// ESTADO DE RESULTADOS
Route::match(['get', 'post'], 'contable/estado/resultados', 'contable\EstadoResultadosController@index')->name('estadoresultados.legacy.index');

// LLAMADA A LA MODAL LISTA PRODUCTOS Y SERVICIOS
// Route::get('contable/ventas/modal_lista_producto/', 'contable\VentasController@obtener_lista_productos')->name('modal.lista_productos_servicios');

// CONFIGURACION PLAN DE CUENTA
Route::get('contable/configuraciones', 'contable\Plan_CuentasController@configuracion')->name('configuraciones.index');
Route::get('contable/configuraciones/editar/{id}', 'contable\Plan_CuentasController@editar_configuracion')->name('configuraciones.editar');
Route::post('contable/configuraciones/guardar', 'contable\Plan_CuentasController@guardar_configuracion')->name('configuraciones.plan_cuentas.guardar');
Route::post('contable/plan_cuentas/nuevo/elemento', 'contable\Plan_CuentasController@elementos')->name('plan_cuentas.elementos');
Route::post('contable/plan_cuentas/informacion/mostrar', 'contable\Plan_CuentasController@informacion')->name('plan_cuentas.informacion');
Route::post('contable/plan_cuentas/informacion/guardar', 'contable\Plan_CuentasController@guardar')->name('plan_cuentas.guardar');
Route::get('contable/plan_cuentas/crear/nuevo/padre/{id}', 'contable\Plan_CuentasController@nuevo_padre')->name('plan_cuentas.nuevo_padre');
Route::post('contable/plan/cuentas/guardar/padre/', 'contable\Plan_CuentasController@guardar_nuevo')->name('plan_cuentas.guardar_nuevo');
Route::match(['get', 'post'], 'contable/plan/cuentas/exportar/', 'contable\Plan_CuentasController@exportar')->name('plan_cuentas.exportar');

// LIBRO MAYOR
Route::match(['get', 'post'], 'contable/contabilidad/libro_mayor', 'contable\LibroDiarioController@libro_mayor')->name('libro_mayor.index');
Route::match(['get', 'post'], 'contable/contabilidad/libromayor', 'contable\LibroDiarioController@libromayor')->name('libromayor.index');

// Obtener sucursales de la Empresa Seleccionada
Route::post('sucursales/empresa/seleccionada', 'contable\VentasController@obtener_sucursal_empresa')->name('sucursal.empresa');

// Obtener caja de la sucursal Seleccionada
Route::post('caja/sucursal/seleccionada', 'contable\VentasController@obtener_caja_sucursal')->name('caja.sucursal');

// MANTENIMIENTO COMPROBANTE DE INGRESOS CLIENTE
/*Route::get('contable/comprobante/ingreso/clientes', 'contable\CompIngresoClientesController@index')->name('comp_ing_cliente.index');
Route::get('contable/comprobante/ingreso/clientes/crear', 'contable\CompIngresoClientesController@crear')->name('comp_ing_cliente.crear');
Route::post('contable/comprobante/ingreso/clientes/store', 'contable\CompIngresoClientesController@store')->name('comp_ing_cliente.store');
 */
// Obtener Secuencia Comprobante de Ingreso Clientes
Route::get('contable/comprobante/ingreso/clientes/numero', 'contable\CompIngresoClientesController@obtener_numero')->name('numero_comprobante.ingreso');

// Ingreso de Datos (Detalle de Valores Recibidos-Comprobante de Ingreso de Clientes)
Route::get('detalle/valores/recibido', 'contable\CompIngresoClientesController@cargar_detalle_recibido')->name('ingreso.detalles_recibido');

// Buscar Ruc o Cedula de Identificacion Cliente Comprobante de Ingreso Cliente
Route::post('contable/comprobante/ingreso/clientes/cedula_ruc', 'contable\CompIngresoClientesController@buscar_identificacion')->name('buscar_ident_comp_ing_cliente');

// Obtengo la Cedula del Vendedor Seleccionado
Route::post('contable/comprobante/ingreso/clientes/cedula_vendedor', 'contable\CompIngresoClientesController@buscar_identificacion_vendedor')->name('buscar_ident_vend_comp.ingcliente');

// Autocompleta Numero Factura Venta - Comprobante de Ingreso Clientes
Route::get('contable/comprobante/ingreso/clientes/buscar/numero_factura/autocomplete', 'contable\CompIngresoClientesController@completar_numero')->name('obtener_numero_factura');

// Buscar Factura por Numero - Comprobante de Ingreso Cliente
Route::post('contable/comprobante/ingreso/clientes/buscar_factura', 'contable\CompIngresoClientesController@buscar_factura_numero')->name('buscar_fact_numero.compIngreso');

// Obtener % de Retencion IvA
Route::post('contable/comprobante/ingreso/clientes/porcentaje_iva', 'contable\CompIngresoClientesController@calculo_porcentaje_iva')->name('obtener_porcentaje_retencion_iva');

// Obtener % de Retencion a la Fuente
Route::post('contable/comprobante/ingreso/clientes/porcentaje_retencion_fuente', 'contable\CompIngresoClientesController@calculo_porcentaje_retencion_fuente')->name('obtener_porcentaje_retencion_fuente');

// MANTENIMIENTO COMPROBANTE DE RETENCION CLIENTE
Route::get('contable/comprobante/retencion/clientes', 'contable\CompRetencionClientesController@index')->name('comp_retencion_cliente.index');

Route::get('contable/comprobante/retencion/clientes/crear', 'contable\CompRetencionClientesController@crear')->name('comp_retencion_cliente.crear');

// Obtener Secuencia Comprobante Retencion Cliente
Route::get('contable/comprobante/retencion/clientes/numero', 'contable\CompRetencionClientesController@obtener_numero')->name('numero_comprobante_retencion.cliente');

Route::post('contable/comprobante/retencion/clientes/store', 'contable\CompRetencionClientesController@store')->name('comp_retencion_cliente.store');

// Autocompleta Numero Factura Venta - Comprobante de Retencion Clientes
Route::get('contable/comprobante/retencion/clientes/buscar/numero_factura/autocomplete', 'contable\CompRetencionClientesController@completar_numero')->name('obtener_datos_factura');

// Buscar Factura por Numero - Comprobante Retencion Clientes
Route::post('contable/comprobante/retencion/clientes/buscar_factura', 'contable\CompRetencionClientesController@buscar_factura_numero')->name('buscar_fact.numero');

// Buscar Ruc o Cedula de Identificacion Cliente - Comprobante de Retencion Cliente
Route::post('contable/comprobante/retencion/clientes/cedula_ruc', 'contable\CompRetencionClientesController@buscar_identificacion')->name('buscar_identificacion_client');

// Obtener % de Retencion Iva - Comprobante de Retencion Cliente
Route::post('contable/comprobante/retencion/clientes/porcentaje_iva', 'contable\CompRetencionClientesController@calculo_porcentaje_iva')->name('obt_retenc_porcent_iva');

// Obtener % de Retencion a la Fuente - Comprobante de Retencion Cliente
Route::post('contable/comprobante/retencion/clientes/porcentaje_retencion_fuente', 'contable\CompRetencionClientesController@calculo_porcentaje_retencion_fuente')->name('obt_retenc_porc.fuent');

// MANTENIMIENTO DEPOSITO BANCARIO FACTURA DE VENTAS
Route::get('contable/deposito/bancario/fact_ventas', 'contable\Depo_Banca_Fact_VentasController@index')->name('depo_bancario_factventas.index');
Route::get('contable/deposito/bancario/fact_ventas/crear', 'contable\Depo_Banca_Fact_VentasController@crear')->name('depo_bancario_factventas.crear');
Route::post('contable/deposito/bancario/fact_ventas/store', 'contable\Depo_Banca_Fact_VentasController@store')->name('deposito_bancario_store');
// Obtener Secuencia Deposito Bancario
Route::get('contable/deposito/bancario/numero', 'contable\Depo_Banca_Fact_VentasController@obtener_numero_deposito_bancario')->name('numero_deposito_bancario');

// Autocompleta Numero Factura Venta - Deposito Bancario
Route::get('contable/deposito/bancario/fact_ventas/autocomplete', 'contable\Depo_Banca_Fact_VentasController@completar_numero')->name('obtener_num_factura_vent');

// Buscar Factura por Numero - Deposito Bancario
Route::post('contable/deposito/bancario/fact_ventas/buscar_factura', 'contable\Depo_Banca_Fact_VentasController@buscar_factura_numero')->name('numero_fact_dep_bancario');

// Generar Comprobante no Tributario Factura de Venta
Route::get('contable/ventas/comprobante/no.tributario/{id}', 'contable\VentasController@imprimir_comprobante_factura')->name('pdf_comprobante_no.tributario');

// Generar Detalle Factura
Route::get('contable/ventas/comprobante/detalle_paquete/{id}', 'contable\Factura_AgendaController@imprimir_comprobante_detalle_paquete')->name('pdf_comprobante_detalle.paquete');

// MODAL RETENCIONES FACTURA VENTA

// Retencion Factura de Venta
Route::get('contable/ventas/retenciones2/modal_retencion_venta', 'contable\RetencionVentasController@obtener_modal')->name('retencion_ventas');

// Obtener Porcentaje de Retencion Ventas
Route::post('retenciones/iva_fuente/seleccionada', 'contable\RetencionVentasController@obtener_porcent_iva_fuente')->name('iva_fuente.porcentaje');

// Obtener Codigo de Retencion
Route::post('obtener/codigo/porcentaje', 'contable\RetencionVentasController@obtener_codigo')->name('codigo.porcentaje');

// Modal Agregar Paquete
Route::match(['get', 'post'], 'contable/productos_servicios/modal_crear_paquete', 'contable\Productos_ServiciosController@modal_crear_paquete')->name('modal_crear_paquete.productos');

// Carga Tabla Producto Paquete al Iniciar
Route::get('contable/productos_servicios/tabla_prod_paquete', 'contable\Productos_ServiciosController@cargar_producto_paquete')->name('detalle.carga_paquete');
Route::get('contable/productos_servicios/tabla_prod_paquete/{id}', 'contable\Productos_ServiciosController@cargar_producto_paquete');

// Busqueda de Nombre Producto
Route::post('contable/productos_servicios/buscar_producto', 'contable\Productos_ServiciosController@buscar_producto_nombre')->name('buscar_prod.nombre');

// Agregar Producto Paquete
// Route::match(['get', 'post'], 'contable/productos_servicios/guardar_prod_paquete',
// 'contable\Productos_ServiciosController@store_produc_paquete')->name('producto.agregar_paquete');

Route::match(
    ['get', 'post'],
    'contable/productos_servicios/guardar_prod_paquete',
    'contable\Productos_ServiciosController@store_produc_paquete'
)->name('producto.agregar_paquete');

// Elimina Producto Paquete
Route::get('contable/anula_producto_paquete/item', 'contable\Productos_ServiciosController@anula_producto_paquete')->name('anula_producto.paquete');

// Agregar Precio del Producto
Route::match(
    ['get', 'post'],
    'contable/productos_servicios/guardar_prec_producto',
    'contable\Productos_ServiciosController@store_precio_produc'
)->name('producto.agregar_precio');

// Carga Tabla Precio Producto a Iniciar
Route::get('contable/productos_servicios/tabla_prod_precio', 'contable\Productos_ServiciosController@cargar_precio_producto')->name('detalle.carga_precio');
Route::get('contable/productos_servicios/tabla_prod_precio/{id}', 'contable\Productos_ServiciosController@cargar_precio_producto');

// Elimina Precio Producto
Route::get('contable/anula_producto_precio/item_prod', 'contable\Productos_ServiciosController@anula_producto_precio')->name('anula_producto.precio');

// Carga Tabla ct_orden_venta_detalle_paquete
Route::get('contable/carga/orden_detalle_paquete/{id_venta}', 'contable\Productos_ServiciosController@recarga_orden_detalle_paquete');

Route::get('contable/carga/orden_detalle_paquete', 'contable\Productos_ServiciosController@recarga_orden_detalle_paquete')->name('recarga_orden_detalle_paquete.index');

// Edita ct_orden_venta_detalle_paquete
Route::match(['get', 'post'], 'contable/edit_orden_detalle/paquete/{id_orden_det}', 'contable\Productos_ServiciosController@edit_orden_detalle_paquete')->name('actualiza_orden_detalle.paquete');

// Actualiza ct_orden_venta_detalle_paquete
Route::match(['get', 'post'], 'contable/update/orden_detalle_paquete/{id_ord_det_paq}', 'contable\Productos_ServiciosController@update_ord_detalle_paquete')->name('producto_detalle_paquete.update');

// Modal Actualiza Valor Total de Paquete con Seguro Particular en la Tabla Producto
Route::get('contable/productos_servicios/update_producto_val_paq/{id_producto}', 'contable\Productos_ServiciosController@update_val_total_paquete_part')->name('upd_total_valor.paquete');

// Guarda valor_total_paq
Route::match(['get', 'post'], 'contable/productos_servicios/store_val_tol_paq', 'contable\Productos_ServiciosController@store_total_valor_paquete')->name('valor_total_paquete.guardar');

// MODAL VISAUALIZAR ASIENTO DIARIO
Route::match(['get', 'post'], 'contable/compras/modal_estado/{id}', 'contable\LibroDiarioController@modal_estado')->name('compras.modal_estado');

// Guardad ciudad y direccion
Route::match(['get', 'post'], 'contable/ventas/guardar/ciudad', 'contable\VentasController@guardarCiudad')->name('ventas.guardarCiudad');
Route::post('sucursales/guardarCiudad/{ciudad}', 'contable\SucursalesController@guardarCiudad');
// Actializa sucursal en asiento
Route::post('contable/actualizarsucursalasiento', 'contable\GlobalesController@actualizarSucursalAsiento')->name('actualizar_sucursal_asiento');
