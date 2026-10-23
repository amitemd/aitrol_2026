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




Route::get('historiaclinica/procedimientos/{ag}', 'hc_admision\ProcedimientosController@mostrar')->name('procedimientos_historia.mostrar');
Route::get('historiaclinica/procedimientos/agregar/{hc_id}', 'hc_admision\ProcedimientosController@agregar')->name('procedimientos_hc.agregar');
Route::get('historiaclinica/procedimientos/eliminar/{hc_id}', 'hc_admision\ProcedimientosController@eliminar');
Route::get('historiaclinica/procedimientos/eliminar/', 'hc_admision\ProcedimientosController@eliminar')->name('procedimientos_hc.eliminar');
Route::post('historiaclinica/procedimientos/guardar2/', 'hc_admision\ProcedimientosController@guardar')->name('procedimientos_hc.guardar');

Route::get('estudio/agregar/{id}', 'hc_admision\ProcedimientosController@estudio_agregar')->name('estudio.agregar');
Route::get('estudio/editar/{id}/{agenda}', 'hc_admision\ProcedimientosController@estudio_editar')->name('estudio.editar');
Route::get('estudio/lista/{id}', 'hc_admision\ProcedimientosController@estudio_lista')->name('estudio.lista');
Route::get('estudio/agregar/nuevo/{agenda}', 'hc_admision\ProcedimientosController@estudio_nuevo')->name('estudio.nuevo');

Route::get('procedimientos/ruta/{id}', 'hc_admision\ProcedimientosController@ruta')->name('procedimiento.ruta');

Route::post('procedimientos/actualiza/paciente', 'hc_admision\ProcedimientosController@actualiza_paciente')->name('procedimiento.paciente');
Route::post('procedimientos/recupera/tecnica/quiri', 'hc_admision\ProcedimientosController@tecnica')->name('procedimiento.tecnica');

Route::get('hc_ima/{name}', 'hc_admision\ProcedimientosController@load');




//ruta para descargar el reporte// para resumen de historia
Route::get('Procedimiento/descargar/resumen/{id}/{tipo}', 'hc_admision\ProcedimientosController@descarga_resumen')->name('hc_reporte.descargar');
Route::get('Procedimiento2/descargar2/resumen2/{id}/{tipo}', 'hc_admision\ProcedimientosController@descarga_resumen2')->name('hc_reporte.descargar2');
Route::post('Procedimiento2/guardar_descargar/', 'hc_admision\ProcedimientosController@descarga_resumen3')->name('hc_reporte.descargar3');
//ruta para seleccion y descarga de imagen
Route::get('historia/admision/seleccion/imagen/{id}/{agenda_ori}/{ruta}', 'hc_admision\ProcedimientosController@descarga_seleccion')->name('hc_reporte.seleccion');
Route::get('historia/imagenes/cambio/seleccion/{id}', 'hc_admision\ProcedimientosController@imagen_cambio');
Route::get('historia/imagenes/cambio/seleccion/', 'hc_admision\ProcedimientosController@imagen_cambio')->name('hc_reporte.cambio_seleccion');
Route::get('historia/imagenes2/cambio2/seleccion2/{id}', 'hc_admision\ProcedimientosController@imagen_cambio2');
Route::get('historia/imagenes2/cambio2/seleccion2/', 'hc_admision\ProcedimientosController@imagen_cambio2')->name('hc_reporte.cambio_seleccion2');

//ruta para cargar las imagenes por default
Route::get('procedimiento_completo/grupo_imagenes/{imagen}', 'hc_admision\ProcedimientosController@load2')->name('grupo_imagen.load');


Route::get('Procedimiento/seleccion_descargar/resumen/{id_protocolo}/', 'hc_admision\ProcedimientosController@seleccion_descargar')->name('hc_reporte.seleccion_descargar');



