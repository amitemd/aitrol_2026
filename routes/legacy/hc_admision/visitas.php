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



Route::get('historialclinico/visitas/{id_paciente}/{id_agenda}', 'hc_admision\ConsultaController@visitas')->name('visitas.index');
Route::get('historialclinico/visitas_ingreso/{id_protocolo}/{agenda}', 'hc_admision\ConsultaController@ingreso_actualiza_visita')->name('visita.crea_actualiza_funcion');
Route::get('historialclinico/visitas_ingreso/', 'hc_admision\ConsultaController@ingreso_actualiza_visita')->name('visita.crea_actualiza');

Route::post('historialclinico/visita/actualiza/paciente', 'hc_admision\ConsultaController@actualiza_paciente')->name('visita.paciente');
Route::post('historialclinico/visita/actualiza_crea/guarda', 'hc_admision\ConsultaController@actualizar_visita')->name('visita.actualiza');

Route::post('historialclinico/visita/actualiza_crea2/guardar', 'hc_admision\ConsultaController@actualizar_visita2')->name('visita.actualiza2');

Route::get('historialclinico/regresar', 'hc_admision\ConsultaController@regresar')->name('visitas.regresar'); 

//vistas para agregar una nueva evolucion 

Route::get('historiaclinica/evolucion/crear_evolucion_procedimiento/{id_agenda}/{hc_id_procedimiento}', 'hc_admision\ConsultaController@crear_evolucion_procedimiento')->name('evolucion.crear_evolucion_procedimiento');
