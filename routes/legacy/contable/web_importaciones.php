<?php

Route::get('contable/importacion/gastos/index', 'contable\GastosImportacionController@index')->name('gastosimportacion.index');
Route::get('contable/importacion/gastos/create', 'contable\GastosImportacionController@create')->name('gastosimportacion.create');
Route::post('contable/importacion/gastos/store', 'contable\GastosImportacionController@store')->name('gastosimportacion.store');
Route::get('contable/importacion/gastos/edit/{id}', 'contable\GastosImportacionController@edit')->name('gastosimportacion.edit');
Route::post('contable/importacion/gastos/update/{id}', 'contable\GastosImportacionController@update')->name('gastosimportacion.update');
Route::get('contable/importacion/gastos/eliminar/{id}', 'contable\GastosImportacionController@eliminar')->name('gastosimportacion.eliminar');

/*iMPORTACIONES */
Route::match(['get','post'],'contable/importaciones/index', 'contable\ImportacionesController@index')->name('importaciones.index');
Route::match(['get','post'],'contable/importaciones/create', 'contable\ImportacionesController@create')->name('importaciones.create');
Route::match(['get','post'],'contable/importaciones/store', 'contable\ImportacionesController@store')->name('importaciones.store');
Route::match(['get','post'],'contable/importaciones/edit/{id}', 'contable\ImportacionesController@edit')->name('importaciones.edit');
Route::match(['get','post'],'contable/importaciones/update/{id}', 'contable\ImportacionesController@update')->name('importaciones.update');


Route::match(['get','post'],'contable/importaciones/view/{id}', 'contable\ImportacionesController@viewImportaciones')->name('importaciones.view');


Route::match(['get','post'],'contable/importaciones/create_recibo/{id}', 'contable\ImportacionesController@create_recibo')->name('importaciones.create_recibo');
Route::match(['get','post'],'contable/importaciones/store_recibo', 'contable\ImportacionesController@store_recibo')->name('importaciones.store_recibo');

Route::match(['get','post'],'contable/importaciones/create_orden/{id}', 'contable\ImportacionesController@create_orden')->name('importaciones.create_orden');
Route::match(['get','post'],'contable/importaciones/store_orden', 'contable\ImportacionesController@store_orden')->name('importaciones.store_orden');

Route::match(['get','post'],'contable/importaciones/pdf_importaciones/{id}', 'contable\ImportacionesController@pdf_importaciones')->name('importaciones.pdf_importaciones');
Route::get('importaciones/documento/{id}', 'ImportacionesController@index')->name('index_importaciones');
Route::match(['get','post'],'importaciones/save', 'ImportacionesController@save')->name('save_importaciones');

// GASTOS FACTURA

Route::get('contable/importacion/ingreso_factura/{id}','contable\GastosImportacionController@ingreso_factura')->name('gastosimportacion.ingreso_factura');

Route::match(['get','post'],'contable/importacion/edit_factura/{id_factura}','contable\GastosImportacionController@edit_factura')->name('gastosimportacion.edit_factura');
Route::post('contable/importacion/store_factura','contable\GastosImportacionController@store_factura')->name('gastosimportacion.store_factura');
//excel
Route::get('importaciones/excel/{id}', 'ImportacionesController@excel')->name('index_excel');

//LIQUIDACION 

Route::get('contable/importaciones/liquidacion/{id}','contable\ImportacionesController@liquidacion')->name('importaciones.liquidacion');
Route::match(['get','post'],'contable/importaciones/store_liquidacion', 'contable\ImportacionesController@store_liquidacion')->name('importaciones.store_liquidacion');
Route::match(['get','post'],'contable/importaciones/store_pais', 'contable\ImportacionesController@store_pais')->name('importaciones.store_pais');


Route::get('contable/importaciones/crear_agrupada','contable\ImportacionesController@crear_agrupada')->name('importaciones.crear_agrupada');
Route::get('contable/importaciones/store_agrupada','contable\ImportacionesController@store_agrupada')->name('importaciones.store_agrupada');

Route::match(['get','post'],'contable/importaciones/store/compras/kardex', 'contable\ImportacionesController@store_kardex')->name('importaciones.store_compras_importacion');


Route::match(['get','post'],'contable/ingreso/arreglar/secuencia', 'contable\ImportacionesController@arreglarSecuencia')->name('importaciones.arreglar_secuencia');

// subir archivos
Route::get('contable/importaciones/subir_archivo/{id}', 'contable\GastosImportacionController@subir_archivo')->name('gastosimportacion.subir_archivo');
Route::post('contable/importaciones/guardar_archivo', 'contable\GastosImportacionController@guardar_archivo')->name('gastosimportacion.guardar_archivo');
Route::get('contable/importaciones/archivo_descarga/{name}', 'contable\GastosImportacionController@archivo_descarga')->name('gastosimportacion.archivo_descarga');
Route::get('contable/importaciones/eliminar_archivo/{id}', 'contable\GastosImportacionController@eliminar_archivo')->name('gastosimportacion.eliminar_archivo');

Route::get('contable/importaciones/ver/anteprima', 'contable\GastosImportacionController@anteprima')->name('gastosimportacion.anteprima');

Route::match(['get', 'post'], 'contable/importaciones/buscar/proveedor', 'contable\ImportacionesController@buscarProveedor' )->name('importaciones.proveedores');
Route::match(['get', 'post'], 'contable/importaciones/buscar/productos', 'contable\ImportacionesController@buscarProductos')->name('importaciones.productos');
Route::get('contable/importaciones/producto-detalle/{id}', 'contable\ImportacionesController@productoDetalle')->name('importaciones.producto_detalle');
Route::match(['get', 'post'], 'contable/importaciones/buscar/direccion', 'contable\ImportacionesController@buscarDireccion')->name('importaciones.direccion');
Route::match(['get', 'post'], 'contable/importaciones/buscar/pais', 'contable\ImportacionesController@buscarPais')->name('importaciones.pais');
//resumen faustito 
//Route::get('importaciones/documento', 'ImportacionesController@index')->name('index_importaciones');

Route::match(['get', 'post'], 'contable/importaciones/busca_precio_bodega', 'contable\ImportacionesController@buscarPrecioBodega')->name('importaciones.buscarPrecioBodega');

//Pre orden
Route::get('contable/importaciones/pre_orden','contable\ImportacionesController@pre_orden')->name('contable.importaciones.pre_orden');
Route::match(['get', 'post'], 'contable/importaciones/pre_orden/store','contable\ImportacionesController@preOrdenStore')->name('importaciones.preOrdenStore');
Route::match(['get', 'post'], 'contable/importaciones/pre_orden/mostrar','contable\ImportacionesController@preOrdenMostrar')->name('importaciones.preOrdenMostrar');
//nueva orden
Route::match(['get', 'post'], 'contable/importaciones/new_orden_index','contable\ImportacionesController@new_orden_index')->name('importaciones.new_orden_index');
Route::match(['get', 'post'], 'contable/importaciones/new_orden_store','contable\ImportacionesController@new_orden_store')->name('importaciones.new_orden_store');
Route::match(['get', 'post'], 'contable/importaciones/new_orden_edit/{id}','contable\ImportacionesController@new_orden_edit')->name('importaciones.new_orden_edit');
Route::match(['get', 'post'], 'contable/importaciones/BuscadorProductos','contable\ImportacionesController@BuscadorProductos')->name('importaciones.BuscadorProductos');
Route::match(['get', 'post'], 'contable/importaciones/guardar_detalles','contable\ImportacionesController@guardar_detalles')->name('importaciones.guardar_detalles');
Route::match(['get', 'post'], 'contable/importaciones/actualizar_cabecera','contable\ImportacionesController@actualizar_cabecera')->name('importaciones.actualizar_cabecera');
Route::match(['get', 'post'], 'contable/importaciones/eliminar_detalle','contable\ImportacionesController@eliminar_detalle')->name('importaciones.eliminar_detalle');
Route::match(['get', 'post'], 'contable/importaciones/editar_detalle','contable\ImportacionesController@editar_detalle')->name('importaciones.editar_detalle');
Route::match(['get', 'post'], 'contable/importaciones/AprobarOrden/{id}','contable\ImportacionesController@AprobarOrden')->name('importaciones.AprobarOrden');
Route::match(['get', 'post'], 'contable/importaciones/EliminarOrden/{id}','contable\ImportacionesController@EliminarOrden')->name('importaciones.EliminarOrden');
Route::match(['get', 'post'], 'contable/importaciones/OrdenCargar','contable\ImportacionesController@OrdenCargar')->name('importaciones.OrdenCargar');


Route::match(['get', 'post'], 'contable/precio/producto/aprobado','contable\PrecioProductoAprobadoController@index')->name('importaciones.PrecioProductoAprobado.index'); 
Route::match(['get', 'post'], 'contable/precio/producto/aprobado/create','contable\PrecioProductoAprobadoController@create')->name('importaciones.PrecioProductoAprobado.create');
Route::match(['get', 'post'], 'contable/precio/producto/aprobado/store','contable\PrecioProductoAprobadoController@store')->name('importaciones.PrecioProductoAprobado.store');
Route::match(['get', 'post'], 'contable/precio/producto/aprobado/buscarTabla','contable\PrecioProductoAprobadoController@buscarTabla')->name('importaciones.PrecioProductoAprobado.buscarTabla');

Route::post('contable/precio/producto/aprobado/delete','contable\PrecioProductoAprobadoController@delete')->name('importaciones.PrecioProductoAprobado.delete');

Route::match(['get', 'post'], 'contable/precio/producto/aprobado/mostrar','contable\ImportacionesController@mostrar')->name('importaciones.Imprtaciones.mostar');

//vista para kardex
Route::match(['get', 'post'], 'contable/importaciones/kardex/resumen/{id}','contable\ImportacionesController@kardex_resumen')->name('importaciones.kardex_resumen');

Route::match(['get', 'post'], 'contable/importaciones/edit/preorden/{id}','contable\ImportacionesController@edit_preorden')->name('importaciones.edit_preorden');

Route::match(['get', 'post'], 'contable/importaciones/index/preorden','contable\ImportacionesController@index_preorden')->name('importaciones.index_preorden');

Route::match(['get', 'post'], 'contable/preorden/update','contable\ImportacionesController@update_orden')->name('importaciones.update_orden');

Route::match(['get', 'post'], 'contable/importaciones/aprobar/preorden/{id}','contable\ImportacionesController@aprobar_orden')->name('importaciones.aprobar_orden');

Route::match(['get', 'post'], 'contable/importaciones/eliminar/preorden/{id}','contable\ImportacionesController@eliminar_orden')->name('importaciones.eliminar_orden');

Route::match(['get', 'post'], 'contable/importaciones/guardar/fecha/lote','contable\ImportacionesController@guardar_fecha_lote')->name('importaciones.guardar_fecha_lote');


//anclar en el menu
Route::match(['get', 'post'], 'dashboard/menu/usuario','MenuController@menu_usuario')->name('menu.menu_usuario');
Route::match(['get', 'post'], 'dashboard/menu/eliminar', 'MenuController@eliminar')->name('menu.eliminar');

Route::match(['get', 'post'], 'dashboard/menu/buscador', 'MenuController@buscadorTarget')->name('menu.buscadorTarget');
Route::match(['get', 'post'], 'dashboard/index/caducado', 'MenuController@index_caducados')->name('menu.index_caducados');
Route::get('menu/estadisticas-caducados-producto', 'MenuController@estadisticas_caducados_producto')->name('menu.estadisticas_caducados_producto');
Route::get('/caducados/autocomplete', 'MenuController@autocomplete_caducados')->name('menu.autocomplete_caducados');
Route::match(['get', 'post'], 'dashboard/index/stock_minimo', 'MenuController@stock_minimo')->name('menu.stock_minimo');
Route::get('productos_caducados/excel', 'MenuController@exportar_caducados_excel')->name('menu.exportar_caducados_excel');
Route::get('stock_minimo/excel', 'MenuController@exportar_stock_minimo_excel')->name('menu.exportar_stock_minimo_excel');

Route::match(['get', 'post'], 'contable/importaciones/ver/archivo/imp/{id}', 'contable\GastosImportacionController@ver_archivo_imp')->name('importaciones.ver_archivo_imp');
Route::match(['get', 'post'], 'contable/importaciones/fecha/lote/{id}', 'contable\ImportacionesController@info_fecha_lote')->name('importaciones.info_fecha_lote');
Route::get('contable/importaciones/busca_asientos_costo', 'contable\ImportacionesController@busca_asientos_costo')->name('importaciones.busca_asientos_costo');

Route::match(['get', 'post'], 'contable/asientosCompras', 'contable\ImportacionesController@asientosCompras')->name('importaciones.asientosCompras');

Route::get('contable/buscar_asientos_compras/{id}','contable\ImportacionesController@buscar_asientos_compras')->name('importaciones.buscar_asientos_compras');
Route::match(['get', 'post'],'contable/guardarAsientoAnulado', 'contable\ImportacionesController@guardarAsientoAnulado')->name('importaciones.guardarAsientoAnulado');
