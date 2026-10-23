<?php
Route::match(['get', 'post'], 'contable/productos/productos_tarifario', 'contable\ProductosController@productos_tarifario')->name('productos.productos_tarifario');

Route::match(['get', 'post'], 'contable/productos/crear', 'contable\ProductosController@crear')->name('productos.crear');

Route::match(['get', 'post'], 'contable/productos/guardar', 'contable\ProductosController@guardar')->name('productos.guardar');

//Guarda Producto Tarifario Paquete
Route::match(['get', 'post'], 'contable/tarifario/paquete', 'contable\ProductosController@guardar_tarifario_paquete')->name('tarifario_paquete.guardar');

Route::match(['get', 'post'], 'contable/productos/edit/{id_producto}', 'contable\ProductosController@edit')->name('productos.edit');

Route::match(['get', 'post'], 'contable/productos/nivel', 'contable\ProductosController@nivel')->name('productos.nivel');

Route::match(['get', 'post'], 'contable/productos/precios', 'contable\ProductosController@precios')->name('productos.precios');

Route::match(['get', 'post'], 'contable/productos/update/{id_producto}', 'contable\ProductosController@update')->name('productos.update');

Route::match(['get', 'post'], 'contable/productos/edit2/{id_producto}/{id_seguro}', 'contable\ProductosController@edit2')->name('productos.edit2');

Route::match(['get', 'post'], 'contable/productos/buscar', 'contable\ProductosController@buscar')->name('productos.buscar');

//Mantenimiento Bodegas
Route::match(['get', 'post'], 'contable/bodegas', 'contable\BodegasController@index')->name('bodegas.index');
Route::get('contable/bodegas/crear', 'contable\BodegasController@crear')->name('bodegas.crear');
Route::post('contable/bodegas/guardar', 'contable\BodegasController@store')->name('bodegas.store');
Route::get('contable/bodegas/editar/{id}/{id_empresa}', 'contable\BodegasController@editar')->name('bodegas.editar');
Route::post('contable/bodegas/update', 'contable\BodegasController@update')->name('bodegas.update');
Route::match(['get', 'post'], 'contable/bodegas/buscar', 'contable\BodegasController@buscar')->name('bodegas.buscar');
//Configuraciones Pdf
Route::match(['get', 'post'], 'contable/configuraciones_pdf', 'contable\ProductosController@configuraciones_pdf')->name('configuraciones_pdf');
Route::post('contable/configuraciones_pdf/guardar_confi', 'contable\ProductosController@guardar_confi')->name('configuraciones.guardar');
Route::match(['get', 'post'], 'contable/configuraciones_pdf/index', 'contable\ProductosController@index_confi')->name('configuraciones_pdf_index');
Route::match(['get', 'post'], 'contable/configuraciones_pdf/editar_pdf/{id}', 'contable\ProductosController@editar_pdf')->name('editar_pdfconfi');
Route::match(['get', 'post'], 'contable/configuraciones_pdf/actualizar', 'contable\ProductosController@actualizar_pdf')->name('actualizar_pdf');

//Buzqueda de Codigo de Producto
Route::post('contable/productos/buscar_codigo', 'contable\ProductosController@buscar_codigo_producto')->name('buscar_codigo.producto');
Route::match(['get', 'post'], 'contable/productos/iniciales', 'contable\Productos_ServiciosController@saldos_iniciales')->name('productos.saldos_iniciales');
Route::get('contable/productos/iniciales/create_saldos', 'contable\Productos_ServiciosController@create_saldos')->name('productos.create_saldos');
Route::get('contable/productos/iniciales/edit_saldos/{id}', 'contable\Productos_ServiciosController@edit_saldos')->name('productos.edit_saldos');
Route::post('contable/productos/inciales/store', 'contable\Productos_ServiciosController@store_iniciales')->name('productos.store_iniciales');
Route::post('contable/productos/inciales/update_saldos/{id}', 'contable\Productos_ServiciosController@update_saldos')->name('productos.update_saldos');

Route::get('contable/productos/comparativo/{id}/{ix}', 'contable\ProductosController@comparar')->name('productos.comparar');
Route::get('contable/productosc/anular/comparativo/{id}', 'contable\ProductosController@anular_comparativo')->name('productos.anular_comparativo');
Route::get('contable/productosc/anular/productos', 'contable\ProductosController@anular_detalle')->name('productos.anular_detalle');
Route::match(['get', 'post'], 'contable/productos/comparative/index', 'contable\ProductosController@index_comparar')->name('productos.comparar.index');
Route::get('contable/productos/asientogenerar/index', 'contable\ProductosController@modalAsiento')->name('productos.modal.asiento');
Route::get('contable/productos/comparative/edit/{id}', 'contable\ProductosController@edit_comparar')->name('productos.edit_comparar');
Route::post('contable/productos/storecheck', 'contable\ProductosController@storecheck')->name('productos.storecheck');
Route::post('contable/productos/storeAsiento', 'contable\ProductosController@storeAsiento')->name('productos.storeAsiento');
Route::post('contable/productos/storeAprobado', 'contable\ProductosController@storeAprobado')->name('productos.storeAprobado');
Route::post('contable/productos/comparative/store', 'contable\ProductosController@storeData')->name('productos.storeData');
Route::match(['get', 'post'], 'contable/kardex/inventario', 'contable\KardexInternoController@index')->name('kardex_inventario.index');
Route::get('contable/productos/buscar/productos', 'contable\KardexInternoController@productosearch')->name('kardexinterno.productosearch');

Route::match(['get', 'post'], 'contable/pedidos/inventario', 'contable\KardexInternoController@pedidos')->name('pedidos_inventario.index');

Route::get('honorarios_procedimientos/generar_asiento/{tipo}/{id}', 'contable\ProductosController@genera_asiento_honorarios')->name('productos.genera_asiento_honorarios');
Route::post('honorarios_procedimientos/guardar_asiento_honorarios', 'contable\ProductosController@guardar_asiento_honorarios')->name('productos.guardar_asiento_honorarios');



Route::get('contable/asientos/descuadres/observar', 'contable\BancoClientesController@ver_asientos_descuadrados')->name('web_producto.ver_asientos');

Route::get('tarifario_masivo/subir/{id_seguro}', 'contable\SubirTarifarioController@importar_tarifario')->name('subirtarifario.importar_tarifario');
Route::get('tarifario_masivo/subir/crear/producto', 'contable\SubirTarifarioController@crear_producto')->name('subirtarifario.crear_producto');
Route::get('tarifario_masivo/subir/actualizar/producto/new', 'contable\SubirTarifarioController@actualizar_valor')->name('subirtarifario.actualizar_valor');

Route::get('cm_detalles_paquetes/carga_masiva', 'contable\SubirTarifarioController@carga_masiva_paquetes')->name('subirtarifario.carga_masiva_paquetes');

Route::match(['get', 'post'], 'vt_dar_baja_producto', 'Insumos\ProductoController@vt_dar_baja_producto')->name('insumos.producto.vt_dar_baja_producto');

//MEMBRESIAS
Route::get('ieced_membresias/{id_paciente}', 'laboratorio\MembresiasLabsController@buscar_membresia_IECED')->name('membresiaslabs.buscar_membresia_IECED');

Route::get('nuevo_dar_baja/modal/{serie}/{bodega}/{id_producto}', 'Insumos\ProductoController@nuevo_dar_baja_serie')->name('insumo.producto.nuevo_dar_baja');

Route::get('contable/reprocesar/FIFO/productos/{id}', 'contable\ProductosController@reprocesar_serie')->name('producto.reprocesar_serie');
Route::get('xmasivo_valores/contable', 'ImportarController@xmasivo_valores')->name('importar.xmasivo_valores');
Route::get('vt_masivo_productos_todos', 'ImportarController@vt_masivo_productos')->name('importar.vt_masivo_productos');

//MASIVO CREACION Y ACTUALIZACION DE PRODUCTOS
Route::get('masivo_prod/productos_contable/{nombre}', 'contable\ProductosController@masivo_crear_productos')->name('productos.masivo_crear_productos');
Route::get('masivo_prod/actualiza/productos_contable/{nombre}', 'contable\ProductosController@masivo_actualizar_productos')->name('productos.masivo_actualizar_productos');

Route::get('masivo_prod/productos_contable/codenew/{nombre}', 'contable\ProductosController@masivo_crear_productos_nuevos')->name('productos.masivo_crear_productos_nuevos');