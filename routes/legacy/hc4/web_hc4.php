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

// 11/2/2019 Llamando a la vista

Route::match(['get', 'post'], 'inicio/', 'hc4\Hc4Controller@vista1')->name('nuevo.diseño');
Route::match(['get', 'post'], 'inicio/perfil', 'hc4\Hc4Controller@perfil')->name('perfil.editar_nuevo');

Route::post('inicio_buscador/', 'hc4\Hc4Controller@buscar_paciente')->name('hc4.busqueda');
// Route::match(['get', 'post'],'inicio45/','hc4\Hc4Controller@buscar_paciente')->name('hc4.busqueda');

// busqueda de pacientes por consulta o procedimiento de distinto doctores
Route::match(['get', 'post'], 'inicio/busqueda/fecha', 'hc4\Hc4Controller@buscar_paciente_fecha')->name('busqueda_fecha');

// busqueda de pacientes por consulta o procedimiento de un doctor
Route::match(['get', 'post'], 'inicio/busqueda/paciente/doctor', 'hc4\Hc4Controller@buscar_pacientes_doctor')->name('busqueda_pacientes_doctor');

// 1/02/2019 RUTA WALTER

Route::get('inicio/buscador/{id_paciente}', 'hc4\HistoriaPacienteController@historia_paciente_index')->name('nd.buscador');

Route::post('inicio/buscador/guardar/filiacion', 'hc4\HistoriaPacienteController@ingreso')->name('n_filiacion');

// para para acceder a consultas
Route::get('inicio/cargar/consultas/{id_paciente}', 'hc4\ConsultaController@index')->name('paciente.consulta');

// para para acceder a procedimientos endoscopicos
Route::get('inicio/procedimientos/endoscopiocos/{id_paciente}', 'hc4\ProcedimientosEndoscopicosController@index')->name('paciente.procedimiento_endoscopico');

Route::get('inicio/procedimientos/ecografia/{id_paciente}', 'hc4\ProcedimientosEcografiaController@index')->name('paciente.procedimiento_ecografia');

// para actualizar observacion de paciente
Route::post('inicio/actualizacion', 'hc4\HistoriaPacienteController@actualizar')->name('paciente_observacion_act');

// para ver el detalle de filiacion del paciente
Route::get('inicio/detalle/filiacion/{id_paciente}', 'hc4\Hc4Controller@search_detalle_filiacion')->name('paciente.detalle_filiacion');

// Rutas para acceder a Recetas//
// para acceder a recetas
Route::get('inicio/recetas/{id_paciente}', 'hc4\RecetasController@historial_receta_paciente')->name('paciente.recetas');

// para ingresar nueva receta asociada al ultimo idreceta
Route::get('inicio/nueva/recetas/{id_paciente}', 'hc4\RecetasController@agregar_nueva_receta')->name('paciente_nueva.receta');
Route::get('inicio/nueva/recetas/funcional/{id_paciente}/{id_hc}', 'hc4\RecetasController@agregar_nueva_receta_fun')->name('paciente_nueva.receta_fun');
Route::get('inicio/nueva/recetas/publica/{id_paciente}', 'hc4\RecetasController@agregar_nueva_receta_publica')->name('paciente_nueva.receta_publica');

// para acceder actualizar receta
Route::get('inicio/actualiza/recetas', 'hc4\RecetasController@editar')->name('paciente.actualiza.receta');
Route::get('inicio/actualiza/recetas/{id}/{idpaciente}', 'hc4\RecetasController@editar');

// para volver a la vista receta index
Route::post('inicio/retorna/vista/receta', 'hc4\RecetasController@actua_receta')->name('paciente_receta_act');

// para poder imprimir la receta del paciente
Route::get('inicio/hc4/imprime/recetas/{id}/{tipo}', 'hc4\RecetasController@imprime')->name('hc_receta.imprime_hc4');

// para buscar por nombre de medicina
Route::post('inicio/buscar_nombre/recetas', 'hc4\RecetasController@buscar_nombre')->name('buscar_nombre.receta');

// para anadir a rp y prescripcion data
Route::post('inicio/buscar_nombre2/recetas', 'hc4\RecetasController@buscar_nombre2')->name('buscar_nombre2.receta');

// Para actualizar los campos rp y prescripcion nueva version
// Route::post('inicio/modificar/recetas','hc4\RecetasController@modifica')->name('receta.modifica');

// para actualizar los campos rp y prescripcion
Route::post('inicio/modificar_detalle/recetas', 'hc4\RecetasController@update_receta_2')->name('update_receta_2.receta');

Route::get('detalle_receta/detalle_crear/{receta}/{medicina}/{pac}', 'hc4\RecetasController@crear_detalle')->name('crear_detalle.receta');

Route::get('new_detalle_receta/index/{receta}', 'hc4\RecetasController@receta_detalle_index')->name('recetas.receta_detalle_index');
Route::match(['get', 'post'], 'detalle_receta/detalle_editar/{id}', 'hc4\RecetasController@editar_detalle')->name('editar_detalle.receta');
Route::get('hc4/receta_detalle/eliminar_detalle/{receta}/{id}', 'hc4\RecetasController@eliminar_detalle')->name('recetas.eliminar_detalle');

// para mostrar button crear y editar medicina//
Route::get('inicio/crea_edita/medicina', 'hc4\MedicinaController@create_edit_medicina')->name('agregar_edit.medicina');

// para crear la medicina
Route::get('inicio/crear/medicina', 'hc4\MedicinaController@crear_medicina')->name('crear.medicina_hc4');

// para listar las medicinas
Route::get('inicio/listado/medicina', 'hc4\MedicinaController@listar_medicina')->name('editar.medicina_hc4');

// para guardar medicina
Route::post('medici/store', 'hc4\MedicinaController@guarda_medicina')->name('guarda.medicina');

// para buscar por nombre medicina
Route::post('medicina/busqueda', 'hc4\MedicinaController@search')->name('medicina.buscar_hc4');

// para editar la medicina
Route::get('medicina/edit', 'hc4\MedicinaController@edit_med')->name('edit.medicina');
Route::get('medicina/edit/{id}', 'hc4\MedicinaController@edit_med');

// para actualizar la medicina
Route::post('inicio/actualizar/medicina', 'hc4\MedicinaController@update_medic')->name('actualiza_medicina_hc4');

// Rutas para Medicina Generica
// Muestra la lista de medicina Generica
Route::get('inicio/muestra/lista_genericos', 'hc4\GenericosController@listar_medicina_generico')->name('listar.medicina_generica.hc4');

// Crea la medicina Generica
Route::get('inicio/crear_hc4/generico', 'hc4\GenericosController@crear_generico')->name('crear.generico_hc4');

// Para guardar la medicina generica
Route::post('generico/store', 'hc4\GenericosController@guarda_generico')->name('guarda.generico');

// Para editar el generico
Route::get('generico/editar', 'hc4\GenericosController@edit_generico')->name('edit.generico');
Route::get('generico/editar/{id}', 'hc4\GenericosController@edit_generico');

// para actualizar la medicina generica
Route::post('inicio/actualizarhc4/generico', 'hc4\GenericosController@update_generico')->name('actualiza_generico_hc4');

// para buscar por nombre medicina generica
Route::post('generico/busqueda', 'hc4\GenericosController@search_generico')->name('generico.buscar_hc4');

// Rutas para acceder a Laboratorio//
// para acceder a laboratorio
Route::get('inicio/laboratorio/{id_paciente}', 'hc4\LaboratorioController@search')->name('paciente.laboratorio');

Route::get('inicio/laboratorio/orden/descargar/{id}', 'hc4\LaboratorioController@descargar')->name('descargar.orden');

Route::get('inicio/laboratorio/hc4resultados/imprimir/{id_orden}', 'hc4\LaboratorioController@imprimir_resultado')->name('hc4resultados.imprimir');

Route::get('hc4resultados/valida/imprimir/{id_orden}', 'hc4\LaboratorioController@puede_imprimir')->name('valida.puede_imprimir');

// para obtener el horario del doctor logueado al entrar al Sistema HC
Route::get('inicio/horario/doctor', 'hc4\Hc4Controller@cargar_hor_doctor')->name('obtener.horario_doctor');

// para obtener las ordenes de laboratorio del dia de todos los doctores
Route::get('inicio/ord/lab', 'hc4\Hc4Controller@cargar_ordenes_lab')->name('obtener.ordenes_lab');
Route::match(['get', 'post'], 'inicio/ord/nuevo/lab', 'hc4\Hc4Controller@cargar_ordenes_lab_nuevo')->name('obtener.ordenes_lab_nuevo');

// para obtener las ordenes de laboratorio del dia de todos los doctores opcion buscador
Route::get('inicio/ord/lab/buscador', 'hc4\Hc4Controller@cargar_ordenes_lab_buscador')->name('busqueda_ord_lab');

// para mostrar la barra de progreso
Route::get('muestra/barra/{id_orden}', 'hc4\Hc4Controller@carga_barra_progress')->name('barra.progress_imprimir');

// Rutas para acceder a Biopsias//
Route::get('inicio/biopsias/{id_paciente}', 'hc4\BiopsiasController@crear')->name('paciente.biopsias');

// Rutas para acceder a Resultados de Biopsias
Route::get('inicio/resultado/biopsias/{id_paciente}', 'hc4\ResultadoBiopsiasController@obtener_resultado')->name('paciente_resultados.biopsias');

// Para Imprimir los resultados de Biopsia de cada Frasco
Route::get('imprimir/resultado/biopsia/frasco/{id_re}', 'hc4\ResultadoBiopsiasController@imprimir_resultado_frasco')->name('resultado_frasco.imprimir');

// para acceder a evoluciones
Route::get('inicio/evoluciones/{id_paciente}', 'hc4\EvolucionesController@index')->name('paciente.evoluciones');
// para acceder a las opciones de evoluciones
Route::get('inicio/evoluciones/opciones/{id_paciente}', 'hc4\EvolucionesController@opciones')->name('paciente.evoluciones_opciones');

// para accceder a procedimientos_funcionales
Route::get('inicio/procedimientos_funcionales/{id_paciente}', 'hc4\ProcedimientosFuncionalesController@index')->name('paciente.procedimiento_funcional');

// para acceder a  hc4resultados externos
Route::get('inicio/hc4resultados/externos/{id_paciente}', 'hc4\ResultadosExternosController@index')->name('paciente.hc4resultados_externos');

// para acceder a editar evoluciones
Route::get('inicio/editar/editar_evoluciones/{id_paciente}/{id_agenda}', 'hc4\EvolucionesController@editar');
Route::get('inicio/editar/editar_evoluciones/', 'hc4\EvolucionesController@editar')->name('paciente.editar_evol');

// para acceder a editar procedimientos funcionales
Route::get('inicio/editar/editar_proc_funcionales/{id_paciente}/{id_agenda}', 'hc4\ProcedimientosFuncionalesController@editar');
Route::get('inicio/editar/editar_proc_funcionales/', 'hc4\ProcedimientosFuncionalesController@editar')->name('paciente.editar_proc_funcionales');

Route::get('inicio/agregar/nuevo/paciente', 'hc4\Hc4Controller@agregar_paciente')->name('agregar.paciente_hc4');

// busca paciente por nombre 24/04/2019
Route::match(['get', 'post'], 'inicio/busca/paciente/pornombre', 'hc4\Hc4Controller@pacientexnombre')->name('busca.pacientexnombre');

// para actualizar evoluciones de paciente
Route::post('inicio/actualizar/evolucion', 'hc4\EvolucionesController@actualizar')->name('paciente_evolucion_act');

// PARA ACTUALIZAR PROCEDIMIENTOS FUNCIONALES
Route::post('inicio/actualizar/proc_funcionales', 'hc4\ProcedimientosFuncionalesController@actualizar')->name('paciente.proc_fun_act');

// editar imagenes de un procedimiento
Route::get('inicio/procedimientos/endoscopiocos/imagenes/', 'hc4\ImagenesController@index')->name('paciente.imagenes_protocolo');
Route::get('inicio/procedimientos/endoscopiocos/imagenes/{id_protocolo}', 'hc4\ImagenesController@index');

// AGREGAR PROCEDIMIENTO ENDOSCOPICO
Route::get('procedimiento/selecciona/{tipo}/{paciente}', 'hc4\Hc4Controller@selecciona_procedimiento')->name('hc4_procedimiento.selecciona_procedimiento');
Route::post('procedimiento/proc_endoscopico/crear', 'hc4\Hc4Controller@crear_procedimiento')->name('hc4_procedimiento.crear');

// agregar procedimientos desde la burbuja
Route::get('procedimiento2/selecciona2/{tipo}/{paciente}/{hcid}', 'hc4\Hc4Controller@selecciona_procedimiento2')->name('hc4_procedimiento.selecciona_procedimiento2');
Route::post('procedimiento/agregar_editar/crear', 'hc4\Hc4Controller@crear_procedimiento2')->name('hc4_procedimiento.editar_burbuja');
// AGREGAR PROCEDIMIENTO FUNCIONAL
Route::get('procedimiento/selecciona/proc_funcional/{tipo}/{paciente}', 'hc4\ProcedimientosFuncionalesController@selecciona_procedimiento_fun')->name('proc_fun.selecciona_procedimiento');
Route::post('procedimiento/proc_funcional/crear', 'hc4\ProcedimientosFuncionalesController@crear_procedimiento_funcional')->name('proc_fun.crear');

// NUEVO PACIENTE
Route::post('paciente/crear', 'hc4\Hc4Controller@crear_paciente')->name('hc4_paciente.crear_paciente');

// PARA ACCEDER A IMAGENES_PACIENTE
Route::get('inicio/imagenes_paciente/{id_paciente}', 'hc4\Imagenes_PacienteController@index')->name('paciente.imagenes_paciente');

// PARA ACCEDER A Visualizador de Estudio
Route::get('inicio//hc4/visualizador_estudio/{id_paciente}', 'hc4\EstudioVisualizadorController@index')->name('paciente.visualizador_estudio');

// PARA LLAMAR A LA MODAL DE IMAGENES DE PROCEDIMIENTOS
Route::get('Procedimiento/imagenes/seleccion_descargar/resumen/{id_protocolo}/', 'hc4\Imagenes_PacienteController@seleccion_descargar')->name('hc_reporte.seleccion_descargar.imagenes');

// editar proc_ecografia
route::get('ecografia/ingresar/historial/', 'hc4\ProcedimientosEcografiaController@editar')->name('editar.procedimiento_ecografia');
route::get('ecografia/ingresar/historial/{id_procedimiento}/{id_paciente}', 'hc4\ProcedimientosEcografiaController@editar');
// editar proc_endoscopico
route::get('Procedimiento/ingresar/historial/', 'hc4\ProcedimientosEndoscopicosController@editar')->name('editar.procedimiento_endoscopico');
route::get('Procedimiento/ingresar/historial/{id_procedimiento}/{id_paciente}', 'hc4\ProcedimientosEndoscopicosController@editar');

// editar proc_funcional
route::get('Procedimiento/funcional/ingresar/historial/', 'hc4\ProcedimientosFuncionalesController@editar')->name('editar.procedimiento_funcional');
route::get('Procedimiento/funcional/ingresar/historial/{id_procedimiento}/{id_paciente}', 'hc4\ProcedimientosFuncionalesController@editar');

route::get('Procedimiento/ingresar2/historial2/', 'hc4\ProcedimientosEndoscopicosController@editar2')->name('editar.procedimiento_endoscopico2');
route::get('Procedimiento/ingresar2/historial2/{id_procedimiento}', 'hc4\ProcedimientosEndoscopicosController@editar2');
// ecografia
route::get('ecografia/ingresar2/historial2/', 'hc4\ProcedimientosEcografiaController@editar2')->name('editar.procedimiento_ecografia2');
route::get('ecografia/ingresar2/historial2/{id_procedimiento}', 'hc4\ProcedimientosEcografiaController@editar2');
route::post('Procedimientoecografia_datos/guardar/historial/', 'hc4\ProcedimientosEcografiaController@guardar')->name('guardar.procedimiento_ecografia');
route::post('Procedimientoecografia_datos_2/guardar_2/historial/', 'hc4\ProcedimientosEcografiaController@guardar_2')->name('guardar.procedimiento_ecografia_autoguardado');

route::post('Procedimientoendoscopicos_datos/guardar/historial/', 'hc4\ProcedimientosEndoscopicosController@guardar')->name('guardar.procedimiento_endoscopico');

route::post('Procedimientoendoscopicos_datos_2/guardar_2/historial/', 'hc4\ProcedimientosEndoscopicosController@guardar_2')->name('guardar.procedimiento_endoscopico_autoguardado');

// GUARDAR PROCEDIMIENTO FUNCIONAL
route::post('Procedimiento_funcional/guarda_pf/', 'hc4\ProcedimientosFuncionalesController@guardar_proc_fun')->name('guardar.procedimiento_funcional');

route::get('Procedimiento/ingresar_evolucion/historial2/', 'hc4\ProcedimientosEndoscopicosController@editar_evolucion')->name('editar.procedimiento_evolucion');
route::get('Procedimiento/ingresar_evolucion/historial2/{id_procedimiento}/{id_paciente}', 'hc4\ProcedimientosEndoscopicosController@editar_evolucion');
route::post('Procedimientoendoscopicos/guardaevolucion/historial/', 'hc4\ProcedimientosEndoscopicosController@guardar_evolucion')->name('guardar.procedimiento_evolucion');

route::get('Procedimiento/agregar_evolucion/historial2/', 'hc4\ProcedimientosEndoscopicosController@agregar_evolucion')->name('agregar.procedimiento_evolucion');
route::get('Procedimiento/agregar_evolucion/historial2/{id_procedimiento}', 'hc4\ProcedimientosEndoscopicosController@agregar_evolucion');

Route::get('cie10/cargar22/', 'hc4\ProcedimientosEndoscopicosController@cargar_cie10')->name('epicrisis.cargar22');
Route::get('cie10/cargar22/{id}', 'hc4\ProcedimientosEndoscopicosController@cargar_cie10');

Route::get('consulta/evolucion_consulta/{id}/{ag}', 'hc4\ConsultaController@crear_evolucion');
Route::get('consulta/evolucion_consulta/', 'hc4\ConsultaController@crear_evolucion')->name('consulta.crear_nueva_consulta');

Route::get('/limpiar_cache', function () {
    // $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:clear');
    // $exitCode = Artisan::call('cache:config');
    // $exitCode = Artisan::call('views:clear');
});

Route::get('cie10/cargar/', 'hc_admision\EpicrisisController@cargar')->name('epicrisis.cargar2');

Route::match(['get', 'post'], 'historiaclinica/consulta/actualizar', 'hc4\ConsultaController@actualizar_consulta')->name('consulta.modificacion');
Route::match(['get', 'post'], 'historiaclinica/consulta/receta/actualizar', 'hc4\ConsultaController@actualizar_receta')->name('consulta.modificacion_receta');
Route::match(['get', 'post'], 'cie10/agregar/evol', 'hc4\EvolucionesController@hc4_agregar_cie10')->name('hc4.evolucion_agregar_cie10');
Route::match(['get', 'post'], 'cie10/agregar/proc/endos', 'hc4\ProcedimientosEndoscopicosController@agregar_cie10_proc')->name('procedimiento.agregar_cie10');

Route::get('historiaclinica/video/mostar_foto/eliminar/{id}', 'hc4\ProcedimientosEndoscopicosController@mostrar_foto_eliminar')->name('hc4_mostrar_foto_eliminar');

Route::get('proc_endos/hallazgos/mostar_plantilla/{id}', 'hc4\ProcedimientosEndoscopicosController@mostrar_proc_endo_plantilla')->name('hc4_proc_endos_plantilla');

Route::get('historiaclinica/video/eliminar_foto/eliminar/{id}', 'hc4\ProcedimientosEndoscopicosController@eliminar_foto_eliminar')->name('hc4_eliminar_foto_eliminar');

// Master de Consultas y Procedimientos
Route::get('inicio/master/consultas/procedimientos', 'hc4\ConsultaProcedimientoController@index')->name('hc4_master.consultas_procedimientos');

Route::match(['get', 'post'], 'inicio2/calendario_fullcalendar', 'hc4\Hc4Controller@calendario')->name('hc4.calendario_fullcalendar');

Route::post('plantilla/procedimientos/recupera/tecnica/quiri/plantilla', 'hc4\ProcedimientosEndoscopicosController@tecnica_plantilla')->name('procedimiento.tecnica_plantilla');

// 27/05/2019

Route::get('hc4/agendar_doctor/{id}/{i}', 'hc4\Hc4Controller@hc4_agendar_doctor')->name('hc4/agendar_dr.hc4_agendar_doctor');
Route::get('hc4/agendar_reunion/{id}/{i}', 'hc4\Hc4Controller@hc4_agendar_reunion')->name('hc4/agendar_dr.hc4_reunion');

Route::post('historiaclinica4/documentos4/grabacion_captura_documentos/', 'hc4\Hc4Controller@guardado_foto2_documento')->name('hc4.documento_guardar');
Route::post('historiaclinica4/estudios4/grabacion_captura_estudios/', 'hc4\Hc4Controller@guardado_foto2_estudios')->name('hc4.estudio_guardar');

Route::get('hc4/busqueda_datos/{id}', 'hc4\ProcedimientosFuncionalesController@mostrar_div');
Route::get('hc4/busqueda_datos/')->name('hc_4.mostar_div');

Route::get('inicio/img/{id_paciente}', 'hc4\EstudiosController@index')->name('hc4.estudios_paciente');

Route::post('hc4/datos_filiacion', 'hc4\HistoriaPacienteController@d_filiacion')->name('hc4.datos_filiacion');

Route::get('hc4/filiacion/editarcortesia/{id}/{c}', 'hc4\HistoriaPacienteController@actualizacortesia')->name('hc4_filiacion.cortesia');

// Carga CPRE+ECO
/* Route::get('hc4_cpre_eco/modal_cpre_eco/{hcid}', 'hc4\ProcedimientosEndoscopicosController@modal_cpre_eco_hc4')->name('hc4_cpre_eco._cpre_eco_modal'); */

Route::get('hc4_cpre_eco/modal_cpre_eco/{hcid}', 'hc4\ProcedimientosEndoscopicosController@modal_cpre_eco_hc4')->name('hc4_cpre.eco_modal');

Route::post('hc4_protocolo_cpre_eco/modal/', 'hc4\ProcedimientosEndoscopicosController@modal_hc4_crear_editar')->name('protocolo_hc4_cpre_eco.modal_crear_editar');

Route::get('inicio/plantillas/procedimientos', 'hc4\PlantillaProcedimientoController@index')->name('hc4/plantilla_proc.index');

Route::post('plantilla/procedimientos/buscar/', 'hc4\PlantillaProcedimientoController@search')->name('hc4/plantilla_proc.search');

Route::get('editar/plantilla/proc_endoscopico')->name('hc4/plantilla_proc.edit');
Route::get('editar/plantilla/proc_endoscopico/{id}', 'hc4\PlantillaProcedimientoController@edit');

Route::post('inicio/plantilla_proc/crear/', 'hc4\PlantillaProcedimientoController@create')->name('hc4/plantilla_proc.create');

Route::post('inicio/plantilla_proc/guardar', 'hc4\PlantillaProcedimientoController@store')->name('hc4/plantilla_proc.store');

Route::match(['get', 'post'], 'plantilla_proc_nuevo/actualizar/', 'hc4\PlantillaProcedimientoController@update')->name('hc4/plantilla_proc.update');

Route::match(['get', 'post'], 'hc4/cie10/agregar/consulta', 'hc4\ConsultaController@agregar_cie10')->name('hc4/epicrisis.agregar_cie10');

Route::get('unico/proc_endos/regresar/', 'hc4\ProcedimientosEndoscopicosController@unico_proc_index')->name('hc4/regresar_proc_endo');
Route::get('unico/proc_endos/regresar/{id}/{id_paciente}', 'hc4\ProcedimientosEndoscopicosController@unico_proc_index');

Route::get('inicio/epicrisis/edit/{id_paciente}', 'hc4\EpicrisisController@index')->name('hc4_paciente.epicrisis');
Route::get('inicio/proximos/procedimientos/{id_paciente}', 'hc4\Hc4Controller@index_proximos_proc')->name('hc4.index_proximos_proc');

Route::match(['get', 'post'], 'lab/grafico', 'hc4\LaboratorioController@grafico')->name('hc4/laboratorio.grafico');

// Crea Visita Onni Hospital
// Route::get('visita/evolucion_visita/{id}/{ag}', 'hc4\VisitaController@crear_visita');
// Route::get('visita/evolucion_visita/', 'hc4\VisitaController@crear_visita')->name('visita.crear_nueva_visita');

Route::get('visita/evolucion_visita/{id}/{ag}', 'hc4\ConsultaController@crear_visita');
Route::get('visita/evolucion_visita/', 'hc4\ConsultaController@crear_visita')->name('visita.crear_nueva_visita');

// Para para acceder a las visitas
// Route::get('inicio/cargar/visitas/{id_paciente}', 'hc4\VisitaController@index')->name('paciente.visita');

// Actualizar Visita
// Route::match(['get', 'post'],'historiaclinica/visita/actualizar', 'hc4\VisitaController@actualizar_visita')->name('visita.modificacion');

Route::match(['get', 'post'], 'hc4/consultam/pastelpentax/buscar', 'hc4\Hc4Controller@pasteles_hc4')->name('hc4_consulta.pasteles_hc4');
Route::match(['get', 'post'], 'hc4/consultam/estimado/ganacia/buscar', 'hc4\Hc4Controller@ganancia_hc4')->name('hc4_consulta.ganancia_hc4');
// Ganancia Efectiva
Route::match(['get', 'post'], 'hc4/consultam/estimado/ganacia/efectiva/buscar', 'hc4\Hc4Controller@ganancia_efectiva')->name('hc4.ganancia_efectiva');

// para obtener las Ordenes de Procedimientos de los Doctores
Route::get('inicio_hc4/ordenes/procedimientos', 'hc4\ordenes\Orden_Proc_EndosController@buscador_contador_ordenes_proced')->name('buscar_hc4.ordenes_procedimiento');

// Buscador Ordenes de Procedimientos
Route::post('buscador_contador/anio_mes/ordenes/procedimientos', 'hc4\ordenes\Orden_Proc_EndosController@buscar_anio_mes_ord')->name('hc4_busqueda.anio_mes_ord');

// Agregar Prescripcion Biopsia
Route::match(
    ['get', 'post'],
    'biopsia/agregar/proc/endos',
    'hc4\ProcedimientosEndoscopicosController@agregar_biopsia_proc'
)->name('procedimiento.agregar_biopsia');

// Carga Tabla Prescripcion Biopsias al Iniciar
Route::get('biopsia/cargar/tabla_prescripcion/', 'hc4\ProcedimientosEndoscopicosController@cargar_biopsia_frasco')->name('biopsias.carga_prescripcion');
Route::get('biopsia/cargar/tabla_prescripcion/{id}', 'hc4\ProcedimientosEndoscopicosController@cargar_biopsia_frasco');

Route::get('hc4_prescripcion_biopsia/eliminar/{id}', 'hc4\ProcedimientosEndoscopicosController@eliminar_prescripcion')->name('biopsia_prescripcion.eliminar');

// para poder imprimir los detalles y Observacion de Biopsias
Route::get('iniciohc4/imprime/biopsias/{id}/{id_hcid}/{id_doct}', 'hc4\ProcedimientosEndoscopicosController@imprime_biopsia')->name('imprimir.biopsias_hc4');

// para obtener cuadro clinico y diagnostico
Route::post('plantilla/procedimientos/cuadroclinico/diagnostico', 'hc4\ProcedimientosEndoscopicosController@obtener_cuad_diagnost')->name('obtener.cuadclin_diagnostico');

// Actualiza cuadro clinico Orden de biopsias
Route::post('orden_biopsias/cuadroclinico/actualizar', 'hc4\ProcedimientosEndoscopicosController@actualiza_cuadclin_biopsia')->name('update.cuad_biopsia');

// Actualiza diagnostico Orden de biopsias
Route::post('orden_biopsias/diagnostico/actualizar', 'hc4\ProcedimientosEndoscopicosController@actualiza_diagnostico_biopsia')->name('update.diag_biopsia');

// LABORATORIO WEB ESTADISTICOS
Route::get('laboratorio/estadistico/hc4/{anio}/{mes}', 'hc4\LaboratorioController@cargar_anio_mes');
// REVISION DE PROCEDIMIENTOS
Route::post('revision/procedimientos/doctor', 'hc4\Hc4Controller@revisar_procedimientos')->name('hc4_revisar.procedimientos');
Route::get('revision/cargar/{id}/{var}', 'hc4\Hc4Controller@carga_revision')->name('hc4_revisar.carga_revision');
Route::post('revision/formulario/', 'hc4\Hc4Controller@revision_formulario')->name('hc4_revisar.formulario');
Route::get('revision/cambiar_supervision/{ch}/{id}', 'hc4\Hc4Controller@cambiar_supervision')->name('hc4controller.cambiar_supervision');
Route::get('revision/cambiar_crm/{ch}/{id}', 'hc4\Hc4Controller@cambiar_crm')->name('hc4controller.cambiar_crm');
Route::post('revision/procedimientos/doctor/seleccion_procs', 'hc4\Hc4Controller@formulario_procs')->name('hc4_revisar.formulario_procs');
Route::get('arevision/agenda/log/{id}', 'hc4\Hc4Controller@agenda_log')->name('hc4controller.agenda_log');
// EXPORTAR REVICION
Route::post('reporte/exportar_revision/', 'hc4\Hc4Controller@exportar_revision')->name('hc4controller.exportar_revision');

// modal vademecun
Route::post('hc4/revisar/informacion/vademecun', 'hc4\ConsultaController@vademecun');
Route::post('hc4/revisar/informacion/ventana', 'hc4\ConsultaController@vademecun2');
Route::get('inicio/procedimientos/evolucion/eliminar/{id_evolucion}', 'hc4\ProcedimientosEndoscopicosController@eliminar_evolucion');

Route::get('inicio/procedimientos/eliminar/{id_evolucion}', 'hc4\ProcedimientosEndoscopicosController@eliminar_procedimiento');
Route::get('consulta/agenda/hc4/logagenda/{id_agenda}', 'hc4\Hc4Controller@busca_log_agenda')->name('hc4controller.busca_log_agenda');

// carga hora inicio y fin cita
Route::get('consulta/inicio/{hcid}', 'hc4\ConsultaController@carga_hora_inicio')->name('consulta.carga_hora_inicio');
Route::get('consulta/fin/{hcid}', 'hc4\ConsultaController@carga_hora_fin')->name('consulta.carga_hora_fin');
Route::get('modalimage/edit/{hcid}', 'hc4\Hc4Controller@modalimagen')->name('hc4.modalimg4');
Route::post('uploadimage/edit', 'hc4\Hc4Controller@saveimage')->name('hc4.saveimage');

// Ordenes Fausto 7/06/2021
Route::get('consulta/ordernes/vista', 'OrdenesListadoController@index')->name('consulta.index');
Route::get('hc4_interconsulta/crear/{hcid}', 'hc4\ConsultaController@hc4_crear')->name('consulta.hc4_crear');
Route::post('hc4_interconsulta/actualizar', 'hc4\ConsultaController@hc4_guardar')->name('consulta.hc4_guardar');
Route::get('hc4_interconsulta/crear/{hcid}/imprimir', 'hc4\ConsultaController@hc4_imprimir')->name('consulta.hc4_imprimir');
Route::get('enfermeria/egreso/paciente/{id}', 'hc4\EgresoController@enfermero')->name('egreso.enfermero');
Route::post('enfermeria/egreso/actualizar', 'hc4\EgresoController@guardar_enfermero')->name('egreso.guardar_enfermero');
Route::get('enfermeria/egreso/guardar/{id_procedimiento}/{condicion}/{conducido}', 'hc4\EgresoController@guardar_doctor')->name('egreso.guardar_doctor');
Route::post('hc4_interconsulta/crear/hcid/imprimir/buscar', 'hc4\ConsultaController@examenes_buscar_publicos_otros')->name('consulta.examenes_buscar_publicos_otros');
Route::get('inicio/procedimientos_funcionales/prueba_pulmonar/{hcid}/{id_procedimiento}', 'hc4\ProcedimientosFuncionalesController@index_prueba_pulmonar')->name('procedimiento_funcional.index_prueba_pulmonar');
Route::post('inicio/procedimientos_funcionales/prueba_pulmonar/guardar', 'hc4\ProcedimientosFuncionalesController@guardar_pulmonar')->name('procedimiento_funcional.guardar_pulmonar');
Route::get('hc4/imprimir/etiquetas/frascos/{id}', 'hc4\ProcedimientosEndoscopicosController@imprimir_etiquetas_frascos')->name('hc4.imprimir_etiquetas_frascos');
Route::get('imprimir/procedimientos_funcionales/prueba_pulmonar/{id}', 'hc4\ProcedimientosFuncionalesController@imprimir_pdf_pulmonar')->name('procedimiento_funcional.imprimir_pdf_pulmonar');

Route::get('procedimientoendoscopicos_datos/formulario013/cargar/{id_procedimiento}', 'hc4\ProcedimientosEndoscopicosController@cargar_formulario_013')->name('procedimiento_endoscopico.formulario013');
Route::get('vthospital/proc_endoscopicos/formulario013/cargar/{id_procedimiento}', 'hc4\ProcedimientosEndoscopicosController@hospital_cargar_formulario_013')->name('procedimiento_endoscopico.hospital_formulario013');

Route::post('procedimientoendoscopicos_datos/formulario013/update', 'hc4\ProcedimientosEndoscopicosController@formulario_013_update')->name('hc4.formulario013.update');

Route::get('formulario013B/pdf/{id}', 'pdf_hospital\Formulario007Controller@formulario013B')->name('formulario.013B');
Route::get('informre/bioxias/{id}', 'pdf_hospital\Formulario007Controller@informe_bioxias_crear')->name('pdf_hospital.informe_bioxias_crear');
Route::match(['get', 'post'], 'informe/bioxias/edit/{id}', 'pdf_hospital\Formulario007Controller@informe_bioxias_edit')->name('pdf_hospital.informe_bioxias_edit');
Route::post('informe/bioxias/guardar', 'pdf_hospital\Formulario007Controller@informe_bioxias_guardar')->name('pdf_hospital.informe_bioxias_guardar');

Route::get('hc4/carga/insumos/{id_hcprocedimientos}', 'hc4\ProcedimientosEndoscopicosController@carga_insumos');

Route::get('inicio/laboratorio/hc4resultados/imprimir/por_orden/{id_orden}', 'hc4\LaboratorioController@imprimir_resultado_x_orden')->name('hc4laboratorio.imprimir_resultado_x_orden');
Route::get('formulario013A/pdf/{id_orden}', 'pdf_hospital\Formulario007Controller@formulario013A')->name('formulario.013A');
