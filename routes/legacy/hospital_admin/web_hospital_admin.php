<?php

Route::match(['get', 'post'], 'hospital/admin', 'hospital\HospitalAdminController@index')->name('hospital_admin.index');
Route::match(['get', 'post'], 'hospital/admin/buscador', 'hospital\HospitalAdminController@buscador')->name('hospital_admin.buscador');
Route::match(['get', 'post'], 'hospital/admin/dashboard', 'hospital\HospitalAdminController@dashboard')->name('hospital_admin.dashboard');
Route::match(['get', 'post'], 'hospital/admin/agregarprodu', 'hospital\TiposAdminController@agregarprodu')->name('hospital_admin.agregarprodu.tipo');
Route::match(['get', 'post'], 'hospital/admin/insumos', 'hospital\HospitalAdminController@insumo')->name('hospital_admin.insumo');
Route::match(['get', 'post'], 'hospital/admin/emergencia', 'hospital\HospitalAdminController@emergenciadmin')->name('hospital_admin.emergenciadmin');

// FARMACIA
Route::match(['get', 'post'], 'hospital/admin/farmacia', 'hospital\HospitalAdminController@farmacia')->name('hospital_admin.farmacia');
Route::match(['get', 'post'], 'hospital/admin/farmacia/buscadorfa', 'hospital\HospitalAdminController@buscadorfa')->name('hospital_admin.buscadorfa');

// QUIROFANO
Route::match(['get', 'post'], 'hospital/admin/gestionqui', 'hospital\HospitalAdminController@gestionqui')->name('hospital_admin.gestionqui');
Route::match(['get', 'post'], 'hospital/admin/resultado/{id}', 'hospital\HospitalAdminController@resultadoquirofano')->name('hospital_admin.resultadoquirofano');
Route::match(['get', 'post'], 'hospital/admin/resultado/editar/{id}', 'hospital\HospitalAdminController@editar')->name('hospital_admin.editar');

// HABITACION
Route::match(['get', 'post'], 'hospital/admin/gestionh', 'hospital\HospitalAdminController@gestionh')->name('hospital_admin.gestionh');
Route::match(['get', 'post'], 'hospital/admin/gestionh/modalagcuarto', 'hospital\HospitalAdminController@modalagcuarto')->name('hospital_admin.modalagcuarto');
Route::match(['get', 'post'], 'hospital/admin/gestionh/aghabitaciones', 'hospital\HospitalAdminController@aghabitaciones')->name('hospital_admin.aghabitaciones');
Route::match(['get', 'post'], 'hospital/admin/gestionh/editarh/{id}', 'hospital\HospitalAdminController@editarh')->name('hospital_admin.editarh');
Route::match(['get', 'post'], 'hospital/admin/gestionh/editarh/updateh/{id}', 'hospital\HospitalAdminController@updateh')->name('hospital_admin.updateh');

// Camillas
Route::match(['get', 'post'], 'hospital/admin/gestion_c', 'hospital\HospitalAdminController@gestion_c')->name('hospital_admin.gestion_c');
Route::match(['get', 'post'], 'hospital/admin/gestionh/editarc/{id}', 'hospital\HospitalAdminController@editarc')->name('hospital_admin.editarc');
// CREACION DE PLATO / MENÚ
Route::match(['get', 'post'], 'hospital/admin/servicios/lista', 'hospital\ServiciosAdminController@listamenu')->name('hospital_admin.listamenu');
Route::match(['get', 'post'], 'hospital/admin/servicios/crear', 'hospital\ServiciosAdminController@crearmenu')->name('hospital_admin.crearmenu');
Route::match(['get', 'post'], 'hospital/admin/servicios/editar/{id}', 'hospital\ServiciosAdminController@editar')->name('hospital_admin.editarplato');

Route::post('hospital/admin/servicios/ingrediente', 'hospital\ServiciosAdminController@buscaringrediente')->name('hospital_admin.ingrediente');
Route::post('hospital/admin/servicios/agregarigrediente', 'hospital\ServiciosAdminController@agregarigrediente')->name('hospital_admin.agregaing');
Route::get('hospital/admin/servicios/autocomplete', 'hospital\ServiciosAdminController@fetch')->name('autocomplete.fetch');
Route::post('hospital/admin/servicios/guardar', 'hospital\ServiciosAdminController@guardar')->name('hospital_admin.guardarplato');
Route::post('hospital/admin/servicios/actualizarplato/{id}', 'hospital\ServiciosAdminController@actualizarplato')->name('hospital_admin.actualizarPlato');

// BODEGA
Route::match(['get', 'post'], 'hospital/admin/bodega', 'hospital\BodegaAdminController@bodega')->name('hospital_admin.bodega');
Route::match(['get', 'post'], 'hospital/admin/bodega/agregarb', 'hospital\BodegaAdminController@agregarb')->name('hospital_admin.agregarb');
Route::match(['get', 'post'], 'hospital/admin/bodega/editar/{id}', 'hospital\BodegaAdminController@editarb')->name('hospital_admin.editarb');
Route::match(['get', 'post'], 'hospital/admin/bodega/agregar', 'hospital\BodegaAdminController@agregar')->name('hospital_admin.agregar');
Route::match(['get', 'post'], 'hospital/admin/bodega/updateb/{id}', 'hospital\BodegaAdminController@updateb')->name('hospital_admin.updateb');

// MARCAS
Route::match(['get', 'post'], 'hospital/admin/marcas', 'hospital\MarcasAdminController@marcas')->name('hospital_admin.marcas');
Route::match(['get', 'post'], 'hospital/admin/marcas/agregar', 'hospital\MarcasAdminController@agregarm')->name('hospital_admin.agregarm');
Route::match(['get', 'post'], 'hospital/admin/marcas/editar/{id}', 'hospital\MarcasAdminController@editarm')->name('hospital_admin.editarm');
Route::match(['get', 'post'], 'hospital/admin/modalmarcas', 'hospital\MarcasAdminController@modalmarcas')->name('hospital_admin.modalmarcas');
Route::match(['get', 'post'], 'hospital/admin/marcas/updatema/{id}', 'hospital\MarcasAdminController@updatema')->name('hospital_admin.updatema');

// TIPO DE PRODUCTO
Route::match(['get', 'post'], 'hospital/admin/tipoprodu', 'hospital\TiposAdminController@tipoprodu')->name('hospital_admin.tipoprodu');
Route::match(['get', 'post'], 'hospital/admin/tipo/agregar', 'hospital\TiposAdminController@agregartipo')->name('hospital_admin.agregartipo');
Route::match(['get', 'post'], 'hospital/admin/tipo/editar/{id}', 'hospital\TiposAdminController@editartip')->name('hospital_admin.editartip');
Route::match(['get', 'post'], 'hospital/admin/modaltipop', 'hospital\TiposAdminController@modaltipop')->name('hospital_admin.modaltipop');
Route::match(['get', 'post'], 'hospital/admin/tipo/updatematipo/{id}', 'hospital\TiposAdminController@updatematipo')->name('hospital_admin.updatematipo');

// PRODUCTO
Route::match(['get', 'post'], 'hospital/admin/producto', 'hospital\ProductoAdminController@producto')->name('hospital_admin.producto');
Route::match(['get', 'post'], 'hospital/admin/producto/agregar', 'hospital\ProductoAdminController@agregarprodu')->name('hospital_admin.agregarprodu');
Route::match(['get', 'post'], 'hospital/admin/producto/editar/{id}', 'hospital\ProductoAdminController@modaleditarp')->name('hospital_admin.modaleditarp');
Route::match(['get', 'post'], 'hospital/admin/producto/modalproducto', 'hospital\ProductoAdminController@modalproducto')->name('hospital_admin.modalproducto');
Route::match(['get', 'post'], 'hospital/admin/producto/updatepro/{id}', 'hospital\ProductoAdminController@updatepro')->name('hospital_admin.updatepro');
Route::match(['get', 'post'], 'hospital/admin/producto/movimiento/{id}', 'hospital\ProductoAdminController@movimientop')->name('hospital_admin.movimientop');
Route::match(['get', 'post'], 'hospital/admin/producto/darbaja', 'hospital\ProductoAdminController@darbaja')->name('hospital_admin.darbaja');
Route::match(['get', 'post'], 'hospital/admin/producto/baja/{id}', 'hospital\ProductoAdminController@bajaprodu')->name('hospital_admin.bajaprodu');
Route::match(['get', 'post'], 'hospital/admin/producto/darbaja/tablap', 'hospital\ProductoAdminController@tablap')->name('hospital_admin.tablap');
Route::match(['get', 'post'], 'hospital/admin/producto/movientop/{id}', 'hospital\ProductoAdminController@movientop')->name('hospital_admin.movientop');
Route::match(['get', 'post'], 'hospital/admin/producto/modalbaja/{cant}/{tipo}/{id_prod}/{serie}/{bodega}/{pedido}/{f_vencimiento}/{lote}', 'hospital\ProductoAdminController@modalbaja')->name('hospital_admin.modalbaja');
Route::match(['get', 'post'], 'hospital/admin/producto/descuento', 'hospital\ProductoAdminController@descuento')->name('hospital_admin.descuento');
Route::match(['get', 'post'], 'hospital/admin/producto/borrar', 'hospital\ProductoAdminController@borrar')->name('hospital_admin.borrar');
Route::match(['get', 'post'], 'hospital/admin/producto/darbaja', 'hospital\ProductoAdminController@darbaja')->name('hospital_admin.darbaja');

// INGRESO PRODUCTO
Route::match(['get', 'post'], 'hospital/admin/producto/codigo', 'hospital\ProductoAdminController@codigo')->name('hospital_admin.codigo');
Route::match(['get', 'post'], 'hospital/admin/producto/codigo2', 'hospital\ProductoAdminController@codigo2')->name('hospital_admin.codigo2');
Route::match(['get', 'post'], 'hospital/admin/producto/seguimiento/{id}', 'hospital\ProductoAdminController@seguimiento')->name('hospital_admin.seguimiento');
Route::match(['get', 'post'], 'hospital/admin/producto/barras', 'hospital\ProductoAdminController@codigo')->name('hospital_admin.codigobarra');
Route::match(['get', 'post'], 'hospital/admin/producto/buscar/nombre', 'hospital\ProductoAdminController@nombre')->name('hospital_admin.nombre');
Route::match(['get', 'post'], 'hospital/admin/producto/buscador/nombre', 'hospital\ProductoAdminController@nombre2')->name('hospital_admin.nombre2');

// TRANSITO PRODUCTO
Route::get('hospital/admin/producto/transito', 'hospital\TransitoController@transito')->name('hospital_admin.transito');
Route::get('hospital/admin/producto/transito/transitoag', 'hospital\TransitoController@transitoag')->name('htransito.transitoag');
Route::match(['get', 'post'], 'hospital/admin/producto/transito/agregar', 'hospital\ProductoAdminController@agregart')->name('hospital_admin.agregart');
Route::get('hospital/admin/producto/transito/agregart', 'hospital\TransitoController@agregartransito')->name('htransito.agregartransito');
Route::get('hospital/admin/eliminar/pedido/{id}', 'hospital\IngresoController@eliminar_pedido')->name('hospital_admin.eliminar_pedido');
Route::match(['get', 'post'], 'hospital/admin/transito/nombre_encargado/', 'hospital\TransitoController@nombre')->name('htransito.nombre');
Route::match(['get', 'post'], 'hospital/admin/transito/nombre_encargado2/', 'hospital\TransitoController@nombre2')->name('htransito.nombre2');
Route::match(['get', 'post'], 'hospital/admin/transito/codigo/', 'hospital\TransitoController@codigo')->name('htransito.codigo');
Route::post('hospital/admin/paciente/buscar', 'hospital\TransitoController@serie_enfermero')->name('htransito.serie_enfermero');
Route::post('hospital/admin/paciente_equipo/buscar', 'hospital\TransitoController@serie_enfermero_equipo')->name('htransito.serie_enfermero_equipo');
Route::get('hospital/admin/paciente_equipo/eliminar/{id}', 'hospital\TransitoController@eliminar_equipo')->name('htransito.eliminar_equipo');

// PROVEEDORES
Route::match(['get', 'post'], 'hospital/admin/proveedores', 'hospital\ProveedoresAdminController@proveedores')->name('hospital_admin.proveedores');
Route::match(['get', 'post'], 'hospital/admin/modalprovedor', 'hospital\ProveedoresAdminController@modalprovedor')->name('hospital_admin.modalprovedor');
Route::match(['get', 'post'], 'hospital/admin/registro', 'hospital\ProveedoresAdminController@registro')->name('hospital_admin.registro');
Route::match(['get', 'post'], 'hospital/admin/modalprovedord', 'hospital\ProveedoresAdminController@modalprovedord')->name('hospital_admin.modalprovedord');
Route::match(['get', 'post'], 'hospital/admin/registropro', 'hospital\ProveedoresAdminController@registropro')->name('hospital_admin.registropro');
Route::match(['get', 'post'], 'hospital/admin/updatep/{id}', 'hospital\ProveedoresAdminController@updatep')->name('hospital_admin.updatep');
Route::match(['get', 'post'], 'hospital/admin/editarp/{id}', 'hospital\ProveedoresAdminController@modaleditarpr')->name('hospital_admin.modaleditarpr');
Route::match(['get', 'post'], 'hospital/admin/buscador', 'hospital\ProveedoresAdminController@buscador')->name('hospital_admin.buscador');

// PEDIDOS (PEDIDOS REALIZADOS)
Route::match(['get', 'post'], 'hospital/admin/pedidos', 'hospital\PedidosAdminController@pedidos')->name('hospital_admin.pedido');
Route::get('hospital/admin/pedidos/ingreso', 'hospital\IngresoController@ingresopedido')->name('hospital_admin.ingresopedido');
Route::match(['get', 'post'], 'hospital/admin/gestionh/modalprodu', 'hospital\HospitalAdminController@modalprodu')->name('hospital_admin.modalprodu');
Route::match(['get', 'post'], 'hospital/admin/ingreso/search', 'hospital\IngresoController@search')->name('hospital_admin.search');
Route::get('hospital/admin/ingresodato', 'hospital\IngresoController@formulario')->name('hospital_admin.formulario');
Route::get('hospital/admin/ingresar/informacion/', 'hospital\IngresoController@guardar')->name('hospital_admin.guardar');
Route::get('hospital/admin/ingresar/informacion/recuperar', 'hospital\IngresoController@informacion')->name('hingreso.informacion');
Route::get('hospital/admin/producto/master/', 'hospital\ProductoAdminController@seguimientom')->name('hospital_admin.seguimientom');
/*Route::match(['get', 'post'],'hospital/admin/gestionh/buscar','hospital\HospitalAdminController@buscar')->name('hospital_admin.buscar');>
/*Route::match(['get', 'post'],'hospital/gestioncuartos','HospitalController@gcuartos')->name('hospital.gcuartos');
Route::match(['get', 'post'],'hospital/admcuarto','HospitalController@admcuarto')->name('hospital.admcuarto');
Route::match(['get', 'post'],'hospital/farmacia/agregarp','HospitalController@agregarp')->name('hospital.agregarp');
Route::match(['get', 'post'],'hospital/quirofano','HospitalController@quirofano')->name('hospital.quirofano');
Route::match(['get', 'post'],'hospital/modalq','HospitalController@modalq')->name('hospital.modalq');
Route::resource('ingreso_producto', 'Insumos\IngresoController');
Route::match(['get', 'post'], 'ingreso/search', 'Insumos\IngresoController@search')->name('ingreso_producto.search');
Route::post('producto/ingresodato', 'Insumos\IngresoController@formulario')->name('ingreso.formulario');
Route::post('producto/ingresar/informacion/', 'Insumos\IngresoController@guardar')->name('ingreso.guardar');
*/
