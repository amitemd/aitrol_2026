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



Route::get('historiaclinica/receta2/{hcid}', 'hc_admision\RecetaController@receta')->name('receta.receta');
Route::post('historiaclinica/receta3/', 'hc_admision\RecetaController@guardarpaciente')->name('receta.paciente');
Route::post('historiaclinica/receta4/', 'hc_admision\RecetaController@guardar2')->name('receta.guardar2');

Route::post('historiaclinica/buscar_nombre/', 'hc_admision\RecetaController@buscar_nombre')->name('receta.buscar_nombre');
Route::post('historiaclinica/buscar_nombre2/', 'hc_admision\RecetaController@buscar_nombre2')->name('receta.buscar_nombre2');

//ingreso de receta
Route::get('historiaclinica/receta/{ag}', 'hc_admision\RecetaController@mostrar')->name('receta.mostrar');
Route::post('receta/crea_actualiza/', 'hc_admision\RecetaController@crear_actualizar')->name('receta.update_crea');
Route::get('historiaclinica/receta/imprime/{id}/{tipo}', 'hc_admision\RecetaController@imprime')->name('hc_receta.imprime');

Route::match(['get', 'post'],'medicina/{ag}/search', 'hc_admision\MedicinaController@search')->name('medicina.search');
//Route::resource('medicina', 'hc_admision\MedicinaController');
Route::get('medicina/{ag}','hc_admision\MedicinaController@index')->name('medicina.index');
Route::get('medicina/{ag}/create','hc_admision\MedicinaController@create')->name('medicina.create');
Route::get('medicina/{ag}/{id}/edit','hc_admision\MedicinaController@edit')->name('medicina.edit');
Route::post('medicina/store','hc_admision\MedicinaController@store')->name('medicina.store');
Route::match(['put', 'patch'],'medicina/{id}/update', 'hc_admision\MedicinaController@update')->name('medicina.update');

Route::match(['get', 'post'],'generico/{ag}/search', 'hc_admision\GenericoController@search')->name('generico.search');
//Route::resource('generico', 'hc_admision\GenericoController');
Route::get('generico/{ag}','hc_admision\GenericoController@index')->name('generico.index');
Route::get('generico/{ag}/create','hc_admision\GenericoController@create')->name('generico.create');
Route::get('generico/{ag}/{id}/edit','hc_admision\GenericoController@edit')->name('generico.edit');
Route::post('generico/store','hc_admision\GenericoController@store')->name('generico.store');
Route::match(['put', 'patch'],'generico/{id}/update', 'hc_admision\GenericoController@update')->name('generico.update');


Route::get('generico/admision/find', 'hc_admision\GenericoController@find')->name('generico.find');
Route::get('receta_detalle/crear_detalle/{receta}/{medicina}/{pac}', 'hc_admision\RecetaController@crear_detalle')->name('receta.crear_detalle');
Route::get('receta_detalle/index_detalle/{receta}', 'hc_admision\RecetaController@index_detalle')->name('receta.index_detalle');
Route::match(['get', 'post'],'receta_detalle/editar_detalle/{receta}/{id}', 'hc_admision\RecetaController@editar_detalle')->name('receta.editar_detalle');
Route::get('receta_detalle/eliminar_detalle/{receta}/{id}', 'hc_admision\RecetaController@eliminar_detalle')->name('receta.eliminar_detalle');
Route::match(['get', 'post'], 'receta_modificada/modificar_detalle/', 'hc_admision\RecetaController@update_receta_2')->name('receta.update_receta_2');

Route::get('medicina2/{ag}/{ruta}/create','hc_admision\MedicinaController@create2')->name('medicina2.create2');
Route::post('medicina2/store','hc_admision\MedicinaController@store2')->name('medicina2.store2');

Route::post('generico2/store','hc_admision\GenericoController@store2')->name('generico2.store2');

Route::get('reporte/medicinas','hc_admision\MedicinaController@reporte')->name('medicina.reporte');

//Nuevas Rutas Receta

//Ruta para Historial de Recetas
Route::get('paciente/historial/recetas/{id_paciente}', 'hc_admision\RecetaController@historial_recetas')->name('paciente.historial_recetas');

//Ruta para actualizar recetas
Route::get('historiaclinica/actualiza/recetas', 'hc_admision\RecetaController@editar_receta')->name('historial.actualiza_receta');
Route::get('historiaclinica/actualiza/recetas/{id}/{idpaciente}', 'hc_admision\RecetaController@editar_receta');

//Ruta para volver a la vista historial recetas
Route::post('historiaclinica/retorna/vista/receta', 'hc_admision\RecetaController@retorna_vista_historial_recetas')->name('historiaclin_paciente.receta_act');

//Ruta para crea una nueva receta al paciente
Route::get('paciente/nueva/receta/{id_paciente}', 'hc_admision\RecetaController@agregar_nueva_receta')->name('historiaclinica_paciente_nueva.receta');

//para actualizar los campos rp y prescripcion
Route::post('historiaclinicahc4/modificar_detalle/recetas', 'hc_admision\RecetaController@updatehc4_receta_2')->name('update_receta_2.recetahc4');

//Para actualizar fecha_doctor
Route::post('actualiza/fecha/doctor', 'hc_admision\RecetaController@update_fech_doct')->name('update_fecha.iddoctor');
Route::get('receta/nota_entrega/{id}', 'hc_admision\RecetaController@nota_entrega')->name('receta.nota_entrega');
Route::match(['get', 'post'],'receta/pacientes/nota_entrega/index','hc_admision\RecetaController@index_nota_entrega')->name('receta.index_nota_entrega');
Route::get('nota_entrega/sube/archivo/{id_receta}/{id_nota}', 'hc_admision\RecetaController@sube_archivo_nota_entrega')->name('receta.sube_archivo_nota_entrega');
Route::match(['get', 'post'], 'nota_entrega/update', 'hc_admision\RecetaController@update_nota_entrega')->name('receta.update_nota_entrega');
Route::get('nota_entrega/vizualizar_pdf', 'hc_admision\RecetaController@visualizar_nota_entrega_pdf')->name('receta.visualizar_nota_entrega_pdf');
Route::match(['get', 'post'], 'historiaclinica/full_control/nota_entrega', 'hc_admision\RecetaController@index_generar_nota_entrega')->name('receta.index_generar_nota_entrega'); 
Route::get('nota_entrega/agenda/recetas/{id}', 'hc_admision\RecetaController@detalle_receta_nota_entrega')->name('receta.detalle_receta_nota_entrega');
Route::get('nota_entrega/editar/{id}', 'nota_entrega\Nota_EntregaController@editar')->name('nota_entrega.editar');
Route::match(['get', 'post'], 'nota_entrega/detalles/{id}', 'nota_entrega\Nota_EntregaController@detalles')->name('nota_entrega.detalles');
Route::match(['get', 'post'], 'nota_entrega/buscar/medicinas', 'nota_entrega\Nota_EntregaController@buscarMedicinas')->name('nota_entrega.buscarMedicinas');
Route::post('nota_entrega/guardar_medicina', 'nota_entrega\Nota_EntregaController@guardar_medicina')->name('nota_entrega.guardar_medicina');
Route::match(['get', 'post'], 'nota_entrega/actualizar/medicina', 'nota_entrega\Nota_EntregaController@actualizar_medicina')->name('nota_entrega.actualizar_medicina');
Route::post('nota_entrega/eliminar/medicina', 'nota_entrega\Nota_EntregaController@eliminar_medicina')->name('nota_entrega.eliminar_medicina');
Route::get('nota_entrega/emitir/{id}', 'nota_entrega\Nota_EntregaController@emitir_recibo')->name('nota_entrega.emitir_recibo');
Route::get('nota_entrega/imprimir/pdf/{id_nota}', 'nota_entrega\Nota_EntregaController@imprimir_pdf_nota_entrega')->name('nota_entrega.imprimir_pdf_nota_entrega');
Route::match(['get', 'post'], 'nota_entrega/validar_stock', 'nota_entrega\Nota_EntregaController@validar_stock')->name('nota_entrega.validar_stock');
Route::post('nota_entrega/guardar_planilla_detalle', 'nota_entrega\Nota_EntregaController@guardar_planilla_detalle')->name('nota_entrega.guardar_planilla_detalle');
Route::match(['get', 'post'], 'nota_entrega/agregar_stock_medicina', 'nota_entrega\Nota_EntregaController@agregar_stock_medicina')->name('nota_entrega.agregar_stock_medicina');
Route::get('nota_entrega/validar_detalle/{id}', 'nota_entrega\Nota_EntregaController@validar_detalle')->name('nota_entrega.validar_detalle');
Route::match(['get','post'],'nota_entrega/aprobacion/planilla/index', 'nota_entrega\Nota_EntregaController@planilla_index')->name('nota_entrega.planilla_index');
Route::get('nota_entrega/medicinas/comparative/edit/{id}', 'nota_entrega\Nota_EntregaController@edit_comparar')->name('nota_entrega.edit_comparar');
Route::post('nota_entrega/medicinas/storeAprobado', 'nota_entrega\Nota_EntregaController@storeAprobado')->name('nota_entrega.storeAprobado');
Route::get('nota_entrega/medicinas/devolver_nota/{id}', 'nota_entrega\Nota_EntregaController@devolver_nota_entrega')->name('nota_entrega.devolver_nota_entrega');

//excel pasante 
Route::post('receta/pacientes/nota_entrega/excel', 'hc_admision\RecetaController@excel_nota_entrega')->name('receta.excel_nota_entrega');

