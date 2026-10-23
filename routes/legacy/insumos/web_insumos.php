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

// bodegas

Route::resource('bodega', 'Insumos\BodegaController');
Route::match(['get', 'post'], 'bodega/search', 'Insumos\BodegaController@search')->name('bodega.search');

// proveedores
Route::resource('proveedor', 'Insumos\ProveedorController');
Route::match(['get', 'post'], 'proveedores/search', 'Insumos\ProveedorController@search')->name('proveedor.search');
Route::get('proveedor/logo/{name}', 'Insumos\ProveedorController@load');
Route::post('proveedor/subir_logo', 'Insumos\ProveedorController@subir_logo')->name('proveedor.subir_logo');
Route::post('proveedor/selected', 'Insumos\ProveedorController@query_cuentas')->name('proveedor.query_cuentas');
// tipo de proveedores
Route::resource('tipo_proveedor', 'Insumos\TipoProveedorController');
Route::match(['get', 'post'], 'tipo_proveedores/search', 'Insumos\TipoProveedorController@search')->name('tipoproveedor.search');

// marca
Route::resource('marca', 'Insumos\MarcaController');
Route::match(['get', 'post'], 'marca/search', 'Insumos\MarcaController@search')->name('marca.search');

// marca
Route::resource('tipo', 'Insumos\TipoController');
Route::match(['get', 'post'], 'tipo/search', 'Insumos\TipoController@search')->name('tipo.search');

// productos
Route::resource('producto', 'Insumos\ProductoController');
// Route::match(['get', 'post'],'producto/dar_baja', 'Insumos\ProductoController@bajar_producto')->name('producto.dar_baja');
Route::match(['get', 'post'], 'producto_search/search', 'Insumos\ProductoController@search')->name('producto.search');
Route::match(['get', 'post'], 'producto/codigo_datos/', 'Insumos\ProductoController@codigo')->name('producto.codigo');
Route::match(['get', 'post'], 'producto_activo/listar/activos', 'Insumos\ProductoController@listar')->name('producto.listar');
Route::match(['get', 'post'], 'producto/buscadorcodigo', 'Insumos\ProductoController@codigo2')->name('producto.codigo2');
Route::match(['get', 'post'], 'producto/buscar/{nombre}', 'Insumos\ProductoController@nombre');
Route::match(['get', 'post'], 'producto/buscar/nombre', 'Insumos\ProductoController@nombre')->name('producto.nombre');
Route::match(['get', 'post'], 'producto/buscador/nombre', 'Insumos\ProductoController@nombre2')->name('producto.nombre2');
Route::get('producto/seguimiento/{i}', 'Insumos\ProductoController@seguimiento')->name('producto.seguimiento');
Route::get('producto/revisar/pedido/{id_pedido}', 'Insumos\ProductoController@revisar_pedido')->name('producto.revisar_pedido');
Route::get('producto/revisar/edit/pedido/new/{id_pedido}', 'Insumos\ProductoController@editar_pedido_new')->name('producto.editar_pedido_new');

Route::match(['get', 'post'], 'pedido/ingreso/verificar/cod/siempre', 'Insumos\ProductoController@verificarCodSiempre')->name('insumos.producto.verificarCodSiempre');

Route::match(['get', 'post'], 'producto/revisar/edit/pedido/new/update', 'Insumos\TransitoController@editar_pedido_new_update')->name('producto.editar_pedido_new_update');

Route::resource('despacho', 'Insumos\DespachoController');

Route::match(['get', 'post'], 'producto/buscadorcodigo/producto', 'contable\CompPedidosRealizadosController@codigo2')->name('pedido_realizados.codigo2');

Route::match(['get', 'post'], 'producto/buscadorcodigo/buscarProductos', 'Insumos\ProductoController@buscarProductos')->name('pedido_realizados.buscarProductos');

// buscarProductos

// productos
Route::resource('ingreso_producto', 'Insumos\IngresoController');
Route::match(['get', 'post'], 'ingreso/search', 'Insumos\IngresoController@search')->name('ingreso_producto.search');
Route::post('producto/ingresodato', 'Insumos\IngresoController@formulario')->name('ingreso.formulario');
Route::post('producto/ingresar/informacion/', 'Insumos\IngresoController@guardar')->name('ingreso.guardar');
Route::post('vt_producto/ingresodato/buscador/serie', 'Insumos\IngresoController@formulario_serie')->name('ingreso.formulario_serie');

// codigo de barras
Route::match(['get', 'post'], 'codigo/barras/', 'Insumos\ProductoController@codigo')->name('codigo.barra');
Route::match(['get', 'post'], 'codigo/barras/imprimir/{id}', 'Insumos\ProductoController@imprimirbarras')->name('barra.generar');
Route::match(['get', 'post'], 'codigo/barras/pedido/{id}', 'Insumos\ProductoController@pedido')->name('pedido.seguimiento.codigo_barras');

Route::match(['get', 'post'], 'codigo/barras/reportePedido/excel/{id}', 'Insumos\ProductoController@reportePedido')->name('insumos.producto.reportePedido');

Route::match(['get', 'post'], 'codigo/barras/reportePedido/masivo/excel', 'Insumos\ProductoController@reportePedidoMasivo')->name('insumos.producto.reportePedidoMasivo');

// productos en transito
Route::resource('transito', 'Insumos\TransitoController');
Route::match(['get', 'post'], 'producto/transito/nombre_encargado/', 'Insumos\TransitoController@nombre')->name('transito.nombre');
Route::match(['get', 'post'], 'producto/transito/nombre_encargado2/', 'Insumos\TransitoController@nombre2')->name('transito.nombre2');
Route::match(['get', 'post'], 'producto/transito/searching/', 'Insumos\TransitoController@search')->name('transito.searching');
Route::get('transito/insumo/pedido/{id}', 'Insumos\TransitoController@modal')->name('transito.modals');
Route::match(['get', 'post'], 'producto/transito/codigo/', 'Insumos\TransitoController@codigo')->name('transito.codigo');

// excel transito 1/04/2021
Route::get('transito/descarga/excel', 'contable\TipoTarjetaController@descarga_excel')->name('transito_excel');

// descagar reporte en excel
Route::get('producto/descarga/archivo', 'Insumos\ProductoController@reporte')->name('producto.reporte');
Route::get('producto/dar/baja/', 'Insumos\ProductoController@bajar_producto')->name('producto_dar_baja');
Route::match(['get', 'post'], 'reporte/insumo/dado_de_baja', 'Insumos\ReportesController@reporte_dar_baja')->name('reporte.dar_baja');
Route::match(['get', 'post'], 'insumos/reporte/descargar_dar_baja', 'Insumos\ReportesController@descargar_dar_baja')->name('descarga.dar_baja.index');

Route::match(['get', 'post'], 'producto/dar_baja/codigo/', 'Insumos\ProductoController@codigo_baja')->name('producto.codigo_baja');
Route::post('producto/dar/baja/guardar/', 'Insumos\ProductoController@guardar_bajar_producto')->name('producto_dar_baja_guardar');

Route::post('producto/uso/paciente/buscar', 'Insumos\TransitoController@serie_enfermero')->name('transito.serie_enfermero');
Route::post('producto/uso/paciente_equipo/buscar', 'Insumos\TransitoController@serie_enfermero_equipo')->name('transito.serie_enfermero_equipo');
Route::get('producto/uso/paciente_equipo/eliminar/{id}', 'Insumos\TransitoController@eliminar_equipo')->name('transito.eliminar_equipo');

Route::match(['get', 'post'], 'producto/dar/baja/descontar/{cant}/{tipo}/{id_prod}/{serie}/{bodega}/{pedido}/{f_vencimiento}/{lote}', 'Insumos\ProductoController@descontar')->name('producto.descontar');
Route::post('producto/borrar', 'Insumos\ProductoController@borrar')->name('producto.borrar');

Route::resource('equipo', 'Insumos\EquipoController');
Route::match(['get', 'post'], 'equipo/search', 'Insumos\EquipoController@search')->name('equipo.search');

Route::match(['get', 'post'], 'producto/reporte/bodega', 'Insumos\ReportesController@reporte_bodega')->name('reporte.reporte_bodega');

Route::match(['get', 'post'], 'producto/buscador/master', 'Insumos\ReportesController@buscador_master')->name('reporte.buscador_master');
// excel master cambie xq estaba duplicado
Route::match(['get', 'post'], 'reporte/master/insumos/excel', 'Insumos\ReportesController@reporte_master')->name('reporte.reporte_master_insumos');

Route::match(['get', 'post'], 'producto/buscador/descargar/master', 'Insumos\ReportesController@reporte_master')->name('reporte.reporte_master');

Route::match(['get', 'post'], 'producto/reporte/caducado', 'Insumos\ReportesController@reporte_caducado')->name('reporte.reporte_caducado');

Route::match(['get', 'post'], 'producto/descarga/reporte/producto', 'Insumos\ReportesController@reporte_producto_bodega')->name('reporte.reporte_producto_bodega');

Route::match(['get', 'post'], 'producto/descarga/reporte/caducado', 'Insumos\ReportesController@reporte_producto_caducado')->name('reporte.reporte_producto_caducado');

Route::match(['get', 'post'], 'insumos/reporte/caducado', 'Insumos\ReportesController@buscar_caducado')->name('reporte.buscar_caducado');

Route::get('producto/eliminar/pedido/{id}', 'Insumos\IngresoController@eliminar_pedido')->name('ingreso.eliminar_pedido');
Route::post('producto/eliminar/ingreso_clave/', 'Insumos\IngresoController@eliminar_clave')->name('ingreso.eliminar_clave');

Route::match(['get', 'post'], 'reporte/producto/buscador_usos', 'Insumos\ReportesController@buscador_usos')->name('reporte.buscador_usos');
Route::match(['get', 'post'], 'reporte/producto/buscador_usos_farmacia', 'Insumos\ReportesController@buscador_usos_farmacia')->name('reporte.buscador_usos_farmacia');
Route::match(['get', 'post'], 'reporte/producto/buscador_usos/excel', 'Insumos\ReportesController@reporte_usos_productos')->name('reporte.reporte_usos_productos');
Route::match(['get', 'post'], 'reporte/producto/buscador_usos_farmacia/excel', 'Insumos\ReportesController@reporte_usos_productos_farmacia')->name('reporte.reporte_usos_productos_farmacia');
Route::match(['get', 'post'], 'reporte/equipo/buscador_usos', 'Insumos\ReportesController@buscador_usos_equipo')->name('reporte.buscador_usos_equipo');

Route::match(['get', 'post'], 'reporte/producto/buscador_usos_excel', 'Insumos\ReportesController@buscador_usos_excel')->name('reporte.buscador_usos_excel');

Route::match(['get', 'post'], 'reporte/equipo/buscador_usos_excel', 'Insumos\ReportesController@buscador_equipo_excel')->name('reporte.buscador_equipo_excel');

Route::get('producto/editar/pedido/{id}', 'Insumos\IngresoController@editar_pedido')->name('ingreso.editar_pedido');
Route::post('producto/ingresar/actualizar/', 'Insumos\IngresoController@actualizar_pedido')->name('ingreso.actualizar_pedido');
Route::post('producto/ingresar/create/Fact', 'Insumos\IngresoController@generar_factura')->name('ingreso.generar_facturas');
Route::post('producto/ingresar/store/pedidos', 'Insumos\IngresoController@store_new')->name('ingreso.store_new');
Route::get('producto/unico/imprimir/barra/{id}', 'Insumos\ProductoController@imprimir_barra')->name('imprimir.barras_unico');
Route::get('producto/equipo/imprimir/barra/{id}', 'Insumos\EquipoController@imprimir_barra')->name('imprimir.barras_unico_equipo');
Route::get('producto/buscador/usos/modal/{id}', 'Insumos\ReportesController@modal_detalle')->name('reporte.modal_detalle');

Route::match(['get', 'post'], 'proveedor/validar/ruc', 'Insumos\ProveedorController@validar_ruc')->name('proveedor.validar_ruc');

// plantillas insumos
Route::match(['get', 'post'], 'insumos/plantillas', 'Insumos\PlantillaController@index')->name('plantilla.index');
Route::get('insumos/plantillas/crear', 'Insumos\PlantillaController@crear_plantilla')->name('plantilla.crear');
Route::post('insumo/items', 'Insumos\PlantillaController@buscar_item')->name('planilla.find_item');
Route::post('insumo/items/buscar/productos', 'Insumos\PlantillaController@buscar_item_producto')->name('planilla.buscar_item_producto');
Route::post('insumo/id_item', 'Insumos\PlantillaController@agregar_id_item')->name('planilla.find_id_item');
Route::post('insumos/plantillas/guardar', 'Insumos\PlantillaController@guardar')->name('insumo_planilla.guardar');
Route::get('insumos/plantillas/editar/{id}', 'Insumos\PlantillaController@edit')->name('plantilla.edit');
Route::post('insumos/plantillas/actualizar/{id}', 'Insumos\PlantillaController@update')->name('plantilla.update');
Route::get('insumos/plantillas/item_lista/{id}', 'Insumos\PlantillaController@item_lista')->name('plantilla.item_lista');
Route::get('insumos/plantillas/item_lista2/{id}/{hcid}/{tipo}', 'Insumos\PlantillaController@item_lista2')->name('plantilla.item_lista2');
Route::match(['get', 'post'], 'insumos/plantillas/buscar', 'Insumos\PlantillaController@buscar')->name('plantilla.buscar');
Route::get('insumo/plantilla/eliminar_plantilla/{id}', 'Insumos\PlantillaController@eliminar_plantilla')->name('plantilla.eliminar_plantilla');

// plantilla procededimiento control
Route::get('insumos/plantillas/procedimiento/control', 'Insumos\PlantillaControlController@index')->name('plantilla_procedimiento.index');
Route::get('insumos/plantillas/procedimiento/crear', 'Insumos\PlantillaControlController@create')->name('plantilla_procedimiento.crear');
Route::post('insumos/plantillas/save', 'Insumos\PlantillaControlController@save')->name('plantilla_procedimiento.save');
Route::get('insumos/plantillas/procedimiento/editar/{id}', 'Insumos\PlantillaControlController@edit')->name('plantilla_procedimiento.edit');
Route::post('insumos/plantillas/procedimiento/actualizar/', 'Insumos\PlantillaControlController@update')->name('plantilla_procedimiento.update');
Route::get('insumos/plantillas/procedimiento/lista/{id}', 'Insumos\PlantillaControlController@item_lista')->name('plantilla_procedimiento.item_lista');
Route::match(['get', 'post'], 'insumos/plantillas/procedimiento/buscar', 'Insumos\PlantillaControlController@buscar')->name('plantilla_procedimiento.buscar');

Route::match(['get', 'post'], 'insumos/plantillas/save/subir', 'Insumos\PlantillaControlController@masivo_plantilla_costo')->name('plantilla_procedimiento.save_masivo');

Route::match(['get', 'post'], 'insumos/plantillas/procedimiento/buscar/producto', 'Insumos\PlantillaControlController@buscar_producto')->name('plantilla_procedimiento.buscar_producto');

// masivo consecion
Route::match(['get', 'post'], 'insumos/consecion_detalle', 'Insumos\IngresoController@consecion_detalle')->name('ingreso.consecion_detalle');

Route::get('transito/datails/{id_producto}', 'Insumos\TransitoController@getData')->name('trans.getData');
// guardar_item_plantilla 1/04/2021
Route::get('enfermeria/insumos/guardar_productos', 'Insumos\PlantillaController@guardar_productos')->name('guardar_productos_nuevo');

Route::get('producto/ingreso/conglomerada', 'Insumos\IngresoController@conglomerada')->name('ingreso.conglomerada');
Route::get('producto/ingreso/details', 'Insumos\IngresoController@details')->name('ingreso.details');
Route::get('producto/eliminar/conglomerada/{id}', 'Insumos\IngresoController@eliminar_conglomerada')->name('ingreso.eliminar.conglomerada');
Route::post('producto/eliminar/conglomerada/ingreso_clave/', 'Insumos\IngresoController@eliminar_conglomerada_clave')->name('ingreso.conglomerada.eliminar_clave');

// kardex
Route::get('insumos/kardex', 'Insumos\KardexController@index')->name('insumos.kardex.index');
Route::match(['get', 'post'], 'insumos/kardex/show', 'Insumos\KardexController@show')->name('insumos.kardex.show');
Route::get('insumos/existencias', 'Insumos\InventarioController@index')->name('insumos.inventario.index');
Route::match(['get', 'post'], 'insumos/existencias/show', 'Insumos\InventarioController@show')->name('insumos.inventario.show');
Route::get('insumos/busqueda', 'Insumos\InventarioController@busqueda')->name('insumos.inventario.busqueda');
Route::match(['get', 'post'], 'insumos/busquedaserie', 'Insumos\InventarioController@busquedaserie')->name('insumos.inventario.busqueda.serie');
Route::match(['get', 'post'], 'insumos/kardex/exportar/nuevo', 'Insumos\KardexController@exportar')->name('insumos.kardex.exportar');

/* Inventario Serie */
Route::get('insumos/existencias/serie', 'Insumos\InventarioSerieController@index')->name('insumos.inventario_serie.index');
Route::match(['get', 'post'], 'insumos/existencias/serie/show', 'Insumos\InventarioSerieController@show')->name('insumos.inventario_serie.show');
Route::get('insumos/serie/busqueda', 'Insumos\InventarioSerieController@busqueda')->name('insumos.inventario_serie.busqueda');

Route::match(['get', 'post'], 'transito/index/pedido', 'Insumos\TransitoController@transito')->name('transito.index_transito');
Route::match(['get', 'post'], 'transito/index/excel_transproducc', 'Insumos\TransitoController@excel_transproducc')->name('transito.excel_transproducc');
Route::match(['get', 'post'], 'transito/index/pdf/{id}', 'Insumos\TransitoController@pdf_transito')->name('transito.pdf_transito');

Route::match(['get', 'post'], 'transito/busqueda/pedido', 'Insumos\TransitoController@detalleTransito')->name('transito.detalle_transito');

Route::match(['get', 'post'], 'transito/pedido/newupdate', 'Insumos\TransitoController@updatenew')->name('transito.newupdate');
Route::get('transito/edit/pedido/{id}', 'Insumos\TransitoController@editnew')->name('transito.editnew');
Route::get('transito/modal/pedido', 'Insumos\TransitoController@modal_new')->name('transito.modalnew');
Route::get('transito/show/source', 'Insumos\TransitoController@showSource')->name('transito.showSource');
Route::get('transito/html/source', 'Insumos\TransitoController@htmlSource')->name('transito.htmlSource');
Route::get('transito/html/source/lote', 'Insumos\TransitoController@htmlSource_lote')->name('transito.htmlSource_lote');
Route::get('transito/html/source/pedido', 'Insumos\TransitoController@htmlSourcePedido')->name('transito.htmlSource.pedido');
Route::post('transito/modal/store', 'Insumos\TransitoController@storenew')->name('transito.storenew');
Route::get('transito/eliminar/transito/{id}', 'Insumos\TransitoController@eliminarTransito')->name('transito.eliminar.por_id');
Route::match(['get', 'post'], 'transito/eliminar/transito', 'Insumos\TransitoController@eliminarTransito')->name('transito.eliminar.sin_id');
Route::match(['get', 'post'], 'transito/buscar/codigo/producto', 'Insumos\TransitoController@buscarCodigoProducto')->name('transito.buscarCodigoProducto');

Route::get('compras/ingresar/nuevo/pedido/', 'Insumos\TransitoController@eliminarTransito')->name('transito.eliminar');
// CONGLOMERADA
Route::get('producto/editar/conglomerada/{id}', 'Insumos\IngresoController@editar_conglomerada')->name('ingreso.editar_conglomerada');
Route::post('producto/editar/conglomerada/update', 'Insumos\IngresoController@update_conglomerada')->name('ingreso.update_conglomerada');
Route::post('producto/editar/conglomerada/crear/orden', 'Insumos\IngresoController@crear_orden_conglomerada')->name('ingreso.crear_orden_conglomerada');
Route::post('producto/editar/conglomerada/anular/envio/orden', 'Insumos\IngresoController@anular_envio_orden')->name('ingreso.anular_envio_orden');
// CONGLOMERADA ANTERIOR
Route::get('producto/ingreso/conglomerada/anterior', 'Insumos\IngresoController@conglomeradaAnterior')->name('ingreso.conglomerada.anterior');
Route::get('producto/ingreso/conglomerada/anterior/details', 'Insumos\IngresoController@detailsConglomeradaAnterior')->name('ingreso.details.conglomerada.anterior');

// REPORTE INSUMOS
Route::get('insumos/egresoprocedimiento', 'Insumos\InsumosController@egresoprocedimiento')->name('insumos.inventario.egresoprocedimiento');
Route::match(['get', 'post'], 'insumos/egresoprocedimiento/show', 'Insumos\InsumosController@show_egresoprocedimiento')->name('insumos.inventario.egresoprocedimiento.show');
// PROCESOS INVENTARIO
Route::match(['get', 'post'], 'insumos/carga_inicial', 'Inventario\InvProcesosController@carga_inicial')->name('insumos.inventario.procesos.carga.inicial');
Route::match(['get', 'post'], 'insumos/reprocesoInventarioSerie', 'Inventario\InvProcesosController@reproceso_inventario_serie')->name('insumos.inventario.reproceso.inventario.serie');
Route::match(['get', 'post'], 'insumos/reprocesoInventarioSerieEliminados', 'Inventario\InvProcesosController@reproceso_inventario_serie_eliminados')->name('insumos.inventario.reproceso.inventario.serie.eliminados');
Route::match(['get', 'post'], 'insumos/reprocesoInventarioSerieBodegasEliminados', 'Inventario\InvProcesosController@reproceso_inventario_serie_bodegas_eliminados')->name('insumos.inventario.reproceso.inventario.serie.bodegas.eliminados');
Route::match(['get', 'post'], 'insumos/trasladoMasivo', 'Inventario\InvProcesosController@trasladoMasivo')->name('insumos.inventario.traslado.masivo');
Route::match(['get', 'post'], 'insumos/reprocesaRoxicaina', 'Inventario\InvProcesosController@reprocesaRoxicaina')->name('insumos.inventario.reprocesa.roxicaina');
Route::match(['get', 'post'], 'insumos/trasladoMasivoBodega1', 'Inventario\InvProcesosController@trasladoMasivoBodega1')->name('insumos.inventario.traslado.masivo.bodega1');

Route::match(['get', 'post'], 'insumos/plantillas/comprobar', 'Insumos\PlantillaControlController@comprobar')->name('plantilla.comprobar');
// PACIETE DEL DIA
Route::match(['get', 'post'], 'transito/paciente/dia/agregar/medicamento', 'Insumos\TransitoController@agregarMedicamentoPacienteDia')->name('transito.agregar.medicamento.paciente.dia');
// PDF PLANILLAS
Route::get('contable/planilla/costo/detalle/pdf/{id_venta}/{id_hc_procedimiento}', 'Insumos\PlantillaController@imprimirPlanillaCostoDetalle')->name('planilla.costo.detalle.pdf');
Route::get('contable/planilla/costo/detalle/xls/{id_venta}/{id_hc_procedimiento}', 'Insumos\PlantillaController@ExportarPlanillaCostoDetalle')->name('planilla.costo.detalle.xls');
Route::get('contable/planilla/venta/detalle/pdf/{id_venta}/{id_hc_procedimiento}', 'Insumos\PlantillaController@imprimirPlanillaVentaDetalle')->name('planilla.venta.detalle.pdf');
Route::get('contable/planilla/venta/vs/costo/detalle/pdf/{id_venta}/{id_hc_procedimiento}', 'Insumos\PlantillaController@imprimirPlanillaVentaVsCostoDetalle')->name('planilla.venta.vs.compra.detalle.pdf');
// Planillas para el sector publico NO FUNCAN LOPEZ
Route::get('contable/planilla/ventap/detalle/pdf/{id_venta}/{id_hc_procedimiento}', 'Insumos\PlantillaController@imprimirPlanillaVentapDetalle')->name('planilla.ventap.detalle.pdf');
Route::get('contable/planilla/ventap/vs/costop/detalle/pdf/{id_venta}/{id_hc_procedimiento}', 'Insumos\PlantillaController@imprimirPlanillaVentapVsCostopDetalle')->name('planilla.ventap.vs.comprap.detalle.pdf');
// TOMA FISICA INVENTARIO
Route::match(['get', 'post'], 'insumos/tomafisica/proceso/{file}/{id_bodega}', 'Insumos\TomaFisicaController@proceso')->name('insumos.tomafisica.proceso');
// PROCESOS
Route::match(['get', 'post'], 'insumos/proceso/reingreso/{id_movimiento}', 'Inventario\InvProcesosController@reingreso')->name('inventario.proceso.reingreso');
// INGRESOS Y EGRESOS VARIOS
Route::match(['get', 'post'], 'inventario/ingresos/egresos/varios', 'Inventario\InvIngresoEgresoVarioController@index')->name('inventario.ingresos.egresos.varios');
Route::get('inventario/ingresos/egresos/crear', 'Inventario\InvIngresoEgresoVarioController@crear')->name('inventario.ingresos.egresos.crear');
Route::match(['get', 'post'], 'inventario/ingresos/egresos/guardar', 'Inventario\InvIngresoEgresoVarioController@guardar')->name('inventario.ingresos.egresos.guardar');
Route::get('inventario/ingresos/egresos/editar/{id}', 'Inventario\InvIngresoEgresoVarioController@editar')->name('inventario.ingresos.egresos.editar');
Route::post('inventario/ingresos/egresos/actualizar', 'Inventario\InvIngresoEgresoVarioController@actualizar')->name('inventario.ingresos.egresos.actualizar');

Route::match(['get', 'post'], 'insumos/egresoprocedimiento/excelMaterialesUtilizados', 'Insumos\InsumosController@excelMaterialesUtilizados')->name('insumos.insumos.excelMaterialesUtilizados');
// Acta de Entrega
Route::match(['get', 'post'], 'insumos/acta_de_entrega.pdf', 'insumos\ProductoController@pdf_acta_de_entrega')->name('producto.pdf_acta_de_entrega');

Route::match(['get', 'post'], 'insumos/valida/numero/pedido', 'Insumos\IngresoController@validarNumeroPedido')->name('insumos.ingreso.validarNumeroPedido');

Route::match(['get', 'post'], 'insumos/valida/numero/pedido', 'Insumos\IngresoController@validarNumeroPedido')->name('insumos.ingreso.validarNumeroPedido');

// //////////////////////////////Carga Inicial
Route::match(['get', 'post'], 'insumos/carga/inicial/index', 'Insumos\CargaInicialController@index')->name('insumos.cargaIncial');
Route::match(['get', 'post'], 'insumos/carga/inicial/store/excel', 'Insumos\CargaInicialController@storeExcel')->name('insumos.storeExcel');
Route::match(['get', 'post'], 'insumos/carga/inicial/edit/{id}', 'Insumos\CargaInicialController@edit')->name('insumos.edit');
Route::match(['get', 'post'], 'insumos/carga/inicial/procesar', 'Insumos\CargaInicialController@procesar')->name('insumos.procesar');

Route::match(['get', 'post'], 'insumos/carga/inicial/delete/{id}', 'Insumos\CargaInicialController@delete')->name('insumos.delete');
Route::match(['get', 'post'], 'insumos/carga/inicial/crearSerie', 'Insumos\CargaInicialController@crearSerie')->name('insumos.crearSerie');

Route::match(['get', 'post'], 'insumos/carga/inicial/verificarSerieVenta', 'Insumos\CargaInicialController@verificarSerieVenta')->name('insumos.verificarSerieVenta');

Route::get('new_proforma/compras/modal', 'Insumos\ProformaInsumosController@modalTransito')->name('insumos.proformaInsumos.modal');
Route::post('new_proforma/compras/subir/excel', 'Insumos\ProformaInsumosController@subirExcel')->name('insumos.proformaInsumos.subirExcel');

Route::get('proforma/compra/pdf/nuevo/{id}', 'Insumos\ReportesController@nuevaproforma_pdf')->name('insumos.nuevo_formatoproforma');

// PPROFORMA DE INSUMOS
Route::match(['get', 'post'], 'new_proforma/transito/insumos/editar/{id}', 'Insumos\ProformaInsumosController@editarTransito')->name('transito.proformaInsumos.editarTransito');

Route::match(['get', 'post'], 'new_proforma/transito/insumos/new/editarFila', 'Insumos\ProformaInsumosController@editarFila')->name('insumos.proformaInsumos.editarFila');

Route::match(['get', 'post'], 'new_proforma/compras/insumos/updatePublica', 'Insumos\ProformaInsumosController@updatePublica')->name('insumos.proformaInsumos.updatePublica');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/updatePublicaDetails', 'Insumos\ProformaInsumosController@updatePublicaDetails')->name('insumos.proformaInsumos.updatePublicaDetails');
Route::post('new_proforma/compras/preaprobar', 'Insumos\ProformaInsumosController@preaprobar')->name('insumos.proformaInsumos.preaprobar');

Route::get('new_proforma/sin/inventario/compras/insumos', 'Insumos\ProformaInsumosController@proformaSinInventario')->name('insumos.proformaInsumos.sin_inventario');
Route::get('new_proforma/sin/inventario/compras/insumos/{id}', 'Insumos\ProformaInsumosController@proformaSinInventarioEdit')->name('insumos.proformaInsumos.sin_inventario.edit');
Route::post('new_proforma/sin/inventario/buscador/producto', 'Insumos\ProformaInsumosController@proformaSinInventarioBuscador')->name('insumos.proformaInsumos.proformaSinInventarioBuscador');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/updateDetailsSinInventario', 'Insumos\ProformaInsumosController@updateDetailsSinInventario')->name('insumos.proformaInsumos.updateDetailsSinInventario');
Route::get('proforma/compra/pdf/sin/kardex/{id}', 'Insumos\ProformaInsumosController@pdfProformaSinKardex')->name('insumos.pdfProformaSinKardex');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/observarProformaSinKardex/{id_proforma}', 'Insumos\ProformaInsumosController@observarProformaSinKardex')->name('insumos.proformaInsumos.observarProformaSinKardex');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/buscadorResponsable', 'Insumos\ProformaInsumosController@buscadorResponsable')->name('insumos.proformaInsumos.buscadorResponsable');

Route::match(['get', 'post'], 'new_proforma/compras/insumos/buscarAsientoVenta', 'Insumos\ProformaInsumosController@buscarAsientoVenta')->name('insumos.proformaInsumos.buscarAsientoVenta');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/buscarPosibleAsiento/{id}', 'Insumos\ProformaInsumosController@buscarPosibleAsiento')->name('insumos.proformaInsumos.buscarPosibleAsiento');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/guardarAsientoAnulado', 'Insumos\ProformaInsumosController@guardarAsientoAnulado')->name('insumos.proformaInsumos.guardarAsientoAnulado');

Route::get('contable/asientoventa/insumos', 'Insumos\ProformaInsumosController@asientoVentaInsumos')->name('insumos.proformaInsumos.asientoVentaInsumos');
Route::get('contable/masivo/vincular/asiento/costo/venta', 'Insumos\ProformaInsumosController@vincularAsientoVentaInsumos')->name('insumos.proformaInsumos.vincularAsientoVentaInsumos');

// ///////////modulo bodega

Route::match(['get', 'post'], 'insumos/modulo/bodega', 'Insumos\ModuloBodegaController@index')->name('modulobodega.index');
Route::match(['get', 'post'], 'insumos/modulo/bodega/store', 'Insumos\ModuloBodegaController@store')->name('modulobodega.store');

Route::match(['get', 'post'], 'insumos/modulo/bodega/usuario', 'Insumos\ModuloBodegaController@store_bodega_usuario')->name('modulobodega.store_bodega_usuario');

Route::match(['get', 'post'], 'insumos/modulo/bodega/mostrarSugerencias', 'Insumos\TransitoController@mostrarSugerencias')->name('transito.mostrarSugerencias');

Route::match(['get', 'post'], 'insumos/egreso/ingreso/pdf/{id}', 'Inventario\InvIngresoEgresoVarioController@pdf_ingreso_egreso')->name('InvIngresoEgreso.pdf_ingreso_egreso');

// //vt_ingresos_egresos
Route::match(['get', 'post'], 'vt_inventario/ingreso_egreso/listar_series', 'Inventario\InvIngresoEgresoVarioController@listar_series')->name('inv_ingreso_egreso_vario.listar_series');

// vt nuevo kardex por usos
Route::match(['get', 'post'], 'vt_kardex_usos/index/serie', 'Insumos\KardexController@vtindex_kardex_usos_series')->name('insumos.kardex.vtindex_kardex_usos_series');
Route::match(['get', 'post'], 'vt_kardex_usos/index/por_serie/{serie}', 'Insumos\KardexController@vtindex_kardex_usos_por_serie')->name('insumos.kardex.vtindex_kardex_usos_por_serie');
// Reporte de Costos Global
Route::match(['get', 'post'], 'reporte/costos/planilla_global/excel', 'Insumos\ReportesController@reporte_global_costos')->name('reporte.reporte_global_costos');
Route::match(['get', 'post'], 'reporte/costos/planilla_global/index', 'Insumos\ReportesController@index_costos_planillas')->name('reporte.index_costos_planillas');
// Jorge
Route::match(['get', 'post'], 'producto/ingreso/conglomerada/nueva', 'Insumos\IngresoController@conglomerada_nueva')->name('ingreso.conglomerada_nueva');
Route::match(['get', 'post'], 'producto/ingreso/conglomerada/nueva/consumibles', 'Insumos\IngresoController@guardar_conglomerada_nueva_consumibles')->name('ingreso.guardar_conglomerada_nueva_consumibles');
Route::match(['get', 'post'], 'producto/editar/conglomerada/nueva/{id}', 'Insumos\IngresoController@editar_conglomerada_nueva')->name('ingreso.editar_conglomerada_nueva');
Route::post('producto/guardar/conglomerada/nueva', 'Insumos\IngresoController@guardar_conglomerada_nueva')->name('ingreso.guardar_conglomerada_nueva');
// nuevo conglomerada
Route::post('nuevo_conglomerada/actualizar_item', 'Insumos\IngresoController@nvo_cgl_actualizar_item')->name('ingreso.nvo_cgl_actualizar_item');
Route::post('nuevo_conglomerada/eliminar_item', 'Insumos\IngresoController@nvo_cgl_eliminar_item')->name('ingreso.nvo_cgl_eliminar_item');
Route::get('nuevo_conglomerada/recalcular/{id}', 'Insumos\IngresoController@recalcular')->name('ingreso.recalcular');
Route::post('producto/crear/conglomerada', 'Insumos\IngresoController@guardar_pre_pedido')->name('ingreso.crear_conglomerada');
Route::match(['get', 'post'], 'producto/editar/conglomerada/nueva/pedido/{id}', 'Insumos\IngresoController@editar_conglomerada_nueva_pedido')->name('ingreso.editar_conglomerada_nueva_pedido');
Route::post('producto/eliminar/ingreso_clave/force', 'Insumos\IngresoController@eliminar_clave_force')->name('ingreso.eliminar_clave_force');

Route::match(['get', 'post'], 'new_proforma/compras/insumos/index', 'Insumos\ProformaInsumosController@index')->name('insumos.proformaInsumos.index');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/store', 'Insumos\ProformaInsumosController@store')->name('insumos.proformaInsumos.store');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/editar/{id}', 'Insumos\ProformaInsumosController@editar')->name('insumos.proformaInsumos.editar');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/updateDetails', 'Insumos\ProformaInsumosController@updateDetails')->name('insumos.proformaInsumos.updateDetails');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/buscador', 'Insumos\ProformaInsumosController@buscador')->name('insumos.proformaInsumos.buscador');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/updateCabecera', 'Insumos\ProformaInsumosController@updateCabecera')->name('insumos.proformaInsumos.updateCabecera');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/buscadorClientes', 'Insumos\ProformaInsumosController@buscadorClientes')->name('insumos.proformaInsumos.buscadorClientes');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/aprobarProforma', 'Insumos\ProformaInsumosController@aprobarProforma')->name('insumos.proformaInsumos.aprobarProforma');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/observarProforma/{id_proforma}', 'Insumos\ProformaInsumosController@observarProforma')->name('insumos.proformaInsumos.observarProforma');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/delete', 'Insumos\ProformaInsumosController@delete')->name('insumos.proformaInsumos.delete');
Route::get('new_proforma/compras/insumos/imprimir/{id}', 'Insumos\ProformaInsumosController@imprimir')->name('insumos.proformaInsumos.imprimir');
Route::match(['get', 'post'], 'new_proforma/compras/insumos/excel', 'Insumos\ProformaInsumosController@excelproforma')->name('insumos.proformaInsumos.excelproforma');

Route::post('nuevo_conglomerada/eliminar_items_check', 'Insumos\IngresoController@nvo_cgl_eliminar_items_check')->name('ingreso.nvo_cgl_eliminar_items_check');
Route::match(['get', 'post'], 'new_proforma/compras/seleccionar', 'Insumos\ProformaInsumosController@seleccionarProforma')->name('insumos.proformaInsumos.seleccionar');
// Reverso de Planilla
Route::match(['get', 'post'], 'insumos/reverso/planilla_pac/index', 'Insumos\IngresoController@index_reverso')->name('insumos.index_reverso');
Route::match(['get', 'post'], 'insumos/reverso/planilla_pac', 'Insumos\IngresoController@reverso_planilla')->name('insumos.reverso_planilla');
Route::get('reverso_planilla_por_procedimiento/{id}', 'Insumos\IngresoController@reverso_planilla_por_procedimiento')->name('insumos.reverso_planilla_por_procedimiento');

// Reporte de Costos Vs Ventas
Route::match(['get', 'post'], 'reporte/contable/costos_vs_ventas/index', 'contable\ReporteCostosVentasController@index_costos_ventas')->name('reportecostosventas.index_costos_ventas');

Route::match(['get', 'post'], 'new_proforma/compras/pdf_proforma/{id}', 'Insumos\ProformaInsumosController@pdf_proforma')->name('insumos.proformaInsumos.pdf_proforma');
Route::get('nuevo/reporte/plantilla/descargar', 'Insumos\CargaInicialController@nuevo_reporte_plantilla')->name('insumos.nuevo_reporte_plantilla');

Route::get('ins_labs/corrige/Kxu', 'ImportarController@insumos_labs_kxu')->name('importar.insumos_labs_kxu');

Route::get('ins_labs/corrige/Kxu/series_kxu', 'ImportarController@actualiza_serie_human')->name('importar.actualiza_serie_human');
// Masivo Nuevo Kardex
Route::get('ins_labs/nuevo_kardex/kxu/{id}', 'Insumos\TransitoController@masivo_kardex_nuevo')->name('transito.masivo_kardex_nuevo');

Route::get('ins_labs/nuevo_kardex/kxu/reproceso/cantidad', 'Insumos\TransitoController@actualiza_kxu')->name('transito.actualiza_kxu');
Route::get('ins_labs/nuevo_kardex/kxu/reproceso/generar_kxu', 'Insumos\TransitoController@proceso_crear_kardex_nuevo')->name('transito.proceso_crear_kardex_nuevo');

// Reporte de Costos Vs Ventas
// Route::match(['get', 'post'],'reporte/contable/costos_vs_ventas/index', 'Contable\ReporteCostosVentasController@index_costos_ventas')->name('reportecostosventas.index_costos_ventas');
Route::get('contable/precio/producto/index', 'contable\PrecioProductoController@index')->name('insumos.precioProducto.index');
Route::get('contable/precio/producto/buscador', 'contable\PrecioProductoController@buscador')->name('insumos.precioProducto.buscador');

// reporte de precios
Route::get('reporte/contable/precios/index', 'Insumos\ReportesController@precios_gastritech_index')->name('reportes.precio_index');
Route::match(['get', 'post'], 'reporte/contable/precios/excel', 'Insumos\ReportesController@excel_gastritech')->name('reporte.excel_precios');
Route::post('reporte/contable/precios/buscador', 'Insumos\IngresoController@buscadorgastritech')->name('precioproducto.buscador');
Route::match(['get', 'post'], 'masivo/guardar/id_pedido/inventario_serie', 'ImportarController@masivoguardar_id_cab')->name('importar.masivoguardar_id_cab');

Route::match(['get', 'post'], 'producto/descarga/reporte/listado_productos', 'Insumos\ProductoController@reporte_listado_productos')->name('producto.reporte_listado_productos');
Route::post('transito/guia_remision/store', 'Insumos\TransitoController@store_guia_remision')->name('transito.store_guia_remision');
Route::post('transito/guia_remision/modal', 'Insumos\TransitoController@modal_guia_remision')->name('transito.modal_guia_remision');

Route::post('transito/guia_remision/anular', 'insumos\TransitoController@anular_guia')->name('transito.anular_guia');
// ejemplo de excel
Route::match(['get', 'post'], 'new_proforma/compras/insumos/excel1', 'Insumos\ProformaInsumosController@excelproforma1')->name('insumos.proformaInsumos.excelproforma1');

// Reporte de Productos para proforma
Route::match(['get', 'post'], 'reporte/producto/nuevo_uso_productos/profoma_excel', 'Insumos\ReportesController@reporte_uso_productos_proforma')->name('reporte.reporte_uso_productos_proforma');
Route::match(['get', 'post'], 'reporte/producto/nuevo_consumibles/profoma_excel', 'Insumos\ReportesController@reporte_consumibles_proforma')->name('reporte.reporte_consumibles_proforma');

// Masivo carga incial
Route::get('nuevo/carga/inicial/productos/corregir_producto', 'Insumos\CargaInicialController@corregirDescripcionProducto')->name('insumos.corregirDescripcionProducto');
Route::get('nuevo/carga/inicial/productos/corregirFechaVenceProducto/{id}', 'Insumos\CargaInicialController@corregirFechaVenceProducto')->name('insumos.corregirFechaVenceProducto');

/* Alex Mite Reporte de Kardex nuevo */
Route::get('nuevo/modulo/kardex', 'nuevo_insumos\KardexNuevoController@index')->name('nuevo_modulo_karex.index');
Route::match(['get', 'post'], 'g', 'nuevo_insumos\KardexNuevoController@show')->name('nuevo_modulo_karex.show');
Route::match(['get', 'post'], 'nuevo/modulo/usos/index/serie', 'nuevo_insumos\KardexNuevoController@vtindex_kardex_usos_series')->name('nuevo_modulo_karex.usos_series');
Route::match(['get', 'post'], 'nuevo/kardex/usovt_kardex_usos/uso/{serie}', 'nuevo_insumos\KardexNuevoController@vtindex_kardex_usos_por_serie')->name('nuevo_modulo_karex.vtindex_kardex_usos_por_serie');
Route::get('nuevo/modulo/detalle/producto', 'nuevo_insumos\KardexNuevoController@detalle_kardex')->name('detalle_kardex.detalle');

Route::get('vtpedido/recalcular/{id}', 'Insumos\IngresoController@recalcular_pedido')->name('insumos.ingreso.recalcular_pedido');

// Complaint
Route::match(['get', 'post'], 'complaint/index', 'complaint\ReporteComplaintController@index')->name('complaint.index');
Route::get('reporte/complaint', 'complaint\ReporteComplaintController@fromulario_complaint')->name('formulario.complaint');
Route::get('reporte/complaint/pdf/{id}', 'complaint\ReporteComplaintController@pdf_complaint')->name('formulario.complaint_pdf');
Route::post('reporte/complaint/guardar', 'complaint\ReporteComplaintController@guardar')->name('formulario.reporte_complaint');
Route::match(['get', 'post'], 'complaint/notificacion', 'complaint\ReporteComplaintController@notificacion')->name('notificacion.global');
Route::get('complaint/show/{id}', 'complaint\ReporteComplaintController@show')->name('complaint.show');
Route::post('complaint/buscador', 'complaint\ReporteComplaintController@buscador')->name('complaint.buscador');

// Recalcular Valor Planilla
Route::get('vtplanilla/recalcular/{id}', 'Insumos\ReportesController@reclacular_detalle_planilla')->name('insumos.reportes.reclacular_detalle_planilla');

// STOCK MINIMO
Route::match(['get', 'post'], 'insumos/suministros/listado_Stock_minimo', 'Insumos\ReportesController@index_stock_minimo')->name('insumos.reportes.index_stock_minimo');

Route::match(['get', 'post'], 'insumos/recalcular/recalcularCargaInicial', 'Insumos\CargaInicialController@recalcularCargaInicial')->name('insumos.recalcularCargaInicial');

// victor pilozo
Route::match(['get', 'post'], 'contable/pedidos/insumos', 'contable\PedidoController@pedidos_insumos')->name('PedidoController.pedidos_insumos');
Route::post('contable/pedidos/insumos/buscarProveedor', 'contable\PedidoController@buscar_proveedor')->name('PedidoController.buscar_proveedor');
Route::get('nuevo_conglomerada/recalcular_prepedido/{id}', 'Insumos\IngresoController@recalcular_prepedido')->name('ingreso.recalcular_prepedido');
// //victor pilozo
Route::get('/ordenes-compra', 'Insumos\InsumosController@index_orden_compra')->name('ordenes_compra.index');
Route::get('/ordenes-compra/create', 'Insumos\InsumosController@orden_compra')->name('ordenes_compra.create');
Route::post('/ordenes-compra', 'Insumos\InsumosController@store')->name('ordenes_compra.store');
Route::get('/ordenes-compra/{id}/edit', 'Insumos\InsumosController@edit')->name('ordenes_compra.edit');
Route::post('/ordenes-compra/{id}', 'Insumos\InsumosController@update')->name('ordenes_compra.update');
Route::get('/ordenes-compra/{id}/pdf', 'Insumos\InsumosController@pdf')->name('ordenes_compra.pdf');
Route::match(['get', 'post'], 'contable/Insumos/buscar/productos', 'Insumos\InsumosController@buscarProductos')->name('orden_buscar.productos');
// reporte caducados
Route::match(['get', 'post'], 'reporte/insumos/caducados/index', 'Insumos\ReportesController@reporteProdCaducados')->name('reporte.reporteProdCaducados');
// 28/5/2026
Route::post('carro/paro/cambiar/estado', 'Insumos\InsumosController@insumos_cambiarEstado')->name('insumos_cambiarEstado');
Route::post('carro/paro/cargar/tipo', 'Insumos\InsumosController@insumos_cargarTipo')->name('insumos_cargarTipo');
Route::post('carro/paro/guardar', 'Insumos\InsumosController@insumos_guardarForm')->name('insumos_guardarForm');
Route::match(['get', 'post'], 'carro/paro/index', 'Insumos\InsumosController@index_carro_paro')->name('insumos_index');
Route::get('carro/paro', 'Insumos\InsumosController@carro_paro')->name('insumos.carro.paro');
Route::get('carro/paro/index/calendario', 'Insumos\InsumosController@vista_calendario')->name('insumos.paro_index');
Route::get('carro/paro/index/calendario', 'Insumos\InsumosController@vista_calendario')->name('insumos.paro_index');
Route::post('carro/paro/consultar/uso', 'Insumos\InsumosController@consultar_uso')->name('insumos.consultar_uso');
Route::post('carro/paro/consultar/stock', 'Insumos\InsumosController@validar_stock')->name('insumos.validar_stock');

Route::post('carro/paro/consultar/carro/pdf', 'Insumos\InsumosController@carro_pdf')->name('insumos.carro.pdf');
Route::post('carro/paro/registro/anterior', 'Insumos\InsumosController@registro_anterior')->name('carro_paro.registro_anterior');
